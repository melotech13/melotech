<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Analysis Report - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .report-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .report-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        
        .report-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }
        
        .report-meta {
            background: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        
        .meta-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .meta-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .report-content {
            padding: 30px;
        }
        
        .summary-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #28a745;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .analyses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .analyses-table th {
            background: #28a745;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }
        
        .analyses-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        
        .analyses-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .analysis-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .analysis-type.leaves {
            background: #d4edda;
            color: #155724;
        }
        
        .analysis-type.watermelon {
            background: #cce5ff;
            color: #004085;
        }
        
        .confidence-score {
            font-weight: bold;
        }
        
        .confidence-high {
            color: #28a745;
        }
        
        .confidence-medium {
            color: #ffc107;
        }
        
        .confidence-low {
            color: #dc3545;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        
        .report-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .report-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Report Header -->
        <div class="report-header">
            <h1 class="report-title">Photo Analysis Report</h1>
            <p class="report-subtitle">AI-Powered Crop Diagnosis Results</p>
        </div>
        
        <!-- Report Meta Information -->
        <div class="report-meta">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">Generated For</div>
                    <div class="meta-value">{{ $user->name }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Email</div>
                    <div class="meta-value">{{ $user->email }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Generated On</div>
                    <div class="meta-value">{{ $exportDate }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Total Analyses</div>
                    <div class="meta-value">{{ $totalAnalyses }}</div>
                </div>
                @if($searchTerm)
                <div class="meta-item">
                    <div class="meta-label">Search Term</div>
                    <div class="meta-value">"{{ $searchTerm }}"</div>
                </div>
                @endif
                @if($filter && $filter !== 'all')
                <div class="meta-item">
                    <div class="meta-label">Filter Applied</div>
                    <div class="meta-value">{{ ucfirst(str_replace('_', ' ', $filter)) }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Report Content -->
        <div class="report-content">
            <!-- Summary Section -->
            <div class="summary-section">
                <h2 class="section-title">Analysis Summary</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ $totalAnalyses }}</div>
                        <div class="stat-label">Total Analyses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $leavesCount }}</div>
                        <div class="stat-label">Leaves Analyzed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $watermelonCount }}</div>
                        <div class="stat-label">Fruits Analyzed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            @if($totalAnalyses > 0)
                                {{ number_format($analyses->avg('confidence_score'), 1) }}%
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="stat-label">Avg Confidence</div>
                    </div>
                </div>
            </div>
            
            <!-- Analyses Table -->
            <div class="analyses-section">
                <h2 class="section-title">Analysis Details</h2>
                
                @if($analyses->count() > 0)
                    <table class="analyses-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Identified</th>
                                <th>Condition</th>
                                <th>Confidence</th>
                                <th>Recommendations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyses as $analysis)
                                <tr>
                                    <td>{{ $analysis->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="analysis-type {{ $analysis->analysis_type }}">
                                            {{ ucfirst($analysis->analysis_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $analysis->identified_type }}</td>
                                    <td>{{ $analysis->identified_condition ?? 'N/A' }}</td>
                                    <td>
                                        <span class="confidence-score 
                                            @if($analysis->confidence_score >= 80) confidence-high
                                            @elseif($analysis->confidence_score >= 60) confidence-medium
                                            @else confidence-low
                                            @endif">
                                            {{ $analysis->confidence_score }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($analysis->recommendations)
                                            @if(is_array($analysis->recommendations))
                                                @php
                                                    $recommendationText = '';
                                                    if (isset($analysis->recommendations['recommendations']) && is_array($analysis->recommendations['recommendations'])) {
                                                        $recommendationText = implode(', ', $analysis->recommendations['recommendations']);
                                                    } elseif (isset($analysis->recommendations['overall'])) {
                                                        $recommendationText = $analysis->recommendations['overall'];
                                                    } else {
                                                        // Fallback: try to extract text from the array
                                                        $textParts = [];
                                                        foreach ($analysis->recommendations as $key => $value) {
                                                            if (is_string($value)) {
                                                                $textParts[] = $value;
                                                            } elseif (is_array($value) && isset($value['action'])) {
                                                                $textParts[] = $value['action'];
                                                            }
                                                        }
                                                        $recommendationText = implode(', ', $textParts);
                                                    }
                                                @endphp
                                                {{ Str::limit($recommendationText, 50) }}
                                            @else
                                                {{ Str::limit($analysis->recommendations, 50) }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">
                        <p>No analyses found for the selected criteria.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Report Footer -->
        <div class="report-footer">
            <p>Generated by MeloTech Photo Diagnosis System</p>
            <p>This report contains AI-powered analysis results for crop health assessment.</p>
        </div>
    </div>
</body>
</html>
