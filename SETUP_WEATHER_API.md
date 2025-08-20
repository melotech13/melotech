# 🌤️ Weather API Setup Guide

This guide will help you add an actual OpenWeatherMap API key to make the weather features in MeloTech accurate and functional.

## Step 1: Get Your Free API Key

1. **Visit OpenWeatherMap**: Go to https://openweathermap.org/api
2. **Sign Up**: Create a free account (if you don't have one)
3. **Get API Key**: 
   - Log into your account
   - Go to "API keys" section
   - Copy your default API key
   - It will look like: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`

## Step 2: Create Your .env File

Since you don't have a `.env` file yet, you need to create one:

1. **Copy the template**: Copy the `ENV_TEMPLATE.txt` file to `.env`
   ```bash
   copy ENV_TEMPLATE.txt .env
   ```

2. **Edit the .env file**: Open the `.env` file and find this line:
   ```
   OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59
   ```

3. **Replace with your actual API key**:
   ```
   OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59
   ```
   ✅ **API Key Configured!** Your actual OpenWeatherMap API key is now set.

## Step 3: Clear Laravel Cache

After adding the API key, clear Laravel's configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## Step 4: Test the Weather API

You can test if your API key is working by visiting:
```
http://your-domain/weather/test-connection
```

**Expected Success Response:**
```json
{
    "success": true,
    "message": "API connection successful",
    "api_key_configured": true
}
```

**If Failed:**
```json
{
    "success": false,
    "message": "API key is not configured or invalid"
}
```

## What Weather Features You'll Get

Once the API key is configured, your MeloTech application will have:

### 🌤️ **Current Weather**
- Real-time temperature, humidity, wind speed
- Sunrise/sunset times
- Weather conditions and descriptions
- Location-specific data for each farm

### 📅 **5-Day Forecast**
- Detailed weather predictions
- Hourly breakdowns
- Temperature and humidity forecasts

### 🚨 **Weather Alerts**
- Critical weather warnings
- Farming-specific recommendations
- Severity-based alert system

### 📊 **Historical Weather Trends**
- 7-day weather history analysis
- Temperature and rainfall patterns
- Farming condition assessments

### 🎯 **Barangay-Level Precision**
- Enhanced location targeting
- Philippine-specific geocoding
- Fallback coordinates for major cities

## API Usage Limits

The free OpenWeatherMap API provides:
- **1,000 API calls per day**
- **60 calls per minute**
- Perfect for small to medium farms

If you need more calls, you can upgrade to a paid plan.

## Troubleshooting

If weather data shows "Weather Data Unavailable":

1. **Check your API key** in the `.env` file
2. **Verify the key is valid** at openweathermap.org
3. **Clear Laravel cache** with the commands above
4. **Check logs** in `storage/logs/laravel.log` for errors

## Security Note

- Never commit your `.env` file to version control
- Keep your API key private
- The `.env` file is already in `.gitignore`

---

**Need help?** Check the existing `WEATHER_TROUBLESHOOTING.md` file for common issues and solutions.
