<?php

namespace App\Services\Accounting;

use App\Models\AccountingEvent;
use App\Models\AccountingEventMapping;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Collection;

class AccountingEventService
{
    public function registerEvent(string $eventCode, string $eventName, string $eventType, string $description = null): AccountingEvent
    {
        return AccountingEvent::create([
            'event_code' => $eventCode,
            'event_name' => $eventName,
            'event_type' => $eventType,
            'description' => $description,
            'is_active' => true,
        ]);
    }

    public function addMapping(
        AccountingEvent $event,
        ChartOfAccount $debitAccount,
        ChartOfAccount $creditAccount,
        float $debitMultiplier = 1.0,
        float $creditMultiplier = 1.0,
        int $sequence = 1
    ): AccountingEventMapping {
        return AccountingEventMapping::create([
            'event_id' => $event->id,
            'debit_account_id' => $debitAccount->id,
            'credit_account_id' => $creditAccount->id,
            'debit_multiplier' => $debitMultiplier,
            'credit_multiplier' => $creditMultiplier,
            'sequence' => $sequence,
        ]);
    }

    public function getEvent(string $eventCode): ?AccountingEvent
    {
        return AccountingEvent::where('event_code', $eventCode)->first();
    }

    public function getActiveEvents(): Collection
    {
        return AccountingEvent::active()
            ->ordered()
            ->with('mappings')
            ->get();
    }

    public function getEventsByType(string $type): Collection
    {
        return AccountingEvent::active()
            ->byType($type)
            ->ordered()
            ->with('mappings')
            ->get();
    }

    public function getMappingsForEvent(AccountingEvent $event): Collection
    {
        return $event->mappings()
            ->ordered()
            ->get();
    }

    public function deactivateEvent(AccountingEvent $event): AccountingEvent
    {
        $event->update(['is_active' => false]);

        return $event;
    }

    public function activateEvent(AccountingEvent $event): AccountingEvent
    {
        $event->update(['is_active' => true]);

        return $event;
    }

    public function initializeDefaultEvents(ChartOfAccountsService $coaService): void
    {
        $user = auth()->user();

        // Create default Chart of Accounts
        if (ChartOfAccount::count() === 0) {
            $coaService->getInitialChartOfAccounts($user);
        }

        // Deposit Approved Event
        $depositEvent = $this->registerEvent(
            'DEPOSIT_APPROVED',
            'Deposit Approved',
            'DEPOSIT',
            'Member deposit approved and posted to account'
        );

        $this->addMapping(
            $depositEvent,
            ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first(),
            ChartOfAccount::where('code', '2100')->first()
        );

        // Expense Approved Event
        $expenseEvent = $this->registerEvent(
            'EXPENSE_APPROVED',
            'Expense Approved',
            'EXPENSE',
            'Organization expense approved and posted'
        );

        $this->addMapping(
            $expenseEvent,
            ChartOfAccount::where('code', '5100')->first(),
            ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first()
        );

        // Investment Created Event
        $investmentEvent = $this->registerEvent(
            'INVESTMENT_CREATED',
            'Investment Created',
            'INVESTMENT',
            'New investment initiated'
        );

        $this->addMapping(
            $investmentEvent,
            ChartOfAccount::where('code', '1300')->first(),
            ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first()
        );

        // Investment Profit Event
        $profitEvent = $this->registerEvent(
            'INVESTMENT_PROFIT',
            'Investment Profit Received',
            'INVESTMENT',
            'Profit from investment received'
        );

        $this->addMapping(
            $profitEvent,
            ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first(),
            ChartOfAccount::where('code', '4100')->first()
        );

        // Share Issued Event
        $shareEvent = $this->registerEvent(
            'SHARE_ISSUED',
            'Share Capital Issued',
            'SHARE',
            'New shares issued to member'
        );

        $this->addMapping(
            $shareEvent,
            ChartOfAccount::where('code', '1100')->first() ?? ChartOfAccount::where('code', '1200')->first(),
            ChartOfAccount::where('code', '3100')->first()
        );
    }
}
