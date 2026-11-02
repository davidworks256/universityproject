# Screenshots

Six pictures of the working application, taken after `php artisan migrate:fresh --seed` with
`php artisan serve` running on <http://localhost:8000>. Sign in with the account named for each row
(password `password` for all of them) and keep the address bar in the picture so the marker can see
which page it is.

| File | Sign in as | Page | What must be visible |
|---|---|---|---|
| `admin-dashboard.png` | `admin@example.com` | `/dashboard` | the four counts and the latest registrations |
| `admin-students.png` | `admin@example.com` | `/students` | student numbers, the course counts, the pagination links |
| `admin-courses.png` | `admin@example.com` | `/courses` | the course list with codes, titles, credits, the lecturer and how many students are registered |
| `lecturer-course-roster.png` | `lecturer@example.com` | `/courses/1` | the registered students of a course assigned to that lecturer |
| `student-dashboard.png` | `susan.achan@student.mubs.ac.ug` | `/dashboard` | her registrations and the courses still open to her |
| `student-forbidden.png` | `susan.achan@student.mubs.ac.ug` | `/students/5` | the 403 page, after typing another student's address |

The same six file names are listed in the README, section 8.

One more picture is worth having if there is time: sign in as `admin@example.com`, open
`/courses/create`, leave the code as an existing one and set credits to `24`, then capture the red
validation errors. That is the marker check for question 4.
