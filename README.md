# ID Job Finder BD

A Laravel 12 job portal for Bangladesh with public job search, employer and candidate workspaces, and an administrator review area.

## Local setup

Requirements: PHP 8.2+, Composer, MySQL 8+, and a web server (XAMPP is supported).

1. Copy `.env.example` to `.env` if needed and set the MySQL connection values.
2. Create the `id_job_finder_bd` database with `utf8mb4` encoding.
3. Run `php artisan key:generate` if `APP_KEY` is empty.
4. Run `php artisan migrate --seed`.
5. Start the development server with `php artisan serve`.
6. Visit `http://127.0.0.1:8000`.

The local seeder adds job classifications, Bangladesh's eight divisions and 64 districts, a starter set of upazilas, three local demo accounts, and clearly identified demo jobs. Demo accounts and jobs are only created when `APP_ENV=local`.

| Role | Email |
|---|---|
| Admin | `admin@example.test` |
| Employer | `employer@example.test` |
| Job seeker | `employee@example.test` |

Set the three `DEMO_*_PASSWORD` values in your ignored local `.env` before seeding. Passwords are hashed before storage; demo accounts are only created when `APP_ENV=local`. Never copy local demo passwords into a deployed environment. Password reset mail is logged by Laravel's local mail driver; configure a real mail provider before deployment.

## Included workflows

- Public home with database-backed hiring counts, current verified hiring companies, and a compact responsive keyword/category search; job search/detail, company directory/detail, and career information pages.
- Shared authentication, job-seeker/employer registration, role-specific access control, throttled login, password reset, and logout.
- Employer company profiles, draft and approval-submitted jobs, job editing/closing, and applicant lists.
- Admin job approval/rejection, account activation, and company verification.
- Administrator CRUD for job-search classifications and Bangladesh locations, with reference-aware deletion checks.
- Candidate profiles, private resume uploads/downloads, saved jobs, and applications.
- Relational job classifications and location data with dependent division/district/upazila selects.
- Search by keyword, multiple classification/skill/workplace filters, salary and experience ranges, location, posting date, and sort order.
- Pagination and database constraints for duplicate applications and saved jobs.

The Laravel queue's `jobs` table is retained for background processing; job postings are stored separately in `job_listings`.

## Contributor and AI guidance

See [AGENTS.md](AGENTS.md) for the current project snapshot, codebase map, and development guidance. Keep both `README.md` and `AGENTS.md` updated whenever application code changes so project documentation remains useful when the folder is shared or provided as a ZIP.

## Tests

Run `php artisan test`. PHPUnit is configured to use an in-memory SQLite database so feature tests never reset the configured MySQL database.
