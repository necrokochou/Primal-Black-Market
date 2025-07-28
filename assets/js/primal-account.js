/**
 * PRIMAL BLACK MARKET - USER ACCOUNT FUNCTIONALITY
 * Includes tab navigation, logout, and account settings (alias, email, password, delete)
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeAccount();
});

function initializeAccount() {
  setupTabNavigation();
  setupLogout();
  setupAccountSettings();
  console.log("🔐 Primal Account initialized");
}

// ===============================
// Tab Navigation
// ===============================
function setupTabNavigation() {
  const tabs = document.querySelectorAll(".nav-tab");
  const sections = document.querySelectorAll(".tab-content");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const targetTab = tab.dataset.tab;

      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

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

// ===============================
// Logout
// ===============================
function setupLogout() {
  const logoutBtn = document.getElementById("logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      window.location.href = "/handlers/logout.handler.php";
    });
  }
}

// ===============================
// Account Settings
// ===============================
function setupAccountSettings() {
  const aliasForm = document.getElementById("alias-form");
  const emailForm = document.getElementById("email-form");
  const passwordForm = document.getElementById("password-form");
  const deleteForm = document.getElementById("delete-account-form");
  const deleteBtn = document.getElementById("delete-account-btn");

  if (aliasForm) {
    aliasForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const aliasInput = aliasForm.querySelector("#new-alias");
      const alias = aliasInput ? aliasInput.value.trim() : "";
      if (!alias) return alert("Alias cannot be empty.");
      updateAccountSetting("alias", { alias });
    });
  }

  if (emailForm) {
    emailForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const emailInput = emailForm.querySelector("input[type='email']");
      const email = emailInput ? emailInput.value.trim() : "";
      if (!email || !validateEmail(email)) return alert("Enter a valid email.");
      updateAccountSetting("email", { email });
    });
  }

  if (passwordForm) {
    passwordForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const inputs = passwordForm.querySelectorAll("input[type='password']");
      const current = inputs[0]?.value || "";
      const newPass = inputs[1]?.value || "";
      const confirm = inputs[2]?.value || "";

      if (!current || !newPass || !confirm)
        return alert("Fill in all password fields.");
      if (newPass !== confirm) return alert("New passwords do not match.");

      updateAccountSetting("password", {
        current_password: current,
        new_password: newPass,
      });
    });
  }

  if (deleteBtn && deleteForm) {
    deleteBtn.addEventListener("click", function () {
      const confirmed = confirm(
        "This action is irreversible. Delete your account?"
      );
      if (confirmed) {
        updateAccountSetting("delete", {});
      }
    });
  }
}

function updateAccountSetting(settingType, data) {
  fetch("/handlers/account.handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      settingType,
      ...data,
    }),
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        alert(response.message || "Update successful.");
        if (settingType === "delete") {
          window.location.href = "/pages/login/index.php";
        } else {
          window.location.reload();
        }
      } else {
        alert(response.message || "Update failed.");
      }
    })
    .catch(() => {
      alert("Request failed. Please try again.");
    });
}

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ===============================
// Product Buttons Placeholder
// ===============================
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
  openProductModal();
}

function openProductModal(product = {}) {
  // Prevent duplicate modals
  const existing = document.getElementById("product-modal");
  if (existing) existing.remove();

  const isEdit = !!product.id;

  const modal = document.createElement("div");
  modal.id = "product-modal";
  modal.innerHTML = `
    <div class="pm-backdrop"></div>
    <div class="pm-modal">
      <header class="pm-header">
        <h2>${isEdit ? "Edit" : "Add"} Product</h2>
        <button class="pm-close" aria-label="Close">&times;</button>
      </header>

      <form id="product-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="${isEdit ? "update" : "create"}" />
        ${isEdit ? `<input type="hidden" name="id" value="${product.id}">` : ""}

        <div class="pm-group">
          <label>Title <span>*</span></label>
          <input type="text" name="title" required value="${escapeHtml(product.title || "")}">
        </div>

        <div class="pm-group">
          <label>Description <span>*</span></label>
          <textarea name="description" rows="4" required>${escapeHtml(product.description || "")}</textarea>
        </div>

        <div class="pm-flex">
          <div class="pm-group">
            <label>Price (₱) <span>*</span></label>
            <input type="number" name="price" min="0" step="0.01" required value="${product.price ?? ""}">
          </div>

          <div class="pm-group">
          <label>Stock <span>*</span></label>
            <input type="number" name="stock" min="0" step="1" required value="${product.stock ?? ""}">
          </div>
        </div>

        <div class="pm-flex">
          <div class="pm-group">
            <label>Category <span>*</span></label>
            <select name="category" required>
              ${renderCategoryOptions(product.category)}
            </select>
          </div>

          <div class="pm-group">
            <label>Status</label>
            <select name="status">
              <option value="active" ${product.status === "active" ? "selected" : ""}>Active</option>
              <option value="inactive" ${product.status === "inactive" ? "selected" : ""}>Inactive</option>
            </select>
          </div>
        </div>

        <div class="pm-group">
          <label>Images ${isEdit ? "(leave blank to keep existing)" : "<span>*</span>"}</label>
          <input type="file" name="images[]" accept="image/*" ${isEdit ? "" : "required"} multiple>
          <small>Up to 5 images, JPG/PNG/WebP, max 3MB each.</small>
          <div id="pm-image-preview" class="pm-image-preview"></div>
        </div>

        <footer class="pm-footer">
          <button type="button" class="pm-cancel">Cancel</button>
          <button type="submit" class="pm-submit">${isEdit ? "Save Changes" : "Create Product"}</button>
        </footer>
      </form>
    </div>
  `;

  injectProductModalStylesOnce();
  document.body.appendChild(modal);

  // Events
  const form = modal.querySelector("#product-form");
  const closeBtn = modal.querySelector(".pm-close");
  const cancelBtn = modal.querySelector(".pm-cancel");
  const backdrop = modal.querySelector(".pm-backdrop");
  const imageInput = form.querySelector('input[type="file"]');

  const cleanup = () => modal.remove();

  [closeBtn, cancelBtn, backdrop].forEach((el) =>
    el.addEventListener("click", cleanup)
  );

  if (imageInput) {
    imageInput.addEventListener("change", () =>
      previewImages(imageInput.files, modal.querySelector("#pm-image-preview"))
    );
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Client-side validation
    const fd = new FormData(form);

    const title = fd.get("title")?.toString().trim();
    const desc = fd.get("description")?.toString().trim();
    const price = parseFloat(fd.get("price"));
    const stock = parseInt(fd.get("stock"));

    if (!title || !desc || isNaN(price) || isNaN(stock)) {
      return alert("Please fill all required fields correctly.");
    }

    // Validate images
    if (!isEdit) {
      const imgCount = imageInput?.files?.length || 0;
      if (imgCount === 0) {
        return alert("Please upload at least one product image.");
      }
    }

    if (!validateImages(imageInput?.files || [])) return;

    const PRODUCT_API = "/handlers/products.handler.php";

    try {
      const res = await fetch(PRODUCT_API, {
        method: "POST",
        body: fd,
      });

      const data = await res.json();
      if (data.success) {
        showNotification(data.message || `Product ${isEdit ? "updated" : "created"}!`, "success");
        cleanup();

        // Optimistically inject or update in list (optional)
        if (!isEdit) {
          renderNewProductInList(data.product || {
            id: data.id,
            title,
            category: fd.get("category"),
            status: fd.get("status") || "active",
          });
        } else {
          // If you maintain the list, refresh or update the DOM node here
          window.location.reload();
        }
      } else {
        alert(data.message || "Operation failed.");
      }
    } catch (err) {
      console.error(err);
      alert("Request failed. Please try again.");
    }
  });
}

function renderCategoryOptions(selected) {
  // You can fetch these dynamically; hard-coded for now
  const categories = [
    "apparel",
    "art",
    "collectibles",
    "electronics",
    "services",
    "other",
  ];
  return categories
    .map(
      (c) =>
        `<option value="${c}" ${
          selected === c ? "selected" : ""
        }>${capitalize(c)}</option>`
    )
    .join("");
}

function previewImages(files, container) {
  if (!container) return;
  container.innerHTML = "";
  [...files].forEach((file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = document.createElement("img");
      img.src = e.target.result;
      img.alt = file.name;
      container.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

function validateImages(files) {
  const maxFiles = 5;
  const maxSize = 3 * 1024 * 1024; // 3MB
  const allowed = ["image/jpeg", "image/png", "image/webp"];

  if (files.length > maxFiles) {
    alert(`You can upload up to ${maxFiles} images only.`);
    return false;
  }

  for (const f of files) {
    if (!allowed.includes(f.type)) {
      alert(`"${f.name}" has an unsupported type.`);
      return false;
    }
    if (f.size > maxSize) {
      alert(`"${f.name}" exceeds 3MB.`);
      return false;
    }
  }
  return true;
}

function renderNewProductInList(product) {
  // If you have a container list, append the new product DOM here.
  // Otherwise, just reload.
  const container = document.querySelector("#seller-products-list");
  if (!container) {
    window.location.reload();
    return;
  }

  const div = document.createElement("div");
  div.className = "seller-product-management-item";
  div.dataset.productId = product.id;

  div.innerHTML = `
    <div class="product-status-indicator ${product.status === "active" ? "active" : "inactive"}"></div>
    <h3>${escapeHtml(product.title)}</h3>
    <p class="product-category">${escapeHtml(product.category || "")}</p>
    <div class="actions">
      <button class="edit-seller-product" data-product-id="${product.id}">
        <i class="fas fa-edit"></i> Edit
      </button>
      <button class="toggle-seller-product-status" data-product-id="${product.id}">
        <i class="fas ${product.status === "active" ? "fa-pause" : "fa-play"}"></i>
        ${product.status === "active" ? "Deactivate" : "Activate"}
      </button>
      <button class="duplicate-seller-product" data-product-id="${product.id}">
        <i class="fas fa-clone"></i> Duplicate
      </button>
      <button class="delete-seller-product" data-product-id="${product.id}">
        <i class="fas fa-trash"></i> Delete
      </button>
    </div>
  `;
  container.prepend(div);
}

function injectProductModalStylesOnce() {
  if (document.getElementById("pm-styles")) return;
  const style = document.createElement("style");
  style.id = "pm-styles";
  style.textContent = `
    .pm-backdrop {
      position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(2px); z-index: 9998;
    }
    .pm-modal {
      position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
      background: #111; color: #fff; width: 95%; max-width: 720px; z-index: 9999;
      border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,.4);
      display: flex; flex-direction: column; max-height: 90vh;
    }
    .pm-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .pm-close {
      background: none; border: none; color: #fff; font-size: 1.8rem; cursor: pointer;
    }
    #product-form {
      padding: 1rem 1.25rem; overflow-y: auto;
    }
    .pm-group {
      margin-bottom: 1rem;
    }
    .pm-group label {
      display: block; font-weight: 600; margin-bottom: .35rem;
    }
    .pm-group label span {
      color: #e74c3c;
    }
    .pm-group input[type="text"],
    .pm-group input[type="number"],
    .pm-group textarea,
    .pm-group select {
      width: 100%; padding: .6rem .75rem;
      background: #1a1a1a; border: 1px solid rgba(255,255,255,.1);
      border-radius: 8px; color: #fff; outline: none;
    }
    .pm-flex {
      display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
    }
    .pm-footer {
      display: flex; justify-content: flex-end; gap: .5rem;
      padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.08);
    }
    .pm-footer button {
      padding: .6rem 1rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;
    }
    .pm-cancel {
      background: #2c2c2c; color: #fff;
    }
    .pm-submit {
      background: #50c878; color: #111;
    }
    .pm-image-preview {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: .5rem; margin-top: .5rem;
    }
    .pm-image-preview img {
      width: 100%; height: 80px; object-fit: cover; border-radius: 6px;
      border: 1px solid rgba(255,255,255,.08);
    }
  `;
  document.head.appendChild(style);
}

function escapeHtml(str) {
  return (str || "").replace(/[&<>'"]/g, (c) =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" }[c])
  );
}

function capitalize(s) {
  return s ? s[0].toUpperCase() + s.slice(1) : s;
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
