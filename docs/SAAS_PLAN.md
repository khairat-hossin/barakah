# Echo of Unity → SaaS — Comprehensive Conversion Plan

**Purpose:** Turn the current single-organisation platform into a multi-tenant SaaS where many associations sign up, each with isolated data, branding, subscription, and users.
**Status:** Strategy & architecture blueprint. Not to be started until the current single-tenant app is stable in production (bugs + feedback resolved).
**Author's note:** This is a long-run plan. It is intentionally sequenced so the live Echo of Unity instance keeps running throughout the transition and becomes "tenant #1".

---

## 1. Executive summary

The platform is a well-scoped domain app for a savings/investment association: members, shares, monthly deposits & dues, loans, investments, expenses, double-entry accounting, governance, and reporting. That exact feature set is valuable to **thousands of similar organisations** — cooperative societies, samitis, community funds, welfare associations, ROSCAs/DPS-style groups — who today run on spreadsheets.

**Recommendation:** convert using **database-per-tenant multi-tenancy** (via `stancl/tenancy`). This is the lowest-risk, fastest path because the codebase is already written as if it serves one organisation — each tenant simply gets a database that looks like today's. Business logic barely changes; the work is in the tenancy shell, onboarding, billing, and a platform admin.

**Why this matters technically:** the single-org assumption lives in ~27 `OrganizationProfile::first()` calls and 56 tenant-scoped tables with no `tenant_id`. Row-level (shared-schema) tenancy would require touching all of them + global scopes + auth + permissions. Database-per-tenant sidesteps almost all of that.

---

## 2. Current architecture — SaaS-readiness analysis

| Aspect | Today (single-tenant) | Implication for SaaS |
|---|---|---|
| Organisation identity | One `OrganizationProfile` row; `OrganizationProfile::first()` in ~27 places | Already the *de facto* tenant. DB-per-tenant keeps `first()` working per tenant. |
| Data model | 56 tables (members, deposits, shares, loans, investments, expenses, accounting…) — **no `tenant_id`** | Row-level tenancy = add `tenant_id` to all + global scopes (big). DB-per-tenant = zero schema change. |
| Users | Global `users` table, no org link | Needs a tenant↔user relationship (central) and per-tenant membership. |
| Roles/permissions | Spatie, `teams => false` (global) | Per-tenant roles needed. DB-per-tenant → roles live in each tenant DB, no `teams` flag required. |
| Branding | `OrganizationProfile` + `Branding` support class (logo, name, motto, colours) | **Strong foundation** — per-tenant white-label is already ~80% there. |
| Storage | `local/public/s3/private` disks; member photos & logos on `public` | Must isolate per tenant (S3 prefix/bucket). S3 already configured. |
| Auth | tyro-login + OTP 2FA + multi-identifier (email/phone/code) | Keep as-is per tenant; add tenant resolution before auth. |
| Sessions/queue | `database` driver | Fine; make jobs tenant-aware. |
| Config | env-driven (OTP, branding, deposit start month, etc.) | Move tenant-specific settings into tenant DB (mostly already in `OrganizationProfile`). |

**Verdict:** the app is single-tenant but *clean* — one org, one config object, one DB. That cleanliness is exactly what makes **database-per-tenant** cheap to adopt.

---

## 3. Product vision

- **What we sell:** "Run your savings & investment association online" — members, monthly collection & dues tracking, loans, investments, transparent accounting, and reports, with a member mobile app.
- **Target tenants:** cooperative societies, samitis/somities, DPS/ROSCA groups, welfare & community funds, alumni/staff associations, micro-SACCOs. Initially Bangladesh; the model generalises regionally.
- **Personas:**
  - **Platform owner (you):** manages tenants, plans, billing, support.
  - **Tenant admin/committee:** the association's Super Admin/Treasurer — runs their org.
  - **Member:** views own dues/deposits/loans, uses the mobile app.
- **Core value:** replaces spreadsheets/paper with transparent, auditable, mobile-accessible fund management — with built-in trust (double-entry accounting, audit logs, constitution).

---

## 4. Multi-tenancy strategy

### 4.1 Options compared
| Strategy | Isolation | Code change from today | Ops cost | Best when |
|---|---|---|---|---|
| **Shared DB + `tenant_id`** (row-level) | Logical only | **High** — add `tenant_id` to 56 tables, global scopes, rewrite `OrganizationProfile::first()`, scope auth & permissions | Low | Very many small tenants |
| **Database-per-tenant** ✅ | **Physical** (separate DB) | **Low** — tenancy shell + provisioning; business logic ~unchanged | Medium | Hundreds of mid-size tenants, strong isolation, fast to market |
| **Schema-per-tenant** (Postgres) | Strong | Medium | Medium | Postgres shops wanting fewer DBs |

### 4.2 Recommendation — **Database-per-tenant** via `stancl/tenancy`
- **Central (landlord) DB:** `tenants`, `domains`, `plans`, `subscriptions`, `invoices`, platform users, feature flags, usage counters.
- **Tenant DB (one per org):** everything the app has today — members, deposits, shares, loans, investments, expenses, accounting, roles/permissions, users-of-that-tenant. `OrganizationProfile::first()` stays valid **inside** each tenant DB.
- **Tenant resolution:** subdomain first — `acme.eou.app` → tenant `acme`. Custom domains (`portal.acme.org`) as a paid add-on later.
- **Trade-off accepted:** running migrations/seeders across N tenant DBs and provisioning overhead. Mitigated with tenant-aware `artisan tenants:migrate` (package-provided) and queued provisioning. Revisit shared-schema only if tenant count reaches the thousands and DB sprawl becomes a cost problem.

### 4.3 Request lifecycle
```
Request → resolve tenant (subdomain/domain) → switch DB connection + storage disk + cache prefix
        → boot tenant context → normal app (auth, permissions, controllers) runs unchanged
Central routes (signup, billing, platform admin) run WITHOUT tenant context.
```

---

## 5. Data isolation & security
- **DB:** each tenant a separate database → hard isolation, per-tenant backup/restore, and simple "export my data / delete my org".
- **Storage:** S3 with a per-tenant prefix (`tenants/{id}/…`) or bucket; tenancy package rewrites the `public`/`private` disk roots automatically. Member photos & logos become tenant-scoped.
- **Cache/queue/session:** prefix cache keys per tenant; make queued jobs re-enter tenant context (package supports this) so a deposit-reminder job runs in the right DB.
- **Cross-tenant leakage guards:** central-vs-tenant route separation, automated tests that assert tenant A can never read tenant B, and a "no tenant context on central routes" invariant.
- **Backups:** nightly per-tenant DB dumps + S3 prefix snapshots; documented restore runbook.

---

## 6. Auth & identity
- Keep the existing **tyro-login + OTP 2FA + multi-identifier** login — but **tenant is resolved before auth** (from subdomain), so a member logs in *within their org*.
- **User model:** central registry maps a person to tenant(s); credentials + roles live in the tenant DB (simplest with DB-per-tenant). One email can belong to different tenants without collision because user tables are separate.
- **Roles/permissions:** unchanged Spatie usage, but per tenant DB → no `teams` flag needed. Seed the standard role set (Super Admin, Association Admin, Treasurer, Project Manager, Member) on tenant creation.
- **Platform users** (you/support) live centrally with an **impersonation** capability to enter any tenant for support.
- Future: SSO/social login, and a member belonging to multiple associations with an org-switcher.

---

## 7. Tenant lifecycle
1. **Signup** (central): org name, subdomain, admin name/email, plan/trial. Public marketing site + signup form.
2. **Provisioning** (queued job): create DB → run tenant migrations → seed roles/permissions, chart of accounts, default payment methods/expense categories, and an `OrganizationProfile` → create the first Super Admin → send welcome/verify email.
3. **Trial → paid:** 14-day trial, then subscription required; grace period + read-only lock on lapse.
4. **Suspension:** non-payment/abuse → tenant marked suspended → login blocked with a billing notice (data retained).
5. **Offboarding:** self-serve **data export** (per-tenant DB dump + files) and **hard delete** after a retention window (drop DB + purge S3 prefix).

---

## 8. Subscription & billing
- **Plan tiers (illustrative):**

| Tier | Members cap | Modules | Storage | Price model |
|---|---|---|---|---|
| Free/Trial | ≤ 25 | Members, Deposits, Dues | 100 MB | 14-day trial |
| Starter | ≤ 100 | + Loans, basic reports | 1 GB | flat monthly |
| Pro | ≤ 500 | + Investments, Accounting, Governance | 10 GB | flat monthly |
| Enterprise | custom | all + custom domain, priority support, SLA | custom | quote |

- **Billing engine:** **Laravel Cashier (Stripe)** for cards/international; **local gateways (SSLCommerz / bKash / Nagad)** for Bangladesh (essential for the target market). Abstract behind a billing interface so gateways are swappable.
- **Mechanics:** monthly/annual, proration on upgrade, dunning + retries, invoices/receipts, tax handling, grace periods, and metered add-ons (extra members/storage).
- **Entitlements source of truth:** the central `subscriptions` + `plans` tables; enforced per request (§9).

---

## 9. Feature gating / entitlements
- Use **Laravel Pennant** (or a small entitlement service) keyed by the tenant's plan.
- Gate at three layers: **route/middleware** (block module access), **UI** (hide menus), **usage limits** (member count, storage, records) with friendly upgrade prompts.
- Example gates: `feature('accounting')`, `feature('investments')`, `limit('members', planCap)`.
- Enforce limits at write time (e.g. block adding the 101st member on Starter) with a clear "upgrade to add more" message.

---

## 10. Platform (super) admin — central control panel
A separate central app/area for the platform owner:
- Tenant list & health (plan, status, member count, last active, storage).
- Subscription & revenue dashboard (MRR, churn, trials, dunning).
- Create/suspend/delete tenants; run/replay provisioning; per-tenant migration status.
- **Support impersonation** (audited) into any tenant.
- Global feature flags, announcements/banners, and platform-wide audit log.

---

## 11. Per-tenant customization (white-label)
Already strong via `OrganizationProfile` + `Branding`:
- Name (en/bn), logo, motto, colours, contact, currency, `share_face_value`, `deposit_start_month` → all per tenant automatically.
- Add: **custom subdomain** (all tiers) and **custom domain** (Enterprise), branded emails (per-tenant from-name/logo), locale (bn/en), and optionally per-tenant landing page.
- The existing public landing page becomes a **template** each tenant can enable at `their-subdomain`.

---

## 12. Infrastructure & scaling
- **Topology:** app servers (stateless) + central DB + a fleet/cluster of tenant DBs (managed MySQL, or DB-per-tenant on a managed instance to start) + S3 + Redis (cache/queue at scale) + queue workers (tenant-aware).
- **Provisioning at scale:** queued tenant creation; `tenants:migrate` across all tenant DBs on deploy; migration versioning per tenant.
- **Observability:** per-tenant metrics (active members, jobs, errors), central logging with tenant tags, uptime/error monitoring, and cost attribution per tenant.
- **Deploy:** blue/green or rolling; **critical rule** — every schema change ships as a tenant migration and is applied to *all* tenant DBs in the release pipeline.
- **Cost model:** DB-per-tenant is cheap up to hundreds of tenants on shared managed instances; plan a migration to consolidated/sharded infra if the count explodes.

---

## 13. Migration path (single-tenant → multi-tenant, without downtime)
1. Keep the current app live as-is.
2. Build the tenancy shell in a branch: central DB, tenant resolution, provisioning, S3 isolation.
3. **Convert Echo of Unity into "tenant #1"**: create its tenant record + DB, move current data into it (the current DB *becomes* the tenant DB), point `echoofunity`'s subdomain at it. Because the schema is identical, this is largely a data move + connection switch.
4. Everything the current org uses keeps working; new signups create new tenant DBs.
5. Layer on billing, platform admin, and feature gating afterward.

---

## 14. Roadmap / phases
| Phase | Goal | Key work |
|---|---|---|
| **0. Stabilise** | Prereq | Current app stable in prod; feedback/bugs resolved; test coverage on core flows |
| **1. Tenancy foundation** | Multi-tenant shell | `stancl/tenancy`, central DB, tenant resolution (subdomain), tenant-aware storage/queue/cache, migrate current app into tenant #1 |
| **2. Onboarding & provisioning** | Self-serve signup | Marketing site + signup, queued provisioning (DB+migrate+seed+first admin+welcome), trials |
| **3. Billing** | Monetise | Cashier/Stripe + local gateway (bKash/SSLCommerz), plans, invoices, dunning, grace/suspension |
| **4. Entitlements & platform admin** | Control & tiers | Pennant feature gates + limits, central admin panel, impersonation, usage metering |
| **5. White-label & polish** | Differentiation | Custom domains, branded emails, per-tenant landing, locale, support tooling |
| **6. Mobile/API multi-tenant** | Scale reach | Extend the v1 API (tenant-resolved) to all tenants; per-tenant push |

---

## 15. Technical work breakdown (Laravel specifics)
- **Package:** `stancl/tenancy` (DB-per-tenant, storage/queue/cache isolation, `tenants:migrate`).
- **Central migrations/models:** `Tenant`, `Domain`, `Plan`, `Subscription`, `Invoice`, `PlatformUser`, `UsageCounter`.
- **Tenant boot:** register the current migrations as *tenant* migrations; seed roles/permissions + defaults on creation.
- **`OrganizationProfile::first()`:** keep as-is (runs inside tenant DB) — no rewrite needed. ✅ (this is the big win)
- **Storage:** switch member-photo/logo writes to the tenant-suffixed disk (package handles root rewriting); backfill tenant #1 paths.
- **Mail:** per-tenant from-name/branding; ensure queued mailers carry tenant context.
- **Billing:** Laravel Cashier + a `PaymentGateway` interface (Stripe, bKash/SSLCommerz/Nagad impls).
- **Feature flags:** Laravel Pennant tied to plan.
- **Testing:** tenant-isolation tests (A can't see B), provisioning tests, billing lifecycle tests.

---

## 16. Risks & mitigations
| Risk | Mitigation |
|---|---|
| Cross-tenant data leakage | DB-per-tenant + central/tenant route separation + isolation tests |
| Migrating 56 tables across many tenant DBs | Automated `tenants:migrate` in CI/CD; migration status dashboard; run in batches |
| Billing edge cases (dunning, proration, refunds) | Use Cashier's battle-tested flows; start with simple monthly plans |
| Local payments (bKash/Nagad) complexity | Gateway abstraction; launch with one local + one card gateway |
| DB sprawl / cost at high tenant counts | Monitor; plan optional shift to sharded/shared-schema for the long tail |
| Support burden | Impersonation tooling, self-serve docs, in-app help |
| Data ownership/privacy | Per-tenant export & delete, clear data-processing terms |

---

## 17. Pricing & go-to-market (brief)
- **Localised pricing** for Bangladesh (low ARPU, high volume) vs. regional/Enterprise.
- Free trial → Starter conversion; annual discount; per-member or per-tier caps.
- GTM: target cooperative federations, community leaders, alumni/staff associations; referral incentives; the mobile app as a member-facing hook.

---

## 18. Compliance & data ownership
- Tenants own their data; provide **export** (DB + files) and **delete** on request.
- Clear DPA/terms; per-tenant retention windows; audit logs already exist in-app (good for trust).
- Consider data residency if expanding beyond Bangladesh.

---

## 19. Success metrics
- Activation: % of signups that add ≥ 10 members and record a first deposit.
- MRR, trial→paid conversion, churn, ARPU.
- Provisioning success rate & time; per-tenant error/uptime.
- Member-app adoption per tenant.

---

## 20. Open decisions (to settle before Phase 1)
1. **Isolation model** — confirm **database-per-tenant** (recommended) vs shared-schema. Drives everything.
2. **Tenant addressing** — subdomains only at launch, custom domains later? (recommended)
3. **Primary payment gateway** for Bangladesh (bKash / SSLCommerz / Nagad) + whether Stripe is needed at launch.
4. **Plan tiers & caps** — finalise modules-per-tier and member/storage limits.
5. **Hosting** — managed MySQL + S3 + Redis footprint and budget.
6. **Brand** — is the SaaS "Echo of Unity" (the flagship becomes tenant #1) or a new platform brand with Echo of Unity as a showcase tenant?

---

### TL;DR
The app's clean single-tenant design makes **database-per-tenant** the pragmatic route: minimal business-logic change, strong isolation, and the current org simply becomes tenant #1. The real build is the *shell* around it — onboarding, provisioning, billing, entitlements, and a platform admin — sequenced so the live app never stops working.
