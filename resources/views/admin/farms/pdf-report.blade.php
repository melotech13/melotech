<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Farms Management Report</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            background-color: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #059669;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
            margin: 0 0 5px 0;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 0 0 15px 0;
        }
        
        .stats-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .stat-item {
            text-align: center;
            width: 33.33%;
            padding: 5px;
            vertical-align: top;
        }
        
        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }
        
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
        }
        
        th {
            background: #059669;
            color: white;
            font-weight: bold;
            padding: 8px 10px;
            text-align: left;
            border: none;
            font-size: 11px;
        }
        
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .badge {
            background: #e5f3ff;
            color: #1d4ed8;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-variety {
            background: #d1fae5;
            color: #059669;
        }
        
        .badge-size {
            background: #fef3c7;
            color: #d97706;
        }
        
        .no-data {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-style: italic;
        }
        
        .export-info {
            text-align: right;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        
        @media print {
            body { 
                margin: 10mm; 
                font-size: 10px;
            }
            .header {
                margin-bottom: 15px;
            }
            .report-title {
                font-size: 16px;
            }
            .table-title {
                font-size: 14px;
            }
            th, td {
                padding: 6px 8px;
                font-size: 10px;
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
        <div class="report-title">MeloTech Farm Management</div>
        <div class="table-title">Farms Report</div>
        @if(!empty($searchTerm))
            <div class="search-info" style="margin-top: 10px; font-size: 14px; color: #666;">
                <strong>Search Results for:</strong> "{{ $searchTerm }}"
            </div>
        @endif
    </div>

    <div class="stats-info">
        <table class="stats-table">
            <tr>
                <td class="stat-item">
                    <div class="stat-number">{{ $totalFarms }}</div>
                    <div class="stat-label">Total Farms</div>
                </td>
                <td class="stat-item">
                    <div class="stat-number">{{ $uniqueOwners }}</div>
                    <div class="stat-label">Unique Owners</div>
                </td>
                <td class="stat-item">
                    <div class="stat-number">{{ $activeFarms }}</div>
                    <div class="stat-label">Active Farms</div>
                </td>
            </tr>
        </table>
    </div>

    @if($farms->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Farm Name</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Variety</th>
                    <th>Field Size</th>
                    <th>Planting Date</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($farms as $farm)
                    <tr>
                        <td>{{ $farm->farm_name }}</td>
                        <td>{{ $farm->user->name }}</td>
                        <td>{{ $farm->city_municipality_name }}, {{ $farm->province_name }}</td>
                        <td>
                            @if($farm->watermelon_variety)
                                <span class="badge badge-variety">{{ $farm->watermelon_variety }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($farm->field_size)
                                <span class="badge badge-size">{{ $farm->field_size }} {{ $farm->field_size_unit }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($farm->planting_date)
                                {{ $farm->planting_date->format('M d, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $farm->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No farms found.</p>
        </div>
    @endif

    <div class="export-info">
        <p><strong>Export Date:</strong> {{ $exportDate }}</p>
    </div>
</body>
</html>
