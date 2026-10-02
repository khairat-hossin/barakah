# Association Investment Platform Project Plan

## 1. Product Summary

Build a web application for associations, savings groups, clubs, and informal investment syndicates that:

- collects recurring member contributions
- tracks shared funds and balances
- manages investment opportunities as projects
- records profit, loss, and distributions
- provides transparent reporting to members
- can be sold to other organizations as a hosted SaaS product

Working product direction:

- internal product name: `Association Investment Platform`
- initial stack: `Laravel + Bootstrap`
- initial deployment mode: `single organization`
- long-term commercial mode: `multi-tenant SaaS`

This should not be framed as only a custom app for one association. It should be built as a generic operating system for member-funded investment groups.

## 2. Core Problem

Groups that save together and invest together usually run operations in spreadsheets, chat groups, notebooks, and manual calculations. That creates predictable failures:

- poor visibility into who paid and who did not
- weak trust because balances and profit calculations are hard to verify
- no standard workflow for approving projects or tracking returns
- no audit trail for decisions, withdrawals, and distributions
- poor continuity when one admin leaves

The product solves that by making contributions, approvals, project funding, returns, and profit sharing visible and traceable.

## 3. Target Customers

Primary customers:

- savings associations
- community investment groups
- employee investment clubs
- family investment pools
- small member-based cooperatives

Secondary customers:

- NGOs or coordinators running multiple savings groups
- accountants or operators managing several associations
- consultants serving cooperative finance groups

## 4. Positioning

### Internal-use positioning

Use it first as your own association management platform.

### Commercial positioning

Sell it as software for groups that pool money monthly and invest in projects together.

### Better framing than "money collection app"

The stronger market position is:

`Contribution management + investment governance + profit distribution`

That is more differentiated than a simple ledger or member dues tracker.

## 5. Business Goals

Your product should support three business outcomes:

1. run your own association accurately and transparently
2. become reusable for other associations with minimal code changes
3. become licensable either as one-time sale or monthly SaaS

Suggested commercial models:

- one-time license + setup fee
- annual support and maintenance fee
- SaaS subscription per organization
- SaaS subscription by number of members
- white-label enterprise edition for large groups

## 6. Product Principles

These principles matter if you want this to be sellable:

- every money movement must be auditable
- every change to key financial records should be attributable to a user
- calculations must be deterministic and reproducible
- workflows should support both strict governance and lightweight groups
- organization data must be isolated from other organizations
- configuration should replace hardcoded business rules where possible

## 7. Recommended Product Scope

### MVP

The MVP should solve the whole operating loop for one association:

- organization setup
- member onboarding
- monthly contribution tracking
- fund balance management
- investment project creation
- contribution of association funds into projects
- project status tracking
- profit/loss recording
- distribution calculation
- payment and ledger reports
- admin and member dashboards

### Phase 2

Once the MVP is stable:

- installment schedules
- penalties for late payments
- member loans or advance withdrawals
- voting and approval workflow for projects
- document storage for project contracts and receipts
- notifications by email or SMS
- meeting minutes and decision logs
- export to PDF and Excel

### SaaS/Commercial Phase

When packaging for sale:

- multi-tenancy
- subscription billing
- organization-specific branding
- plan-based feature limits
- organization self-registration
- onboarding wizard
- support/admin super panel
- reseller or partner onboarding

## 8. Primary Users and Roles

Suggested roles:

- `Super Admin`: manages the SaaS platform and all tenant organizations
- `Organization Owner`: top-level controller for one association
- `Treasurer`: handles contributions, funds, payouts, and reconciliations
- `Project Manager`: tracks funded projects and updates performance
- `Auditor/Reviewer`: read-only access to finance and audit logs
- `Member`: views own payments, balances, distributions, and project summaries

Do not start with too many roles. For MVP:

- `Organization Owner`
- `Treasurer`
- `Member`

## 9. Core Modules

### 9.1 Organization Management

- organization profile
- currency, fiscal settings, contribution cycle
- rules for contribution amount
- policies for distribution and withdrawals

### 9.2 Member Management

- member profile
- status: active, inactive, suspended
- join date
- member share rules
- nomination or beneficiary details if needed later

### 9.3 Contribution Management

- monthly contribution generation
- manual or bulk payment entry
- due, paid, partial, overdue status
- receipts
- history per member

### 9.4 Fund Ledger

- cash in
- cash out
- transfer to project
- return from project
- adjustments
- running balance

This must be ledger-driven, not dashboard-driven. Dashboards should read from the ledger, not be the source of truth.

### 9.5 Investment Project Management

Each investment opportunity should be treated as a project with:

- title
- category
- description
- requested capital
- approved capital
- start date
- target end date
- status
- risk notes
- expected return
- actual return
- attachments

Project statuses:

- draft
- proposed
- approved
- funded
- active
- completed
- closed
- cancelled

### 9.6 Profit and Loss Management

- record project returns
- record direct expenses
- compute gross profit
- compute net distributable profit
- allocate share per member
- track paid and unpaid distributions

### 9.7 Reports

- member contribution report
- overdue report
- project profitability report
- fund balance report
- distribution report
- audit trail report

### 9.8 Notifications

For later phases:

- payment due reminders
- overdue alerts
- project approval notifications
- payout notifications

## 10. Critical Workflows

### Workflow A: Monthly contribution cycle

1. System generates monthly dues for active members
2. Treasurer records payments
3. Member sees updated contribution status
4. Ledger updates organization fund balance
5. Reports reflect paid and unpaid members

### Workflow B: New investment project

1. Admin creates project proposal
2. Association reviews and approves project
3. Treasurer allocates funds to project
4. Ledger records capital outflow
5. Project enters active state

### Workflow C: Project completion and distribution

1. Admin records returned capital and profit/loss
2. System calculates net result
3. Distribution rule allocates earnings
4. Treasurer marks payouts
5. Members can view summary and payment status

## 11. Key Product Decisions

These should be decided early because they affect the data model.

### 11.1 Contribution rule

Options:

- fixed equal amount for all members
- role-based or tier-based amount
- manual custom amount

Recommendation for MVP:

- fixed equal amount with optional per-member override

### 11.2 Profit distribution rule

Options:

- equal split across active members
- proportionate to contribution amount
- proportionate to total historical contribution
- project-specific participation based

Recommendation for MVP:

- support `equal split` and `contribution-weighted split`

### 11.3 Project funding model

Options:

- all projects funded from one shared pool
- members opt into specific projects

Recommendation:

- start with `shared pool`
- design schema so `project-specific participation` can be added later

## 12. SaaS Readiness Requirements

If you want to sell this later, these boundaries should exist from the beginning.

### Required from day one

- `organizations` table and organization-scoped data
- tenant-aware authorization checks
- organization-specific settings
- audit logging
- clean seeding for demo data
- no hardcoded organization names or rules in business logic

### Can wait until later

- tenant billing
- subdomain tenancy
- self-service sign-up
- white-label branding

### Multi-tenancy recommendation

For Laravel, prefer a single database with `organization_id` in tenant-owned tables for the first commercial version. It is simpler to operate, simpler to report across tenants, and enough for this category unless you later need strict isolated databases per client.

## 13. Technical Architecture

### Recommended stack

- backend: `Laravel`
- frontend templating: `Blade`
- UI: `Bootstrap` using your paid admin template
- auth: Laravel auth starter of your choice
- permissions: role/permission package or custom RBAC
- database: `MySQL` or `MariaDB`
- queue: `Redis` or database queue at first
- storage: local/S3-compatible object storage for attachments

### Architectural style

- server-rendered admin application
- modular domain structure
- service classes for finance calculations
- ledger-based accounting records
- policy-based authorization

### Suggested module boundaries

- `Auth`
- `Organizations`
- `Members`
- `Contributions`
- `Ledger`
- `Projects`
- `Distributions`
- `Reports`
- `Notifications`
- `Audit`
- `Billing` later

## 14. Suggested Database Entities

Core entities:

- organizations
- users
- organization_user
- members
- contribution_cycles
- contribution_dues
- contribution_payments
- ledger_accounts
- ledger_entries
- projects
- project_fundings
- project_returns
- distributions
- distribution_lines
- attachments
- audit_logs
- settings

Important modeling rule:

Money should flow through ledger entries even if there are specialized tables for payments, funding, and returns.

## 15. Non-Functional Requirements

The product becomes sellable only if these are taken seriously:

- auditability
- role-based access control
- transaction safety for financial writes
- backup and restore plan
- export capability
- pagination and performance for reports
- timezone and date handling consistency
- test coverage for financial calculations

## 16. Security Requirements

- hashed passwords and standard Laravel security defaults
- CSRF protection
- rate limiting on auth endpoints
- activity logs for sensitive changes
- authorization at route, policy, and query scope levels
- no direct cross-organization access
- optional 2FA later

## 17. MVP Screens

Admin side:

- login
- dashboard
- organization settings
- members list and member profile
- monthly contributions list
- payment entry screen
- fund ledger view
- projects list
- project create/edit/view
- project funding screen
- project return entry screen
- distributions list
- reports
- audit log

Member side:

- my dashboard
- my contributions
- my distributions
- project summaries
- profile

## 18. Reporting and Metrics

For operators:

- total collected this month
- total overdue
- available fund balance
- capital committed to active projects
- expected return vs actual return
- distribution outstanding

For product success:

- monthly active organizations
- payment recording frequency
- percentage of dues paid on time
- number of projects funded
- report export usage
- churn by tenant once SaaS starts

## 19. Delivery Roadmap

### Phase 0: Product definition

- finalize business rules
- finalize terminology
- finalize contribution and profit distribution logic
- define roles and permissions
- choose SaaS strategy and pricing direction

### Phase 1: Foundation

- Laravel project bootstrap
- Bootstrap admin template integration
- auth and roles
- organization-scoped architecture
- basic dashboard shell

### Phase 2: MVP finance operations

- members
- monthly dues
- payment recording
- ledger
- project management
- funding and returns
- distribution logic

### Phase 3: Reporting and hardening

- reporting
- exports
- audit logs
- validation improvements
- automated tests for finance logic

### Phase 4: Commercial packaging

- tenant onboarding
- subscriptions
- plan limits
- tenant admin setup
- demo environment

## 20. Recommended Build Sequence

Build in this order:

1. organization and user architecture
2. member management
3. contribution cycles and payments
4. ledger engine
5. investment projects and funding
6. project return and profit calculation
7. distribution and member statements
8. reports and audit logs
9. SaaS packaging and billing

This order is important because the ledger and finance model should stabilize before you build advanced reporting or SaaS billing.

## 21. Risks

### Product risks

- trying to support too many association models too early
- unclear profit-sharing rules across groups
- building features for rare edge cases before core operations are stable

### Technical risks

- weak tenant isolation
- mixing balance logic into controllers and views
- no canonical ledger model
- under-testing financial calculations

### Commercial risks

- product too customized to one association
- pricing too low for support burden
- lack of onboarding and reporting polish

## 22. What Not To Build First

Do not start with:

- mobile app
- public marketing website complexity
- advanced AI features
- deep accounting integrations
- multi-language support unless required immediately
- complex voting engine unless it is central to your operations

## 23. Practical Recommendation

The correct first version is:

- one codebase
- one association running in production
- tenant-aware schema from day one
- no subscription billing yet
- no self-service public signup yet
- strong ledger, reporting, and audit foundation

That gives you a usable internal tool and keeps the path open for SaaS without overbuilding too early.

## 24. Suggested MVP Statement

`A Laravel-based association investment management system that tracks member contributions, pooled funds, investment projects, returns, and profit distributions with full transparency and auditability.`

## 25. Immediate Next Planning Outputs

After this plan, the next useful artifacts are:

1. product requirements document
2. database schema draft
3. role and permission matrix
4. screen list with navigation map
5. milestone-based development backlog
6. SaaS packaging strategy

## 26. My Recommendation To You

Treat this as a `vertical financial operations product` for associations, not as a generic club management app.

That sharper framing helps with:

- scope control
- future pricing
- marketing clarity
- SaaS packaging
- feature prioritization

