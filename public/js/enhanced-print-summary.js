// Enhanced Print Summary Function
function printSummary() {
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    
    // Check if this is AI Recommendations or Questions Summary
    if (modalTitle && modalTitle.textContent.includes('AI-Powered Recommendations')) {
        // Call the recommendations print function
        if (typeof printRecommendations === 'function') {
            printRecommendations();
        } else {
            // Fallback to basic print if recommendations function not loaded
            window.open('', '_blank').document.write(`
                <html>
                    <head><title>AI Recommendations - MeloTech</title></head>
                    <body style="font-family: Arial, sans-serif; margin: 20px;">
                        <h2>AI-Powered Recommendations</h2>
                        ${modalContent.innerHTML}
                    </body>
                </html>
            `).document.close();
        }
        return;
    }
    
    // Original Questions Summary print logic
    const printWindow = window.open('', '_blank');
    
    // Extract questions and answers from the modal content
    const answerItems = modalContent.querySelectorAll('.answer-item');
    const questions = Array.from(answerItems).map(item => {
        const questionLabel = item.querySelector('.question-label');
        const answerValue = item.querySelector('.answer-value');
        return {
            question: questionLabel ? questionLabel.textContent : '',
            answer: answerValue ? answerValue.textContent : ''
        };
    });
    
    // Get progress from summary footer
    const summaryFooter = modalContent.querySelector('.summary-footer');
    const progressText = summaryFooter ? summaryFooter.textContent : '100%';
    
    // Function to determine answer class for styling
    function getAnswerClass(answer) {
        const answerLower = answer.toLowerCase();
        if (answerLower.includes('good') || answerLower.includes('positive') || answerLower.includes('on track')) return 'good';
        if (answerLower.includes('moderate') || answerLower.includes('slower') || answerLower.includes('minor')) return 'moderate';
        if (answerLower.includes('low')) return 'low';
        if (answerLower.includes('satisfied')) return 'satisfied';
        return 'good';
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Questions Summary - MeloTech</title>
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
                    
                    .summary-container {
                        background: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
                        border: 1px solid #e2e8f0;
                    }
                    
                    .summary-header {
                        background: linear-gradient(135deg, #059669 0%, #047857 100%);
                        color: white;
                        padding: 10px;
                        text-align: center;
                    }
                    
                    .summary-title {
                        font-size: 14px;
                        font-weight: 700;
                        margin: 0 0 4px 0;
                        letter-spacing: -0.3px;
                    }
                    
                    .summary-date {
                        font-size: 10px;
                        opacity: 0.9;
                        margin: 0;
                    }
                    
                    .answers-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 8px;
                        padding: 12px;
                        background: #f8fafc;
                    }
                    
                    .answer-item {
                        background: #ffffff;
                        border-radius: 6px;
                        padding: 8px;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                        transition: all 0.2s ease;
                        position: relative;
                        overflow: hidden;
                    }
                    
                    .answer-item::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        height: 2px;
                        background: linear-gradient(90deg, #059669 0%, #10b981 100%);
                    }
                    
                    .question-label {
                        font-size: 9px;
                        font-weight: 600;
                        color: #2d3748;
                        margin-bottom: 4px;
                        line-height: 1.2;
                    }
                    
                    .answer-value {
                        display: inline-block;
                        padding: 3px 8px;
                        border-radius: 12px;
                        font-size: 8px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                        border: 1px solid transparent;
                    }
                    
                    .answer-good {
                        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                        color: #065f46;
                        border-color: #6ee7b7;
                    }
                    
                    .answer-moderate {
                        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                        color: #92400e;
                        border-color: #fbbf24;
                    }
                    
                    .answer-low {
                        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
                        color: #991b1b;
                        border-color: #f87171;
                    }
                    
                    .answer-positive {
                        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                        color: #1e40af;
                        border-color: #93c5fd;
                    }
                    
                    .answer-satisfied {
                        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
                        color: #3730a3;
                        border-color: #a5b4fc;
                    }
                    
                    .answer-on-track {
                        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                        color: #065f46;
                        border-color: #6ee7b7;
                    }
                    
                    .answer-slower {
                        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                        color: #92400e;
                        border-color: #fbbf24;
                    }
                    
                    .answer-minor {
                        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                        color: #92400e;
                        border-color: #fbbf24;
                    }
                    
                    .summary-footer {
                        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
                        padding: 10px;
                        text-align: center;
                        border-top: 1px solid #e2e8f0;
                    }
                    
                    .progress-display {
                        display: inline-block;
                        padding: 6px 15px;
                        background: linear-gradient(135deg, #059669 0%, #047857 100%);
                        color: white;
                        border-radius: 15px;
                        font-size: 11px;
                        font-weight: 700;
                        box-shadow: 0 2px 3px rgba(5, 150, 105, 0.3);
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
                        
                        .answers-grid {
                            padding: 8px;
                            gap: 6px;
                        }
                        
                        .answer-item {
                            padding: 6px;
                            page-break-inside: avoid;
                        }
                        
                        .question-label {
                            font-size: 8px;
                        }
                        
                        .answer-value {
                            padding: 2px 6px;
                            font-size: 7px;
                        }
                        
                        .progress-display {
                            padding: 4px 12px;
                            font-size: 9px;
                        }
                        
                        .report-info {
                            gap: 15px;
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
                    <div class="report-title">Questions Answer Summary</div>
                    <div class="report-subtitle">Crop Progress Assessment Report</div>
                    <div class="report-info">
                        <div class="info-item">
                            <div class="info-label">Report Date</div>
                            <div class="info-value">${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Assessment Type</div>
                            <div class="info-value">Progress Evaluation</div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-container">
                    <div class="summary-header">
                        <div class="summary-title">Assessment Results</div>
                        <div class="summary-date">Generated on ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                    </div>
                    
                    <div class="answers-grid">
                        ${questions.map(q => `
                            <div class="answer-item">
                                <div class="question-label">${q.question}</div>
                                <div class="answer-value answer-${getAnswerClass(q.answer)}">${q.answer}</div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="summary-footer">
                        <div class="progress-display">Total Progress: ${progressText}</div>
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
