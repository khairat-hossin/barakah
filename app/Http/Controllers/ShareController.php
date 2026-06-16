<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\Member;
use App\Models\MemberShareOwnership;
use App\Models\OrganizationProfile;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShareController extends Controller
{
    public function index(): View
    {
        $shares = Share::with(['currentOwner.member'])
            ->orderBy('share_number')
            ->get()
            ->map(function (Share $share) {
                $share->current_owner_name = $share->currentOwner?->member?->name ?? 'Unallocated';
                $share->current_owner_id = $share->currentOwner?->member_id;
                return $share;
            });

        $allocated = $shares->filter(fn(Share $s) => $s->current_owner_id)->count();
        $unallocated = 40 - $allocated;

        return view('shares.index', [
            'shares' => $shares,
            'allocated' => $allocated,
            'unallocated' => $unallocated,
            'totalShares' => 40,
        ]);
    }

    public function show(Share $share): View
    {
        $share->load(['ownershipHistory.member', 'currentOwner.member']);

        return view('shares.show', [
            'share' => $share,
        ]);
    }

    public function distribution(): View
    {
        $orgProfile = OrganizationProfile::first();
        $totalShares = $orgProfile?->total_shares ?? 0;

        $memberShares = Member::withCount([
            'shares' => function ($query) {
                $query->current();
            }
        ])
        ->orderBy('name')
        ->get();

        $assignedShares = $memberShares->sum('shares_count');
        $availableShares = $totalShares - $assignedShares;

        return view('shares.distribution', [
            'memberShares' => $memberShares,
            'totalShares' => $totalShares,
            'assignedShares' => $assignedShares,
            'availableShares' => $availableShares,
        ]);
    }

    public function updateMemberShares(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'share_count' => ['required', 'integer', 'min:0'],
        ]);

        $orgProfile = OrganizationProfile::first();
        $totalShares = $orgProfile?->total_shares ?? 0;

        $currentShares = $member->shares()
            ->current()
            ->count();

        // Calculate assigned shares for other members using direct count
        $assignedShares = MemberShareOwnership::whereNull('ownership_end_date')
            ->whereIn('member_id', Member::where('id', '!=', $member->id)->pluck('id'))
            ->count();

        $newShareCount = $validated['share_count'];
        $totalAfterUpdate = $assignedShares + $newShareCount;

        if ($totalAfterUpdate > $totalShares) {
            return response()->json([
                'error' => "Cannot assign {$newShareCount} shares. Total would be {$totalAfterUpdate} but maximum is {$totalShares}.",
                'errors' => ['share_count' => ["Exceeds available shares. Maximum: " . ($totalShares - $assignedShares + $currentShares)]]
            ], 422);
        }

        // Update shares for this member
        if ($newShareCount > $currentShares) {
            // Add new shares
            $sharesToAdd = $newShareCount - $currentShares;
            $lastShareNumber = Share::max('share_number') ?? 0;
            for ($i = 1; $i <= $sharesToAdd; $i++) {
                $share = Share::create([
                    'share_number' => $lastShareNumber + $i,
                    'issue_date' => now(),
                    'status' => 'active',
                ]);
                MemberShareOwnership::create([
                    'member_id' => $member->id,
                    'share_id' => $share->id,
                    'ownership_start_date' => now(),
                ]);
            }
        } elseif ($newShareCount < $currentShares) {
            // Remove shares (end ownership)
            $sharesToRemove = $currentShares - $newShareCount;
            $currentOwnerships = $member->shares()
                ->current()
                ->limit($sharesToRemove)
                ->get();

            foreach ($currentOwnerships as $share) {
                $share->ownershipHistory()
                    ->where('member_id', $member->id)
                    ->whereNull('ownership_end_date')
                    ->update(['ownership_end_date' => now()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Shares updated successfully for {$member->name}"
        ]);
    }
}
