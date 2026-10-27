# Code structure
- Add declare(strict_types=1) to new PHP files and follow PSR-12; run Laravel Pint
- Keep controllers and Livewire components thin; put business logic in Action
  or Service classes (e.g. app/Actions/Tasks/AssignTask.php)
- Use Enums for fixed values (task priority, notification type)
- Build reusable Blade components for repeated UI (buttons, modals, badges, task card)
- Use Tailwind utility classes only; no custom CSS unless unavoidable

# Database
- Always define foreign keys with cascade or restrict rules, and add indexes on
  columns used for filtering (assignee_id, status_id, due_date, project_id)
- Use soft deletes on workspaces, projects, lists, and tasks
- Use polymorphic relations for comments, attachments, and activity logs
- Define $fillable on every model; never use $guarded = []
- Create a factory for every model and use them in seeders and tests
- Store dates in UTC and display them in the company timezone (set in config)

# Performance
- Prevent N+1 queries: eager load relationships, and call
  Model::preventLazyLoading() in non-production environments
- Paginate every list; never load all tasks at once
- Queue all emails and notifications (database queue driver)
- Use the scheduler for due-date reminder notifications

# Security
- This is an internal tool: disable public registration; Admins create or invite users
- Store attachments on a private disk, validate type and size, and serve them
  through an authorized route
- Rate limit login and any public endpoints
- Never hardcode secrets; use .env and config()
- Escape all output; avoid {!! !!} unless the content is sanitized

# Activity and audit
- Use spatie/laravel-activitylog to record task create, update, status change,
  assignment, and delete, with the acting user

# How Kiro should work
- Complete one task from tasks.md at a time and run the Pest tests before moving on
- Do not install new packages without telling me why
- Do not modify files unrelated to the current task
- Never run migrate:fresh or destructive commands outside the local environment
- If a requirement is ambiguous, ask instead of guessing
- Do not create unnecessary markdown files (no summary, notes, changelog, or
  "implementation complete" .md files). Only create .md files I explicitly ask for
  or that the spec workflow requires (requirements.md, design.md, tasks.md).
  Put explanations in the chat reply instead.