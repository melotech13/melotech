// Photo Diagnosis Show Page Scripts (extracted from Blade)
(function() {
  'use strict';

  function qs(selector, scope) { return (scope || document).querySelector(selector); }
  function qsa(selector, scope) { return Array.prototype.slice.call((scope || document).querySelectorAll(selector)); }

  function getPageContext() {
    const root = qs('.analysis-results-container');
    return {
      root,
      analysisId: root ? root.getAttribute('data-analysis-id') : null,
      farmLocation: root ? root.getAttribute('data-farm-location') : null
    };
  }

  // Function to load growth progress data
  function loadGrowthProgress(analysisId) {
    const growthProgressEl = document.getElementById('growth-progress-content');
    if (!growthProgressEl || !analysisId) return;

    fetch(`/api/analysis/${encodeURIComponent(analysisId)}/growth-progress`)
      .then(r => r.json())
      .then(data => {
        if (data.success && data.html) {
          growthProgressEl.innerHTML = data.html;
        } else {
          growthProgressEl.innerHTML = '<div class="alert alert-warning">No growth data available.</div>';
        }
      })
      .catch(err => {
        console.error('Error loading growth progress:', err);
        growthProgressEl.innerHTML = '<div class="alert alert-danger">Failed to load growth data. Please try refreshing the page.</div>';
      });
  }

  // Function to load weather information
  function loadWeatherInfo(farmLocation) {
    const weatherInfoEl = document.getElementById('weather-info-content');
    if (!weatherInfoEl || !farmLocation) return;

    fetch(`/api/weather?location=${encodeURIComponent(farmLocation)}`)
      .then(r => r.json())
      .then(data => {
        if (data.success && data.html) {
          weatherInfoEl.innerHTML = data.html;
        } else {
          weatherInfoEl.innerHTML = '<div class="alert alert-warning">Weather data not available.</div>';
        }
      })
      .catch(err => {
        console.error('Error loading weather info:', err);
        weatherInfoEl.innerHTML = '<div class="alert alert-danger">Failed to load weather information. Please try again later.</div>';
      });
  }

  // Replace inline onerror for .analysis-photo
  function bindImageErrorFallback() {
    const img = qs('.analysis-photo');
    if (!img) return;
    img.addEventListener('error', function() {
      img.style.display = 'none';
      const placeholder = img.parentElement ? img.parentElement.querySelector('.no-image-placeholder') : null;
      if (placeholder) {
        placeholder.classList.remove('hidden');
        placeholder.style.display = 'flex';
      }
    }, { once: true });
  }

  // Apply dynamic styles for probability visualization
  function applyProbabilityStyles() {
    qsa('.probability-fill[data-width][data-color]').forEach(function(el) {
      var width = el.getAttribute('data-width');
      var color = el.getAttribute('data-color');
      if (width !== null) el.style.width = String(width) + '%';
      if (color) el.style.background = color;
    });

    qsa('.probability-row .icon[data-color]').forEach(function(el) {
      var color = el.getAttribute('data-color');
      if (color) {
        el.style.color = color;
        el.style.background = color + '20';
      }
    });

    qsa('.percent-badge[data-color]').forEach(function(el) {
      var color = el.getAttribute('data-color');
      if (color) {
        el.style.color = color;
        el.style.borderColor = color + '40';
      }
    });
  }

  function bindShareButton() {
    const btn = document.getElementById('share-results-btn');
    if (!btn) return;
    btn.addEventListener('click', function() {
      if (navigator.share) {
        navigator.share({
          title: 'Photo Analysis Results',
          text: 'Check out my crop photo analysis results from MeloTech!',
          url: window.location.href
        });
      } else {
        // Fallback: copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
          }, function() {
            fallbackCopy(window.location.href);
          });
        } else {
          fallbackCopy(window.location.href);
        }
      }
    });
  }

  function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try { document.execCommand('copy'); } catch(e) { /* ignore */ }
    document.body.removeChild(textArea);
    alert('Link copied to clipboard!');
  }

  document.addEventListener('DOMContentLoaded', function() {
    const ctx = getPageContext();
    loadGrowthProgress(ctx.analysisId);
    loadWeatherInfo(ctx.farmLocation);
    applyProbabilityStyles();
    bindImageErrorFallback();
    bindShareButton();
  });
})();
