/**
 * PRIMAL BLACK MARKET - USER ACCOUNT FUNCTIONALITY
 * Basic account management with logout functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeAccount();
});

function initializeAccount() {
  setupTabNavigation();
  setupLogout();
  console.log("🔐 Primal Account initialized");
}

// Tab Navigation
function setupTabNavigation() {
  const tabs = document.querySelectorAll(".nav-tab");
  const sections = document.querySelectorAll(".tab-content");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const targetTab = tab.dataset.tab;

      // Update navigation
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      // Update content
      sections.forEach((section) => {
        section.classList.remove("active");
      });

      const activeSection = document.getElementById(`${targetTab}-content`);
      if (activeSection) {
        activeSection.classList.add("active");
      }
    });
  });
}

// Logout functionality
function setupLogout() {
  const logoutBtn = document.getElementById("logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      // const confirmation = confirm('Are you sure you want to logout?');
      // if (confirmation) {
      window.location.href = "/handlers/logout.handler.php";
      // }
    });
  }
}

// Basic product management (placeholder)
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("edit-product")) {
    alert("Edit product functionality - coming soon!");
  }

  if (e.target.classList.contains("remove-product")) {
    const confirmation = confirm(
      "Are you sure you want to remove this product?"
    );
    if (confirmation) {
      alert("Remove product functionality - coming soon!");
    }
  }

  if (e.target.id === "add-product-btn") {
    alert("Add product functionality - coming soon!");
  }

  // Product Management Tab Functionality
  if (e.target.classList.contains("edit-seller-product")) {
    const productId = e.target.dataset.productId;
    editSellerProduct(productId);
  }

  if (e.target.classList.contains("toggle-seller-product-status")) {
    const productId = e.target.dataset.productId;
    toggleSellerProductStatus(productId);
  }

  if (e.target.classList.contains("duplicate-seller-product")) {
    const productId = e.target.dataset.productId;
    duplicateSellerProduct(productId);
  }

  if (e.target.classList.contains("delete-seller-product")) {
    const productId = e.target.dataset.productId;
    deleteSellerProduct(productId);
  }

  if (e.target.id === "add-new-product-btn") {
    addNewProduct();
  }
});

// Product Management Functions
function editSellerProduct(productId) {
  // Check if the seller product modal component exists
  if (typeof openProductModal === 'function') {
    // Use the existing modal if available
    openProductModal({ id: productId });
  } else {
    // Fallback alert
    alert(`Edit product functionality for product ID: ${productId} - Integration with product modal needed!`);
  }
}

function toggleSellerProductStatus(productId) {
  const confirmation = confirm('Are you sure you want to toggle the status of this product?');
  if (confirmation) {
    // Here you would typically make an API call to toggle the product status
    showNotification('Product status toggled successfully!', 'success');
    
    // Update the UI
    const productItem = document.querySelector(`[data-product-id="${productId}"]`);
    if (productItem) {
      const statusIndicator = productItem.querySelector('.product-status-indicator');
      const toggleBtn = productItem.querySelector('.toggle-seller-product-status');
      
      if (statusIndicator.classList.contains('active')) {
        statusIndicator.classList.remove('active');
        statusIndicator.classList.add('inactive');
        toggleBtn.innerHTML = '<i class="fas fa-play"></i> Activate';
      } else {
        statusIndicator.classList.remove('inactive');
        statusIndicator.classList.add('active');
        toggleBtn.innerHTML = '<i class="fas fa-pause"></i> Deactivate';
      }
    }
  }
}

function duplicateSellerProduct(productId) {
  const confirmation = confirm('Are you sure you want to create a copy of this product?');
  if (confirmation) {
    // Here you would typically make an API call to duplicate the product
    showNotification('Product duplicated successfully!', 'success');
  }
}

function deleteSellerProduct(productId) {
  const confirmation = confirm('Are you sure you want to permanently delete this product? This action cannot be undone.');
  if (confirmation) {
    // Here you would typically make an API call to delete the product
    showNotification('Product deleted successfully!', 'success');
    
    // Remove the product from the UI
    const productItem = document.querySelector(`[data-product-id="${productId}"]`);
    if (productItem) {
      productItem.style.transition = 'all 0.3s ease';
      productItem.style.opacity = '0';
      productItem.style.transform = 'scale(0.8)';
      
      setTimeout(() => {
        productItem.remove();
      }, 300);
    }
  }
}

function addNewProduct() {
  // Check if the seller product modal component exists
  if (typeof openProductModal === 'function') {
    // Use the existing modal if available
    openProductModal();
  } else {
    // Fallback alert
    alert('Add new product functionality - Integration with product modal needed!');
  }
}

// Search and Filter Functionality for Product Management
function setupProductManagementFilters() {
  const searchInput = document.getElementById('seller-product-search');
  const categoryFilter = document.getElementById('seller-product-category-filter');
  const statusFilter = document.getElementById('seller-product-status-filter');

  if (searchInput) {
    searchInput.addEventListener('input', filterProducts);
  }

  if (categoryFilter) {
    categoryFilter.addEventListener('change', filterProducts);
  }

  if (statusFilter) {
    statusFilter.addEventListener('change', filterProducts);
  }
}

function filterProducts() {
  const searchTerm = document.getElementById('seller-product-search')?.value.toLowerCase() || '';
  const categoryFilter = document.getElementById('seller-product-category-filter')?.value || 'all';
  const statusFilter = document.getElementById('seller-product-status-filter')?.value || 'all';
  
  const productItems = document.querySelectorAll('.seller-product-management-item');
  
  productItems.forEach(item => {
    const title = item.querySelector('h3')?.textContent.toLowerCase() || '';
    const category = item.querySelector('.product-category')?.textContent.toLowerCase() || '';
    const statusIndicator = item.querySelector('.product-status-indicator');
    const isActive = statusIndicator?.classList.contains('active');
    
    const matchesSearch = title.includes(searchTerm);
    const matchesCategory = categoryFilter === 'all' || category.includes(categoryFilter.replace('-', ' '));
    const matchesStatus = statusFilter === 'all' || 
                         (statusFilter === 'active' && isActive) || 
                         (statusFilter === 'inactive' && !isActive);
    
    if (matchesSearch && matchesCategory && matchesStatus) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}

// Notification function (if not already defined)
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existing = document.querySelectorAll('.notification');
  existing.forEach(n => n.remove());
  
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
    <span>${message}</span>
    <button onclick="this.parentElement.remove()">
      <i class="fas fa-times"></i>
    </button>
  `;
  
  // Add notification styles if not already defined
  if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
      .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        z-index: 10000;
        max-width: 400px;
        word-wrap: break-word;
        animation: slideInRight 0.3s ease;
      }
      
      .notification-success {
        border-left: 4px solid #50c878;
      }
      
      .notification button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        margin-left: auto;
        padding: 0.2rem;
        border-radius: 3px;
        transition: background 0.3s ease;
      }
      
      .notification button:hover {
        background: rgba(255, 255, 255, 0.1);
      }
      
      @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
    `;
    document.head.appendChild(style);
  }
  
  document.body.appendChild(notification);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 5000);
}

// Initialize product management filters when the page loads
document.addEventListener('DOMContentLoaded', function() {
  setupProductManagementFilters();
});
