# BARAKAH Accounting Core & Financial Engine - Architecture Design

## Overview
Event-driven accounting system that automatically generates accounting entries from business operations following double-entry accounting principles.

---

## System Architecture

### Core Components

1. **Chart of Accounts (COA)** - Hierarchical account master
2. **Journal Engine** - Creates and manages journal vouchers
3. **Posting Engine** - Automatically posts entries from business events
4. **General Ledger** - Generates ledger from journal entries (on-demand)
5. **Trial Balance Engine** - Validates double-entry integrity
6. **Financial Statements Engine** - Generates Income Statement, Balance Sheet, Cash Flow

---

## Chart of Accounts Hierarchy

### Asset Accounts (1000-1999)
- 1100 Cash
- 1200 Bank
- 1300 Investments
- 1400 Receivables

### Liability Accounts (2000-2999)
- 2100 Member Deposits
- 2200 Payables

### Equity Accounts (3000-3999)
- 3100 Share Capital
- 3200 Retained Earnings

### Income Accounts (4000-4999)
- 4100 Investment Income
- 4200 Other Income

### Expense Accounts (5000-5999)
- 5100 Meeting Expense
- 5200 Office Expense
- 5300 Bank Charges
- 5400 Miscellaneous Expense

---

## Event-Driven Posting Rules

### Event: Deposit Approved
```
Dr Cash/Bank (1100/1200)
Cr Member Deposits (2100)
```

### Event: Expense Approved
```
Dr Expense Account (5XXX)
Cr Cash/Bank (1100/1200)
```

### Event: Investment Created
```
Dr Investment Asset (1300)
Cr Cash/Bank (1100/1200)
```

### Event: Investment Profit Received
```
Dr Cash/Bank (1100/1200)
Cr Investment Income (4100)
```

### Event: Investment Loss Recorded
```
Dr Investment Loss (Expense)
Cr Investment Asset (1300)
```

### Event: Share Capital Issued
```
Dr Cash/Bank (1100/1200)
Cr Share Capital (3100)
```

---

## Database Schema

### Tables

1. **chart_of_accounts**
   - id (UUID PK)
   - code (string, unique) - e.g., "1100"
   - name (string) - e.g., "Cash"
   - parent_id (UUID FK, nullable) - For hierarchical structure
   - account_type (enum) - ASSET, LIABILITY, EQUITY, INCOME, EXPENSE
   - normal_balance (enum) - DEBIT, CREDIT
   - is_active (boolean)
   - description (text, nullable)
   - created_by (FK to users)
   - updated_by (FK to users)
   - deleted_at (soft delete)
   - created_at, updated_at

2. **accounting_events**
   - id (UUID PK)
   - event_code (string, unique) - e.g., "DEPOSIT_APPROVED"
   - event_name (string)
   - event_type (enum) - DEPOSIT, EXPENSE, INVESTMENT, SHARE, OTHER
   - description (text)
   - is_active (boolean)
   - created_at, updated_at

3. **accounting_event_mappings**
   - id (UUID PK)
   - event_id (FK to accounting_events)
   - debit_account_id (FK to chart_of_accounts)
   - credit_account_id (FK to chart_of_accounts)
   - debit_multiplier (decimal) - 1.0 for normal, -1.0 for reversed
   - credit_multiplier (decimal) - 1.0 for normal, -1.0 for reversed
   - sequence (integer) - For multi-line entries
   - created_at, updated_at

4. **journal_vouchers**
   - id (UUID PK)
   - voucher_number (string, unique) - e.g., "JV-2026-000001"
   - voucher_date (date)
   - voucher_type (enum) - DEPOSIT, EXPENSE, INVESTMENT, SHARE, MANUAL, REVERSAL
   - source_module (string) - e.g., "deposits", "expenses", "investments"
   - source_record_id (UUID, nullable)
   - description (text)
   - status (enum) - DRAFT, POSTED, REVERSED
   - posted_date (timestamp, nullable)
   - posted_by (FK to users, nullable)
   - reversed_date (timestamp, nullable)
   - reversed_by (FK to users, nullable)
   - reversal_reason (text, nullable)
   - created_by (FK to users)
   - created_at, updated_at
   - deleted_at (soft delete)

5. **journal_entries**
   - id (UUID PK)
   - voucher_id (FK to journal_vouchers)
   - account_id (FK to chart_of_accounts)
   - debit_amount (decimal, 14,2) - NULL if credit
   - credit_amount (decimal, 14,2) - NULL if debit
   - description (text, nullable)
   - entry_sequence (integer)
   - created_at, updated_at

6. **accounting_audit_logs**
   - id (UUID PK)
   - entity_type (string) - e.g., "journal_voucher", "chart_of_accounts"
   - entity_id (UUID)
   - action (enum) - CREATED, POSTED, REVERSED, UPDATED
   - user_id (FK to users)
   - old_values (json)
   - new_values (json)
   - ip_address (string)
   - user_agent (text, nullable)
   - timestamp (timestamp)

---

## Service Layer Architecture

### ChartOfAccountsService
- Create account with validation
- Update account
- Hierarchy management
- Account balance calculation
- Activate/Deactivate accounts

### AccountingEventService
- Register new events
- Map events to GL entries
- Support dynamic event configuration

### JournalEngine
- Create journal vouchers
- Validate double-entry
- Handle voucher numbering
- Transaction management

### PostingEngine
- Listen to business events
- Create automatic journal entries
- Apply event mappings
- Update GL automatically

### GeneralLedgerService
- Generate ledger on-demand (no storage)
- Calculate running balances
- Date range filtering
- Export functionality

### TrialBalanceService
- Generate trial balance
- Validate total debits = total credits
- Account-wise drill-down

### FinancialStatementsService
- Income Statement
- Balance Sheet
- Cash Flow Statement
- Fund Position Report

---

## API Endpoints

### Chart of Accounts
```
GET    /api/accounts              (List accounts with hierarchy)
GET    /api/accounts/:id          (Account detail)
POST   /api/accounts              (Create account)
PUT    /api/accounts/:id          (Update account)
DELETE /api/accounts/:id          (Soft delete)
GET    /api/accounts/tree         (Hierarchical tree)
```

### Journal Vouchers
```
GET    /api/journal-vouchers              (List vouchers)
GET    /api/journal-vouchers/:id          (Voucher detail with entries)
POST   /api/journal-vouchers              (Create manual voucher - DRAFT)
POST   /api/journal-vouchers/:id/post     (Post voucher)
POST   /api/journal-vouchers/:id/reverse  (Reverse voucher)
```

### Reports
```
GET    /api/reports/general-ledger/:account_id  (GL for account)
GET    /api/reports/trial-balance               (Trial balance)
GET    /api/reports/income-statement            (Period-based)
GET    /api/reports/balance-sheet               (As-of date)
GET    /api/reports/cash-flow                   (Period-based)
GET    /api/reports/fund-position               (Fund balances)
```

---

## Role-Based Permissions

### Accountant
- View Chart of Accounts
- Create/Update Chart of Accounts
- View Journal Vouchers
- Post Manual Journal Vouchers
- View All Reports
- Export Reports

### Treasurer
- View Chart of Accounts
- View Journal Vouchers
- View Reports (own business area)

### Admin
- Full accounting access
- Configure accounting events
- Manage audit logs
- System configuration

---

## Implementation Phases

### Phase 1: Database & Models
- Create migrations
- Create models (ChartOfAccount, JournalVoucher, JournalEntry, etc.)
- Create scopes and relationships

### Phase 2: Service Layer
- Implement Chart of Accounts Service
- Implement Journal Engine
- Implement Posting Engine (event listeners)
- Implement Report Services

### Phase 3: API & Controllers
- Chart of Accounts Controller
- Journal Voucher Controller
- Reports Controller

### Phase 4: Event Integration
- Integrate with Deposits module
- Integrate with Expenses module
- Integrate with Investments module
- Integrate with Shares module

### Phase 5: Views & Dashboard
- Chart of Accounts Management
- Journal Entry Views
- Reports Dashboard
- Accounting Dashboard

### Phase 6: Testing & Documentation
- Unit tests
- Integration tests
- API documentation

---

## Future Extensibility

### Ready for:
- Loans Module (2300 Loans Payable)
- Advances Module (1500 Member Advances)
- Profit Distribution (4300 Profit Distribution Income)
- Member Accounts (Individual ledgers per member)
- Multi-year rollover
- Budget vs Actual comparison

---

## Security & Audit

- All accounting transactions are immutable
- Reversals create opposing entries (not deletions)
- Complete audit trail per transaction
- Role-based access control
- Change tracking on all GL tables
- Created by / Modified by / Approved by tracking

---

## Design Principles

1. **Event-Driven**: Business modules trigger accounting entries
2. **Immutable Ledger**: No physical deletions, only reversals
3. **Double-Entry**: Always balanced (Dr = Cr)
4. **On-Demand Reporting**: Ledger generated from journal entries
5. **Extensible**: Easy to add new events and mappings
6. **Audit-Ready**: Complete history of all transactions
7. **Role-Based**: Different views based on permissions

