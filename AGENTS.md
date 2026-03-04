# Repository Instructions for Codex

- Laravel 12 + Blade + Bootstrap 5
- No starter kits (no Breeze/Jetstream/UI)
- Use migrations + seeders for ALL tables in `MILESTONE_PLAN.md`
- Use `FormRequest` validation everywhere
- Use resource controllers + named routes
- Tenant scope: every business table must have `subscriber_id`
- Add middleware + helpers to enforce tenant scoping
- RBAC via roles/permissions + middleware checks
- After each milestone: run `php artisan migrate:fresh --seed` and fix failures
