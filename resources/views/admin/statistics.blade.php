@extends('layouts.admin')

@section('title', 'Statistics - MeloTech Admin')

@section('page-title', 'Statistics & Insights')

@section('content')
<div class="welcome-section mb-4">
	<div class="welcome-card">
		<div class="welcome-content">
			<h2 class="welcome-title">System Overview</h2>
			<p class="welcome-subtitle">Key metrics and visual insights at a glance</p>
		</div>
		<div class="welcome-time">
			<div class="current-time">{{ now()->format('l, F j, Y') }}</div>
			<div class="current-time-small">{{ now()->format('g:i A') }}</div>
		</div>
	</div>
</div>

<div class="metrics-section mb-4">
	<div class="metrics-grid">
		<div class="metric-card">
			<div class="metric-icon users">
				<i class="fas fa-users"></i>
			</div>
			<div class="metric-content">
				<div class="metric-number">{{ $stats['users']['total'] }}</div>
				<div class="metric-label">Total Users</div>
				<div class="metric-trend positive">
					<i class="fas fa-user-shield"></i>
					<span>{{ $stats['users']['admins'] }} admins</span>
				</div>
			</div>
		</div>

		<div class="metric-card">
			<div class="metric-icon farms">
				<i class="fas fa-seedling"></i>
			</div>
			<div class="metric-content">
				<div class="metric-number">{{ $stats['farms']['total'] }}</div>
				<div class="metric-label">Registered Farms</div>
				<div class="metric-trend positive">
					<i class="fas fa-check-circle"></i>
					<span>{{ $stats['farms']['active'] }} active</span>
				</div>
			</div>
		</div>

		<div class="metric-card">
			<div class="metric-icon analyses">
				<i class="fas fa-camera"></i>
			</div>
			<div class="metric-content">
				<div class="metric-number">{{ $stats['analyses']['total'] }}</div>
				<div class="metric-label">Photo Analyses</div>
				<div class="metric-trend positive">
					<i class="fas fa-calendar-plus"></i>
					<span>{{ $stats['analyses']['this_month'] }} this month</span>
				</div>
			</div>
		</div>

		<div class="metric-card">
			<div class="metric-icon updates">
				<i class="fas fa-chart-line"></i>
			</div>
			<div class="metric-content">
				<div class="metric-number">{{ $stats['updates']['total'] }}</div>
				<div class="metric-label">Progress Updates</div>
				<div class="metric-trend positive">
					<i class="fas fa-calendar-plus"></i>
					<span>{{ $stats['updates']['this_month'] }} this month</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="main-content-grid">
	<div class="content-section">
		<div class="section-header">
			<h3 class="section-title">
				<i class="fas fa-chart-pie"></i>
				User & Farm Distribution
			</h3>
			<p class="section-subtitle">Composition of users and farm activity</p>
		</div>
		<div class="row g-4">
			<div class="col-12 col-lg-6">
				<div class="card shadow-sm" style="border-radius: 12px; min-height: 400px;">
					<div class="card-body" style="padding: 2rem;">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="mb-0">Users by Role</h5>
							<i class="fas fa-users text-muted"></i>
						</div>
						<canvas id="usersRoleChart" height="305"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6">
				<div class="card shadow-sm" style="border-radius: 12px; min-height: 400px;">
					<div class="card-body" style="padding: 2rem;">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="mb-0">Farm Activity</h5>
							<i class="fas fa-seedling text-muted"></i>
						</div>
						<canvas id="farmActivityChart" height="305"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="content-section">
		<div class="section-header">
			<h3 class="section-title">
				<i class="fas fa-chart-bar"></i>
				Operational Activity
			</h3>
			<p class="section-subtitle">System activity and user engagement metrics</p>
		</div>
		<div class="row g-4">
			<div class="col-12 col-lg-6">
				<div class="card shadow-sm" style="border-radius: 12px; min-height: 400px;">
					<div class="card-body" style="padding: 2rem;">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="mb-0">Activity Distribution</h5>
							<i class="fas fa-chart-bar text-muted"></i>
						</div>
						<canvas id="activityDistributionChart" height="305"></canvas>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6">
				<div class="card shadow-sm" style="border-radius: 12px; min-height: 400px;">
					<div class="card-body" style="padding: 2rem;">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="mb-0">Monthly Activity</h5>
							<i class="fas fa-chart-bar text-muted"></i>
						</div>
						<canvas id="monthlyActivityChart" height="305"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

<script id="stats-data" type="application/json">{!! json_encode($stats) !!}</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
	(function() {
		const statsEl = document.getElementById('stats-data');
		const stats = statsEl ? JSON.parse(statsEl.textContent || '{}') : {};

		// Colors aligned with admin theme
		const colors = {
			primary: '#3b82f6',
			secondary: '#8b5cf6',
			success: '#10b981',
			warning: '#f59e0b',
			danger: '#ef4444',
			muted: '#9ca3af'
		};

		// Users by role (Doughnut)
		const usersCtx = document.getElementById('usersRoleChart');
		if (usersCtx) {
			new Chart(usersCtx, {
				type: 'doughnut',
				data: {
					labels: ['Admins', 'Regular Users'],
					datasets: [{
						data: [stats.users.admins, stats.users.regular_users],
						backgroundColor: [colors.secondary, colors.primary],
						borderWidth: 0
					}]
				},
				options: {
					plugins: {
						legend: { position: 'bottom' }
					},
					cutout: '65%'
				}
			});
		}

		// Farm activity (Doughnut)
		const farmsCtx = document.getElementById('farmActivityChart');
		if (farmsCtx) {
			const inactive = Math.max(stats.farms.total - stats.farms.active, 0);
			new Chart(farmsCtx, {
				type: 'doughnut',
				data: {
					labels: ['Active', 'Inactive'],
					datasets: [{
						data: [stats.farms.active, inactive],
						backgroundColor: [colors.success, colors.muted],
						borderWidth: 0
					}]
				},
				options: {
					plugins: {
						legend: { position: 'bottom' }
					},
					cutout: '65%'
				}
			});
		}

		// Activity Distribution (Bar)
		const activityCtx = document.getElementById('activityDistributionChart');
		if (activityCtx) {
			new Chart(activityCtx, {
				type: 'bar',
				data: {
					labels: ['Photo Analyses', 'Progress Updates'],
					datasets: [{
						label: 'Total Activity',
						data: [stats.analyses.total, stats.updates.total],
						backgroundColor: [colors.warning, colors.success],
						borderRadius: 8,
						maxBarThickness: 48
					}]
				},
				options: {
					plugins: { legend: { display: false } },
					scales: {
						y: { beginAtZero: true, ticks: { precision: 0 } }
					}
				}
			});
		}

		// Monthly Activity (Bar)
		const monthlyCtx = document.getElementById('monthlyActivityChart');
		if (monthlyCtx) {
			new Chart(monthlyCtx, {
				type: 'bar',
				data: {
					labels: ['This Month Analyses', 'This Month Updates'],
					datasets: [{
						label: 'Monthly Activity',
						data: [stats.analyses.this_month, stats.updates.this_month],
						backgroundColor: [colors.danger, colors.secondary],
						borderRadius: 8,
						maxBarThickness: 48
					}]
				},
				options: {
					plugins: { legend: { display: false } },
					scales: {
						y: { beginAtZero: true, ticks: { precision: 0 } }
					}
				}
			});
		}

		// Animate number counters in KPI cards
		const metricNumbers = document.querySelectorAll('.metric-number');
		metricNumbers.forEach(el => {
			const target = parseInt(el.textContent || '0', 10) || 0;
			const duration = 1200;
			const increment = Math.max(1, Math.floor(target / (duration / 16)));
			let current = 0;
			const timer = setInterval(() => {
				current += increment;
				if (current >= target) {
					current = target;
					clearInterval(timer);
				}
				el.textContent = current.toString();
			}, 16);
		});
	})();
</script>
@endpush


