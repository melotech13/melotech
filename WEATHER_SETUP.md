# Enhanced Weather Feature Setup Guide

This guide explains how to set up the comprehensive weather feature for the MeloTech dashboard, including real-time weather, alerts, historical trends, and barangay-level precision.

## Prerequisites

1. **OpenWeatherMap API Key**: You need a free API key from OpenWeatherMap
   - Visit: https://openweathermap.org/api
   - Sign up for a free account
   - Generate an API key (it's free for up to 1000 calls/day)
   - **Important**: Ensure your API key has access to:
     - Current Weather API
     - 5-Day Forecast API
     - Geocoding API
     - OneCall API (for alerts and historical data)

## Configuration

### 1. Add API Key to Environment

Add the following line to your `.env` file:

```env
OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59
```

### 2. Verify Configuration

The weather service is configured in `config/services.php` and will automatically use the API key from your environment file.

## Enhanced Features

The weather feature now provides:

### 🌤️ **Current Weather**
- Temperature, humidity, wind speed/direction, visibility
- Sunrise/sunset times
- Real-time weather conditions
- Location precision indicator (barangay/city/province level)

### 📅 **5-Day Forecast**
- Detailed weather predictions for farm planning
- Hourly breakdowns for each day
- Temperature, humidity, and wind forecasts

### 🚨 **Weather Alerts**
- Critical weather warnings and notifications
- Severity-based alert system (Critical, High, Moderate, Low)
- Farming-specific recommendations for each alert type
- Real-time updates for severe weather conditions

### 📊 **Historical Weather Trends**
- 7-day weather history and analysis
- Temperature, humidity, and rainfall trends
- Farming condition assessments
- Weather pattern analysis for crop planning

### 🎯 **Barangay-Level Precision**
- Enhanced location targeting using barangay data
- Multiple geocoding strategies for better accuracy
- Fallback coordinates for major Philippine cities
- Location precision indicators

### 🔄 **Smart Caching & Performance**
- Current weather: 30-minute cache
- Forecast: 2-hour cache
- Alerts: 1-hour cache
- Historical data: 6-hour cache
- Geocoding: 24-hour cache

## How It Works

1. **Enhanced Location Resolution**: 
   - Tries barangay + city + province combinations first
   - Falls back to city + province if barangay not found
   - Uses hardcoded coordinates for major Philippine locations as final fallback

2. **Multi-Source Weather Data**:
   - Current weather from main weather API
   - Forecast from 5-day forecast API
   - Alerts from OneCall API
   - Historical data from time machine API

3. **Smart Data Processing**:
   - Weather trend analysis and pattern recognition
   - Farming-specific condition assessments
   - Crop management recommendations

4. **Intelligent Display**:
   - Real-time updates every 30 minutes
   - Manual refresh options
   - Responsive design for all devices

## API Usage & Limits

- **Current Weather**: 1 API call per farm location
- **Forecast**: 1 API call per farm location  
- **Alerts**: 1 API call per farm location
- **Historical**: 7 API calls per farm location (7 days)
- **Geocoding**: 1 API call per unique location (cached for 24 hours)

**Total**: ~11 API calls per farm per day
**Free Tier**: 1000 calls/day supports ~90 farms

## Troubleshooting

### Weather Data Not Loading

1. Check if your API key is correctly set in `.env`
2. Verify the API key has all required permissions
3. Check browser console for error messages
4. Ensure your farm has valid location data
5. Review Laravel logs in `storage/logs/laravel.log`

### Location Not Found

1. Verify farm location names are correct
2. Check if the city/municipality and province names match official Philippine locations
3. The enhanced geocoding works with multiple location formats
4. **Common Solutions**:
   - Use "Manila" instead of "City of Manila"
   - Use "Metro Manila" instead of "National Capital Region"
   - Use "Cebu City" instead of "Cebu"
   - Use "Davao City" instead of "Davao"

### API Rate Limiting

1. Check your OpenWeatherMap account for current usage
2. The caching system reduces API calls significantly
3. Consider upgrading to a paid plan for more farms

### Debug Geocoding Issues

If you're experiencing geocoding problems:

1. **Enable Debug Mode**: Set `APP_DEBUG=true` in your `.env` file
2. **Test Geocoding**: Visit `/weather/debug/geocoding?city=Manila&province=Metro Manila`
3. **Check Logs**: Review `storage/logs/laravel.log` for detailed error information
4. **Verify API Key**: Ensure your OpenWeatherMap API key has geocoding permissions

## Security Notes

- API keys are stored in environment variables (never in code)
- Weather data is cached to reduce API calls
- All weather requests are authenticated through Laravel's auth middleware
- Users can only access weather data for their own farms
- Debug endpoints are only available in debug mode

## Support

If you encounter issues:

1. Check the Laravel logs in `storage/logs/laravel.log`
2. Verify API key permissions and quotas
3. Ensure all required packages are installed
4. Check network connectivity to OpenWeatherMap APIs
5. Test individual API endpoints using the debug routes

## Feature Benefits for Farmers

### 🚨 **Weather Alerts**
- **Critical Alerts**: Immediate action required (storms, floods)
- **High Alerts**: Prepare for adverse conditions (heavy rain, wind)
- **Moderate Alerts**: Monitor conditions (fog, heat)
- **Low Alerts**: Informational updates

### 📊 **Historical Trends**
- **Temperature Patterns**: Plan crop timing and irrigation
- **Rainfall Analysis**: Optimize drainage and water management
- **Wind Patterns**: Plan trellising and wind protection
- **Farming Conditions**: Get specific recommendations for your crops

### 🎯 **Precise Location Data**
- **Barangay Level**: Most accurate weather for your exact farm location
- **City Level**: Good accuracy for urban and suburban farms
- **Province Level**: Reliable data for rural areas

The enhanced weather feature provides comprehensive, location-specific weather intelligence to help farmers make informed decisions for optimal crop management and farm activities! 🌾🌤️🚨
