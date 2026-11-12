@echo off
cd /d "%~dp0"
set COURTONE_DB_NAME=
php scripts\setup-local.php
if errorlevel 1 goto failed
set COURTONE_DB_NAME=courtone_local_demo
echo Open http://localhost:8000 in your browser.
echo Keep this window open. Press Ctrl+C to stop.
php -S localhost:8000 -t .
:failed
pause
