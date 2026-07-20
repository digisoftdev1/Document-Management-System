/**
 * Smooth Navigation System
 * Handles AJAX-based page navigation without full page reload
 * Properly reinitializes complex content (maps, charts, forms)
 */

export function initSmoothNavigation() {
    const MAIN_SELECTOR = 'main';
    const SIDEBAR_LINKS_SELECTOR = 'aside a, aside button[onclick*="location"]';
    
    let isLoading = false;
    let currentUrl = window.location.href;

    // Initialize navigation on page load
    interceptSidebarLinks();
    handlePopState();

    /**
     * Intercept all sidebar links and buttons
     */
    function interceptSidebarLinks() {
        document.addEventListener('click', (e) => {
            // Only intercept actual navigation links (not dropdown buttons)
            const link = e.target.closest('a[href]');
            
            if (!link) return; // Not a link
            if (!link.closest('aside')) return; // Must be in sidebar
            
            const href = link.getAttribute('href');
            if (!href || !isNavigationLink(href)) return;

            e.preventDefault();
            navigate(href);
        });
    }

    /**
     * Check if a link is a valid navigation target
     */
    function isNavigationLink(href) {
        // Skip anchor links, downloads, external links, etc.
        if (href.startsWith('#')) return false;
        if (href.startsWith('javascript:')) return false;
        if (href.includes('://') && !href.includes(window.location.origin)) return false;
        if (href.includes('download')) return false;
        return true;
    }

    /**
     * Navigate to a new page via AJAX
     */
    function navigate(url) {
        if (isLoading || url === currentUrl) return;

        isLoading = true;
        const main = document.querySelector(MAIN_SELECTOR);
        
        // Show loading state
        main?.classList.add('opacity-50', 'pointer-events-none');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.text();
        })
        .then(html => {
            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector(MAIN_SELECTOR)?.innerHTML;

            if (!newContent) throw new Error('No main content found');

            // Update content
            main.innerHTML = newContent;
            main?.classList.remove('opacity-50', 'pointer-events-none');

            // Update URL without full reload
            window.history.pushState({ url }, '', url);
            currentUrl = url;

            // Scroll to top
            window.scrollTo(0, 0);

            // Reinitialize content
            reinitializeContent();

            isLoading = false;
        })
        .catch(error => {
            console.error('[Smooth Nav] Navigation error:', error);
            main?.classList.remove('opacity-50', 'pointer-events-none');
            isLoading = false;
            // Fallback to full page load
            window.location.href = url;
        });
    }

    /**
     * Handle browser back/forward buttons
     */
    function handlePopState() {
        window.addEventListener('popstate', (e) => {
            const url = e.state?.url || window.location.href;
            if (url !== currentUrl) {
                navigate(url);
            }
        });
    }

    /**
     * Reinitialize complex content after AJAX load
     * Trigger custom events for maps, charts, forms to reinitialize
     */
    function reinitializeContent() {
        // Dispatch event for Alpine.js components to reinitialize
        window.dispatchEvent(new CustomEvent('content-loaded'));

        // Re-initialize any maps (Leaflet)
        if (window.L) {
            window.dispatchEvent(new CustomEvent('reinitialize-maps'));
        }

        // Re-initialize forms
        window.dispatchEvent(new CustomEvent('reinitialize-forms'));

        // Execute any inline scripts in the loaded content
        // This allows page-specific initialization code to run
        const scripts = document.querySelectorAll('main script:not([src])');
        scripts.forEach(script => {
            try {
                // Create new script element to execute in current context
                const newScript = document.createElement('script');
                newScript.textContent = script.textContent;
                // Insert before the closing of main or just in head
                document.head.appendChild(newScript);
                // Clean up
                document.head.removeChild(newScript);
            } catch (error) {
                console.error('[Smooth Nav] Error executing inline script:', error);
            }
        });

        // Re-initialize charts AFTER inline scripts have run
        // Each page defines its own window.initialize*Charts function via its inline <script>.
        // The inline script already ran above, updated window.*ChartData, and defined the function,
        // but we call it again explicitly here to be safe with async timing.
        if (window.Chart && typeof window.initializeDashboardCharts === 'function') {
            window.initializeDashboardCharts();
        }
        if (window.Chart && typeof window.initializeWelcomeCharts === 'function') {
            window.initializeWelcomeCharts();
        }
        if (window.Chart && typeof window.initializeSurveyReportCharts === 'function') {
            window.initializeSurveyReportCharts();
        }
    }

    // Expose navigation function globally for custom usage
    window.smoothNavigate = navigate;
    
    if (window.SMOOTH_NAV_DEBUG) {
        console.log('[Smooth Nav] Initialized');
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSmoothNavigation);
} else {
    initSmoothNavigation();
}
