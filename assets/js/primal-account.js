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
});
