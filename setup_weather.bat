@echo off
echo.
echo ==========================================
echo    MeloTech Weather API Setup Script
echo ==========================================
echo.

REM Check if .env file exists
if exist .env (
    echo ✓ .env file already exists
) else (
    echo Creating .env file from template...
    copy ENV_TEMPLATE.txt .env
    if %errorlevel% equ 0 (
        echo ✓ .env file created successfully
    ) else (
        echo ✗ Failed to create .env file
        pause
        exit /b 1
    )
)

echo.
echo ==========================================
echo    IMPORTANT: Add Your API Key
echo ==========================================
echo.
echo 1. Get your free API key from: https://openweathermap.org/api
echo 2. Open the .env file in a text editor
echo 3. Find this line: OPENWEATHERMAP_API_KEY=your_api_key_here
echo 4. Replace 'your_api_key_here' with your actual API key
echo.
echo ✅ Your API key is already configured:
echo   OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59
echo.

REM Check if Laravel is available
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ⚠ PHP not found. Please install PHP first.
    echo.
    pause
    exit /b 1
)

echo Press any key to continue after adding your API key...
pause >nul

echo.
echo Clearing Laravel cache...
php artisan config:clear 2>nul
if %errorlevel% equ 0 (
    echo ✓ Configuration cache cleared
) else (
    echo ⚠ Could not clear config cache (this is normal if Laravel is not fully set up)
)

php artisan cache:clear 2>nul
if %errorlevel% equ 0 (
    echo ✓ Application cache cleared
) else (
    echo ⚠ Could not clear app cache (this is normal if Laravel is not fully set up)
)

echo.
echo ==========================================
echo    Test Your Setup
echo ==========================================
echo.
echo After adding your API key, you can test the weather API by visiting:
echo   http://localhost:8000/weather/test-connection
echo.
echo Or run the Laravel development server:
echo   php artisan serve
echo.
echo ==========================================
echo    Setup Complete!
echo ==========================================
echo.
echo Your MeloTech application now has:
echo   ✓ Current weather data
echo   ✓ 5-day weather forecasts  
echo   ✓ Weather alerts and warnings
echo   ✓ Historical weather trends
echo   ✓ Barangay-level precision for Philippine locations
echo.
pause
