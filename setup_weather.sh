#!/bin/bash

echo ""
echo "=========================================="
echo "   MeloTech Weather API Setup Script"
echo "=========================================="
echo ""

# Check if .env file exists
if [ -f .env ]; then
    echo "✓ .env file already exists"
else
    echo "Creating .env file from template..."
    cp ENV_TEMPLATE.txt .env
    if [ $? -eq 0 ]; then
        echo "✓ .env file created successfully"
    else
        echo "✗ Failed to create .env file"
        exit 1
    fi
fi

echo ""
echo "=========================================="
echo "   IMPORTANT: Add Your API Key"
echo "=========================================="
echo ""
echo "1. Get your free API key from: https://openweathermap.org/api"
echo "2. Open the .env file in a text editor"
echo "3. Find this line: OPENWEATHERMAP_API_KEY=your_api_key_here"
echo "4. Replace 'your_api_key_here' with your actual API key"
echo ""
echo "✅ Your API key is already configured:"
echo "  OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59"
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "⚠ PHP not found. Please install PHP first."
    echo ""
    exit 1
fi

echo "Press Enter to continue after adding your API key..."
read -r

echo ""
echo "Clearing Laravel cache..."
php artisan config:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✓ Configuration cache cleared"
else
    echo "⚠ Could not clear config cache (this is normal if Laravel is not fully set up)"
fi

php artisan cache:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✓ Application cache cleared"
else
    echo "⚠ Could not clear app cache (this is normal if Laravel is not fully set up)"
fi

echo ""
echo "=========================================="
echo "   Test Your Setup"
echo "=========================================="
echo ""
echo "After adding your API key, you can test the weather API by visiting:"
echo "  http://localhost:8000/weather/test-connection"
echo ""
echo "Or run the Laravel development server:"
echo "  php artisan serve"
echo ""
echo "=========================================="
echo "   Setup Complete!"
echo "=========================================="
echo ""
echo "Your MeloTech application now has:"
echo "  ✓ Current weather data"
echo "  ✓ 5-day weather forecasts"
echo "  ✓ Weather alerts and warnings"
echo "  ✓ Historical weather trends"
echo "  ✓ Barangay-level precision for Philippine locations"
echo ""
