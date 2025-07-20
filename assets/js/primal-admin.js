/**
 * PRIMAL BLACK MARKET - ADMIN DASHBOARD FUNCTIONALITY
 * Basic admin functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDashboard();
});

function initializeAdminDashboard() {
    setupTabNavigation();
    setupUserActions();
    console.log('🛡️ Primal Admin Dashboard initialized');
}

// Tab Navigation
function setupTabNavigation() {
    const tabs = document.querySelectorAll('.nav-tab');
    const sections = document.querySelectorAll('.admin-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;
            
            // Update navigation
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update content
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            const activeSection = document.getElementById(`${targetTab}-section`);
            if (activeSection) {
                activeSection.style.display = 'block';
            }
        });
    });
}

// User management actions
function setupUserActions() {
    document.addEventListener('click', function(e) {
        const userId = e.target.dataset.userId;
        
        if (e.target.classList.contains('view-user')) {
            alert(`View user details for user ID: ${userId}`);
        }
        
        if (e.target.classList.contains('ban-user')) {
            const confirmation = confirm(`Are you sure you want to ban this user?`);
            if (confirmation) {
                alert(`User ${userId} has been banned (demo)`);
            }
        }
        
        if (e.target.classList.contains('delete-user')) {
            const confirmation = confirm(`Are you sure you want to delete this user? This action cannot be undone.`);
            if (confirmation) {
                alert(`User ${userId} has been deleted (demo)`);
            }
        }
        
        if (e.target.classList.contains('edit-product-admin')) {
            alert('Edit product functionality - coming soon!');
        }
        
        if (e.target.classList.contains('toggle-product-status')) {
            alert('Toggle product status - coming soon!');
        }
        
        if (e.target.classList.contains('delete-product-admin')) {
            const confirmation = confirm('Are you sure you want to delete this product?');
            if (confirmation) {
                alert('Product deleted (demo)');
            }
        }
    });
}
