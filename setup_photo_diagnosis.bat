@echo off
echo ========================================
echo    Photo Diagnosis Feature Setup
echo ========================================
echo.

echo Setting up Photo Diagnosis feature...
echo.

REM Check if .env file exists
if not exist ".env" (
    echo ERROR: .env file not found!
    echo Please create a .env file first by copying .env.example
    echo.
    pause
    exit /b 1
)

echo 1. Running database migrations...
php artisan migrate --path=database/migrations/2025_08_26_210247_create_photo_analyses_table.php
if %errorlevel% neq 0 (
    echo ERROR: Migration failed!
    pause
    exit /b 1
)

echo.
echo 2. Creating storage link...
php artisan storage:link
if %errorlevel% neq 0 (
    echo WARNING: Storage link creation failed!
    echo You may need to create it manually or check permissions
)

echo.
echo 3. Checking routes...
php artisan route:list --name=photo-diagnosis
if %errorlevel% neq 0 (
    echo ERROR: Route check failed!
    pause
    exit /b 1
)

echo.
echo ========================================
echo    Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Add your Hugging Face API token to .env file:
echo    HUGGINGFACE_API_TOKEN=your_token_here
echo.
echo 2. Start the development server:
echo    php artisan serve
echo.
echo 3. Navigate to /photo-diagnosis in your browser
echo.
echo 4. For free API access, sign up at:
echo    https://huggingface.co
echo.
pause
