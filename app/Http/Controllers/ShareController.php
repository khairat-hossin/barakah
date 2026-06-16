<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\MemberShareOwnership;
use Illuminate\View\View;

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
}
