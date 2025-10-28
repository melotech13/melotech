/**
 * Nutrient Calculator - AI-Powered Feature
 * Melotech - Intelligent Soil Analysis with Together AI (Mixtral)
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('nutrientForm');
    const resultsSection = document.getElementById('resultsSection');
    const loadingState = document.getElementById('loadingState');
    const resultsContent = document.getElementById('resultsContent');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // Form submission handler
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Show results section with loading state
            resultsSection.style.display = 'block';
            loadingState.style.display = 'block';
            resultsContent.style.display = 'none';
            
            // Scroll to results with smooth animation
            setTimeout(() => {
                resultsSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest' 
                });
            }, 100);
            
            // Disable submit button
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Analyzing...</span>';
            
            // Gather form data
            const formData = {
                nitrogen: parseFloat(document.getElementById('nitrogen').value),
                phosphorus: parseFloat(document.getElementById('phosphorus').value),
                potassium: parseFloat(document.getElementById('potassium').value),
                soil_ph: parseFloat(document.getElementById('soil_ph').value),
                soil_moisture: parseFloat(document.getElementById('soil_moisture').value),
                growth_stage: document.getElementById('growth_stage').value,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };
            
            try {
                // Call AI analysis endpoint
                const response = await fetch('/nutrient-calculator/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Display results with animation
                    displayResults(data.results, data.analysis);
                } else {
                    // Show error
                    showError(data.error || 'Analysis failed. Please try again.');
                }
                
            } catch (error) {
                console.error('Analysis error:', error);
                showError('Failed to connect to AI service. Please check your internet connection and API configuration.');
            } finally {
                // Re-enable submit button
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-brain"></i> <span>Analyze with AI</span> <div class="btn-shimmer"></div>';
            }
        });
    }
    
    /**
     * Display AI analysis results with detailed, beautiful formatting
     */
    function displayResults(results, analysis) {
        // Hide loading, show results
        loadingState.style.display = 'none';
        resultsContent.style.display = 'block';
        
        const detailed = results.detailed_analysis || {};
        
        // Build comprehensive results HTML
        resultsContent.innerHTML = `
            ${buildNutrientStatusSection(results, analysis, detailed)}
            ${buildDeficiencySection(results, detailed)}
            ${buildRecommendationsSection(results, detailed)}
            ${buildStageAdvisorySection(results, analysis)}
            ${buildSummaryInsight(results, detailed)}
        `;
        
        // Success notification
        showNotification('✅ AI Analysis Complete!', 'success');
    }
    
    /**
     * Build Nutrient Status Summary Section
     */
    function buildNutrientStatusSection(results, analysis, detailed) {
        const getNutrientStatus = (value, optimal, nutrientName) => {
            const [min, max] = optimal.split('-').map(Number);
            if (value >= min && value <= max) {
                return { icon: '🟢', status: 'Balanced', color: '#10b981', desc: 'Within optimal range' };
            } else if (value < min && value >= min * 0.7) {
                return { icon: '🟡', status: 'Slightly Deficient', color: '#f59e0b', desc: 'Below optimal range' };
            } else if (value < min * 0.7) {
                return { icon: '🔴', status: 'Low', color: '#ef4444', desc: 'Significantly below optimal' };
            } else {
                return { icon: '🟡', status: 'Slightly High', color: '#f59e0b', desc: 'Above optimal range' };
            }
        };
        
        const nStatus = getNutrientStatus(analysis.nitrogen, '50-150', 'Nitrogen');
        const pStatus = getNutrientStatus(analysis.phosphorus, '30-80', 'Phosphorus');
        const kStatus = getNutrientStatus(analysis.potassium, '150-300', 'Potassium');
        
        const phStatus = analysis.soil_ph >= 6.0 && analysis.soil_ph <= 6.8 
            ? { icon: '🟢', status: 'Optimal', color: '#10b981', desc: 'Perfect for nutrient uptake' }
            : analysis.soil_ph < 6.0 
            ? { icon: '🟡', status: 'Slightly Acidic', color: '#f59e0b', desc: 'May reduce nutrient availability' }
            : { icon: '🟡', status: 'Slightly Alkaline', color: '#f59e0b', desc: 'May limit micronutrient absorption' };
        
        return `
            <div class="result-card npk-status-card animate-fade-in">
                <div class="result-header">
                    <h3><i class="fas fa-flask"></i> 🧪 1. Nutrient Status Summary</h3>
                </div>
                <div class="result-body">
                    <div class="nutrient-grid">
                        <div class="nutrient-card" style="border-left-color: ${nStatus.color};">
                            <div class="nutrient-header">
                                <span class="nutrient-icon">${nStatus.icon}</span>
                                <div class="nutrient-title">
                                    <h4>Nitrogen (N)</h4>
                                    <span class="optimal-range">Optimal: 50-150 ppm</span>
                                </div>
                            </div>
                            <div class="nutrient-value-box" style="background: ${nStatus.color}20; border-color: ${nStatus.color};">
                                <span class="current-value">${analysis.nitrogen}</span>
                                <span class="unit">ppm</span>
                            </div>
                            <div class="nutrient-status" style="color: ${nStatus.color};">
                                <strong>${nStatus.status}</strong> – ${nStatus.desc}
                            </div>
                            <p class="nutrient-note">Critical for leaf and stem growth during ${analysis.growth_stage} stage.</p>
                        </div>
                        
                        <div class="nutrient-card" style="border-left-color: ${pStatus.color};">
                            <div class="nutrient-header">
                                <span class="nutrient-icon">${pStatus.icon}</span>
                                <div class="nutrient-title">
                                    <h4>Phosphorus (P)</h4>
                                    <span class="optimal-range">Optimal: 30-80 ppm</span>
                                </div>
                            </div>
                            <div class="nutrient-value-box" style="background: ${pStatus.color}20; border-color: ${pStatus.color};">
                                <span class="current-value">${analysis.phosphorus}</span>
                                <span class="unit">ppm</span>
                            </div>
                            <div class="nutrient-status" style="color: ${pStatus.color};">
                                <strong>${pStatus.status}</strong> – ${pStatus.desc}
                            </div>
                            <p class="nutrient-note">Essential for strong root development and energy transfer.</p>
                        </div>
                        
                        <div class="nutrient-card" style="border-left-color: ${kStatus.color};">
                            <div class="nutrient-header">
                                <span class="nutrient-icon">${kStatus.icon}</span>
                                <div class="nutrient-title">
                                    <h4>Potassium (K)</h4>
                                    <span class="optimal-range">Optimal: 150-300 ppm</span>
                                </div>
                            </div>
                            <div class="nutrient-value-box" style="background: ${kStatus.color}20; border-color: ${kStatus.color};">
                                <span class="current-value">${analysis.potassium}</span>
                                <span class="unit">ppm</span>
                            </div>
                            <div class="nutrient-status" style="color: ${kStatus.color};">
                                <strong>${kStatus.status}</strong> – ${kStatus.desc}
                            </div>
                            <p class="nutrient-note">Important for fruit quality, disease resistance, and water regulation.</p>
                        </div>
                        
                        <div class="nutrient-card" style="border-left-color: ${phStatus.color};">
                            <div class="nutrient-header">
                                <span class="nutrient-icon">${phStatus.icon}</span>
                                <div class="nutrient-title">
                                    <h4>Soil pH</h4>
                                    <span class="optimal-range">Optimal: 6.0-6.8</span>
                                </div>
                            </div>
                            <div class="nutrient-value-box" style="background: ${phStatus.color}20; border-color: ${phStatus.color};">
                                <span class="current-value">${analysis.soil_ph}</span>
                                <span class="unit">pH</span>
                            </div>
                            <div class="nutrient-status" style="color: ${phStatus.color};">
                                <strong>${phStatus.status}</strong> – ${phStatus.desc}
                            </div>
                            <p class="nutrient-note">${detailed.ph_status || 'Directly affects nutrient availability and uptake.'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Build Deficiency & Imbalance Detection Section
     */
    function buildDeficiencySection(results, detailed) {
        const deficiencies = detailed.deficiencies || [];
        const excesses = detailed.excesses || [];
        
        return `
            <div class="result-card deficiency-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="result-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> ⚠️ 2. Deficiency & Imbalance Detection</h3>
                </div>
                <div class="result-body">
                    ${results.deficiency_detection ? `<p class="deficiency-summary">${results.deficiency_detection}</p>` : ''}
                    ${deficiencies.length > 0 ? `
                        <ul class="detection-list">
                            ${deficiencies.map(d => `<li>🔴 Detected low <strong>${d}</strong> levels.</li>`).join('')}
                        </ul>
                    ` : ''}
                    ${excesses.length > 0 ? `
                        <ul class="detection-list">
                            ${excesses.map(e => `<li>🟡 <strong>${e}</strong> is above optimal range.</li>`).join('')}
                        </ul>
                    ` : ''}
                    ${detailed.ph_status ? `<p class="ph-note">💧 ${detailed.ph_status}</p>` : ''}
                    ${detailed.moisture_status ? `<p class="moisture-note">💦 ${detailed.moisture_status}</p>` : ''}
                </div>
            </div>
        `;
    }
    
    /**
     * Build AI-Based Fertilizer Recommendations Section
     */
    function buildRecommendationsSection(results, detailed) {
        const recommendations = detailed.recommendations || [];
        
        return `
            <div class="result-card ai-recommendations-card highlight-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="result-header">
                    <h3><i class="fas fa-lightbulb"></i> 🌱 3. 💡 AI-Based Fertilizer Recommendations</h3>
                    <span class="ai-tag">Mixtral AI</span>
                </div>
                <div class="result-body">
                    <p class="ai-intro"><strong>AI Suggests:</strong></p>
                    ${results.ai_recommendations ? `
                        <div class="recommendations-detailed">
                            ${formatDetailedRecommendations(results.ai_recommendations, recommendations)}
                        </div>
                    ` : '<p>No specific recommendations at this time.</p>'}
                </div>
            </div>
        `;
    }
    
    /**
     * Build Stage-Based Advisory Section
     */
    function buildStageAdvisorySection(results, analysis) {
        const stageName = analysis.growth_stage.charAt(0).toUpperCase() + analysis.growth_stage.slice(1);
        
        return `
            <div class="result-card stage-advisory-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="result-header">
                    <h3><i class="fas fa-seedling"></i> 🌾 4. Stage-Based Advisory</h3>
                </div>
                <div class="result-body">
                    <p class="stage-title"><strong>For the ${stageName} Stage:</strong></p>
                    <div class="stage-content">
                        ${results.stage_advisory || 'Continue monitoring nutrient levels and adjust fertilization as needed.'}
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Build Summary Insight Section
     */
    function buildSummaryInsight(results, detailed) {
        const npkBalance = detailed.npk_balance || 'moderate';
        const statusColors = {
            'balanced': '#10b981',
            'moderate': '#f59e0b',
            'critical': '#ef4444'
        };
        
        return `
            <div class="result-card summary-insight-card animate-fade-in" style="animation-delay: 0.4s; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-left: 5px solid ${statusColors[npkBalance]};">
                <div class="result-header">
                    <h3><i class="fas fa-bullseye"></i> 🎯 Summary Insight</h3>
                </div>
                <div class="result-body">
                    <blockquote class="summary-quote">
                        "${results.nutrient_status || 'Analysis complete. Review recommendations above for optimal crop growth.'}"
                    </blockquote>
                    ${detailed.priority_actions && detailed.priority_actions.length > 0 ? `
                        <div class="priority-actions">
                            <strong>Priority Actions:</strong>
                            <ul>
                                ${detailed.priority_actions.map(action => `<li>✓ ${action}</li>`).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    /**
     * Format detailed recommendations with structured subsections
     */
    function formatDetailedRecommendations(text, recommendations) {
        if (recommendations && recommendations.length > 0) {
            // Parse recommendations to identify categories
            return recommendations.map((rec, index) => {
                // Try to detect if this is a titled recommendation (e.g., "Nitrogen Boost:")
                const titleMatch = rec.match(/^([^:]+):/);
                const title = titleMatch ? titleMatch[1].trim() : null;
                const content = titleMatch ? rec.substring(titleMatch[0].length).trim() : rec;
                
                // Split content by bullets or newlines
                const points = content.split(/[•\n]/).filter(p => p.trim());
                
                return `
                    <div class="recommendation-section">
                        ${title ? `
                            <div class="rec-header">
                                <span class="rec-badge">${index + 1}</span>
                                <h4 class="rec-title">${escapeHtml(title)}</h4>
                            </div>
                        ` : `
                            <div class="rec-header">
                                <span class="rec-badge">${index + 1}</span>
                            </div>
                        `}
                        <div class="rec-body">
                            ${points.length > 1 ? `
                                <ul class="rec-points">
                                    ${points.map(point => `
                                        <li>${escapeHtml(point.trim())}</li>
                                    `).join('')}
                                </ul>
                            ` : `
                                <p>${escapeHtml(content)}</p>
                            `}
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Fallback: Enhanced text formatting
        const lines = text.split('\n').filter(line => line.trim());
        let formattedHTML = '';
        let currentSection = null;
        
        lines.forEach(line => {
            line = line.trim();
            
            // Check if this is a section header (ends with :)
            if (line.endsWith(':') && !line.startsWith('•')) {
                if (currentSection) {
                    formattedHTML += `</div></div>`;
                }
                const sectionTitle = line.slice(0, -1);
                formattedHTML += `
                    <div class="recommendation-section">
                        <div class="rec-header">
                            <h4 class="rec-title">${escapeHtml(sectionTitle)}</h4>
                        </div>
                        <div class="rec-body">
                `;
                currentSection = sectionTitle;
            } else if (line.startsWith('•') || line.startsWith('-')) {
                // Bullet point
                const point = line.replace(/^[•\-]\s*/, '');
                formattedHTML += `<div class="rec-point">✓ ${escapeHtml(point)}</div>`;
            } else if (line) {
                // Regular text
                formattedHTML += `<p class="rec-text">${escapeHtml(line)}</p>`;
            }
        });
        
        if (currentSection) {
            formattedHTML += `</div></div>`;
        }
        
        return formattedHTML || `<p class="rec-text">${escapeHtml(text)}</p>`;
    }
    
    /**
     * Show error message
     */
    function showError(message) {
        loadingState.style.display = 'none';
        resultsContent.style.display = 'block';
        
        resultsContent.innerHTML = `
            <div class="result-card" style="background: #fee2e2; border-color: #ef4444;">
                <div class="result-header">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
                    <h3 style="color: #dc2626;">Analysis Error</h3>
                </div>
                <div class="result-body">
                    <p style="color: #991b1b; font-weight: 500;">${escapeHtml(message)}</p>
                    <p style="color: #7f1d1d; margin-top: 1rem; font-size: 0.9rem;">
                        <strong>Troubleshooting:</strong><br>
                        • Check your internet connection<br>
                        • Verify TOGETHER_API_KEY is configured in .env file<br>
                        • Ensure your Together AI account has credits available<br>
                        • The system will use fallback mode if AI is unavailable
                    </p>
                </div>
            </div>
        `;
        
        showNotification('❌ Analysis Failed', 'error');
    }
    
    /**
     * Format text with icon support
     */
    function formatTextWithIcons(text) {
        if (!text) return '<p style="color: #6b7280;">No data available</p>';
        
        // Split by newlines and format
        const lines = text.split('\n').filter(line => line.trim());
        return lines.map(line => {
            // Detect icons and wrap in styled spans
            let formatted = escapeHtml(line);
            
            // Add highlighting to recommendations
            if (formatted.includes('•')) {
                formatted = `<div class="recommendation-item">${formatted}</div>`;
            } else if (formatted.includes('⚠️')) {
                formatted = `<div class="warning-item">${formatted}</div>`;
            } else if (formatted.includes('✅')) {
                formatted = `<div class="success-item">${formatted}</div>`;
            } else {
                formatted = `<div>${formatted}</div>`;
            }
            
            return formatted;
        }).join('');
    }
    
    /**
     * Animate element with fade-in-up effect
     */
    function animateElement(element, delay = 0) {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.5s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, delay);
    }
    
    /**
     * Show notification toast
     */
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            font-weight: 600;
            z-index: 9999;
            animation: slideInRight 0.5s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease-out';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Input validation and real-time feedback
     */
    const inputs = form.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateInput(this);
        });
    });
    
    function validateInput(input) {
        const value = parseFloat(input.value);
        const min = parseFloat(input.min);
        const max = parseFloat(input.max);
        
        if (value < min || value > max) {
            input.style.borderColor = '#ef4444';
        } else {
            input.style.borderColor = '#10b981';
        }
    }
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    @keyframes pulse-highlight {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        }
        50% {
            box-shadow: 0 0 0 20px rgba(16, 185, 129, 0);
        }
    }
    
    .recommendation-item {
        padding: 0.5rem 0;
        font-weight: 500;
        color: #1f2937;
    }
    
    .warning-item {
        padding: 0.5rem 0;
        color: #d97706;
        font-weight: 500;
    }
    
    .success-item {
        padding: 0.5rem 0;
        color: #059669;
        font-weight: 500;
    }
`;
document.head.appendChild(style);
