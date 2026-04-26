# Phoenix to Laravel Integration Plan

## Context

- Laravel app: `/Volumes/Works/kinvest/barakah`
- Purchased template: `/Volumes/Works/kinvest/phoenix-v1.21.0`
- UI slice to reuse: `Project Management`
- Product direction: association contribution, pooled fund, and investment project management

## 1. Immediate Recommendation

Use Phoenix as the admin application theme for the authenticated part of the product.

Do not mix Phoenix page markup with the default Laravel `welcome` page or the default Tailwind UI.

The clean path is:

1. keep Laravel for backend, routing, auth, policies, models, migrations, services
2. keep Phoenix for admin layout, components, tables, forms, cards, modals, charts
3. convert only the needed Phoenix `Project Management` screens into Blade templates

## 2. Current Laravel State

The app is a clean Laravel + Vite install with:

- default `welcome.blade.php`
- `resources/css/app.css` using Tailwind
- `resources/js/app.js` empty
- no real frontend architecture yet

That is good. It means there is no existing UI layer to untangle.

## 3. Frontend Strategy

### Recommended approach

Treat Phoenix as a parallel admin theme layer.

That means:

- Phoenix CSS and JS become the main admin UI assets
- Blade becomes the rendering layer
- Vite is used only if we want a managed asset pipeline later

### What not to do

Do not try to fully compile Phoenix Pug inside Laravel first.

Reason:

- it adds unnecessary build complexity early
- the Phoenix `public` output is already available
- we only need a subset of the template

## 4. Asset Strategy

### Phase 1

Use the already built Phoenix assets from:

- `phoenix-v1.21.0/public/assets`
- `phoenix-v1.21.0/public/vendors`

Copy the required files into Laravel `public/phoenix`.

Suggested target structure:

- `public/phoenix/assets/...`
- `public/phoenix/vendors/...`

### Phase 2

Once the app is stable, decide whether to:

- keep Phoenix as copied static assets
- or move selected CSS/JS into Vite for tighter control

Recommendation:

Start with copied static assets. Move to Vite only after the Blade conversion settles.

## 5. Layout Conversion Plan

Build one base authenticated layout from Phoenix `LayoutTheme`.

Suggested Blade files:

- `resources/views/layouts/phoenix.blade.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/layouts/partials/topbar.blade.php`
- `resources/views/layouts/partials/footer.blade.php`
- `resources/views/layouts/partials/settings-panel.blade.php`

The base layout should include:

- Phoenix core CSS
- required vendor CSS
- Phoenix core JS
- required vendor JS
- `@stack('styles')`
- `@stack('scripts')`
- `@yield('content')`

## 6. Route and Screen Rollout

Convert Phoenix screens in this order:

1. `dashboard/project-management.html`
2. `apps/project-management/project-list-view.html`
3. `apps/project-management/create-new.html`
4. `apps/project-management/project-details.html`
5. `apps/project-management/project-card-view.html`
6. `apps/project-management/project-board-view.html`
7. `apps/project-management/todo-list.html`

Why this order:

- dashboard gives the app a usable shell
- list/create/details are core product flows
- card/board/todo are secondary enhancements

## 7. Product Mapping

Phoenix `Project Management` should map to the product like this:

### Phoenix dashboard

Use for:

- association overview
- monthly collection summary
- available fund balance
- capital committed to active projects
- recent transactions
- pending approvals

### Phoenix project list view

Use for:

- investment projects index
- status filtering
- project owner / manager
- budget committed
- return status
- progress

### Phoenix create new

Use for:

- create investment project
- requested capital
- planned dates
- project lead
- expected return
- risk category
- notes

### Phoenix project details

Use for:

- project summary
- funding records
- returns
- attachments
- activity timeline
- member exposure

### Phoenix todo list

Optional use for:

- operational tasks
- due payments
- document follow-up
- project milestones

## 8. Laravel View Structure

Suggested Blade structure:

- `resources/views/dashboard/index.blade.php`
- `resources/views/projects/index.blade.php`
- `resources/views/projects/create.blade.php`
- `resources/views/projects/show.blade.php`
- `resources/views/projects/board.blade.php`
- `resources/views/projects/cards.blade.php`
- `resources/views/tasks/index.blade.php`

For reusable pieces:

- `resources/views/components/phoenix/...`
- or `resources/views/partials/...`

Recommendation:

Start with partials. Move to Blade components only where reuse is high.

## 9. Backend Module Sequence

Do not start by converting every template page.

Build backend modules in this order:

1. authentication
2. organizations
3. members
4. contributions
5. ledger
6. projects
7. distributions
8. reports

Then bind the converted Phoenix pages to real data.

## 10. Tailwind and Bootstrap Decision

Current Laravel ships with Tailwind-oriented assets.

Recommendation:

- keep Tailwind installed for now because it is harmless
- do not use Tailwind in authenticated Phoenix pages
- make Phoenix/Bootstrap the primary admin UI system
- later, remove Tailwind if it becomes noise

This avoids framework fighting in the same screens.

## 11. Required Vendor Dependencies for Early Phoenix Pages

Core:

- `simplebar`
- `bootstrap`
- `fontawesome`
- `lodash`
- `list.js`
- `feather-icons`
- `dayjs`
- `phoenix.js`

Page-specific:

- `flatpickr`
- `choices`
- `echarts`
- `dhtmlx-gantt` only if the gantt dashboard stays

## 12. Authentication and Layout Boundary

Suggested route split:

- public routes: marketing / login / landing
- authenticated routes: Phoenix admin

This matters because the SaaS product may later need:

- a public marketing site
- a tenant login experience
- an internal app shell

Do not force Phoenix onto the public marketing surface unless it actually fits.

## 13. First Implementation Milestone

The first milestone should produce:

- Laravel auth working
- Phoenix base layout mounted
- sidebar and topbar converted
- dashboard route rendering in Blade
- projects list route rendering in Blade
- static template content replaced with Laravel route and asset helpers

No business logic is required yet beyond basic route protection.

## 14. Second Implementation Milestone

After the shell is stable:

- projects CRUD
- member management
- contribution cycle screens
- ledger list screen
- dashboard numbers driven by seed data

## 15. Key Integration Rules

- never edit the purchased Phoenix source as the product codebase
- keep Phoenix source as a reference library
- convert only needed HTML structures into Blade
- replace all `../../assets/...` style paths with `asset('phoenix/...')`
- replace hardcoded links with `route(...)`
- replace dummy copy with domain language from the product
- do not let financial calculations live in Blade templates or controllers

## 16. Final Recommendation

The best next step is not full UI conversion.

The best next step is:

1. set up Laravel auth
2. import Phoenix assets into `public/phoenix`
3. build the base Phoenix Blade layout
4. convert the dashboard and projects list screens first

That will give you a usable admin shell and prove the template integration before we invest in domain modules.

