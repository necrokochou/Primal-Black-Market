/**
 * PRIMAL BLACK MARKET - ADMIN DASHBOARD FUNCTIONALITY
 * Enhanced admin functionality with primal theme integration
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeAdminDashboard();
});

function initializeAdminDashboard() {
  setupTabNavigation();
  setupUserActions();
  setupSearchAndFilters();
  setupAnimations();
  console.log("🛡️ Primal Admin Dashboard initialized");
}

// Tab Navigation with smooth transitions
function setupTabNavigation() {
  const tabs = document.querySelectorAll(".nav-tab");
  const sections = document.querySelectorAll(".admin-section");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const targetTab = tab.dataset.tab;

      // Update navigation with animation
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      // Update content with fade effect
      sections.forEach((section) => {
        section.style.opacity = "0";
        setTimeout(() => {
          section.style.display = "none";
        }, 200);
      });

      setTimeout(() => {
        const activeSection = document.getElementById(`${targetTab}-section`);
        if (activeSection) {
          activeSection.style.display = "block";
          setTimeout(() => {
            activeSection.style.opacity = "1";
          }, 50);
        }
      }, 200);
    });
  });
}

// Enhanced user management actions
function setupUserActions() {
  document.addEventListener("click", function (e) {
    const userId = e.target.dataset.userId;
    const userRow = e.target.closest("tr");

    if (e.target.classList.contains("view-user")) {
      showUserModal(userId, userRow);
    }

    if (e.target.classList.contains("ban-user")) {
      banUser(userId, userRow);
    }

    if (e.target.classList.contains("delete-user")) {
      deleteUser(userId, userRow);
    }

    if (e.target.classList.contains("edit-product-admin")) {
      editProduct(e.target.dataset.productId);
    }

    if (e.target.classList.contains("toggle-product-status")) {
      toggleProductStatus(e.target.dataset.productId, e.target);
    }

    if (e.target.classList.contains("delete-product-admin")) {
      deleteProduct(
        e.target.dataset.productId,
        e.target.closest(".admin-product-item")
      );
    }
  });
}

// Search and filter functionality
function setupSearchAndFilters() {
  const userSearch = document.getElementById("user-search");
  const userStatusFilter = document.getElementById("user-status-filter");
  const userRoleFilter = document.getElementById("user-role-filter");
  const productSearch = document.getElementById("product-search");
  const productCategoryFilter = document.getElementById(
    "product-category-filter"
  );

  if (userSearch) {
    userSearch.addEventListener("input", debounce(filterUsers, 300));
  }

  if (userStatusFilter) {
    userStatusFilter.addEventListener("change", filterUsers);
  }

  if (userRoleFilter) {
    userRoleFilter.addEventListener("change", filterUsers);
  }

  if (productSearch) {
    productSearch.addEventListener("input", debounce(filterProducts, 300));
  }

  if (productCategoryFilter) {
    productCategoryFilter.addEventListener("change", filterProducts);
  }
}

// Setup smooth animations
function setupAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.animation = "fadeInUp 0.6s ease-out forwards";
      }
    });
  }, observerOptions);

  document
    .querySelectorAll(".admin-product-item, .analytics-card")
    .forEach((el) => {
      observer.observe(el);
    });
}

// User action functions
function showUserModal(userId, userRow) {
  const userName = userRow.querySelector(".user-name").textContent;
  const userEmail = userRow.querySelector(".user-email").textContent;
  const userAlias = userRow.querySelector(".user-alias").textContent;

  alert(
    `User Details:\n\nName: ${userName}\nEmail: ${userEmail}\n${userAlias}\n\n(Full modal implementation coming soon!)`
  );
}

function banUser(userId, userRow) {
  const userName = userRow.querySelector(".user-name").textContent;
  const confirmation = confirm(`Are you sure you want to ban ${userName}?`);

  if (confirmation) {
    userRow.style.opacity = "0.5";
    userRow.style.background = "rgba(220, 53, 69, 0.1)";
    const statusBadge = userRow.querySelector(".status-badge");
    if (statusBadge) {
      statusBadge.textContent = "Banned";
      statusBadge.className = "status-badge banned";
      statusBadge.style.background =
        "linear-gradient(135deg, #dc3545, #c82333)";
    }
    showNotification(`${userName} has been banned`, "success");
  }
}

function deleteUser(userId, userRow) {
  const userName = userRow.querySelector(".user-name").textContent;
  const confirmation = confirm(
    `Are you sure you want to delete ${userName}? This action cannot be undone.`
  );

  if (confirmation) {
    userRow.style.transition = "all 0.5s ease";
    userRow.style.opacity = "0";
    userRow.style.transform = "translateX(-100%)";

    setTimeout(() => {
      userRow.remove();
      showNotification(`${userName} has been deleted`, "success");
    }, 500);
  }
}

function editProduct(productId) {
  showNotification("Edit product functionality - coming soon!", "info");
}

function toggleProductStatus(productId, button) {
  const productItem = button.closest(".admin-product-item");
  const statusIndicator = productItem.querySelector(
    ".product-status-indicator"
  );
  const icon = button.querySelector("i");

  if (statusIndicator.classList.contains("active")) {
    statusIndicator.classList.remove("active");
    statusIndicator.classList.add("inactive");
    icon.classList.remove("fa-pause");
    icon.classList.add("fa-play");
    showNotification("Product deactivated", "info");
  } else {
    statusIndicator.classList.remove("inactive");
    statusIndicator.classList.add("active");
    icon.classList.remove("fa-play");
    icon.classList.add("fa-pause");
    showNotification("Product activated", "success");
  }
}

function deleteProduct(productId, productItem) {
  const productName = productItem.querySelector("h3").textContent;
  const confirmation = confirm(
    `Are you sure you want to delete "${productName}"?`
  );

  if (confirmation) {
    productItem.style.transition = "all 0.5s ease";
    productItem.style.opacity = "0";
    productItem.style.transform = "scale(0.8)";

    setTimeout(() => {
      productItem.remove();
      showNotification(`"${productName}" has been deleted`, "success");
    }, 500);
  }
}

// Filter functions
function filterUsers() {
  const searchTerm =
    document.getElementById("user-search")?.value.toLowerCase() || "";
  const statusFilter =
    document.getElementById("user-status-filter")?.value || "all";
  const roleFilter =
    document.getElementById("user-role-filter")?.value || "all";

  const userRows = document.querySelectorAll(".admin-table tbody tr");

  userRows.forEach((row) => {
    const userName =
      row.querySelector(".user-name")?.textContent.toLowerCase() || "";
    const userEmail =
      row.querySelector(".user-email")?.textContent.toLowerCase() || "";
    const userRole =
      row.querySelector(".role-badge")?.textContent.toLowerCase() || "";
    const userStatus =
      row.querySelector(".status-badge")?.textContent.toLowerCase() || "";

    const matchesSearch =
      userName.includes(searchTerm) || userEmail.includes(searchTerm);
    const matchesStatus =
      statusFilter === "all" || userStatus.includes(statusFilter);
    const matchesRole = roleFilter === "all" || userRole.includes(roleFilter);

    row.style.display =
      matchesSearch && matchesStatus && matchesRole ? "" : "none";
  });
}

function filterProducts() {
  const searchTerm =
    document.getElementById("product-search")?.value.toLowerCase() || "";
  const categoryFilter =
    document.getElementById("product-category-filter")?.value || "all";

  const productItems = document.querySelectorAll(".admin-product-item");

  productItems.forEach((item) => {
    const productName =
      item.querySelector("h3")?.textContent.toLowerCase() || "";
    const productCategory =
      item.querySelector(".product-category")?.textContent.toLowerCase() || "";

    const matchesSearch = productName.includes(searchTerm);
    const matchesCategory =
      categoryFilter === "all" || productCategory.includes(categoryFilter);

    item.style.display = matchesSearch && matchesCategory ? "" : "none";
  });
}

// Utility functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `admin-notification ${type}`;
  notification.innerHTML = `
    <i class="fas fa-${
      type === "success"
        ? "check-circle"
        : type === "error"
        ? "exclamation-circle"
        : "info-circle"
    }"></i>
    <span>${message}</span>
  `;

  Object.assign(notification.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    padding: "1rem 1.5rem",
    background:
      type === "success"
        ? "rgba(40, 167, 69, 0.9)"
        : type === "error"
        ? "rgba(220, 53, 69, 0.9)"
        : "rgba(255, 140, 0, 0.9)",
    color: "white",
    borderRadius: "10px",
    zIndex: "10000",
    display: "flex",
    alignItems: "center",
    gap: "0.5rem",
    fontFamily: "Cinzel, serif",
    fontWeight: "600",
    boxShadow: "0 4px 15px rgba(0, 0, 0, 0.3)",
    transform: "translateX(100%)",
    transition: "transform 0.3s ease",
  });

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.transform = "translateX(0)";
  }, 100);

  setTimeout(() => {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}
