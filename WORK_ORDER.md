# Work Order — Student Management System (BUC3131, Coursework Two)

Repository: `davidworks256/universityproject`
Team: 5 members. Deadline: 2 November 2026. Marks: 40 for Questions 1–6, plus 10 for the individual presentation.

**Branch rule:** `main` holds stable code and `develop` is where members join their work, exactly as the brief
requires. Each question has one feature branch cut from `develop`. Every member opens a pull request into
`develop`, and only the repo owner (Member 1, `davidworks256`) approves and merges. `develop` goes into `main`
once, at the end, after the whole app has been tested.

## 1. The order we build in

This is the order we merge work, not the order of the questions in the brief. Each row is one pull request.

| # | Question | Branch | Who | Marks | Why it is here |
|---|---|---|---|---|---|
| 1 | Q3, first half | `feature/q3-auth-roles-permissions` | Member 1 | 6 (a,b,c) | Login setup and the roles package must exist before anything else can be tested. Also adds empty routes and controllers for students, courses and registrations. |
| 2 | Q5, first half | `feature/q5-blade-layout-validation` | Member 5 | 5 (a,d) | The page layout and the menu must exist before anyone writes pages that use them. |
| 3 | Q1 | `feature/q1-database-relationships` | Member 2 | 10 | Tables, models, relationships and the "no duplicate registration" rule. Seeders, policies and CRUD all wait on this. |
| 4 | Q2 | `feature/q2-seeders` | Member 3 | 4 | Seeders need the models from Q1 and the roles from Q3 already in place. |
| 5 | Q4 | `feature/q4-application-functionality` | Member 4 | 12 | Student and course CRUD, registering for a course, showing the right records per role, success and error messages. |
| 6 | Q3, second half + Q5, second half | same two branches as above | Member 1 / Member 5 | — | Route protection and policies, plus the student/course/registration pages. These only make sense once real controllers exist. |
| 7 | Q6 | `feature/q6-documentation` | Member 1 | 3 | README, roles and test logins, screenshots, contribution report, and the final merge of `develop` into `main`. |

Member 1 shows up twice (start and end) because he is also the owner who merges every pull request. Members 2,
3, 4 and 5 each work in one block only, so nobody has to switch git accounts halfway through.

## 2. Why we did not build in question order

Three things lower down the question list are needed by things higher up. Each one breaks the app, not just the
plan.

### 2.1 Q2 needs Q3 — seeding roles before the package is there fails

Q2b says seeders must create users and give them roles. That runs `$user->assignRole('admin')`, and:

- the method does not exist until `spatie/laravel-permission` is installed and `HasRoles` is added to `User`;
- even then it fails with a "role does not exist" error unless the roles and permissions tables have been
  created and filled by `RolesAndPermissionsSeeder` (Q3b).

So Q2 cannot pass `php artisan migrate:fresh --seed` before Q3's setup and role seeder are done. That setup
moves to the front.

### 2.2 Q4 needs Q5 — CRUD pages have nothing to sit inside

Every Q4 page starts with `@extends('layouts.app')` (Q5a), and the success/error messages in Q4e are printed by
the flash area inside that layout. If Q4 comes first, Member 4 must either write full pages with their own HTML
that Member 5 then throws away, or invent a second layout that fights with Member 5's. Same files, written
twice. So the layout comes at step 2.

### 2.3 Q3 needs Q4 for its second half — you cannot guard routes that do not exist

"Protect routes with middleware" (Q3d) and "stop users seeing other people's records" (Q3e) work on the
`students`, `courses` and `registrations` routes and on the `Student`, `Course` and `Registration` policies.
Those come from Q1 and Q4. So Q3 is split: install, `HasRoles`, roles seeder and default student role first;
middleware and policies later, after the routes they guard exist.

## 3. Why the login setup goes first (a practical reason)

`php artisan breeze:install blade` rewrites `routes/web.php`, everything in `resources/views/` including
`layouts/`, `resources/css/app.css`, `package.json` and the Vite config. If it runs after another member has
built pages or routes, their work is overwritten in the merge. Doing it first removes that problem.

## 4. Rules that stop members colliding

1. **Empty routes and controllers in step 1.** Member 1 declares the `students`, `courses` and `registrations`
   resource routes with empty controllers in the first pull request. Without them, the menu added in Q5 calls
   `route('students.index')` for a route that is not there yet, and every page fails. Member 4 fills in the
   controller bodies later and leaves the route list alone.
2. **Clear owners for files.** Member 5 owns `layouts/`, `partials/` and the page frames for students, courses
   and registrations. Member 4 may change what is inside those frames (form fields, table rows, `@error`
   messages) but never the layout or the menu. No file has two owners in the same step.
3. **`develop` is the only merge target.** Each branch starts from the newest `develop`, and
   `git pull origin develop` runs before the pull request is opened. `main` only changes when the owner merges
   the fully tested `develop` at the end.
4. **One git identity per member.** Each commit carries that member's own name and email, so the GitHub history
   shows real individual work, as the brief requires.
5. **Small pull requests, in this order.** A step is finished when `php artisan migrate:fresh --seed` runs clean,
   `php artisan test` passes, and the app opens for all three roles.

## 5. Marks per member

| Member | Owns | Marks |
|---|---|---|
| Member 1 (owner) | Q3 login, roles, permissions, middleware, policies + Q6 docs | 6 + 3 |
| Member 2 | Q1 tables, models, relationships, rules | 10 |
| Member 3 | Q2 seeders | 4 |
| Member 4 | Q4 CRUD, registration, records per role | 12 |
| Member 5 | Q5 layout, pages, validation, menu by permission | 5 |

Members 3 and 4 are uneven (4 against 12 marks). If that needs fixing, Member 3 can add the course-registration
commits to Member 4's `feature/q4-application-functionality` branch right after their own step. It is still one
block of work for Member 3, and their name still shows on those commits.

## 6. Where we moved away from the brief's numbering

- **Q1 is not first.** It is third. It has no dependencies, but Q2 and Q4 need login setup and the layout, and Q1
  needs neither of those.
- **Q3 and Q5 each run in two steps** (Q3: setup, then policies; Q5: layout, then pages). The branch count stays
  at one per question; the second pull request comes from the same branch.
- **`AGENT_INSTRUCTIONS.md` still shows an older plan** with different branch names. This file replaces it for
  work order and branch names. The per-member prompts in that file are still useful for scope.

## 7. What we submit (from the brief)

- Repository URL and full source code without the `vendor` folder.
- Migrations, models, controllers, routes, views, seeders.
- README with install steps, database setup, roles and sample logins.
- Screenshots of the working app as admin, lecturer and student.
- Contribution report: members, tasks, branches, pull requests.
- Every member must explain their own code in the presentation.
