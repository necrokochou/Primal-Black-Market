/**
 * PRIMAL BLACK MARKET LOGIN PAGE
 * Enhanced JavaScript for Premium Authentication Experience
 */

document.addEventListener("DOMContentLoaded", function () {
  // ================================
  // LOGIN PAGE INITIALIZATION
  // ================================

  initializeLoginPage();

  function initializeLoginPage() {
    setupFormValidation();
    setupInputEnhancements();
    setupSecurityFeatures();
    setupAccessibilityFeatures();
    setupPerformanceOptimizations();

    console.log("🔐 Primal Black Market Login initialized");
  }

  // ================================
  // FORM VALIDATION & SUBMISSION
  // ================================

  function setupFormValidation() {
    const loginForm = document.getElementById("login-form");
    const usernameInput = document.querySelector('input[name="username"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const submitButton = document.querySelector(".primal-btn-primary");

    if (!loginForm) return;

    // Enhanced form submission
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const username = usernameInput.value.trim();
      const password = passwordInput.value;

      // Validate inputs
      if (!validateInputs(username, password)) {
        return;
      }

      // Show loading state
      showLoadingState(submitButton);

      try {
        // Simulate login process (replace with actual login logic)
        const response = await fetch("/handlers/auth.handler.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "login",
            username: username,
            password: password,
          }),
        });

        const result = await response.json();

        if (result.success) {
          showMessage("Login successful! Redirecting...", "success");
          setTimeout(() => {
            window.location.href = "/";
          }, 1500);
        } else {
          window.trackFailedAttempt?.();
          showMessage(
            result.error || "Login failed. Please try again.",
            "error"
          );
          shakeForm();
        }
      } catch (error) {
        showMessage(
          error.message || "Login failed. Please try again.",
          "error"
        );
        shakeForm();
      } finally {
        hideLoadingState(submitButton);
      }
    });

    // Real-time validation
    usernameInput.addEventListener("input", function () {
      validateField(this, validateUsername(this.value));
    });

    passwordInput.addEventListener("input", function () {
      validateField(this, validatePassword(this.value));
    });
  }

  function validateInputs(username, password) {
    let isValid = true;

    if (!validateUsername(username)) {
      showMessage("Please enter a valid username (3-20 characters)", "error");
      isValid = false;
    }

    if (!validatePassword(password)) {
      showMessage("Password must be at least 6 characters long", "error");
      isValid = false;
    }

    return isValid;
  }

  function validateUsername(username) {
    const isUsername = /^[a-zA-Z0-9_]{3,20}$/.test(username);
    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(username);
    return isUsername || isEmail;
  }

  function validatePassword(password) {
    return password.length >= 6;
  }

  function validateField(input, isValid) {
    if (isValid) {
      input.style.borderColor = "var(--primal-green)";
      input.style.boxShadow = "0 0 10px rgba(101, 109, 74, 0.3)";
    } else if (input.value.length > 0) {
      input.style.borderColor = "#dc3545";
      input.style.boxShadow = "0 0 10px rgba(220, 53, 69, 0.3)";
    } else {
      input.style.borderColor = "var(--login-border)";
      input.style.boxShadow = "";
    }
  }

  // ================================
  // INPUT ENHANCEMENTS
  // ================================

  function setupInputEnhancements() {
    const inputs = document.querySelectorAll(".primal-input");

    inputs.forEach((input) => {
      // Add focus/blur animations
      input.addEventListener("focus", function () {
        this.parentElement.classList.add("input-focused");
        addInputGlow(this);
      });

      input.addEventListener("blur", function () {
        this.parentElement.classList.remove("input-focused");
        removeInputGlow(this);
      });

      // Add typing sound effects (optional)
      input.addEventListener("input", function () {
        addTypingEffect(this);
      });

      // Password visibility toggle for password fields
      if (input.type === "password") {
        addPasswordToggle(input);
      }
    });
  }

  function addInputGlow(input) {
    input.style.transition = "all 0.3s cubic-bezier(0.23, 1, 0.32, 1)";
    input.style.transform = "translateY(-2px)";
  }

  function removeInputGlow(input) {
    input.style.transform = "translateY(0)";
  }

  function addTypingEffect(input) {
    // Subtle pulse effect on typing
    input.style.boxShadow = "0 0 20px rgba(255, 140, 0, 0.4)";
    setTimeout(() => {
      input.style.boxShadow = input.matches(":focus")
        ? "0 8px 25px rgba(255, 140, 0, 0.3)"
        : "";
    }, 150);
  }

  function addPasswordToggle(passwordInput) {
    const toggleButton = document.createElement("button");
    toggleButton.type = "button";
    toggleButton.innerHTML = "👁️";
    toggleButton.className = "password-toggle";
    toggleButton.style.cssText = `
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primal-orange);
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        `;

    // Position the input container relatively
    passwordInput.parentElement.style.position = "relative";
    passwordInput.style.paddingRight = "50px";
    passwordInput.parentElement.appendChild(toggleButton);

    toggleButton.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      this.innerHTML = isPassword ? "🙈" : "👁️";
      this.style.opacity = isPassword ? "1" : "0.7";
    });

    toggleButton.addEventListener("mouseenter", function () {
      this.style.opacity = "1";
    });

    toggleButton.addEventListener("mouseleave", function () {
      this.style.opacity = passwordInput.type === "text" ? "1" : "0.7";
    });
  }

  // ================================
  // UI FEEDBACK FUNCTIONS
  // ================================

  function showMessage(message, type = "info") {
    // Remove existing messages
    const existingMessage = document.querySelector(".form-message");
    if (existingMessage) {
      existingMessage.remove();
    }

    const messageDiv = document.createElement("div");
    messageDiv.className = `form-message ${type}`;
    messageDiv.textContent = message;

    const form = document.querySelector(".primal-form");
    form.insertBefore(messageDiv, form.firstChild);

    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (messageDiv.parentElement) {
        messageDiv.style.opacity = "0";
        messageDiv.style.transform = "translateY(-10px)";
        setTimeout(() => messageDiv.remove(), 300);
      }
    }, 5000);
  }

  function showLoadingState(button) {
    button.classList.add("loading");
    button.textContent = "";
    button.disabled = true;
  }

  function hideLoadingState(button) {
    button.classList.remove("loading");
    button.textContent = "Login";
    button.disabled = false;
  }

  function shakeForm() {
    const card = document.querySelector(".primal-auth-card");
    card.style.animation = "none";
    card.offsetHeight; // Trigger reflow
    card.style.animation = "loginShake 0.5s ease-in-out";

    setTimeout(() => {
      card.style.animation = "";
    }, 500);
  }

  // Add shake animation to CSS dynamically
  const shakeStyles = `
        @keyframes loginShake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;

  const styleSheet = document.createElement("style");
  styleSheet.textContent = shakeStyles;
  document.head.appendChild(styleSheet);

  // ================================
  // SECURITY FEATURES
  // ================================

  function setupSecurityFeatures() {
    // Rate limiting
    let attemptCount = 0;
    const maxAttempts = 5;
    const lockoutTime = 5 * 60 * 1000; // 5 minutes

    // Check if user is locked out
    const lockoutEnd = localStorage.getItem("loginLockout");
    if (lockoutEnd && Date.now() < parseInt(lockoutEnd)) {
      const remainingTime = Math.ceil(
        (parseInt(lockoutEnd) - Date.now()) / 60000
      );
      showMessage(
        `Account temporarily locked. Try again in ${remainingTime} minutes.`,
        "error"
      );
      disableForm();
    }

    // Track failed attempts
    window.trackFailedAttempt = function () {
      attemptCount++;
      if (attemptCount >= maxAttempts) {
        localStorage.setItem(
          "loginLockout",
          (Date.now() + lockoutTime).toString()
        );
        showMessage(
          "Too many failed attempts. Account locked for 5 minutes.",
          "error"
        );
        disableForm();
      }
    };

    // CSRF protection placeholder
    const csrfToken = generateCSRFToken();
    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "csrf_token";
    csrfInput.value = csrfToken;
    document.getElementById("login-form").appendChild(csrfInput);
  }

  function generateCSRFToken() {
    return Math.random().toString(36).substring(2) + Date.now().toString(36);
  }

  function disableForm() {
    const inputs = document.querySelectorAll(".primal-input");
    const button = document.querySelector(".primal-btn-primary");

    inputs.forEach((input) => (input.disabled = true));
    button.disabled = true;
  }

  // ================================
  // ACCESSIBILITY FEATURES
  // ================================

  function setupAccessibilityFeatures() {
    // Keyboard navigation enhancements
    const inputs = document.querySelectorAll(".primal-input");
    const button = document.querySelector(".primal-btn-primary");

    inputs.forEach((input, index) => {
      input.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          if (index < inputs.length - 1) {
            inputs[index + 1].focus();
          } else {
            button.click();
          }
        }
      });
    });

    // Screen reader announcements
    const form = document.getElementById("login-form");
    form.setAttribute("aria-label", "Login form");

    // Add ARIA labels
    inputs.forEach((input) => {
      const placeholder = input.getAttribute("placeholder");
      input.setAttribute("aria-label", placeholder);
    });
  }

  // ================================
  // PERFORMANCE OPTIMIZATIONS
  // ================================

  function setupPerformanceOptimizations() {
    // Debounce input validation
    const debounce = (func, wait) => {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    };

    // Apply debounced validation
    const debouncedValidation = debounce((input) => {
      if (input.name === "username") {
        validateField(input, validateUsername(input.value));
      } else if (input.name === "password") {
        validateField(input, validatePassword(input.value));
      }
    }, 300);

    document.querySelectorAll(".primal-input").forEach((input) => {
      input.addEventListener("input", () => debouncedValidation(input));
    });
  }

  // ================================
  // THEME INTEGRATION
  // ================================

  // Apply dark theme if user preference is set
  if (
    localStorage.getItem("theme") === "dark" ||
    (!localStorage.getItem("theme") &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    document.body.classList.add("login-dark-theme");
  }

  // ================================
  // LOGIN PAGE ANALYTICS
  // ================================

  // Track login page interactions (for analytics)
  const analytics = {
    pageLoad: Date.now(),
    interactions: 0,

    track(event) {
      this.interactions++;
      console.log(
        `Login Analytics: ${event} (${this.interactions} total interactions)`
      );
    },
  };

  document.addEventListener("click", () => analytics.track("click"));
  document.addEventListener("input", () => analytics.track("input"));

  // Log session info for debugging
  console.log("🎯 Login Page Analytics:", {
    userAgent: navigator.userAgent,
    timestamp: new Date().toISOString(),
    viewport: `${window.innerWidth}x${window.innerHeight}`,
    colorScheme: window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light",
  });
});