<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crop Progress Report - {{ $farm->farm_name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 30px;
            color: #2d3748;
            font-size: 13px;
            line-height: 1.6;
            background-color: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 35px;
            padding: 25px 20px;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .logo-section {
            margin-bottom: 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #059669;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .logo-subtitle {
            font-size: 12px;
            color: #718096;
            margin: 5px 0 0 0;
            font-weight: 500;
        }
        
        .farm-title {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            margin: 15px 0 8px 0;
            letter-spacing: -0.3px;
        }
        
        .farm-subtitle {
            font-size: 14px;
            color: #4a5568;
            margin: 0 0 15px 0;
            font-weight: 500;
        }
        
        .report-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 11px;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 13px;
            color: #2d3748;
            font-weight: 600;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 20px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #059669;
            display: inline-block;
        }
        
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e2e8f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background: #fff;
        }
        
        th {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            font-weight: 700;
            padding: 16px 12px;
            text-align: left;
            border: none;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        th:first-child {
            border-top-left-radius: 12px;
        }
        
        th:last-child {
            border-top-right-radius: 12px;
        }
        
        th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        td {
            padding: 16px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            vertical-align: middle;
            transition: background-color 0.2s ease;
        }
        
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        tr:hover {
            background-color: #f1f5f9;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid transparent;
        }
        
        .badge-week {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-color: #93c5fd;
        }
        
        .badge-method {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-color: #fbbf24;
        }
        
        .progress-cell {
            text-align: center;
            font-weight: 700;
            font-size: 14px;
        }
        
        .progress-value {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-radius: 20px;
            border: 1px solid #6ee7b7;
            min-width: 60px;
        }
        
        .date-cell {
            font-weight: 500;
            color: #4a5568;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
            background: #f8fafc;
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .no-data-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .no-data-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
        }
        
        .no-data-text {
            font-size: 14px;
            color: #718096;
        }
        
        .footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 11px;
        }
        
        @media print {
            body { 
                margin: 15mm; 
                font-size: 11px;
                padding: 0;
            }
            
            .header {
                margin-bottom: 25px;
                padding: 20px 15px;
                page-break-inside: avoid;
            }
            
            .logo {
                font-size: 20px;
            }
            
            .farm-title {
                font-size: 18px;
            }
            
            .table-title {
                font-size: 16px;
            }
            
            .table-container {
                page-break-inside: avoid;
            }
            
            th, td {
                padding: 12px 10px;
                font-size: 10px;
            }
            
            .badge {
                padding: 4px 8px;
                font-size: 9px;
            }
            
            .progress-value {
                padding: 6px 12px;
                font-size: 11px;
            }
            
            .report-info {
                gap: 20px;
            }
        }
    </style>
    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo">MeloTech</div>
            <div class="logo-subtitle">Agricultural Intelligence Platform</div>
        </div>
        
        <div class="farm-title">{{ $farm->farm_name }}</div>
        <div class="farm-subtitle">Crop Progress Report</div>
        
        <div class="report-info">
            <div class="info-item">
                <div class="info-label">Report Date</div>
                <div class="info-value">{{ now()->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Updates</div>
                <div class="info-value">{{ $progressUpdates->count() }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Farm Location</div>
                <div class="info-value">{{ $farm->barangay_name }}, {{ $farm->city_municipality_name }}, {{ $farm->province_name }}</div>
            </div>
        </div>
    </div>

    <div class="table-title">Progress History</div>

    @if($progressUpdates->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Update Date</th>
                        <th>Method</th>
                        <th>Progress</th>
                        <th>Next Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($progressUpdates as $update)
                        <tr>
                            <td><span class="badge badge-week">{{ $update->getWeekName() }}</span></td>
                            <td class="date-cell">{{ $update->update_date->format('M d, Y') }}</td>
                            <td><span class="badge badge-method">{{ ucfirst($update->update_method) }}</span></td>
                            <td class="progress-cell">
                                <span class="progress-value">{{ $update->calculated_progress }}%</span>
                            </td>
                            <td class="date-cell">{{ $update->next_update_date->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <div class="no-data-icon">📊</div>
            <div class="no-data-title">No Progress Updates</div>
            <div class="no-data-text">No progress updates are available for this farm at the moment.</div>
        </div>
    @endif

    <div class="footer">
        <p>Generated by MeloTech Agricultural Intelligence Platform • {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
