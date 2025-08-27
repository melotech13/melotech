@extends('layouts.app')

@section('title', 'New Photo Analysis - MeloTech')

@section('content')
<div class="photo-analysis-container">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-upload"></i>
                    New Photo Analysis
                </h1>
                <p class="page-subtitle">Upload your crop photos for AI-powered disease detection and health assessment</p>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-image"></i>
                        <span>Photo Upload</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-brain"></i>
                        <span>AI Analysis</span>
                    </div>
                    <div class="action-status">
                        <a href="{{ route('photo-diagnosis.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; text-decoration: none;">
                            <i class="fas fa-arrow-left me-2"></i>Back to History
                        </a>
                    </div>
                </div>
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
            </div>
        </div>
    </div>



    <!-- Upload Form -->
    <div class="upload-section">
        <div class="section-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-upload me-2"></i>
                    Upload Photo for Analysis
                </h3>
            </div>
            <div class="card-content">
                <form action="{{ route('photo-diagnosis.store') }}" method="POST" enctype="multipart/form-data" id="photo-analysis-form">
                    @csrf
                    
                    <!-- Analysis Type Selection -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-list me-2"></i>
                            What are you analyzing? *
                        </h5>
                        <div class="type-selection-grid">
                            <div class="type-option">
                                <input type="radio" class="type-radio" name="analysis_type" id="leaves" value="leaves" {{ old('analysis_type') == 'leaves' ? 'checked' : '' }} required>
                                <label class="type-label" for="leaves">
                                    <div class="type-icon">
                                        <i class="fas fa-leaf"></i>
                                    </div>
                                    <h6>Leaves</h6>
                                    <p>Analyze leaf health, diseases, and pest issues</p>
                                </label>
                            </div>
                            
                            <div class="type-option">
                                <input type="radio" class="type-radio" name="analysis_type" id="watermelon" value="watermelon" {{ old('analysis_type') == 'watermelon' ? 'checked' : '' }} required>
                                <label class="type-label" for="watermelon">
                                    <div class="type-icon">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                    <h6>Watermelon</h6>
                                    <p>Analyze watermelon ripeness and quality</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-image me-2"></i>
                            Upload Your Photo *
                        </h5>
                        <div class="upload-area" id="upload-area">
                            <div class="upload-content" id="upload-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h6>Click to upload or drag and drop</h6>
                                <p>PNG, JPG, JPEG up to 10MB</p>
                                <input type="file" name="photo" id="photo-input" accept="image/*" required style="display: none;">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('photo-input').click()">
                                    <i class="fas fa-upload me-2"></i>Choose File
                                </button>
                            </div>
                            <div class="preview-content" id="preview-content" style="display: none;">
                                <img id="image-preview" src="" alt="Preview" class="img-fluid">
                                <div class="preview-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="removeImage()">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn" disabled>
                            <i class="fas fa-play me-2"></i>Start Analysis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Basic styling */
    .photo-analysis-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Unified Header */
    .unified-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 25%, #1e40af 50%, #1e3a8a 75%, #1e293b 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .unified-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .header-main {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .header-left {
        flex: 1;
    }

    .header-right {
        display: flex;
        align-items: center;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0 0 0.75rem 0;
        text-shadow: 0 3px 10px rgba(0,0,0,0.3);
        color: white !important;
    }

    .page-subtitle {
        font-size: 1.1rem;
        margin: 0;
        opacity: 0.95;
        font-weight: 400;
        color: white !important;
    }

    .back-action .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .back-action .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        margin-bottom: 2rem;
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Instructions Section */
    .instructions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .instruction-card {
        text-align: center;
        padding: 2rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .instruction-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #d1d5db;
    }

    .instruction-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1.5rem;
    }

    .instruction-card:nth-child(2) .instruction-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .instruction-card h5 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .instruction-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .instruction-list li {
        padding: 0.5rem 0;
        color: #6b7280;
        position: relative;
        padding-left: 1.5rem;
    }

    .instruction-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #10b981;
        font-weight: bold;
    }

    /* Form Sections */
    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    /* Type Selection */
    .type-selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .type-option {
        position: relative;
    }

    .type-radio {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .type-label {
        display: block;
        padding: 2rem 1.5rem;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }

    .type-radio:checked + .type-label {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
    }

    .type-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin: 0 auto 1rem;
    }

    .type-option:nth-child(2) .type-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .type-label h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .type-label p {
        color: #6b7280;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Upload Area */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        background: #f9fafb;
        transition: all 0.3s ease;
        cursor: pointer;
        /* Ensure proper centering */
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .upload-area.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.02);
    }

    .upload-icon {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .upload-content {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
    }

    .upload-content h6 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .upload-content p {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .preview-content {
        display: none;
        text-align: center;
    }

    .preview-content.show {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        min-height: 300px !important;
    }

    #image-preview {
        max-width: 100% !important;
        max-height: 300px !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
        display: block !important;
        margin-left: auto !important;
        margin-right: auto !important;
        object-fit: contain;
        width: auto !important;
        height: auto !important;
        /* Force centering */
        text-align: center;
    }

    .preview-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-control {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Form Actions */
    .form-actions {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
        margin-top: 2rem;
        /* Ensure perfect centering with flexbox */
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        /* Ensure button is centered */
        margin: 0 auto !important;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background: #2563eb;
        transform: translateY(-2px);
    }

    .btn-primary:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    /* Submit button specific centering */
    #submit-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto !important;
        min-width: 200px !important;
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
        .photo-analysis-container {
            max-width: 100%;
            padding: 1.5rem 1rem;
        }

        .unified-header {
            padding: 2rem 1.5rem;
        }

        .header-main {
            flex-direction: column;
            gap: 1.5rem;
        }

        .header-right {
            align-self: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .instructions-grid {
            grid-template-columns: 1fr;
        }

        .type-selection-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .photo-analysis-container {
            padding: 1.25rem 0.75rem;
        }

        .unified-header {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .card-header, .card-content {
            padding: 1.25rem;
        }

        .upload-area {
            padding: 2rem 1rem;
        }

        .preview-content.show {
            min-height: 250px;
        }

        #image-preview {
            max-height: 250px;
        }

        /* Mobile button centering */
        .form-actions {
            padding: 1rem 0.5rem;
        }

        #submit-btn {
            min-width: 180px !important;
            width: 100% !important;
            max-width: 300px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const photoInput = document.getElementById('photo-input');
    const uploadContent = document.getElementById('upload-content');
    const previewContent = document.getElementById('preview-content');
    const imagePreview = document.getElementById('image-preview');
    const submitBtn = document.getElementById('submit-btn');

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // File input change
    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }

        if (file.size > 10 * 1024 * 1024) { // 10MB
            alert('File size must be less than 10MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            uploadContent.style.display = 'none';
            previewContent.style.display = 'block';
            previewContent.classList.add('show');
            submitBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // Remove image
    window.removeImage = function() {
        photoInput.value = '';
        uploadContent.style.display = 'block';
        previewContent.style.display = 'none';
        previewContent.classList.remove('show');
        submitBtn.disabled = true;
    };

    // Form validation
    const form = document.getElementById('photo-analysis-form');
    form.addEventListener('submit', function(e) {
        const analysisType = document.querySelector('input[name="analysis_type"]:checked');
        const photo = photoInput.files[0];

        if (!analysisType) {
            e.preventDefault();
            alert('Please select an analysis type.');
            return;
        }

        if (!photo) {
            e.preventDefault();
            alert('Please select a photo.');
            return;
        }
    });
});
</script>
@endpush
