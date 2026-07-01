<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Investment;
use App\Models\Member;
use App\Models\SavingsEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global quick search across members, deposits, expenses and investments.
     * Each section is only included if the user is allowed to view it.
     */
    public function quick(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['groups' => []]);
        }

        $user = $request->user();
        $like = '%' . $term . '%';
        $groups = [];

        // Members
        if ($user->can('view members')) {
            $members = Member::query()
                ->where(fn ($q) => $q
                    ->where('name', 'like', $like)
                    ->orWhere('member_code', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like))
                ->orderBy('name')
                ->limit(5)
                ->get();

            if ($members->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Members',
                    'icon' => 'users',
                    'items' => $members->map(fn ($m) => [
                        'title' => $m->name . ($m->status !== 'active' ? ' (Inactive)' : ''),
                        'subtitle' => trim(($m->member_code ?: '') . ($m->phone ? ' · ' . $m->phone : '')),
                        'url' => route('members.show', $m),
                    ])->all(),
                ];
            }
        }

        // Deposits
        if ($user->can('view deposits')) {
            $deposits = SavingsEntry::with('member')
                ->where(fn ($q) => $q
                    ->where('transaction_id', 'like', $like)
                    ->orWhereHas('member', fn ($m) => $m->where('name', 'like', $like)))
                ->orderByDesc('deposit_date')
                ->limit(5)
                ->get();

            if ($deposits->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Deposits',
                    'icon' => 'dollar-sign',
                    'items' => $deposits->map(fn ($d) => [
                        'title' => ($d->member?->name ?? 'N/A') . ' — Tk ' . number_format($d->amount, 0),
                        'subtitle' => trim(($d->transaction_id ?: '') . ' · ' . ($d->deposit_date?->format('d M Y') ?? '')),
                        'url' => route('deposits.show', $d),
                    ])->all(),
                ];
            }
        }

        // Expenses
        if ($user->can('view expenses')) {
            $expenses = Expense::with('category')
                ->where(fn ($q) => $q
                    ->where('expense_number', 'like', $like)
                    ->orWhere('title', 'like', $like))
                ->orderByDesc('expense_date')
                ->limit(5)
                ->get();

            if ($expenses->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Expenses',
                    'icon' => 'file-text',
                    'items' => $expenses->map(fn ($e) => [
                        'title' => $e->title . ' — Tk ' . number_format($e->amount, 0),
                        'subtitle' => trim(($e->expense_number ?: '') . ' · ' . ($e->category?->name ?? '')),
                        'url' => route('expenses.show', $e),
                    ])->all(),
                ];
            }
        }

        // Investments
        if ($user->can('view investments')) {
            $investments = Investment::with('investmentType')
                ->where(fn ($q) => $q
                    ->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like))
                ->orderByDesc('start_date')
                ->limit(5)
                ->get();

            if ($investments->isNotEmpty()) {
                $groups[] = [
                    'label' => 'Investments',
                    'icon' => 'trending-up',
                    'items' => $investments->map(fn ($i) => [
                        'title' => $i->name,
                        'subtitle' => trim(($i->code ?: '') . ' · ' . ($i->investmentType?->name ?? '')),
                        'url' => route('investments.show', $i),
                    ])->all(),
                ];
            }
        }

        return response()->json(['groups' => $groups]);
    }
}
