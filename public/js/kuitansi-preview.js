let currentZoom = 100;

// Apply dark mode from localStorage
function applyThemeFromStorage() {
    const theme = localStorage.getItem('theme');
    const body = document.body;
    
    if (theme === 'dark') {
        body.classList.add('dark-mode');
    } else {
        body.classList.remove('dark-mode');
    }
}

// Listen for theme changes from sidebar toggle
window.addEventListener('storage', e => {
    if (e.key === 'theme') applyThemeFromStorage();
});

// Apply theme on page load
applyThemeFromStorage();

// Zoom functionality
function zoomIn() {
    if (currentZoom < 200) {
        currentZoom += 10;
        updateZoom();
    }
}

function zoomOut() {
    if (currentZoom > 50) {
        currentZoom -= 10;
        updateZoom();
    }
}

function resetZoom() {
    currentZoom = 100;
    updateZoom();
}

function updateZoom() {
    const page = document.getElementById('previewPage');
    page.style.transform = `scale(${currentZoom / 100})`;
    document.getElementById('zoomValue').textContent = currentZoom + '%';
}

// Keyboard shortcuts
document.addEventListener('keydown', e => {
    if (e.ctrlKey || e.metaKey) {
        if (e.key === '=' || e.key === '+') {
            e.preventDefault();
            zoomIn();
        } else if (e.key === '-' || e.key === '_') {
            e.preventDefault();
            zoomOut();
        } else if (e.key === '0') {
            e.preventDefault();
            resetZoom();
        } else if (e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    }
});
