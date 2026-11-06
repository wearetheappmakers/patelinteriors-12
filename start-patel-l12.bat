@echo off
title Patel Interiors - Laravel 12 Server (PHP 8.2)
echo ======================================================================
echo    Patel Interiors - Laravel 12 / PHP 8.2 Development Server
echo ======================================================================
echo.

:: 1. Verify MySQL service / daemon
echo [1/3] Checking MySQL / MariaDB service...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL/MariaDB service is currently active.
) else (
    echo [INFO] MySQL process not detected. Attempting to start XAMPP MySQL...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 3 /nobreak >nul
    ) else (
        echo [WARNING] Please ensure MySQL/MariaDB is running in XAMPP Control Panel.
    )
)

:: 2. Set directory and verify PHP binary
cd /d "%~dp0"
set PHP_BIN=C:\xampp\php\php.exe
if not exist "%PHP_BIN%" (
    echo [ERROR] PHP 8.2 binary not found at %PHP_BIN%.
    echo Please install or configure PHP 8.2 and update this script.
    pause
    exit /b 1
)

:: 3. Clear cache and start server
echo.
echo [2/3] Checking environment & cache...
"%PHP_BIN%" artisan config:clear >nul 2>&1
"%PHP_BIN%" artisan view:clear >nul 2>&1

echo.
echo [3/3] Starting Laravel 12 application server on port 8007...
echo.
echo ======================================================================
echo  Application URLs:
echo    - Public / Login:  http://127.0.0.1:8007/
echo    - Admin Login:     http://127.0.0.1:8007/user/login
echo    - Employee Login:  http://127.0.0.1:8007/employee/login
echo    - API Version 1:   http://127.0.0.1:8007/api/v1/
echo ======================================================================
echo  Press Ctrl+C to stop the server at any time.
echo.

"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8007
pause

