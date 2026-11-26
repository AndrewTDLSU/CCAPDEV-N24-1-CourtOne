List of working security features


Protection against Broken Access Control:
[x] Session storage and role-based access in all controllers (*Notes: all are placed within the controllers through if statements and helper functions)

[x] Verify account ownership (e.g. $_SESSION['user_id'] == $requestedUserId) before showing account pages
	>*/views/venues/manageVenue.php and /views/bookings/payment.php must be accessible only by the account owner and the ID is visible in the URL…
	> manageVenue.php: isVenueOwner() function in Venue.php model is invoked in venueStatistics() in VenueController.php enforce this
	> payments.php: implemented through if statement in bookingController.php in venueStatistics() function

[x] Re-check role-based restrictions on every controller action (*done through already created if statements in the controllers)

Protection against SQL Injection:
[x] Prepared statements in the models (SQL injection protection) (*Notes: project is well protected against SQL injections in the model layer already by using PDO)

Protection against XSS:
[x] Output encoding with htmlspecialchars() in the views (*Notes: exists via the courtEscape() function in views/partials/ViewHelpers.php)

[x] Validate all input server-side against expected formats (type, length, allowed characters) 
> Pages that require user text input that creates/reads from the database: 
   login.php, signup.php, VenueForm.php, manageStaff.php, chat.php, manageVenue.php, browseVenues.php 
	> Implemented via app/core/Validator.php w/ rules defined within the controllers, some existing if statements in the controllers also enforces this…
	> in login/signup, “@” symbol is required in the email field and bracket symbols “<” and “>” are not allowed.

Account Security:
[x] Password hashing in the user model (*Notes: password_hash() in User model works as intended, passwords are now stored as a hash value in the database)
[x] Keep the 8-character minimum in StaffController and AuthController 
[x] Optionally require uppercase, a number and a symbol (*added to the rules of the input validation) 

Login rate limiting
[X] Track failed attempts by IP and/or username
[X] Temporarily lock out or delay after a set number of failures (e.g. 5 attempts for 15 minutes)
[X] Reset the counter on successful login
