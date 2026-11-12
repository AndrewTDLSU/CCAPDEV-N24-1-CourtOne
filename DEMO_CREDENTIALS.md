# CourtOne demo logins

These accounts are included in `database/courtOne_db.sql`.
All five accounts use the password: `CourtOneDemo123!`

| Role | Email | Password | Login selection |
| --- | --- | --- | --- |
| Admin | admin@example.com | CourtOneDemo123! | Customer User Matrix |
| Court owner | owner@example.com | CourtOneDemo123! | Customer User Matrix |
| Staff | staff@example.com | CourtOneDemo123! | Staff portal |
| Customer | player@example.com | CourtOneDemo123! | Customer User Matrix |
| Customer | player2@example.com | CourtOneDemo123! | Customer User Matrix |

Open `/index.php?url=auth/login` and enter the email and password above.
For booking and cancellation tests, use either customer account.

The database stores password hashes intentionally. Enter the password above
in the login form, not the hash. Replacing a hash with plain text would break
the application's `password_verify()` login check.

The demo password was verified against all five hashes in the SQL seed file.
If an account's password has since changed in your local database, these seed
credentials will no longer apply to that account. Do not reimport the database
just to reset passwords: the SQL dump drops existing tables.
