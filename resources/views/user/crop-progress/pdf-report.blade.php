<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progress Report</title>
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
        
        .farm-title {
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
        
        .progress-cell {
            text-align: center;
            font-weight: bold;
            color: #059669;
        }
        
        .no-data {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-style: italic;
        }
        
        @media print {
            body { 
                margin: 10mm; 
                font-size: 10px;
            }
            .header {
                margin-bottom: 15px;
            }
            .farm-title {
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
        <div class="farm-title">{{ $farm->farm_name }}</div>
        <div class="table-title">Progress History</div>
    </div>

    @if($progressUpdates->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Progress</th>
                    <th>Next Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($progressUpdates as $update)
                    <tr>
                        <td><span class="badge">{{ $update->getWeekName() }}</span></td>
                        <td>{{ $update->update_date->format('M d, Y') }}</td>
                        <td><span class="badge">{{ ucfirst($update->update_method) }}</span></td>
                        <td class="progress-cell">{{ $update->calculated_progress }}%</td>
                        <td>{{ $update->next_update_date->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No progress updates available.</p>
        </div>
    @endif
</body>
</html>
