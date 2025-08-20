# Weather Feature Troubleshooting Guide

If you're experiencing issues with the weather feature, follow these steps to diagnose and fix the problem.

## 🚨 **Quick Fix Checklist**

### 1. **Check API Key Configuration**
- Ensure `OPENWEATHERMAP_API_KEY=your_key_here` is in your `.env` file
- Verify the API key is valid at [OpenWeatherMap](https://openweathermap.org/api)
- Make sure there are no extra spaces or quotes around the key

### 2. **Test API Connection**
Visit this URL to test if your API key is working:
```
/weather/test-connection
```

**Expected Response:**
```json
{
    "success": true,
    "message": "API connection successful"
}
```

**If Failed:**
```json
{
    "success": false,
    "message": "API key is not configured"
}
```

### 3. **Check Laravel Logs**
Look in `storage/logs/laravel.log` for detailed error messages.

## 🔍 **Common Issues & Solutions**

### **Issue: "Weather Data Unavailable"**

#### **Cause 1: Missing API Key**
**Symptoms:**
- Weather widget shows "Weather Data Unavailable"
- Error message mentions "Weather service may be temporarily unavailable"

**Solution:**
1. Add to your `.env` file:
   ```env
   OPENWEATHERMAP_API_KEY=d9da6799a84a9fef052289eeea15fb59
   ```
2. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

#### **Cause 2: Invalid API Key**
**Symptoms:**
- API test fails with 401 or 403 status
- Weather data loads but shows fallback data

**Solution:**
1. Generate a new API key at [OpenWeatherMap](https://openweathermap.org/api)
2. Update your `.env` file with the new key
3. Clear cache and restart your application

#### **Cause 3: API Rate Limiting**
**Symptoms:**
- Weather data works sometimes but fails at others
- Error messages about API limits

**Solution:**
1. Check your OpenWeatherMap account usage
2. The free tier allows 1000 calls/day
3. Consider upgrading to a paid plan for more farms

### **Issue: "Unable to get coordinates for farm location"**

#### **Cause: Location Names Not Recognized**
**Symptoms:**
- Geocoding fails for farm locations
- Weather data unavailable for specific farms

**Solution:**
1. Use standard Philippine location names:
   - ✅ "Manila" (not "City of Manila")
   - ✅ "Metro Manila" (not "National Capital Region")
   - ✅ "Cebu City" (not "Cebu")
   - ✅ "Davao City" (not "Davao")

2. Update farm location in registration:
   - Use official PSGC names
   - Avoid abbreviations or informal names

### **Issue: Weather Data Shows "Estimated Weather Data"**

#### **Cause: API Service Unavailable**
**Symptoms:**
- Weather widget shows fallback data
- Notice: "Using estimated weather data"

**Solution:**
1. This is a **fallback feature** - your weather widget is working!
2. Fallback data provides reasonable estimates when the API is down
3. Check your internet connection
4. Verify OpenWeatherMap service status
5. Wait for API service to resume

## 🛠️ **Debug Steps**

### **Step 1: Enable Debug Mode**
Set in your `.env` file:
```env
APP_DEBUG=true
```

### **Step 2: Test Individual Components**
1. **Test API Key**: `/weather/test-connection`
2. **Test Geocoding**: `/weather/debug/geocoding?city=Manila&province=Metro Manila`
3. **Test Weather Data**: Check browser console for JavaScript errors

### **Step 3: Check Network Requests**
1. Open browser Developer Tools (F12)
2. Go to Network tab
3. Refresh the dashboard
4. Look for failed requests to `/weather/*` endpoints

### **Step 4: Review Laravel Logs**
```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
- "Weather request for farm"
- "API Key check"
- "Coordinates obtained successfully"
- "Failed to fetch current weather data"

## 🔧 **Advanced Troubleshooting**

### **Check API Key Permissions**
Your OpenWeatherMap API key needs access to:
- ✅ Current Weather API
- ✅ 5-Day Forecast API
- ✅ Geocoding API
- ✅ OneCall API (for alerts and historical data)

### **Verify Farm Data**
Ensure your farm has:
- Valid `province_name`
- Valid `city_municipality_name`
- Optional `barangay_name`

### **Test with Sample Data**
Create a test farm with known good location:
- Province: "Metro Manila"
- City: "Manila"
- Barangay: "Barangay 1"

## 📞 **Getting Help**

### **Before Contacting Support:**
1. ✅ Checked API key configuration
2. ✅ Tested API connection
3. ✅ Reviewed Laravel logs
4. ✅ Verified farm location data
5. ✅ Checked internet connectivity

### **Include in Support Request:**
- Error messages from dashboard
- Laravel log entries
- API test results
- Farm location details
- Browser console errors

## 🎯 **Expected Behavior**

### **When Everything Works:**
- Weather widget loads immediately
- Current weather displays with real data
- 5-day forecast shows accurate predictions
- Weather alerts appear when applicable
- Historical trends load on demand

### **When Using Fallback Data:**
- Weather widget still displays
- Data shows "Using estimated weather data"
- Reasonable weather estimates based on location and season
- Manual refresh attempts to reconnect to API

## 🚀 **Performance Tips**

1. **Cache Management**: Weather data is cached to reduce API calls
2. **Auto-refresh**: Data updates every 30 minutes automatically
3. **Manual Refresh**: Use refresh button for immediate updates
4. **Location Precision**: Barangay-level data provides most accurate weather

---

**Remember**: The fallback system ensures farmers always have weather information, even when external services are unavailable. This is a feature, not a bug! 🌤️
