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
});
