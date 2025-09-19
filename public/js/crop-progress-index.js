(function () {
  'use strict';

  function getRoot() {
    return document.getElementById('crop-progress-root');
  }

  function getNextUpdateDate() {
    const root = getRoot();
    const ts = root ? root.dataset.nextUpdateDate : '';
    return ts ? new Date(ts) : null;
  }

  function getExportPdfUrl() {
    const root = getRoot();
    return root ? root.dataset.exportPdfUrl : '';
  }

  function setProgressWidths() {
    document.querySelectorAll('.ph-progress-bar[data-progress]').forEach((el) => {
      const progress = parseFloat(el.dataset.progress || '0');
      const clamped = isFinite(progress) ? Math.max(0, Math.min(100, progress)) : 0;
      el.style.setProperty('--progress-width', clamped + '%');
      el.style.width = clamped + '%';
    });
  }

  // Countdown
  function initializeCountdown() {
    const countdownElement = document.getElementById('countdown-timer');
    const nextUpdateDate = getNextUpdateDate();

    if (!countdownElement || !nextUpdateDate || isNaN(nextUpdateDate.getTime())) {
      if (countdownElement) countdownElement.textContent = 'Calculating...';
      return;
    }

    function updateCountdown() {
      const now = new Date();
      const timeLeft = nextUpdateDate - now;

      if (timeLeft <= 0) {
        location.reload();
        return;
      }

      const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
      const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));

      const daysElement = document.getElementById('countdown-days');
      const hoursElement = document.getElementById('countdown-hours');
      const minutesElement = document.getElementById('countdown-minutes');

      if (daysElement) daysElement.textContent = days;
      if (hoursElement) hoursElement.textContent = hours;
      if (minutesElement) minutesElement.textContent = minutes;

      if (countdownElement) {
        if (days > 0) countdownElement.textContent = `${days}d ${hours}h ${minutes}m`;
        else if (hours > 0) countdownElement.textContent = `${hours}h ${minutes}m`;
        else countdownElement.textContent = `${minutes}m`;
      }
    }

    updateCountdown();
    setInterval(updateCountdown, 60000);
  }

  // Notifications
  function toast(type, html) {
    const div = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    div.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    div.innerHTML = `${html}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(div);
    setTimeout(() => div.parentNode && div.remove(), 5000);
  }

  function showExportSuccess() { toast('success', '<i class="fas fa-check-circle me-2"></i>PDF generated successfully! Print dialog opened.'); }
  function showExportError(message) { toast('danger', `<i class="fas fa-exclamation-triangle me-2"></i>${message}`); }
  function showExportInfo(message) { toast('info', `<i class="fas fa-info-circle me-2"></i>${message}`); }

  // Summary + Recommendations modal – rely on Bootstrap defaults (no manual z-index/positioning)

  function generateRecommendationsHTML(recommendations, summary) {
    let html = `
      <div class="recommendations-container">
        <div class="recommendations-header">
          <h5>🤖 AI-Powered Recommendations</h5>
          <p>${summary}</p>
        </div>
    `;

    if (recommendations.priority_alerts && recommendations.priority_alerts.length > 0) {
      html += `
        <div class="recommendation-category alert">
          <h6 class="category-title text-danger"><i class="fas fa-exclamation-triangle"></i>Priority Alerts</h6>
          <ul class="recommendation-list">
            ${recommendations.priority_alerts.map(item => `<li class="recommendation-item ${item.includes('✅') ? 'text-success' : 'text-danger'}">${item}</li>`).join('')}
          </ul>
        </div>`;
    }

    if (recommendations.immediate_actions && recommendations.immediate_actions.length > 0) {
      html += `
        <div class="recommendation-category">
          <h6 class="category-title text-primary"><i class="fas fa-bolt"></i>Immediate Actions</h6>
          <ul class="recommendation-list">
            ${recommendations.immediate_actions.map(item => `<li class="recommendation-item ${item.includes('✅') ? 'text-success' : ''}">${item}</li>`).join('')}
          </ul>
        </div>`;
    }

    if (recommendations.weekly_plan && recommendations.weekly_plan.length > 0) {
      html += `
        <div class="recommendation-category weekly">
          <h6 class="category-title text-info"><i class="fas fa-calendar-week"></i>Weekly Plan</h6>
          <ul class="recommendation-list">
            ${recommendations.weekly_plan.map(item => `<li class="recommendation-item">${item}</li>`).join('')}
          </ul>
        </div>`;
    }

    if (recommendations.long_term_tips && recommendations.long_term_tips.length > 0) {
      html += `
        <div class="recommendation-category tips">
          <h6 class="category-title text-warning"><i class="fas fa-lightbulb"></i>Long-term Tips</h6>
          <ul class="recommendation-list">
            ${recommendations.long_term_tips.map(item => `<li class="recommendation-item">${item}</li>`).join('')}
          </ul>
        </div>`;
    }

    html += '</div>';
    return html;
  }

  function generateFullSummaryHTML(summary) {
    return `
      <div class="summary-container">
        <div class="summary-header text-center mb-4">
          <h5 class="text-primary mb-2">📊 Progress Summary</h5>
          <p class='text-muted mb-0'>Updated on ${summary.update_date} | Week: ${summary.week_name || 'N/A'}</p>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="summary-stats mb-4">
              <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded mb-3">
                <div class="stat-icon mb-2"><i class="fas fa-chart-line fa-2x text-primary"></i></div>
                <div class="stat-value text-primary fw-bold fs-4">${summary.progress}%</div>
                <div class="stat-label text-muted">Progress</div>
              </div>
              <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                <div class="stat-icon mb-2"><i class="fas fa-clipboard-check fa-2x text-success"></i></div>
                <div class="stat-value text-success fw-bold fs-4">${summary.method}</div>
                <div class="stat-label text-muted">Method</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="summary-status">
              <h6 class="text-secondary mb-3">📊 Key Insights:</h6>
              <div class="status-grid">
                <div class="status-item d-flex align-items-center mb-2"><i class="fas fa-chart-bar text-success me-3"></i><span>Based on ${summary.questions ? summary.questions.length : 0} detailed responses</span></div>
                <div class="status-item d-flex align-items-center mb-2"><i class="fas fa-calculator text-info me-3"></i><span>Progress calculated using weighted scoring</span></div>
                <div class="status-item d-flex align-items-center mb-2"><i class="fas fa-calendar text-warning me-3"></i><span>Assessment completed on ${summary.update_date}</span></div>
              </div>
            </div>
          </div>
        </div>
        <div class="summary-note mt-4 p-3 bg-light rounded text-center">
          <i class="fas fa-info-circle text-info me-2"></i>
          <span class="text-muted">Progress calculated based on your detailed responses to crop assessment questions.</span>
        </div>
      </div>`;
  }

  function generateAnswerSummaryHTML(summary) {
    return `
      <div class="summary-container">
        <div class="summary-header mb-4">
          <h6 class="text-primary mb-2">Answer Summary</h6>
          <p class="text-muted mb-0">Date: ${summary.update_date} | Week: ${summary.week_name || 'N/A'}</p>
        </div>
        <div class="row">
          ${summary.questions.map((q) => `
            <div class="col-md-6 mb-3">
              <div class="answer-item p-3 border rounded h-100">
                <div class="question-answer">
                  <strong class="question-label d-block mb-2">${q.question}</strong>
                  <div class="answer-section mb-2"><span class="answer-value badge bg-primary fs-6">${q.answer}</span></div>
                  <div class="explanation-section"><small class="text-muted">${q.explanation}</small></div>
                </div>
              </div>
            </div>`).join('')}
        </div>
        <div class="summary-footer mt-4 p-3 bg-light rounded text-center">
          <div class="row">
            <div class="col-md-6"><strong>Total Progress:</strong> ${summary.progress}%</div>
            <div class="col-md-6"><strong>Update Method:</strong> ${summary.method || 'Questions'}</div>
          </div>
        </div>
      </div>`;
  }

  // Exports and printing
  function exportAsPrint() {
    const tableContainer = document.querySelector('.ph-table-container');
    if (!tableContainer) return;
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;
    const root = getRoot();
    const farmName = root ? (root.dataset.farmName || 'N/A') : 'N/A';

    printWindow.document.write(`
      <html><head><title>Progress History - Print</title>
      <style>body{font-family:Arial,sans-serif;margin:20px}table{width:100%;border-collapse:collapse;margin-bottom:20px}th,td{padding:8px;text-align:left;border-bottom:1px solid #ddd}th{background-color:#f8fafc;font-weight:bold}.badge{background:#e5f3ff;color:#1d4ed8;padding:4px 8px;border-radius:4px;font-size:12px}.progress{width:100px;height:20px;background:#f3f4f6;border-radius:4px;overflow:hidden}.progress-bar{height:100%;background:#22c55e}</style>
      </head><body>
      <h1>Progress History Report</h1>
      <div class="export-info"><p><strong>Farm:</strong> ${farmName}</p><p><strong>Export Date:</strong> ${new Date().toLocaleDateString()}</p></div>
      ${tableContainer.querySelector('table') ? tableContainer.querySelector('table').outerHTML : ''}
      </body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
  }

  function exportAsExcel() {
    const table = document.querySelector('.ph-table-container table');
    if (!table) { alert('No table data to export'); return; }
    let csvContent = 'data:text/csv;charset=utf-8,';
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => headers.push(th.textContent.trim()));
    csvContent += headers.join(',') + '\r\n';
    table.querySelectorAll('tbody tr').forEach(row => {
      const rowData = [];
      row.querySelectorAll('td').forEach((td, index) => {
        let cellText = '';
        if (index === 0 || index === 2) {
          const badge = td.querySelector('.ph-badge');
          cellText = badge ? badge.textContent : '';
        } else if (index === 3) {
          const label = td.querySelector('.ph-progress-label');
          cellText = label ? label.textContent : '';
        } else {
          cellText = td.textContent.trim();
        }
        rowData.push('"' + cellText.replace(/"/g, '""') + '"');
      });
      csvContent += rowData.join(',') + '\r\n';
    });
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.href = encodedUri;
    link.download = 'progress_history.csv';
    document.body.appendChild(link); link.click(); document.body.removeChild(link);
  }

  function exportAsPDF() {
    const exportBtn = event && event.target ? event.target.closest('.dropdown-item') : null;
    const originalText = exportBtn ? exportBtn.innerHTML : null;
    if (exportBtn) exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    try {
      const pdfUrl = getExportPdfUrl();
      const link = document.createElement('a');
      link.href = pdfUrl;
      link.download = `progress_history_${new Date().toISOString().split('T')[0]}.pdf`;
      link.style.display = 'none';
      document.body.appendChild(link); link.click(); document.body.removeChild(link);
      setTimeout(() => { if (exportBtn && originalText) exportBtn.innerHTML = originalText; }, 2000);
      console.log('PDF download initiated via server-side generation');
    } catch (error) {
      console.error('PDF export failed:', error);
      if (exportBtn && originalText) exportBtn.innerHTML = originalText;
      alert('PDF export failed. Please try again or use the Print option instead.');
    }
  }

  function exportToPDF(updateId) {
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    if (tableContainer) {
      tableContainer.dataset.originalWidth = tableContainer.style.width || '100%';
      tableContainer.dataset.originalMinWidth = tableContainer.style.minWidth || '';
      tableContainer.style.width = '100%';
      tableContainer.style.minWidth = '100%';
      tableContainer.style.flex = '1';
    }
    if (tableCard) { tableCard.style.minHeight = '400px'; }

    const exportBtn = event && event.target ? event.target : null;
    const originalText = exportBtn ? exportBtn.innerHTML : '';
    const originalWidth = exportBtn ? exportBtn.offsetWidth : 0;
    const originalHeight = exportBtn ? exportBtn.offsetHeight : 0;
    if (exportBtn) {
      exportBtn.style.minWidth = originalWidth + 'px';
      exportBtn.style.minHeight = originalHeight + 'px';
      exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    }

    try {
      const printWindow = window.open(`/crop-progress/export/${updateId}`, '_blank');
      if (printWindow) {
        printWindow.onload = function () {
          setTimeout(() => {
            printWindow.print();
            showExportSuccess();
            setTimeout(() => {
              if (exportBtn) {
                exportBtn.innerHTML = originalText;
                exportBtn.style.minWidth = '';
                exportBtn.style.minHeight = '';
              }
              restoreTableDimensions();
            }, 2000);
          }, 1000);
        };
        printWindow.onerror = function () {
          if (exportBtn) { exportBtn.innerHTML = originalText; exportBtn.style.minWidth = ''; exportBtn.style.minHeight = ''; }
          showExportError('Failed to load the report. Please try again or use the Print Report option.');
        };
      } else {
        if (exportBtn) { exportBtn.innerHTML = originalText; exportBtn.style.minWidth = ''; exportBtn.style.minHeight = ''; }
        showExportInfo('Popup blocked. Opening export in new tab instead.');
        const form = document.getElementById(`export-form-${updateId}`);
        if (form) form.submit(); else window.location.href = `/crop-progress/export/${updateId}`;
      }
    } catch (error) {
      console.error('Export error:', error);
      if (exportBtn) { exportBtn.innerHTML = originalText; exportBtn.style.minWidth = ''; exportBtn.style.minHeight = ''; }
      showExportError('An error occurred while exporting. Please try again.');
    }
  }

  function restoreTableDimensions() {
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    if (tableContainer && tableContainer.dataset.originalWidth) {
      tableContainer.style.width = tableContainer.dataset.originalWidth;
      tableContainer.style.minWidth = tableContainer.dataset.originalMinWidth;
      tableContainer.style.flex = '';
    }
    if (tableCard) { tableCard.style.minHeight = ''; }
  }

  function showQuestionsSummary(updateId, type) {
    const modalEl = document.getElementById('questionsSummaryModal');
    if (modalEl && modalEl.parentElement !== document.body) {
      document.body.appendChild(modalEl);
    }
    const modal = new bootstrap.Modal(modalEl, { backdrop: true, focus: true });
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    modalTitle.textContent = '📊 Progress Summary';
    modal.show();
    modalContent.innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
        <p class="mt-2">Loading progress summary...</p>
      </div>`;
    fetch(`/crop-progress/${updateId}/summary`)
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          modalContent.innerHTML = (type === 'full') ? generateFullSummaryHTML(data.summary) : generateAnswerSummaryHTML(data.summary);
        } else {
          modalContent.innerHTML = `
            <div class="text-center text-danger">
              <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
              <p>Unable to load progress summary. Please try again.</p>
              <button class="btn btn-primary" onclick="showQuestionsSummary('${updateId}', '${type || ''}')"><i class="fas fa-refresh me-2"></i>Retry</button>
            </div>`;
        }
      })
      .catch(() => {
        modalContent.innerHTML = `
          <div class="text-center text-danger">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <p>Error loading progress summary. Please try again.</p>
            <button class="btn btn-primary" onclick="showQuestionsSummary('${updateId}', '${type || ''}')"><i class="fas fa-refresh me-2"></i>Retry</button>
          </div>`;
      });
  }

  function showRecommendations(updateId) {
    const modalEl = document.getElementById('questionsSummaryModal');
    if (modalEl && modalEl.parentElement !== document.body) {
      document.body.appendChild(modalEl);
    }
    const modal = new bootstrap.Modal(modalEl, { backdrop: true, focus: true });
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    modalTitle.innerHTML = '<i class="fas fa-robot"></i> AI-Powered Recommendations';
    modal.show();
    modalContent.innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
        <p class="mt-2">Loading AI recommendations...</p>
      </div>`;
    fetch(`/crop-progress/${updateId}/recommendations`)
      .then(r => r.json())
      .then(data => {
        modalContent.innerHTML = data && data.success
          ? generateRecommendationsHTML(data.recommendations, data.recommendation_summary)
          : `<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p>Unable to load recommendations. Please try again.</p></div>`;
      })
      .catch(() => {
        modalContent.innerHTML = `<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p>Error loading recommendations. Please try again.</p></div>`;
      });
  }

  function printSummary() {
    const modalContent = document.getElementById('modalContent');
    const win = window.open('', '_blank');
    if (!win) return;
    win.document.write(`
      <html><head><title>Questions Summary</title>
      <style>body{font-family:Arial,sans-serif;margin:20px}.question-item{margin-bottom:20px;padding:15px;border:1px solid #ddd;border-radius:5px}.question-text{color:#2563eb;margin-bottom:10px}.answer-text{font-weight:bold}.explanation-text{color:#6b7280;font-style:italic}.summary-footer{background:#f9fafb;padding:15px;border-radius:5px;margin-top:20px}.badge{background:#2563eb;color:#fff;padding:5px 10px;border-radius:3px}</style>
      </head><body>
      <h2>Questions Summary</h2>${modalContent ? modalContent.innerHTML : ''}
      </body></html>`);
    win.document.close();
    win.focus();
    win.print();
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('countdown-timer')) initializeCountdown();
    setProgressWidths();
  });

  // Expose globals for inline handlers
  window.initializeCountdown = initializeCountdown;
  window.exportToPDF = exportToPDF;
  window.restoreTableDimensions = restoreTableDimensions;
  window.showExportSuccess = showExportSuccess;
  window.showExportError = showExportError;
  window.showExportInfo = showExportInfo;
  window.showQuestionsSummary = showQuestionsSummary;
  window.showRecommendations = showRecommendations;
  window.generateRecommendationsHTML = generateRecommendationsHTML;
  window.generateFullSummaryHTML = generateFullSummaryHTML;
  window.generateAnswerSummaryHTML = generateAnswerSummaryHTML;
  window.printSummary = printSummary;
  window.exportAsExcel = exportAsExcel;
  window.exportAsPDF = exportAsPDF;
  window.exportAsPrint = exportAsPrint;
})();
