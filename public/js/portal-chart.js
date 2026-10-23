// Portal Chart Initialization
(function() {
    'use strict';
    
    // Only create chart once
    if (window.portalChartCreated) {
        return;
    }
    window.portalChartCreated = true;
    
    function initChart() {
        const canvas = document.getElementById('savingsTrendChart');
        if (!canvas) {
            console.warn('Chart canvas not found');
            return;
        }
        
        console.log('Canvas found:', canvas);
        
        // Force all parent elements to be visible and have dimensions
        let element = canvas;
        while (element && element !== document.body) {
            if (element.style) {
                element.style.display = 'block';
                element.style.visibility = 'visible';
                element.style.opacity = '1';
            }
            element = element.parentElement;
        }
        
        // Force the chart container specifically
        const chartContainer = canvas.parentElement;
        if (chartContainer) {
            chartContainer.style.height = '220px';
            chartContainer.style.width = '100%';
            chartContainer.style.display = 'block';
            chartContainer.style.minHeight = '220px';
            console.log('Forced chart container dimensions');
        }
        
        console.log('Canvas dimensions:', canvas.offsetWidth, 'x', canvas.offsetHeight);
        createChartInstance(canvas);
    }
    
    function createChartInstance(canvas) {
        // Get data from data attributes
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const balance = JSON.parse(canvas.dataset.balance || '[]');
        
        console.log('Chart labels:', labels);
        console.log('Chart balance:', balance);
        
        if (labels.length === 0 || balance.length === 0) {
            console.warn('No chart data available');
            canvas.style.display = 'none';
            return;
        }
        
        // Destroy existing chart if any
        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }
        
        // Create chart
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Balance',
                    data: balance,
                    borderColor: '#10B981',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(16,185,129,0.0)');
                        gradient.addColorStop(1, 'rgba(16,185,129,0.2)');
                        return gradient;
                    },
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#10B981',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#9CA3AF'
                        }
                    },
                    y: {
                        display: false,
                        beginAtZero: true
                    }
                }
            }
        });
        
        console.log('Portal chart created successfully');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Use 1 second delay to ensure layout is fully calculated
            setTimeout(initChart, 1000);
        });
    } else {
        // DOM already loaded, still wait 1 second
        setTimeout(initChart, 1000);
    }
})();
