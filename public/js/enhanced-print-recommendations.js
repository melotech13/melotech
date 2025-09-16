// Enhanced Print Recommendations Function
function printRecommendations() {
    const modalContent = document.getElementById('modalContent');
    const printWindow = window.open('', '_blank');
    
    // Extract recommendations data from the modal content
    const recommendationsContainer = modalContent.querySelector('.recommendations-container');
    if (!recommendationsContainer) {
        printWindow.document.write(`
            <html>
                <head>
                    <title>AI Recommendations - MeloTech</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
                        .error { color: #dc3545; }
                    </style>
                </head>
                <body>
                    <div class="error">No recommendations data found to print.</div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        return;
    }
    
    // Extract summary
    const summaryElement = recommendationsContainer.querySelector('.recommendations-header p');
    const summary = summaryElement ? summaryElement.textContent : 'AI-Powered Agricultural Recommendations';
    
    // Extract categories
    const categories = [];
    const categoryElements = recommendationsContainer.querySelectorAll('.recommendation-category');
    
    categoryElements.forEach(category => {
        const titleElement = category.querySelector('.category-title');
        const listElement = category.querySelector('.recommendation-list');
        
        if (titleElement && listElement) {
            const title = titleElement.textContent.trim();
            const icon = titleElement.querySelector('i');
            const iconClass = icon ? icon.className : '';
            
            const items = Array.from(listElement.querySelectorAll('.recommendation-item')).map(item => ({
                text: item.textContent.trim(),
                isSuccess: item.classList.contains('text-success'),
                isDanger: item.classList.contains('text-danger')
            }));
            
            categories.push({
                title,
                iconClass,
                items,
                isAlert: category.classList.contains('alert')
            });
        }
    });
    
    // Function to determine category styling
    function getCategoryClass(title) {
        if (title.includes('Priority Alerts')) return 'priority';
        if (title.includes('Immediate Actions')) return 'immediate';
        if (title.includes('Weekly Plan')) return 'weekly';
        if (title.includes('Long-term Tips')) return 'tips';
        return 'default';
    }
    
    // Function to get category icon
    function getCategoryIcon(iconClass) {
        if (iconClass.includes('exclamation-triangle')) return '⚠️';
        if (iconClass.includes('bolt')) return '⚡';
        if (iconClass.includes('calendar-week')) return '📅';
        if (iconClass.includes('lightbulb')) return '💡';
        return '📋';
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>AI Recommendations - MeloTech</title>
                <style>
                    * {
                        box-sizing: border-box;
                    }
                    
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        margin: 0;
                        padding: 15px;
                        color: #2d3748;
                        font-size: 11px;
                        line-height: 1.4;
                        background-color: #ffffff;
                    }
                    
                    .header {
                        text-align: center;
                        margin-bottom: 15px;
                        padding: 15px 10px;
                        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
                        border-radius: 8px;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                    }
                    
                    .logo {
                        font-size: 18px;
                        font-weight: 800;
                        color: #059669;
                        margin: 0 0 3px 0;
                        letter-spacing: -0.5px;
                    }
                    
                    .logo-subtitle {
                        font-size: 9px;
                        color: #718096;
                        margin: 0 0 8px 0;
                        font-weight: 500;
                    }
                    
                    .report-title {
                        font-size: 16px;
                        font-weight: 700;
                        color: #2d3748;
                        margin: 8px 0 4px 0;
                        letter-spacing: -0.3px;
                    }
                    
                    .report-subtitle {
                        font-size: 11px;
                        color: #4a5568;
                        margin: 0 0 8px 0;
                        font-weight: 500;
                    }
                    
                    .report-info {
                        display: flex;
                        justify-content: center;
                        gap: 20px;
                        margin-top: 8px;
                        flex-wrap: wrap;
                    }
                    
                    .info-item {
                        text-align: center;
                    }
                    
                    .info-label {
                        font-size: 8px;
                        color: #718096;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        margin-bottom: 2px;
                    }
                    
                    .info-value {
                        font-size: 10px;
                        color: #2d3748;
                        font-weight: 600;
                    }
                    
                    .recommendations-container {
                        background: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
                        border: 1px solid #e2e8f0;
                    }
                    
                    .recommendations-header {
                        background: linear-gradient(135deg, #059669 0%, #047857 100%);
                        color: white;
                        padding: 10px;
                        text-align: center;
                    }
                    
                    .recommendations-title {
                        font-size: 14px;
                        font-weight: 700;
                        margin: 0 0 4px 0;
                        letter-spacing: -0.3px;
                    }
                    
                    .recommendations-summary {
                        font-size: 10px;
                        opacity: 0.9;
                        margin: 0;
                    }
                    
                    .recommendations-content {
                        padding: 12px;
                        background: #f8fafc;
                    }
                    
                    .recommendation-category {
                        margin-bottom: 12px;
                        background: #ffffff;
                        border-radius: 6px;
                        padding: 8px;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                    }
                    
                    .recommendation-category.priority {
                        border-left: 4px solid #dc3545;
                        background: #fef2f2;
                    }
                    
                    .recommendation-category.immediate {
                        border-left: 4px solid #0d6efd;
                        background: #f0f8ff;
                    }
                    
                    .recommendation-category.weekly {
                        border-left: 4px solid #0dcaf0;
                        background: #f0fdfa;
                    }
                    
                    .recommendation-category.tips {
                        border-left: 4px solid #ffc107;
                        background: #fffbeb;
                    }
                    
                    .category-title {
                        font-size: 11px;
                        font-weight: 700;
                        margin: 0 0 6px 0;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                    }
                    
                    .category-title.priority {
                        color: #dc3545;
                    }
                    
                    .category-title.immediate {
                        color: #0d6efd;
                    }
                    
                    .category-title.weekly {
                        color: #0dcaf0;
                    }
                    
                    .category-title.tips {
                        color: #ffc107;
                    }
                    
                    .recommendation-list {
                        margin: 0;
                        padding-left: 16px;
                    }
                    
                    .recommendation-item {
                        font-size: 9px;
                        margin-bottom: 4px;
                        line-height: 1.3;
                        padding: 2px 0;
                    }
                    
                    .recommendation-item.success {
                        color: #198754;
                        font-weight: 600;
                    }
                    
                    .recommendation-item.danger {
                        color: #dc3545;
                        font-weight: 600;
                    }
                    
                    .footer {
                        margin-top: 10px;
                        padding: 8px;
                        text-align: center;
                        border-top: 1px solid #e2e8f0;
                        color: #718096;
                        font-size: 8px;
                    }
                    
                    @media print {
                        body { 
                            margin: 10mm; 
                            font-size: 9px;
                            padding: 0;
                        }
                        
                        .header {
                            margin-bottom: 10px;
                            padding: 10px 8px;
                            page-break-inside: avoid;
                        }
                        
                        .logo {
                            font-size: 14px;
                        }
                        
                        .report-title {
                            font-size: 12px;
                        }
                        
                        .recommendations-content {
                            padding: 8px;
                        }
                        
                        .recommendation-category {
                            margin-bottom: 8px;
                            padding: 6px;
                            page-break-inside: avoid;
                        }
                        
                        .category-title {
                            font-size: 10px;
                        }
                        
                        .recommendation-item {
                            font-size: 8px;
                            margin-bottom: 3px;
                        }
                        
                        .footer {
                            margin-top: 5px;
                            padding: 5px;
                            font-size: 7px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="logo">MeloTech</div>
                    <div class="logo-subtitle">Agricultural Intelligence Platform</div>
                    <div class="report-title">AI-Powered Recommendations</div>
                    <div class="report-subtitle">Intelligent Agricultural Guidance</div>
                    <div class="report-info">
                        <div class="info-item">
                            <div class="info-label">Report Date</div>
                            <div class="info-value">${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">AI Analysis</div>
                            <div class="info-value">Smart Recommendations</div>
                        </div>
                    </div>
                </div>
                
                <div class="recommendations-container">
                    <div class="recommendations-header">
                        <div class="recommendations-title">🤖 AI-Powered Recommendations</div>
                        <div class="recommendations-summary">${summary}</div>
                    </div>
                    
                    <div class="recommendations-content">
                        ${categories.map(category => `
                            <div class="recommendation-category ${getCategoryClass(category.title)}">
                                <div class="category-title ${getCategoryClass(category.title)}">
                                    ${getCategoryIcon(category.iconClass)} ${category.title}
                                </div>
                                <ul class="recommendation-list">
                                    ${category.items.map(item => `
                                        <li class="recommendation-item ${item.isSuccess ? 'success' : ''} ${item.isDanger ? 'danger' : ''}">
                                            ${item.text}
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="footer">
                    <p>Generated by MeloTech Agricultural Intelligence Platform • ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })} at ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
