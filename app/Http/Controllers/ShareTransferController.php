<?php

namespace App\Http\Controllers;

use App\Models\ShareTransfer;
use App\Models\Member;
use App\Models\Share;
use App\Models\MemberShareOwnership;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ShareTransferController extends Controller
{
    public function index(): View
    {
        $transfers = ShareTransfer::with(['fromMember', 'toMember', 'approver'])
            ->latest()
            ->paginate(15);

        $statusCounts = ShareTransfer::query()
            ->selectRaw('approval_status, COUNT(*) as count')
            ->groupBy('approval_status')
            ->pluck('count', 'approval_status')
            ->all();

        return view('share-transfers.index', [
            'transfers' => $transfers,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(): View
    {
        $authMember = auth()->user()->member;

        if (!$authMember) {
            return abort(403, 'User is not associated with a member profile');
        }

        $ownedShares = MemberShareOwnership::where('member_id', $authMember->id)
            ->whereNull('ownership_end_date')
            ->with('share')
            ->get();

        $members = Member::where('status', 'active')
            ->where('id', '!=', $authMember->id)
            ->orderBy('name')
            ->get();

        return view('share-transfers.create', [
            'ownedShares' => $ownedShares,
            'members' => $members,
            'fromMember' => $authMember,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $authMember = auth()->user()->member;

        if (!$authMember) {
            return back()->with('error', 'User is not associated with a member profile');
        }

        $validated = $request->validate([
            'to_member_id' => ['required', 'exists:members,id', 'not_in:' . $authMember->id],
            'share_ids' => ['required', 'array', 'min:1'],
            'share_ids.*' => ['exists:shares,id'],
            'transfer_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Verify member owns all shares
        $ownedShareIds = MemberShareOwnership::where('member_id', $authMember->id)
            ->whereNull('ownership_end_date')
            ->pluck('share_id')
            ->toArray();

        $unownedShares = array_diff($validated['share_ids'], $ownedShareIds);
        if (!empty($unownedShares)) {
            return back()->with('error', 'You do not own all the shares you are trying to transfer');
        }

        DB::transaction(function () use ($validated, $authMember, $request) {
            $transfer = ShareTransfer::create([
                'from_member_id' => $authMember->id,
                'to_member_id' => $validated['to_member_id'],
                'shares_json' => $validated['share_ids'],
                'share_count' => count($validated['share_ids']),
                'transfer_date' => $validated['transfer_date'],
                'approval_status' => 'pending',
                'remarks' => $validated['notes'] ?? null,
            ]);

            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'transfer_initiated',
                'entity_type' => 'ShareTransfer',
                'entity_id' => $transfer->id,
                'new_value' => $transfer->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        });

        \App\Support\Notify::admins(
            'Share transfer requested',
            $authMember->name . ' requested transfer of ' . count($validated['share_ids']) . ' share(s).',
            'repeat',
            route('share-transfers.index'),
        );

        return redirect()->route('share-transfers.index')
            ->with('success', 'Share transfer initiated and awaiting approval');
    }

    public function show(ShareTransfer $transfer): View
    {
        $transfer->load(['fromMember', 'toMember', 'approver', 'attachments.uploader']);

        return view('share-transfers.show', [
            'transfer' => $transfer,
        ]);
    }

    public function approve(ShareTransfer $transfer): View
    {
        if ($transfer->approval_status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending transfers can be approved']);
        }

        return view('share-transfers.approve', [
            'transfer' => $transfer,
        ]);
    }

    public function approveStore(Request $request, ShareTransfer $transfer): RedirectResponse
    {
        $this->authorize('approve', $transfer);

        if ($transfer->approval_status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending transfers can be approved']);
        }

        $validated = $request->validate([
            'approval_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($transfer, $validated, $request) {
            // Update transfer status
            $transfer->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approval_date' => $validated['approval_date'],
                'remarks' => $validated['remarks'],
            ]);

            // Create new ownership records
            foreach ($transfer->shares_json as $shareId) {
                // End previous ownership
                MemberShareOwnership::where('share_id', $shareId)
                    ->whereNull('ownership_end_date')
                    ->update(['ownership_end_date' => now()->subDay()->toDateString()]);

                // Create new ownership
                MemberShareOwnership::create([
                    'member_id' => $transfer->to_member_id,
                    'share_id' => $shareId,
                    'ownership_start_date' => $transfer->transfer_date,
                    'ownership_end_date' => null,
                    'transfer_reference' => $transfer->id,
                    'notes' => 'Transferred from ' . $transfer->fromMember->name,
                ]);
            }

            // Log approval
            AuditLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'transfer_approved',
                'entity_type' => 'ShareTransfer',
                'entity_id' => $transfer->id,
                'new_value' => $transfer->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        });

        $transfer->loadMissing(['fromMember', 'toMember']);
        \App\Support\Notify::admins(
            'Share transfer completed',
            ($transfer->fromMember?->name ?? 'Member') . ' → ' . ($transfer->toMember?->name ?? 'Member') . ' (' . $transfer->share_count . ' share(s))',
            'repeat',
            route('share-transfers.show', $transfer),
        );

        return redirect()->route('share-transfers.index')
            ->with('toast', ['type' => 'success', 'message' => 'Share transfer approved successfully']);
    }

    public function reject(ShareTransfer $transfer): View
    {
        if ($transfer->approval_status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending transfers can be rejected']);
        }

        return view('share-transfers.reject', [
            'transfer' => $transfer,
        ]);
    }

    public function rejectStore(Request $request, ShareTransfer $transfer): RedirectResponse
    {
        $this->authorize('approve', $transfer);

        if ($transfer->approval_status !== 'pending') {
            return back()->with('toast', ['type' => 'error', 'message' => 'Only pending transfers can be rejected']);
        }

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($transfer, $validated, $request) {
            $transfer->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approval_date' => now()->toDateString(),
                'remarks' => $validated['remarks'],
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'transfer_rejected',
                'entity_type' => 'ShareTransfer',
                'entity_id' => $transfer->id,
                'new_value' => $transfer->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        });

        return redirect()->route('share-transfers.index')
            ->with('toast', ['type' => 'success', 'message' => 'Share transfer rejected']);
    }
}
