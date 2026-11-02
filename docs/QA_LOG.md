# Quality Check Log

Final pass over the finished system, before the presentation. Nothing here came from a single question: the
method was to open the application as administrator, lecturer and student, walk the paths that are meant to
fail, and compare what the documents claim against what the code, the database and the screens really hold.

Each fix is one commit that contains both the failing check and the change that answers it.

| Problem | How it was found | What proves the fix | Commit |
|---|---|---|---|
| The students list showed a table with headings and no rows and no message when the database was empty, while the course and registration lists both explained themselves | opened `/students` with no students in it | `tests/Feature/EmptyStateTest.php`, three tests, one per role | `8f8273e` |
| Three page titles fell back to the word Laravel, while the header and footer fell back to Student Management System | `grep` for `Laravel` across `resources/views` | every view now uses the same fallback | `d5064d9` |
| `docs/screenshots/README.md` told the marker to look for seats in the course list, but the `courses` table has no seats column | compared the note with `database/migrations/2026_10_31_183709_create_courses_table.php` | the note now names the five columns that exist | `fb05dae` |
| `AGENT_INSTRUCTIONS.md` named branches that were never used and said Laravel 11 | read the file against `git branch -a` and `composer show laravel/framework` | a note at the top gives the six real branch names and the correct version | `7ae3168` |
| `composer.json` described the project as the Laravel skeleton | `composer show` on the local package | real description; `composer validate` passes | `7ae3168` |
| `CONTRIBUTIONS.md` still listed two pull requests as "next", and never mentioned the landing page, the MySQL wording change or the two `develop` → `main` merges | read the table against `git log --oneline origin/develop` | the table now carries pull requests #1 to #13 with their merge hashes | `b0de6c7` |
| `CONTRIBUTIONS.md` asked members for full names but had none | the cover page of the project report | all six names and student numbers are in the table | `b0de6c7` |
| The README said five students | the report cover page lists six | the README and the contribution report now say six; `WORK_ORDER.md` and `AGENT_INSTRUCTIONS.md` keep their original wording, because they are planning documents written before the sixth member joined | `b0de6c7` |

## Checked and found correct

- Lists are paginated: students 10 per page, courses 10, registrations 15. They have no search, which is a
  genuine gap but not a defect.
- The application timezone is `Africa/Kampala`, set in `config/app.php`, and dates print through it.
- `courses` really has no seats column, so the course list correctly shows code, title, lecturer, credits and
  the number of registered students.
- The duplicate registration rule is enforced three times: a check in `RegistrationController::store`, a
  caught query exception around the insert, and the unique (`student_id`, `course_id`) index in the database.
- `APP_NAME` is set in `.env`, so the titles display the system name and the Laravel fallback never showed.

## Still open

- `admin-course-validation.png` is not captured yet. It needs the application running against a real MySQL
  server: sign in as `admin@example.com`, open `/courses/create`, reuse an existing code and set credits to
  `24`, then photograph the red errors.
- `DB_CONNECTION=mysql` is documented but has not been exercised against a live server by this pass. The
  test suite runs on in-memory SQLite and does not touch that connection.
- `PRESENTATION.md` is written but not committed; it belongs to the repository owner.
- Member 1's full name is matched from ownership of the repository, not from a student number on a commit,
  so he should confirm it.

## State of the suite

`php artisan test` — 124 tests, 313 assertions, all passing. `vendor/bin/pint --dirty` — clean.
