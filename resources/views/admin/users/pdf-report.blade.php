<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users Management Report</title>
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
        
        .badge-admin {
            background: #fef3c7;
            color: #d97706;
        }
        
        .badge-user {
            background: #d1fae5;
            color: #059669;
        }
        
        .role-cell {
            text-align: center;
            font-weight: bold;
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
        <div class="report-title">MeloTech User Management</div>
        <div class="table-title">Users Report</div>
    </div>

    <div class="stats-info">
        <table class="stats-table">
            <tr>
                <td class="stat-item">
                    <div class="stat-number">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Users</div>
                </td>
                <td class="stat-item">
                    <div class="stat-number">{{ $adminCount }}</div>
                    <div class="stat-label">Admins</div>
                </td>
                <td class="stat-item">
                    <div class="stat-number">{{ $userCount }}</div>
                    <div class="stat-label">Regular Users</div>
                </td>
            </tr>
        </table>
    </div>

    @if($users->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Farm Name</th>
                    <th>Location</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td class="role-cell">
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->farms->count() > 0)
                                {{ $user->farms->first()->farm_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($user->farms->count() > 0)
                                {{ $user->farms->first()->city_municipality_name }}, {{ $user->farms->first()->province_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No users found.</p>
        </div>
    @endif

    <div class="export-info">
        <p><strong>Export Date:</strong> {{ $exportDate }}</p>
    </div>
</body>
</html>
