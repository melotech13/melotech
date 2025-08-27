# Photo Diagnosis Feature

## Overview

The Photo Diagnosis feature provides AI-powered analysis of crop photos to help farmers identify issues and get actionable recommendations. Farmers can upload photos of leaves or watermelon and receive instant analysis with specific recommendations for their crop type.

## Features

### 🖼️ **Two Simple Pages**

1. **Analysis History Page** (`/photo-diagnosis`)
   - Displays all past analysis results in a clean, easy-to-read table
   - Shows photo thumbnails, analysis type, identified type, confidence score, and date
   - Quick statistics dashboard with total analyses count
   - Easy navigation to create new analyses

2. **New Analysis Page** (`/photo-diagnosis/create`)
   - Simple form to select analysis type (Leaves or Watermelon)
   - Drag-and-drop photo upload with preview
   - File validation (PNG, JPG, JPEG up to 2MB)
   - Clear instructions and user guidance

3. **Results Page** (`/photo-diagnosis/{id}`)
   - Displays uploaded photo and analysis details
   - Shows identified type and confidence score
   - Provides specific recommendations based on crop type
   - Type-specific care tips and next steps

### 🤖 **AI-Powered Analysis**

- **Free API Integration**: Uses Hugging Face's free inference API
- **Smart Fallback**: Rule-based analysis when API is unavailable
- **Confidence Scoring**: Provides reliability metrics for each analysis
- **Context-Aware**: Recommendations tailored to specific crop types

### 📊 **Database Storage**

- Stores all analysis results with user association
- Tracks analysis type, identified type, confidence scores
- Maintains photo storage with secure file handling
- Full analysis history for tracking progress over time

## Technical Implementation

### Database Schema

```sql
CREATE TABLE photo_analyses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    analysis_type ENUM('leaves', 'watermelon') NOT NULL,
    identified_type VARCHAR(255) NOT NULL,
    confidence_score DECIMAL(5,2) NULL,
    recommendations TEXT NOT NULL,
    analysis_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### API Integration

- **Hugging Face API**: Free image classification service
- **Fallback System**: Rule-based analysis when external API fails
- **Error Handling**: Graceful degradation for network issues
- **Rate Limiting**: Respects free tier limitations

### File Storage

- Photos stored in `storage/app/public/photo-analyses/`
- Automatic file cleanup with user deletion
- Secure file access through Laravel Storage facade
- File size and type validation

## Setup Instructions

### 1. Environment Configuration

Add to your `.env` file:

```env
HUGGINGFACE_API_TOKEN=your_token_here
HUGGINGFACE_BASE_URL=https://api-inference.huggingface.co
HUGGINGFACE_MODEL=facebook/detr-resnet-50
```

### 2. Database Migration

```bash
php artisan migrate
```

### 3. Storage Setup

```bash
php artisan storage:link
```

### 4. Verify Routes

The following routes are automatically registered:

- `GET /photo-diagnosis` - Analysis history
- `GET /photo-diagnosis/create` - New analysis form
- `POST /photo-diagnosis` - Store analysis
- `GET /photo-diagnosis/{id}` - View results

## Usage Guide

### For Farmers

1. **Navigate to Photo Diagnosis**
   - Click "Photo Diagnosis" in the main navigation
   - Or access via user dropdown menu

2. **Start New Analysis**
   - Click "New Analysis" button
   - Select whether analyzing leaves or watermelon
   - Upload a clear, well-lit photo
   - Click "Analyze Photo"

3. **Review Results**
   - View identified crop type and confidence score
   - Read specific recommendations for your situation
   - Follow type-specific care tips
   - Implement suggested next steps

4. **Track Progress**
   - Return to analysis history to view all results
   - Compare multiple analyses over time
   - Monitor improvement in crop health

### Best Practices

- **Photo Quality**: Use clear, well-lit images
- **Consistent Lighting**: Natural daylight provides best results
- **Proper Focus**: Ensure the main subject is in focus
- **Regular Monitoring**: Take photos at consistent intervals
- **Follow Recommendations**: Implement suggested care practices

## API Configuration

### Hugging Face Setup

1. **Create Account**: Sign up at [huggingface.co](https://huggingface.co)
2. **Generate Token**: Create API token in account settings
3. **Configure Model**: Uses `facebook/detr-resnet-50` by default
4. **Rate Limits**: Free tier includes 30,000 requests/month

### Alternative Models

You can configure different models by updating the config:

```php
// config/services.php
'huggingface' => [
    'model' => env('HUGGINGFACE_MODEL', 'your-preferred-model'),
],
```

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check internet connection
   - Verify API token is valid
   - Check rate limit status
   - System will use fallback analysis

2. **Photo Upload Issues**
   - Ensure file is under 2MB
   - Check file format (PNG, JPG, JPEG)
   - Verify storage permissions

3. **Analysis Accuracy**
   - Use high-quality, well-lit photos
   - Ensure proper crop type selection
   - Consider multiple angles for complex issues

### Support

- Check Laravel logs for detailed error information
- Verify database connection and migrations
- Test API connectivity independently
- Review file storage permissions

## Future Enhancements

- **Multi-Crop Support**: Expand beyond leaves and watermelon
- **Disease Detection**: Specific pathogen identification
- **Growth Stage Analysis**: Crop development tracking
- **Weather Integration**: Context-aware recommendations
- **Mobile App**: Native mobile photo capture
- **Batch Processing**: Multiple photo analysis
- **Export Reports**: PDF analysis summaries

## Security Features

- **User Isolation**: Users can only access their own analyses
- **File Validation**: Strict file type and size restrictions
- **SQL Injection Protection**: Laravel Eloquent ORM
- **XSS Prevention**: Blade template escaping
- **CSRF Protection**: Laravel built-in CSRF tokens

## Performance Considerations

- **Image Optimization**: Automatic resizing for storage
- **Lazy Loading**: Efficient database queries
- **Caching**: API response caching (future enhancement)
- **Background Processing**: Queue-based analysis (future enhancement)

---

**Note**: This feature is designed to provide helpful guidance but should not replace professional agricultural advice. For critical crop issues, always consult with local agricultural experts.
