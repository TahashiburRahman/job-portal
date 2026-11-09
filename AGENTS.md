# Agent Guide

## Project snapshot

This repository contains **ID Job Finder BD**, a Laravel 12 job portal for Bangladesh. It is an active application with public job and company pages, candidate and employer workspaces, and an administrator area. Treat the checked-in code as the source of truth; this document and `README.md` summarize the current implementation and must stay accurate.

### Technology and local development

- PHP 8.2+, Laravel 12, Blade, Vite, and Tailwind CSS 4.
- MySQL is the documented local database. PHPUnit is configured to run feature tests against in-memory SQLite.
- Install PHP dependencies with Composer and frontend dependencies with npm when required.
- Local setup and demo account details are documented in `README.md`.
- Useful commands:
  - `php artisan migrate --seed` — migrate and seed the local database.
  - `php artisan serve` — start the Laravel development server.
  - `php artisan test` — run the test suite.
  - `npm run dev` — start Vite during frontend development.
  - `npm run build` — build frontend assets.

### Implemented functionality

- Public home page with database-backed hiring counts, current verified hiring companies, and a compact responsive keyword/category search; job search and detail pages, company directory and detail pages, and about/contact/articles pages.
- Registration, login/logout, role-based dashboards, throttling for login/registration/password-reset requests, and password reset.
- Employer company profiles, job creation/editing/closing, and applicant lists with resume access.
- Admin dashboards and management of job statuses, user activation, company verification, classifications, and Bangladesh locations.
- Candidate profiles, resume uploads/downloads, saved jobs, and job applications.
- Job search filters for keywords, classifications, skills, workplace, salary, experience, location, posting date, and sorting.
- Relational job classification and division/district/upazila data, dependent location endpoints, pagination, and uniqueness constraints for applications and saved jobs.
- Local-only demo accounts and jobs; credentials are provided through local `.env` values and must not be added to source control or documentation.

### Codebase map

- `routes/` — public, employer, candidate, and admin route definitions.
- `app/Http/Controllers/` — frontend, authentication, employer, candidate, and admin request handling.
- `app/Http/Middleware/EnsureRole.php` — role-based route access.
- `app/Models/` — users, companies, jobs, applications, candidate profiles, classifications, and locations.
- `database/migrations/` and `database/seeders/` — schema and reference/demo data.
- `resources/views/` — Blade templates grouped by frontend, auth, candidate, employer, and admin.
- `resources/views/frontend/home.blade.php` — routed public homepage; hiring metrics and company highlights come from current database records, with a compact responsive keyword/category search.
- `resources/css/` and `resources/js/` — Vite frontend entry points.
- `tests/Feature/` and `tests/Unit/` — automated tests.

Job postings use the `job_listings` table; Laravel's `jobs` table is for queued work. Do not confuse or repurpose these tables.

## Required documentation upkeep

**Whenever you add or change application code, update both `README.md` and this `AGENTS.md` in the same change.** Keep the README useful to people setting up and using the app, and keep this file useful to future AI agents by updating the project snapshot, implemented functionality, codebase map, or development guidance affected by the change. If a code change does not alter anything these documents describe, still review both and make a concise documentation update that accurately records the new code or behavior. Never leave either document describing stale functionality.

When changing setup, dependencies, commands, environment variables, routes, data models, or user-facing behavior, update the corresponding details in both documents. Do not document secrets or real user data.

## Working conventions

- Read the existing implementation and relevant tests before changing behavior; follow established Laravel patterns and naming.
- Add or update tests for behavior changes and run the narrowest relevant test command, then `php artisan test` when appropriate.
- Validate frontend changes with the available Vite build.
- Preserve role checks, validation, authorization, privacy of candidate resumes, and existing database constraints.
- Avoid editing generated or installed dependency files under `vendor/` and `node_modules/`.
- Do not assume a feature is implemented just because it is listed here; verify the relevant route, controller, view, model, and tests before extending it.