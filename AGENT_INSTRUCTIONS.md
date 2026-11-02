# AI Agent Instructions: Student Management System (Laravel)

> **Out of date on two points.** `WORK_ORDER.md` replaces the branch names and the build order written here;
> the branches actually used were `feature/q1-database-relationships`, `feature/q2-seeders`,
> `feature/q3-auth-roles-permissions`, `feature/q4-application-functionality`,
> `feature/q5-blade-layout-validation` and `feature/q6-documentation`. The application runs on Laravel 12,
> not Laravel 11. The per-member prompts below are still useful for scope.

How to use this file:
1. Save the **Shared Context** section as `AGENTS.md` (or `CLAUDE.md`) in the repo root so every member's agent reads it automatically.
2. Each member pastes **only their own prompt** (Section 2) into their agent.
3. Always tell the agent which branch you are on before it starts.

---

# 1. SHARED CONTEXT (put in AGENTS.md / CLAUDE.md)

## Project
Laravel 12 Student Management System for a university course (BUC3131). Manages students, courses, lecturers and course registrations. Three roles: admin, lecturer, student. Built by 5 students on GitHub. Deadline 2 Nov 2026.

## Stack
- Laravel 11, PHP 8.2+, MySQL (or SQLite for local dev)
- Auth: Laravel Breeze (Blade stack) unless the class taught another package
- Roles/permissions: `spatie/laravel-permission`
- Views: Blade only (no React/Vue/Livewire), Tailwind (Breeze default)

## Fixed database contract (do NOT rename anything)
| Table | Columns |
|---|---|
| `users` | id, name, email, password, timestamps (roles via Spatie) |
| `students` | id, user_id (FK users, cascade), student_number (unique), name, email (unique), phone nullable, timestamps |
| `courses` | id, code (unique), title, description nullable, credits (unsigned tinyint), lecturer_id (nullable FK users, nullOnDelete), timestamps |
| `course_registrations` | id, student_id (FK students, cascade), course_id (FK courses, cascade), registered_at (timestamp, default now), timestamps, **unique(student_id, course_id)** |

Relationships:
- `Student` belongsTo `User`; belongsToMany `Course` via `course_registrations` (withPivot `registered_at`, withTimestamps)
- `Course` belongsTo `User` as `lecturer`; belongsToMany `Student` via `course_registrations`
- `User` hasOne `Student`; hasMany `Course` (foreignKey `lecturer_id`) as `taughtCourses`

Roles: `admin`, `lecturer`, `student` (lowercase).

Permissions (Spatie): `manage students`, `manage courses`, `manage registrations`, `manage users`, `view assigned courses`, `view course students`, `view available courses`, `view own registrations`, `register for course`.
- admin: all permissions
- lecturer: `view assigned courses`, `view course students`
- student: `view available courses`, `view own registrations`, `register for course`

## Route and file conventions
- Resource routes: `students`, `courses`, `registrations` (names: `students.*`, `courses.*`, `registrations.*`), plus `dashboard`
- Controllers in `app/Http/Controllers`: `StudentController`, `CourseController`, `RegistrationController`, `DashboardController`
- Form Requests in `app/Http/Requests`
- Policies in `app/Policies`: `StudentPolicy`, `CoursePolicy`, `RegistrationPolicy`
- Views: `resources/views/{students,courses,registrations,dashboard}`; layout is `layouts/app.blade.php` with `partials/nav.blade.php`; every page uses the layout
- Seeders in `database/seeders`: `RolesAndPermissionsSeeder`, `UserSeeder`, `StudentSeeder`, `CourseSeeder`, `RegistrationSeeder`, called in that order from `DatabaseSeeder`

## Git rules (strict)
- Branches: `main` (stable), `develop` (integration). Never commit directly to either.
- Work only on your assigned feature branch, created from latest `develop`.
- Small commits, meaningful messages in imperative mood (e.g. "Add unique constraint to course_registrations").
- Only touch files in your own area. If you need a change elsewhere, say so instead of editing it.
- Before opening a PR: `git pull origin develop` into your branch, run `php artisan migrate:fresh --seed`, confirm the app runs.
- Open PRs to `develop` only. Never force-push shared branches. Never commit `.env` or `vendor/`.

## Code rules
- Validate with Form Request classes, never inline in controllers
- Use Eloquent relationships and eager loading (`with()`), no raw SQL
- Use route middleware (`auth`, `role:`, `permission:`) and policies for authorization
- Show flash messages with `session('success')` and `session('error')` in the layout
- Use `@can` / `@role` in Blade for nav links and action buttons
- Seeders must be idempotent (`firstOrCreate` / `updateOrCreate`)
- Keep code simple and commented where non-obvious. The team is assessed individually and must be able to explain every line.

## Definition of done
Feature works for all three roles, no errors on `migrate:fresh --seed`, validation errors and success messages appear, unauthorized access returns 403, and the PR description lists what changed.

---

# 2. PER-MEMBER PROMPTS (paste into each agent)

## Member 1: Setup, Auth, Roles (branch `feature/setup-auth-roles`)

```
Read AGENTS.md first. You are Member 1 (Lead). Branch: feature/setup-auth-roles.

PHASE 0 (do first, push to develop quickly):
1. Create a fresh Laravel 11 project, install Breeze (Blade stack) and spatie/laravel-permission. Publish the Spatie migration and config.
2. Configure .env.example (MySQL placeholders), add a good .gitignore (vendor, node_modules, .env).
3. Create `main` and `develop` branches, push the skeleton to develop, then tell me the team can branch from it.

PHASE 1:
4. Add the HasRoles trait to User.
5. Create RolesAndPermissionsSeeder with the exact roles and permissions in AGENTS.md. Use firstOrCreate. Call it first in DatabaseSeeder.
6. Registration should default new sign-ups to the `student` role.

PHASE 3 (after other branches merge into develop):
7. Protect routes: auth on everything; permission/role middleware per route group (students and courses management = admin; registrations as defined in AGENTS.md).
8. Create StudentPolicy, CoursePolicy, RegistrationPolicy so a student only sees their own records, a lecturer only sees courses where lecturer_id = their id and those courses' students, admin sees all. Register policies and use $this->authorize() hints for the other members' controllers (tell me what to add rather than editing their files).

PHASE 5:
9. Write the final README (installation, DB setup, roles, sample logins, screenshots placeholder) and a CONTRIBUTIONS.md report (members, tasks, branches, PRs).

Work in small commits. Explain each step briefly so I can present it.
```

## Member 2: Database and Relationships (branch `feature/database-relationships`)

```
Read AGENTS.md first. You are Member 2. Branch: feature/database-relationships (from develop).

PRIORITY: push the migrations within 2-3 hours because everyone depends on them.

1. Create migrations for students, courses, course_registrations exactly as in AGENTS.md (foreign keys, unique pair, correct order so FKs resolve).
2. Create models Student, Course, CourseRegistration (pivot, extends Pivot or normal model with $fillable) with the relationships defined in AGENTS.md: hasMany, belongsTo, belongsToMany. Add fillable arrays and casts.
3. Add the User model relationships (student, taughtCourses).
4. Create model factories for Student and Course.
5. Add helper methods or scopes demonstrating Eloquent queries:
   - $student->courses (a student's registered courses)
   - $course->students (students registered for a course)
   - a scope for courses a student has not registered for
6. Add a Pest/PHPUnit feature test (or tinker script in docs) proving the relationships work and the duplicate registration is rejected at the database level.

Do not touch controllers, views, or seeders. Open a PR to develop as soon as migrations and models are ready, then keep refining in follow-up commits.
```

## Member 3: Seeders and Dashboards (branch `feature/seeders-dashboards`)

```
Read AGENTS.md first. You are Member 3. Branch: feature/seeders-dashboards (from develop). Wait for Member 2's models/migrations to merge before running seeders; until then write them against the contract in AGENTS.md.

1. UserSeeder: create admin@example.com (admin), two lecturers (lecturer@example.com, lecturer2@example.com), and student accounts. Password `password` (hashed). Assign roles with assignRole(). Use updateOrCreate on email.
2. StudentSeeder: at least 5 students, each linked to a student-role user. Use firstOrCreate on student_number.
3. CourseSeeder: at least 4 courses, each assigned to a lecturer. Use firstOrCreate on code.
4. RegistrationSeeder: realistic registrations (each student in 2-3 courses) without duplicates (use syncWithoutDetaching or firstOrCreate).
5. Wire everything in DatabaseSeeder in this order: RolesAndPermissionsSeeder, UserSeeder, StudentSeeder, CourseSeeder, RegistrationSeeder.
6. Verify: run `php artisan migrate:fresh --seed`, then `php artisan db:seed` a second time and confirm no duplicate rows.
7. DashboardController and dashboard views (resources/views/dashboard) per role: admin sees counts and recent registrations; lecturer sees assigned courses; student sees their registrations and available courses. Use the layout from Member 5 (@extends('layouts.app')).

Report the exact seeded login emails so the README can list them. Do not edit migrations, models, or other members' controllers.
```

## Member 4: Student and Course CRUD (branch `feature/student-course-crud`)

```
Read AGENTS.md first. You are Member 4. Branch: feature/student-course-crud (from develop). Use Member 5's layout (@extends('layouts.app')); if it is not merged yet, create a minimal temporary one locally but do not commit it.

1. StudentController resource (index, create, store, show, edit, update, destroy) with routes `students.*`. Creating a student should also create a linked user with the `student` role (random or default password documented in the PR).
2. CourseController resource with routes `courses.*`, including selecting a lecturer from users with the lecturer role.
3. Form Requests: StoreStudentRequest, UpdateStudentRequest, StoreCourseRequest, UpdateCourseRequest. Rules: required fields, unique student_number and email, unique course code (ignore current record on update), credits integer 1-10, lecturer exists.
4. Blade views in resources/views/students and resources/views/courses: index (with pagination), create, edit, show. Show old() values and @error messages on every field.
5. Redirect with `->with('success', ...)` after actions and `->with('error', ...)` on failures (e.g. cannot delete a course that has registrations, or handle cascade clearly).
6. Wrap action buttons in @can checks.

Do not edit migrations, models, seeders, or registration code. Open a PR to develop when done.
```

## Member 5: Layout, Registration, Screenshots (branch `feature/registration-layout`)

```
Read AGENTS.md first. You are Member 5. Branch: feature/registration-layout (from develop).

STEP 1 (do first, push within 2 hours, even if rough): reusable Blade layout.
- resources/views/layouts/app.blade.php with header, navigation (partials/nav.blade.php), @yield('content') (or $slot), footer, and a flash message area for session('success') and session('error') plus validation errors.
- Navigation links shown by permission: Students and Courses (manage), Registrations (manage), My Courses (lecturer), Available Courses and My Registrations (student). Use @can / @role.
- Merge to develop early so Members 3 and 4 can extend it.

STEP 2: Course registration.
- RegistrationController and routes `registrations.*`.
- Student: lists available courses and registers with one click (POST). Validate course exists. Prevent duplicates in code (check before insert / use syncWithoutDetaching) AND rely on the DB unique constraint; show an error message on duplicates.
- Student can view and cancel only their own registrations.
- Admin: view all registrations, create/delete for any student.
- Lecturer: view students registered in their own courses only (read-only).
- Display records according to the logged-in role (scope queries, never trust IDs from the URL; abort(403) otherwise).
- Views in resources/views/registrations.

STEP 3 (Sunday): after integration, take clear screenshots of each role's main pages into docs/screenshots/ and reference them in the README.

Do not edit migrations, seeders, or the student/course CRUD files.
```

---

# 3. INTEGRATION AND FINAL CHECK PROMPT (any member, Sunday)

```
Read AGENTS.md. Check out latest develop. Run composer install, migrate:fresh --seed, and review the app against the BUC3131 coursework requirements:
- migrations, models, hasMany/belongsTo/belongsToMany, Eloquent queries, FKs, unique registrations
- seeders (5+ students, 4+ courses, users with roles, registrations, idempotent)
- auth, roles/permissions, route middleware, policies blocking other users' records
- student CRUD, course CRUD, registration with duplicate prevention, role-based display, flash messages
- reusable layout, validation, permission-based navigation
List every bug or gap as a checklist with the file to fix and who owns it. Do not change code. Then test as admin, lecturer and student (try duplicate registration and opening another user's record by URL).
```

---

# 4. TIPS FOR USING AGENTS WELL
- Tell the agent: "Explain what you did in 3 bullet points after each step." Everyone is graded individually on understanding.
- Make each member read their own code before the presentation and be able to explain relationships, middleware, and policies without the agent.
- If an agent wants to rename a table or column, stop it. The contract in AGENTS.md must stay fixed.
- Commit under your own GitHub account so your contribution shows up in the history.
