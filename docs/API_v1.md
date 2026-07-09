# Echo of Unity — Mobile API Spec (v1)

**Status:** Draft for the Flutter developer. Backend not yet built — this is the contract to build against.
**Scope of v1:** Member-facing app only (self-service). Admin/treasurer endpoints are **v2**.
**Auth:** Laravel Sanctum bearer tokens + OTP 2FA (same flow as the web app).

---

## 1. Conventions

| Topic | Rule |
|---|---|
| Base URL | `https://<host>/api/v1` |
| Format | JSON only. Send `Accept: application/json`. |
| Auth | `Authorization: Bearer <token>` on every protected request. |
| Content type | `application/json`, except file uploads → `multipart/form-data`. |
| Dates | ISO-8601, UTC (`2026-07-07T09:30:00Z`). Month-only fields use `YYYY-MM`. |
| Money | Two forms on every money field: a numeric `*` (integer BDT) **and** a `*_display` string (e.g. `"৳2,000"`). App should show `_display` and compute with the numeric. |
| IDs | Integers. |
| Pagination | `?page=1&per_page=20` (max 100). See §4. |
| Locale | Bengali/English content returned as stored; no translation layer in v1. |

### 1.1 Success envelope
Single resource:
```json
{ "data": { … }, "message": null }
```
Collection (paginated):
```json
{
  "data": [ … ],
  "meta": { "pagination": { "current_page": 1, "per_page": 20, "total": 44, "last_page": 3 } },
  "message": null
}
```

### 1.2 Error envelope
```json
{ "message": "Human-readable summary.", "errors": { "field": ["reason"] } }
```
`errors` is present only for validation (422).

### 1.3 Status codes
| Code | Meaning |
|---|---|
| 200 | OK |
| 201 | Created |
| 401 | Missing/invalid/expired token |
| 403 | Authenticated but not allowed (role/permission or not-your-record) |
| 404 | Not found |
| 409 | Conflict (e.g. OTP already verified) |
| 422 | Validation failed (`errors` populated) |
| 423 | Locked (OTP attempts exceeded / account inactive) |
| 429 | Rate limited (`Retry-After` header set) |

### 1.4 Rate limits
- `auth/*`: 10 req/min per IP.
- Authenticated endpoints: 60 req/min per user.
- `429` returns `Retry-After` seconds.

---

## 2. Authentication

Mirrors the web flow: **credentials → OTP → token**. No access token is issued until OTP is verified. Login identifier accepts **email, phone, or member code** (e.g. `M0002`).

OTP settings (server-driven, do not hardcode in the app): length **6**, expires in **5 min**, max **5** attempts, resend cooldown **30 s**. In development the code is a fixed `123456`; production will deliver by email/SMS.

### 2.1 `POST /auth/login`
Validate credentials and start an OTP challenge.

Request:
```json
{ "login": "M0002", "password": "secret", "device_name": "Murad's iPhone" }
```
`login` = email | phone | member code. `device_name` labels the token (shown in "logged-in devices").

Response `200` — OTP required:
```json
{
  "data": {
    "status": "otp_required",
    "otp_ticket": "eyJ0eXAi...",           // short-lived, opaque; pass to verify/resend
    "destination": "ad***@example.com",     // masked hint of where the code went
    "otp": { "length": 6, "expires_in": 300, "resend_after": 30 }
  },
  "message": "A verification code has been sent to your registered contact method."
}
```
Response `422` — bad credentials: `{ "message": "These credentials do not match our records.", "errors": { "login": ["..."] } }`
Response `423` — account inactive/locked out.

> If OTP is ever disabled server-side, this endpoint returns the `authenticated` payload from §2.2 directly (with `token`). The app should handle both `status` values.

### 2.2 `POST /auth/verify-otp`
Exchange a valid OTP for an access token.

Request:
```json
{ "otp_ticket": "eyJ0eXAi...", "otp": "123456" }
```
Response `200`:
```json
{
  "data": {
    "status": "authenticated",
    "token": "12|abcdef...",
    "token_type": "Bearer",
    "user": { "id": 2, "name": "Md Murad Hossain", "email": "murad2@example.com",
              "roles": ["Member"], "permissions": ["view members", "..."] },
    "member": { "id": 2, "member_code": "M0002", "name": "Md Murad Hossain",
                "photo_url": "https://…/storage/member-photos/xxx.png" },
    "profile_complete": true
  },
  "message": null
}
```
Errors: `422` invalid code (`{errors:{otp:["The verification code is incorrect. You have 4 attempt(s) left."]}}`), `423` too many attempts / expired (client should call resend), `409` ticket already used.

Store `token` in secure storage (Keychain/Keystore). Send it as `Authorization: Bearer` thereafter.

### 2.3 `POST /auth/resend-otp`
Request: `{ "otp_ticket": "eyJ0eXAi..." }`
Response `200`: `{ "data": { "resend_after": 30 }, "message": "A new verification code has been sent…" }`
Response `429`: still in cooldown (`Retry-After`).

### 2.4 `GET /auth/me`  *(auth required)*
Returns the current identity + member summary. Use on app start to validate the stored token.
```json
{
  "data": {
    "user": { "id": 2, "name": "…", "email": "…", "roles": ["Member"], "permissions": ["…"] },
    "member": { /* Member summary object, see §6.1 */ },
    "profile_complete": true
  }
}
```
`401` if the token is invalid/expired → app should route to login.

### 2.5 `POST /auth/logout`  *(auth required)*
Revokes the current token. `200 { "message": "Logged out." }`

### 2.6 `POST /auth/logout-all`  *(auth required)*
Revokes all of the user's tokens (all devices).

### 2.7 `POST /auth/forgot-password`
Request: `{ "login": "M0002" }` (email | phone | member code)
Always responds `200` (no account enumeration): `{ "message": "If the account exists, a reset link has been sent." }`
> Password reset itself completes via the existing web link in v1 (no in-app reset screen). In-app reset is a v2 candidate.

---

## 3. Self-service — `/me/*`  *(auth required; Member scope)*

All `/me` endpoints operate **only on the authenticated user's own member record**. If the user has no linked member, these return `403 { "message": "No member profile linked to this account." }`.

### 3.1 `GET /me`
Full profile — see the **Member (full)** object in §6.2.

### 3.2 `GET /me/summary`
Compact financial summary (the profile "stat card"):
```json
{
  "data": {
    "shares": 1,
    "emi_per_month": 2000, "emi_per_month_display": "৳2,000",
    "total_deposited": 62000, "total_deposited_display": "৳62,000",
    "deposit_due": {
      "configured": true,
      "amount": 26000, "amount_display": "৳26,000",
      "months": 13, "expected_months": 44, "paid_months": 31,
      "start_month": "2022-12"
    },
    "nominee_allocation_percent": 0
  }
}
```
`deposit_due.configured=false` when the org has no collection-start month set → app should show "—".

### 3.3 `PUT /me/profile`
Update own profile. Validated exactly like the web member-profile form. Required fields (server-enforced): `name_bn, father_name, mother_name, nid_number, date_of_birth, phone, email, permanent_address_{village,po,upazila,district,postal}`. Present address is optional.

Request (JSON): the writable subset of the Member object — `name_bn, father_name, mother_name, spouse_name, date_of_birth, gender, marital_status, nationality, nid_number, birth_registration, passport_number, tax_id, email, phone, secondary_mobile, whatsapp_number, permanent_address_*, present_address_*, same_as_permanent (bool), occupation, business_name, trade_license_number, office_designation, employer_name, office_address`.

Response `200`: the updated Member (full) + `profile_complete`.
Response `422`: field errors.

### 3.4 `POST /me/photo`  *(multipart)*
Field: `photo` — image, `jpg|jpeg|png|webp`, ≤ 2 MB.
Response `200`: `{ "data": { "photo_url": "https://…/storage/member-photos/xxx.png" } }`

### 3.5 `PUT /me/password`
Request: `{ "current_password": "…", "password": "…", "password_confirmation": "…" }`
Response `200` / `422` (`current_password` wrong, or new password rules).

### 3.6 `GET /me/deposits`  *(paginated)*
Query: `?page=&per_page=&from=YYYY-MM&to=YYYY-MM`
Item:
```json
{
  "id": 812, "amount": 2000, "amount_display": "৳2,000",
  "deposit_date": "2026-06-30", "contribution_month": "2026-06",
  "payment_method": "Bank transfer", "transaction_id": "IMP-202606-M0002",
  "reference": null, "notes": null
}
```

### 3.7 `GET /me/deposit-status`
Month-by-month paid/unpaid grid from the org collection-start month to the current month:
```json
{
  "data": {
    "start_month": "2022-12",
    "paid_count": 31, "unpaid_count": 13,
    "months": [
      { "month": "2022-12", "paid": true,  "amount": 2000 },
      { "month": "2024-10", "paid": false, "amount": 0 }
    ]
  }
}
```

### 3.8 `GET /me/shares`
```json
{ "data": [ { "share_number": "S-0001", "issue_date": "2022-12-01",
             "ownership_start_date": "2022-12-01", "status": "active" } ] }
```

### 3.9 `GET /me/loans`
```json
{
  "data": [{
    "id": 1, "loan_code": "L-0001",
    "loan_amount": 50000, "service_charge": 0,
    "total_payable": 50000, "total_repaid": 20000, "outstanding_balance": 30000,
    "loan_amount_display": "৳50,000", "outstanding_display": "৳30,000",
    "taken_date": "2026-01-01", "due_date": "2026-06-30",
    "status": "active", "display_status": "overdue", "is_overdue": true,
    "repayments": [ { "amount": 20000, "repaid_date": "2026-03-01", "payment_method": "Cash" } ]
  }]
}
```
`display_status` ∈ `pending | active | overdue | partially_repaid | repaid | rejected`.

### 3.10 `GET /me/nominees`
```json
{ "data": [ { "id": 1, "full_name": "…", "relationship": "Spouse",
             "mobile_number": "…", "allocation_percentage": 100, "is_primary": true } ] }
```

### 3.11 `GET /me/documents`
```json
{ "data": [ { "id": 5, "document_type": "nid", "file_name": "nid.pdf",
             "file_size": 128394, "mime_type": "application/pdf",
             "upload_date": "2026-01-01", "verified": true,
             "url": "https://…/storage/…" } ] }
```
> Read-only in v1. Uploading documents from the app is a v2 candidate.

---

## 4. Pagination
Collection endpoints accept `?page` and `?per_page` (default 20, max 100) and return `meta.pagination` (§1.1). List order is newest-first unless noted.

---

## 5. Reference (read-only)  *(auth required)*

### 5.1 `GET /organization`
Public brand/finance info used across the app.
```json
{
  "data": {
    "name": "Echo of Unity", "name_bn": "…",
    "motto": "Friendship · Growth · Strength",
    "address": "Sonapur, Kajirhat, Nilphamari",
    "phone": "01751767350", "email": "admin@echoofunity.com",
    "currency": "BDT", "currency_symbol": "৳",
    "share_face_value": 2000,
    "deposit_start_month": "2022-12",
    "logo_url": "https://…"
  }
}
```

### 5.2 `GET /constitution`
```json
{
  "data": [
    { "id": 1, "title": "Preamble", "slug": "preamble", "icon": "book",
      "body": "<p>…HTML…</p>", "sort_order": 1 }
  ]
}
```
Only published sections. `body` is sanitized HTML — render in a rich-text/HTML widget.

---

## 6. Data dictionary

### 6.1 Member (summary) — used in auth + `me.member`
```
id, member_code, name, name_bn, status, join_date, photo_url
```

### 6.2 Member (full) — `GET /me`, `PUT /me/profile` response
```json
{
  "id": 2, "member_code": "M0002",
  "name": "Md Murad Hossain", "name_bn": "মুরাদ",
  "status": "active", "join_date": "2022-12-01",
  "photo_url": "https://…",
  "personal": {
    "father_name": "Abdul", "mother_name": "Amina", "spouse_name": null,
    "date_of_birth": "1990-01-01", "gender": "male", "marital_status": "married",
    "nationality": "Bangladeshi",
    "nid_number": "1234567890", "birth_registration": null,
    "passport_number": null, "tax_id": null
  },
  "contact": {
    "email": "murad2@example.com", "phone": "01710000001",
    "secondary_mobile": null, "whatsapp_number": null
  },
  "permanent_address": {
    "village": "Sonapur", "post_office": "Sonapur PO", "union": null,
    "upazila": "Sadar", "district": "Nilphamari", "postal_code": "5300"
  },
  "present_address": {
    "same_as_permanent": true,
    "village": "Sonapur", "post_office": "Sonapur PO", "union": null,
    "upazila": "Sadar", "district": "Nilphamari", "postal_code": "5300"
  },
  "professional": {
    "occupation": null, "business_name": null, "trade_license_number": null,
    "office_designation": null, "employer_name": null, "office_address": null
  },
  "profile_complete": true
}
```

### 6.3 Enums
- `member.status`: `active | inactive | suspended`
- `gender`: `male | female | other`
- `marital_status`: `single | married | divorced | widowed`
- `loan.display_status`: `pending | active | overdue | partially_repaid | repaid | rejected`

---

## 7. Roles & permissions (reference)
Returned in the auth payload so the app can gate UI.

**Roles:** `Super Admin`, `Association Admin`, `Treasurer`, `Project Manager`, `Member`.

v1 is Member-scoped, so the app only needs to check `roles` contains `Member` and route accordingly. The full permission list (used from v2 onward) includes: `view members`, `create deposits`, `view deposits`, `view/create/update/approve loans`, `view/create/approve expenses`, `manage investments`, `view dashboard`, `manage organization profile`, etc. **Rule for v1:** never rely on the app to enforce access — the server authorizes every request; the `permissions` array is only for showing/hiding UI.

---

## 8. Security notes for the app
- Store the bearer token in Keychain/Keystore; never in plain prefs.
- Send `Authorization` on every call; on any `401`, clear the token and go to login.
- Treat the OTP `otp_ticket` as sensitive and short-lived; discard after verify.
- Respect `Retry-After` on `429`.
- All money math client-side must use the numeric fields, not the `_display` strings.

---

## 9. Deferred to v2 (not in this spec)
Recording deposits, member management, loans/investments/expenses management, approvals, dashboard & reports, in-app document upload, in-app password reset, push notifications, "remembered devices" for OTP.

---

## 10. Open items to confirm before build
1. Token lifetime (e.g. 30-day expiry with silent re-login vs. long-lived) and whether to show a "devices" screen.
2. Whether members may see **org-wide** figures (fund position/investment totals) or **only their own** data in v1 (current spec = own data only).
3. Push provider (FCM) — deferred to v2 but affects the `device_name`/token-registration shape.
