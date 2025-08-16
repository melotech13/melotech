/**
 * Preloaded Locations Module
 * Fast client-side cascading dropdowns using preloaded JSON
 * No AJAX calls needed after initial load
 */

class LocationsManager {
    constructor() {
        this.locationsData = null;
        this.isLoading = false;
        this.loadPromise = null;
    }

    /**
     * Load locations data from preloaded JSON
     * @returns {Promise<Object>} The locations data
     */
    async loadLocations() {
        if (this.locationsData) {
            return this.locationsData;
        }

        if (this.isLoading) {
            return this.loadPromise;
        }

        this.isLoading = true;
        this.loadPromise = this._fetchLocations();

        try {
            this.locationsData = await this.loadPromise;
            return this.locationsData;
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Fetch locations from the server
     * @private
     */
    async _fetchLocations() {
        try {
            const response = await fetch('/locations.json', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'max-age=86400' // Cache for 24 hours
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            // Store in localStorage for offline access
            try {
                localStorage.setItem('locations_data', JSON.stringify(data));
                localStorage.setItem('locations_timestamp', Date.now().toString());
            } catch (e) {
                console.warn('Could not cache locations in localStorage:', e);
            }

            return data;
        } catch (error) {
            console.warn('Failed to fetch locations from server, trying localStorage:', error);
            
            // Fallback to localStorage if available
            try {
                const cached = localStorage.getItem('locations_data');
                if (cached) {
                    const timestamp = localStorage.getItem('locations_timestamp');
                    const age = Date.now() - parseInt(timestamp || '0');
                    
                    // Use cache if less than 7 days old
                    if (age < 7 * 24 * 60 * 60 * 1000) {
                        console.info('Using cached locations data');
                        return JSON.parse(cached);
                    }
                }
            } catch (e) {
                console.error('Failed to parse cached locations:', e);
            }

            throw new Error('Could not load locations data');
        }
    }

    /**
     * Get all provinces
     * @returns {Array<string>} Array of province names
     */
    getProvinces() {
        if (!this.locationsData) {
            return [];
        }
        return Object.keys(this.locationsData).sort();
    }

    /**
     * Get municipalities/cities for a province
     * @param {string} provinceName - Name of the province
     * @returns {Array<string>} Array of municipality/city names
     */
    getMunicipalities(provinceName) {
        if (!this.locationsData || !provinceName || !this.locationsData[provinceName]) {
            return [];
        }
        return Object.keys(this.locationsData[provinceName]).sort();
    }

    /**
     * Get barangays for a municipality/city
     * @param {string} provinceName - Name of the province
     * @param {string} municipalityName - Name of the municipality/city
     * @returns {Array<string>} Array of barangay names
     */
    getBarangays(provinceName, municipalityName) {
        if (!this.locationsData || !provinceName || !municipalityName || 
            !this.locationsData[provinceName] || 
            !this.locationsData[provinceName][municipalityName]) {
            return [];
        }
        return this.locationsData[provinceName][municipalityName].sort();
    }

    /**
     * Initialize cascading dropdowns for a form
     * @param {Object} config - Configuration object
     * @param {string} config.provinceSelector - CSS selector for province dropdown
     * @param {string} config.municipalitySelector - CSS selector for municipality dropdown
     * @param {string} config.barangaySelector - CSS selector for barangay dropdown
     * @param {Function} [config.onProvinceChange] - Callback when province changes
     * @param {Function} [config.onMunicipalityChange] - Callback when municipality changes
     * @param {Function} [config.onBarangayChange] - Callback when barangay changes
     */
    async initializeCascadingDropdowns(config) {
        const {
            provinceSelector,
            municipalitySelector,
            barangaySelector,
            onProvinceChange,
            onMunicipalityChange,
            onBarangayChange
        } = config;

        const provinceEl = document.querySelector(provinceSelector);
        const municipalityEl = document.querySelector(municipalitySelector);
        const barangayEl = document.querySelector(barangaySelector);

        if (!provinceEl || !municipalityEl || !barangayEl) {
            console.error('Could not find dropdown elements:', { provinceEl, municipalityEl, barangayEl });
            return;
        }

        try {
            // Show loading state
            this._setLoading(provinceEl, 'Loading provinces...');
            this._setLoading(municipalityEl, 'Select Province first');
            this._setLoading(barangayEl, 'Select Municipality first');

            // Load locations data
            await this.loadLocations();

            // Populate provinces
            this._populateDropdown(provinceEl, this.getProvinces(), 'Select Province');

            // Set up event listeners
            provinceEl.addEventListener('change', (e) => {
                const provinceName = e.target.value;
                
                // Reset dependent dropdowns
                this._populateDropdown(municipalityEl, [], 'Select Municipality/City');
                this._populateDropdown(barangayEl, [], 'Select Barangay');

                if (provinceName) {
                    const municipalities = this.getMunicipalities(provinceName);
                    this._populateDropdown(municipalityEl, municipalities, 'Select Municipality/City');
                }

                if (onProvinceChange) {
                    onProvinceChange(provinceName, e);
                }
            });

            municipalityEl.addEventListener('change', (e) => {
                const municipalityName = e.target.value;
                const provinceName = provinceEl.value;
                
                // Reset barangay dropdown
                this._populateDropdown(barangayEl, [], 'Select Barangay');

                if (provinceName && municipalityName) {
                    const barangays = this.getBarangays(provinceName, municipalityName);
                    this._populateDropdown(barangayEl, barangays, 'Select Barangay');
                }

                if (onMunicipalityChange) {
                    onMunicipalityChange(municipalityName, provinceName, e);
                }
            });

            if (onBarangayChange) {
                barangayEl.addEventListener('change', (e) => {
                    const barangayName = e.target.value;
                    const municipalityName = municipalityEl.value;
                    const provinceName = provinceEl.value;
                    
                    onBarangayChange(barangayName, municipalityName, provinceName, e);
                });
            }

            console.info('✅ Cascading dropdowns initialized successfully');

        } catch (error) {
            console.error('❌ Failed to initialize cascading dropdowns:', error);
            
            // Show error state
            this._setError(provinceEl, 'Failed to load provinces');
            this._setError(municipalityEl, 'Failed to load data');
            this._setError(barangayEl, 'Failed to load data');
        }
    }

    /**
     * Set loading state for a dropdown
     * @private
     */
    _setLoading(selectEl, text = 'Loading...') {
        selectEl.innerHTML = `<option value="">${text}</option>`;
        selectEl.disabled = true;
    }

    /**
     * Set error state for a dropdown
     * @private
     */
    _setError(selectEl, text = 'Error loading data') {
        selectEl.innerHTML = `<option value="">${text}</option>`;
        selectEl.disabled = true;
    }

    /**
     * Populate a dropdown with options
     * @private
     */
    _populateDropdown(selectEl, items, placeholder = 'Select...') {
        selectEl.innerHTML = `<option value="">${placeholder}</option>`;
        
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            selectEl.appendChild(option);
        });

        selectEl.disabled = false;
    }

    /**
     * Get statistics about the loaded data
     * @returns {Object} Statistics object
     */
    getStats() {
        if (!this.locationsData) {
            return null;
        }

        const provinces = Object.keys(this.locationsData);
        let totalMunicipalities = 0;
        let totalBarangays = 0;

        provinces.forEach(province => {
            const municipalities = Object.keys(this.locationsData[province]);
            totalMunicipalities += municipalities.length;

            municipalities.forEach(municipality => {
                totalBarangays += this.locationsData[province][municipality].length;
            });
        });

        return {
            provinces: provinces.length,
            municipalities: totalMunicipalities,
            barangays: totalBarangays,
            dataSize: JSON.stringify(this.locationsData).length
        };
    }
}

// Create global instance
window.LocationsManager = new LocationsManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LocationsManager;
}
