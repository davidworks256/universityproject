# Contribution Report

Coursework Two, BUC3131 — Student Management System. Five members, one repository
(`davidworks256/universityproject`), one branch per question, every piece joined through a pull request
into `develop` and approved by the repository owner.

Each commit was made with that member's own GitHub name and email, so the history shows who wrote what.
The counts can be checked with `git shortlog -sne develop` (merge commits belong to the owner, who
pressed Merge on every pull request).

| Member | GitHub account | Email on the commits | Question | Marks |
|---|---|---|---|---|
| Member 1 — team lead and repository owner | `davidworks256` | lookslikedavid@gmail.com | Question 3 (all of it) and Question 6 | 6 + 3 |
| Member 2 | `2400718770-debug` | 2400718770@mubs.ac.ug | Question 1 | 10 |
| Member 3 | `masha256ug` | nakibuulemariam671@gmail.com | Question 2 | 4 |
| Member 4 | `Emmanuel-kisakye` | Kisakyeemma20@gmail.com | Question 4 | 12 |
| Member 5 | `paulmukasa98` | paulmukasassempijja@gmail.com | Question 5 | 5 |

*Add your full name next to your GitHub account before the report is submitted.*

## Member 1 — `davidworks256` (Question 3 and Question 6)

**Branch `feature/q3-auth-roles-permissions`, pull requests #1 and #6.**

- First half: fresh Laravel 12 project, Breeze authentication with the Blade stack,
  `spatie/laravel-permission`, `HasRoles` on the `User` model, `RolesAndPermissionsSeeder` with the three
  roles and nine permissions, new sign-ups defaulted to `student`, and the resource routes plus empty
  controllers that the other members filled in.
- Second half (pull request #6): the `role` and `permission` middleware shortcuts in `bootstrap/app.php`,
  the permission rules on the routes in `routes/web.php`, `StudentPolicy`, `CoursePolicy` and
  `RegistrationPolicy`, and 22 tests that prove each role is refused records it does not own.

**Branch `feature/q6-documentation`.** This report, the README (installation, database setup, roles,
sample logins, data model, screenshots), the screenshot folder, and the final merge of `develop` into `main`.

## Member 2 — `2400718770-debug` (Question 1)

**Branch `feature/q1-database-relationships`, pull request #3.**

- Migrations for `students`, `courses` and `course_registrations` with the foreign keys, the cascade and
  null-on-delete rules, and the unique pair on (`student_id`, `course_id`) that stops a duplicate
  registration in the database.
- The `Student`, `Course` and `CourseRegistration` models with `hasMany`, `belongsTo` and `belongsToMany`
  relationships, the `registered_at` pivot, `scopeNotRegisteredFor` for the available courses, and the
  `student` and `taughtCourses` relationships added to `User`.
- Factories for `Student` and `Course`, and tests that read the relationships both ways and show the
  duplicate insert failing.

## Member 3 — `masha256ug` (Question 2)

**Branch `feature/q2-seeders`, pull request #4.**

- `UserSeeder` (one administrator, two lecturers), `StudentSeeder` (six students, each with its own login
  and the `student` role), `CourseSeeder` (five courses, each assigned to a lecturer) and
  `RegistrationSeeder` (fifteen registrations, two or three per student).
- All five seeders wired in order inside `DatabaseSeeder`, every one of them written so a second run
  creates nothing.
- Tests that sign in as each role and that compare the row counts after seeding twice.

## Member 4 — `Emmanuel-kisakye` (Question 4)

**Branch `feature/q4-application-functionality`, pull request #5.**

- Student CRUD: the list with the number of courses per student, the add and change forms, the record
  page, and a delete that also removes the login account.
- Course CRUD: the list, the forms with a lecturer chosen from the accounts that really have the lecturer
  role, and a delete that refuses a course that still has students on it.
- Course registration: a student registers with one click, duplicates are refused in code and again by the
  database, and a student may only cancel their own registration.
- Role-based display: `DashboardController` with a different page for each role, and course and
  registration lists that only hold the rows that role is allowed to see.
- Validation in five form requests, success and error messages after every action, and the tests for the
  four areas above.

## Member 5 — `paulmukasa98` (Question 5)

**Branch `feature/q5-blade-layout-validation`, pull request #2 and the follow-up pull request.**

- `resources/views/layouts/app.blade.php`: one reusable layout with a site header, a navigation slot, the
  page content through `@yield('content')` and `{{ $slot }}`, a footer, and the flash area that prints
  `session('success')`, `session('error')` and the list of validation errors. Every page in the system uses it.
- `resources/views/partials/nav.blade.php`: the menu, where each link appears only for the role or
  permission that is allowed to use it (`Students`, `Courses`, `Registrations`, `My Courses`,
  `Available Courses`, `My Registrations`, and `My Record` for a student who has a record).
- The follow-up pull request: the *My Record* link together with the tests that check which links each
  role is offered, and the screenshots of the app for all three roles in `docs/screenshots/`.

## How the pieces went in

| Pull request | Branch | Question | Merged as |
|---|---|---|---|
| #1 | `feature/q3-auth-roles-permissions` | 3a, 3b, 3c | `57a250f` |
| #2 | `feature/q5-blade-layout-validation` | 5a, 5d | `c51870c` |
| #3 | `feature/q1-database-relationships` | 1a, 1b, 1c, 1d | `958081c` |
| #4 | `feature/q2-seeders` | 2a, 2b, 2c, 2d | `8cb2876` |
| #5 | `feature/q4-application-functionality` | 4a, 4b, 4c, 4d, 4e | `31aaac4` |
| #6 | `feature/q3-auth-roles-permissions` | 3d, 3e | `62f722e` |
| next | `feature/q5-blade-layout-validation` | 5b, 5c | student record link and its tests |
| next | `feature/q6-documentation` | 6a, 6b, 6c | README, this report, screenshots |

`WORK_ORDER.md` gives the reasons for that order. Every pull request was tested before it was opened:
`php artisan migrate:fresh --seed` clean, `php artisan test` passing, `vendor/bin/pint --dirty` clean, and
the app opened as admin, lecturer and student.
