/**
 * SIMPLE AND FAST Locations System
 * Direct approach with immediate loading
 */

console.log('🚀 Simple locations system loading...');

// Simple locations data cache
let locationsCache = null;
let isLoading = false;

/**
 * Load locations data directly
 */
async function loadLocationsData() {
    if (locationsCache) {
        return locationsCache;
    }
    
    if (isLoading) {
        // Wait for existing load to complete
        while (isLoading) {
            await new Promise(resolve => setTimeout(resolve, 50));
        }
        return locationsCache;
    }
    
    isLoading = true;
    console.log('📡 Fetching locations data...');
    
    try {
        const response = await fetch('/locations.json');
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        locationsCache = await response.json();
        console.log('✅ Locations data loaded successfully!', {
            provinces: Object.keys(locationsCache).length,
            sizeKB: Math.round(JSON.stringify(locationsCache).length / 1024)
        });
        
        return locationsCache;
    } catch (error) {
        console.error('❌ Failed to load locations:', error);
        throw error;
    } finally {
        isLoading = false;
    }
}

/**
 * Populate a select element with options
 */
function populateSelect(selectElement, options, placeholder = 'Select...') {
    selectElement.innerHTML = '';
    
    // Add placeholder
    const placeholderOption = document.createElement('option');
    placeholderOption.value = '';
    placeholderOption.textContent = placeholder;
    selectElement.appendChild(placeholderOption);
    
    // Add options
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        selectElement.appendChild(optionElement);
    });
    
    selectElement.disabled = false;
}

/**
 * Show a helpful message about barangay data availability
 */
function showBarangayMessage(message) {
    // Remove any existing message
    const existingMessage = document.getElementById('barangay-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.id = 'barangay-message';
    messageDiv.className = 'alert alert-info mt-2';
    messageDiv.innerHTML = `
        <small>
            <strong>ℹ️ Note:</strong> ${message}
            <br><em>You can still proceed with your registration without selecting a barangay.</em>
        </small>
    `;
    
    // Insert after barangay select
    const barangaySelect = document.getElementById('barangay');
    if (barangaySelect && barangaySelect.parentNode) {
        barangaySelect.parentNode.insertBefore(messageDiv, barangaySelect.nextSibling);
    }
}

/**
 * Show data quality information
 */
function showDataQualityInfo(data) {
    const totalProvinces = Object.keys(data).length;
    let totalMunicipalities = 0;
    let totalBarangays = 0;
    let municipalitiesWithBarangays = 0;
    
    Object.values(data).forEach(provinceData => {
        if (Array.isArray(provinceData)) {
            // Province has no municipalities
            totalMunicipalities += 0;
        } else {
            totalMunicipalities += Object.keys(provinceData).length;
            Object.values(provinceData).forEach(municipalityData => {
                if (Array.isArray(municipalityData) && municipalityData.length > 0) {
                    totalBarangays += municipalityData.length;
                    municipalitiesWithBarangays++;
                }
            });
        }
    });
    
    console.log('📊 Data Quality Summary:', {
        provinces: totalProvinces,
        municipalities: totalMunicipalities,
        barangays: totalBarangays,
        municipalitiesWithBarangays: municipalitiesWithBarangays,
        coverage: `${Math.round((municipalitiesWithBarangays / totalMunicipalities) * 100)}%`
    });
}

/**
 * Show province information and data coverage
 */
function showProvinceInfo(provinceName, provinceData) {
    // Remove any existing province info
    const existingInfo = document.getElementById('province-info');
    if (existingInfo) {
        existingInfo.remove();
    }
    
    if (Array.isArray(provinceData)) {
        // Province has no municipalities
        return;
    }
    
    const municipalities = Object.keys(provinceData);
    let municipalitiesWithBarangays = 0;
    let totalBarangays = 0;
    
    Object.values(provinceData).forEach(municipalityData => {
        if (Array.isArray(municipalityData) && municipalityData.length > 0) {
            municipalitiesWithBarangays++;
            totalBarangays += municipalityData.length;
        }
    });
    
    const coverage = municipalities.length > 0 ? Math.round((municipalitiesWithBarangays / municipalities.length) * 100) : 0;
    
    // Create info element
    const infoDiv = document.createElement('div');
    infoDiv.id = 'province-info';
    infoDiv.className = 'alert alert-info mt-2';
    infoDiv.innerHTML = `
        <small>
            <strong>📍 ${provinceName}:</strong> 
            ${municipalities.length} municipalities/cities, 
            ${municipalitiesWithBarangays} with barangay data (${coverage}% coverage)
            <br><em>Some areas may not have barangay-level data - this is normal for Philippine administrative divisions.</em>
        </small>
    `;
    
    // Insert after province select
    const provinceSelect = document.getElementById('province');
    if (provinceSelect && provinceSelect.parentNode) {
        provinceSelect.parentNode.insertBefore(infoDiv, provinceSelect.nextSibling);
    }
}

/**
 * Initialize cascading dropdowns
 */
async function initializeCascadingDropdowns() {
    console.log('🔧 Initializing cascading dropdowns...');
    
    const provinceSelect = document.getElementById('province');
    const municipalitySelect = document.getElementById('city_municipality');
    const barangaySelect = document.getElementById('barangay');
    
    if (!provinceSelect || !municipalitySelect || !barangaySelect) {
        console.error('❌ Required select elements not found');
        return;
    }
    
    try {
        // Show loading state
        provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
        provinceSelect.disabled = true;
        municipalitySelect.innerHTML = '<option value="">Select Province first</option>';
        municipalitySelect.disabled = true;
        barangaySelect.innerHTML = '<option value="">Select Municipality first</option>';
        barangaySelect.disabled = true;
        
        // Load data
        const data = await loadLocationsData();
        
        console.log('📊 Locations data loaded:', {
            provinces: Object.keys(data).length,
            sampleProvince: Object.keys(data)[0],
            sampleMunicipalities: Object.keys(data[Object.keys(data)[0]] || {}).length
        });
        
        // Show data quality information
        showDataQualityInfo(data);
        
        // Populate provinces
        const provinces = Object.keys(data).sort();
        populateSelect(provinceSelect, provinces, 'Select Province');
        
        // Handle province change
        provinceSelect.addEventListener('change', function() {
            const selectedProvince = this.value;
            
            console.log('🏛️ Province selected:', selectedProvince);
            
            // Remove any existing messages
            const existingMessage = document.getElementById('barangay-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            if (selectedProvince && data[selectedProvince]) {
                const municipalities = Object.keys(data[selectedProvince]).sort();
                console.log('🏘️ Found', municipalities.length, 'municipalities in', selectedProvince);
                populateSelect(municipalitySelect, municipalities, 'Select Municipality');
                
                // Reset barangay
                barangaySelect.innerHTML = '<option value="">Select Municipality first</option>';
                barangaySelect.disabled = true;
            } else {
                console.log('❌ Province not found in data');
                municipalitySelect.innerHTML = '<option value="">Select Province first</option>';
                municipalitySelect.disabled = true;
                barangaySelect.innerHTML = '<option value="">Select Municipality first</option>';
                barangaySelect.disabled = true;
            }
        });
        
        // Handle municipality change
        municipalitySelect.addEventListener('change', function() {
            const selectedProvince = provinceSelect.value;
            const selectedMunicipality = this.value;
            
            console.log('🏘️ Municipality selected:', selectedMunicipality, 'in', selectedProvince);
            
            if (selectedProvince && selectedMunicipality && 
                data[selectedProvince] && data[selectedProvince][selectedMunicipality]) {
                const barangays = data[selectedProvince][selectedMunicipality];
                
                console.log('📊 Barangays found:', barangays ? barangays.length : 'undefined');
                
                if (barangays && barangays.length > 0) {
                    // Municipality has barangays
                    console.log('✅ Populating barangay dropdown with', barangays.length, 'barangays');
                    populateSelect(barangaySelect, barangays.sort(), 'Select Barangay');
                } else {
                    // Municipality has no barangays - this is common in Philippine data
                    console.log('⚠️ Municipality has no barangays (this is normal for some areas)');
                    barangaySelect.innerHTML = '<option value="">No barangay data available</option>';
                    barangaySelect.disabled = true;
                    
                    // Show helpful message to user
                    showBarangayMessage('This municipality/city does not have barangay-level data in our system. This is common for newly created cities or areas with simplified administrative structures.');
                }
            } else {
                console.log('❌ Invalid selection or data not found');
                barangaySelect.innerHTML = '<option value="">Select Municipality first</option>';
                barangaySelect.disabled = true;
            }
        });
        
        console.log('✅ Cascading dropdowns initialized successfully!');
        
    } catch (error) {
        console.error('❌ Failed to initialize cascading dropdowns:', error);
        
        // Show error state
        provinceSelect.innerHTML = '<option value="">Error loading provinces - please refresh</option>';
        provinceSelect.disabled = true;
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCascadingDropdowns);
} else {
    initializeCascadingDropdowns();
}

// Also expose globally for manual initialization
window.initializeLocations = initializeCascadingDropdowns;

console.log('📋 Simple locations system ready!');


