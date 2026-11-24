@echo off
setlocal
cd /d "%~dp0"

call :setupDemoDatabase
if errorlevel 1 goto failed
if not exist ".env.local.php" if not defined COURTONE_DB_NAME set "COURTONE_DB_NAME=courtone_local_demo"

set "COURTONE_HTTP_PORT=%~1"
if not defined COURTONE_HTTP_PORT set "COURTONE_HTTP_PORT=8000"
set "COURTONE_PHP_OPTIONS="
php -r "exit(extension_loaded('mbstring') ? 0 : 1);"
if errorlevel 1 set "COURTONE_PHP_OPTIONS=-d extension=mbstring"

echo Open http://localhost:%COURTONE_HTTP_PORT%/index.php in your browser.
echo Keep this window open. Press Ctrl+C to stop.
php %COURTONE_PHP_OPTIONS% -S "localhost:%COURTONE_HTTP_PORT%" -t .
exit /b %ERRORLEVEL%

:setupDemoDatabase
setlocal
set "COURTONE_DB_NAME=mysql"
php scripts\setup-local.php
endlocal & exit /b %ERRORLEVEL%

:failed
echo CourtOne could not set up the local database.
pause
exit /b 1
