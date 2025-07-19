/**
 * PRIMAL BLACK MARKET REGISTER PAGE
 * Enhanced Registration Functionality with Security & UX Features
 */

class PrimalRegister {
  constructor() {
    this.form = document.getElementById("register-form");
    this.inputs = document.querySelectorAll(".primal-input");
    this.submitBtn = document.querySelector(".primal-btn-primary");
    this.passwordInput = document.querySelector('input[name="password"]');
    this.confirmPasswordInput = document.querySelector(
      'input[name="confirm_password"]'
    );

    // Security and validation settings
    this.validationRules = {
      username: {
        minLength: 3,
        maxLength: 20,
        pattern: /^[a-zA-Z0-9_-]+$/,
        message: "Username must be 3-20 characters, alphanumeric with - or _",
      },
      email: {
        pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        message: "Please enter a valid email address",
      },
      password: {
        minLength: 8,
        patterns: {
          uppercase: /[A-Z]/,
          lowercase: /[a-z]/,
          numbers: /\d/,
          special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/,
        },
        message:
          "Password must be at least 8 characters with uppercase, lowercase, number, and special character",
      },
    };

    this.isSubmitting = false;
    this.passwordStrength = 0;

    this.init();
  }

  init() {
    this.setupEventListeners();
    this.createPasswordStrengthIndicator();
    this.createInputLabels();
    this.createValidationMessages();
    this.createFeatureHighlights();
    this.setupAccessibility();
    this.loadSavedData();

    // Analytics tracking
    this.trackPageView();

    console.log("🏛️ Primal Black Market Registration System Initialized");
  }

  setupEventListeners() {
    if (this.form) {
      this.form.addEventListener("submit", this.handleSubmit.bind(this));
    }

    this.inputs.forEach((input) => {
      input.addEventListener("input", this.handleInputChange.bind(this, input));
      input.addEventListener("blur", this.handleInputBlur.bind(this, input));
      input.addEventListener("focus", this.handleInputFocus.bind(this, input));
      input.addEventListener("keydown", this.handleKeyDown.bind(this, input));
    });

    if (this.passwordInput) {
      this.passwordInput.addEventListener(
        "input",
        this.updatePasswordStrength.bind(this)
      );
    }

    if (this.confirmPasswordInput) {
      this.confirmPasswordInput.addEventListener(
        "input",
        this.validatePasswordMatch.bind(this)
      );
    }

    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
      emailInput.addEventListener(
        "input",
        this.debounce(this.checkEmailAvailability.bind(this), 500)
      );
    }

    this.setupSecurityFeatures();
    this.setupKeyboardNavigation();
  }

  handleSubmit(e) {
    e.preventDefault();

    if (this.isSubmitting) return;

    this.trackEvent("registration_attempt");

    if (!this.validateAllFields()) {
      this.showErrorMessage("Please correct the errors above");
      this.trackEvent("registration_validation_failed");
      return;
    }

    if (this.passwordStrength < 3) {
      this.showErrorMessage("Please choose a stronger password");
      this.trackEvent("registration_weak_password");
      return;
    }

    this.submitRegistration();
  }

  async submitRegistration() {
    this.isSubmitting = true;
    this.setButtonLoading(true);

    try {
      const formData = new FormData(this.form);
      formData.append("action", "register"); // required by backend

      const response = await fetch("/handlers/auth.handler.php", {
        method: "POST",
        body: formData,
      });

      const text = await response.text();
      console.log("📦 Raw Response:", text);

      const result = JSON.parse(text);

      if (result.success) {
        this.handleRegistrationSuccess();
      } else {
        throw new Error(result.error || "Registration failed");
      }
    } catch (error) {
      console.error("Registration error:", error);
      this.handleRegistrationError(error);
    } finally {
      this.isSubmitting = false;
      this.setButtonLoading(false);
    }
  }

  handleRegistrationSuccess() {
    this.trackEvent("registration_success");

    this.showSuccessMessage(
      "Registration successful! Welcome to Primal Black Market"
    );

    const userData = {
      username: document.querySelector('input[name="username"]').value,
      email: document.querySelector('input[name="email"]').value,
      registrationDate: new Date().toISOString(),
    };

    localStorage.setItem("primal_user_data", JSON.stringify(userData));

    setTimeout(() => {
      window.location.href = "/pages/login/index.php?registered=true";
    }, 2000);
  }

  handleRegistrationError(error) {
    this.trackEvent("registration_error", { error: error.message });
    this.showErrorMessage(
      error.message || "Registration failed. Please try again."
    );
  }

  validateAllFields() {
    let isValid = true;

    this.inputs.forEach((input) => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    if (this.confirmPasswordInput && !this.validatePasswordMatch()) {
      isValid = false;
    }

    return isValid;
  }

  validateField(input) {
    const name = input.name;
    const value = input.value.trim();
    const rules = this.validationRules[name];

    if (!rules) return true;

    if (!value) {
      this.showFieldError(input, `${this.capitalize(name)} is required`);
      return false;
    }

    if (rules.minLength && value.length < rules.minLength) {
      this.showFieldError(
        input,
        `${this.capitalize(name)} must be at least ${
          rules.minLength
        } characters`
      );
      return false;
    }

    if (rules.maxLength && value.length > rules.maxLength) {
      this.showFieldError(
        input,
        `${this.capitalize(name)} must be no more than ${
          rules.maxLength
        } characters`
      );
      return false;
    }

    if (rules.pattern && !rules.pattern.test(value)) {
      this.showFieldError(input, rules.message);
      return false;
    }

    if (name === "password") {
      return this.validatePassword(input, value);
    }

    this.showFieldSuccess(input);
    return true;
  }

  validatePassword(input, password) {
    const rules = this.validationRules.password;
    const patterns = rules.patterns;

    const checks = {
      length: password.length >= rules.minLength,
      uppercase: patterns.uppercase.test(password),
      lowercase: patterns.lowercase.test(password),
      numbers: patterns.numbers.test(password),
      special: patterns.special.test(password),
    };

    const passed = Object.values(checks).filter(Boolean).length;

    if (passed < 4) {
      this.showFieldError(input, rules.message);
      return false;
    }

    this.showFieldSuccess(input);
    return true;
  }

  validatePasswordMatch() {
    if (!this.confirmPasswordInput) return true;

    const password = this.passwordInput.value;
    const confirmPassword = this.confirmPasswordInput.value;

    if (password !== confirmPassword) {
      this.showFieldError(this.confirmPasswordInput, "Passwords do not match");
      return false;
    }

    this.showFieldSuccess(this.confirmPasswordInput);
    return true;
  }

  updatePasswordStrength() {
    const password = this.passwordInput.value;
    const strength = this.calculatePasswordStrength(password);

    this.passwordStrength = strength.score;
    this.updatePasswordStrengthUI(strength);
  }

  calculatePasswordStrength(password) {
    let score = 0;
    let feedback = [];

    if (password.length >= 8) score++;
    else feedback.push("Use at least 8 characters");

    if (/[A-Z]/.test(password)) score++;
    else feedback.push("Add uppercase letters");

    if (/[a-z]/.test(password)) score++;
    else feedback.push("Add lowercase letters");

    if (/\d/.test(password)) score++;
    else feedback.push("Add numbers");

    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;
    else feedback.push("Add special characters");

    if (password.length >= 12) score++;
    if (password.length >= 16) score++;

    const levels = [
      "Very Weak",
      "Weak",
      "Fair",
      "Good",
      "Strong",
      "Very Strong",
    ];
    const colors = [
      "#e74c3c",
      "#e74c3c",
      "#f39c12",
      "#FF8C00",
      "#656D4A",
      "#656D4A",
    ];

    return {
      score: Math.min(score, 5),
      level: levels[Math.min(score, 5)],
      color: colors[Math.min(score, 5)],
      feedback,
    };
  }

  updatePasswordStrengthUI(strength) {
    const strengthBar = document.querySelector(".strength-fill");
    const strengthText = document.querySelector(".strength-text");

    if (strengthBar && strengthText) {
      const percentage = (strength.score / 5) * 100;

      strengthBar.style.width = percentage + "%";
      strengthBar.style.backgroundColor = strength.color;
      strengthBar.className = `strength-fill ${strength.level
        .toLowerCase()
        .replace(" ", "-")}`;

      strengthText.textContent = `Password Strength: ${strength.level}`;
      strengthText.style.color = strength.color;
    }
  }

  async checkEmailAvailability(email) {
    if (!this.validationRules.email.pattern.test(email)) return;

    try {
      await new Promise((resolve) => setTimeout(resolve, 300));
      const isAvailable = Math.random() > 0.3;

      const emailInput = document.querySelector('input[name="email"]');
      if (isAvailable) {
        this.showFieldSuccess(emailInput, "Email is available");
      } else {
        this.showFieldError(emailInput, "Email is already registered");
      }
    } catch (error) {
      console.error("Email check failed:", error);
    }
  }

  handleInputChange(input) {
    const inputGroup = input.closest(".input-group");
    if (inputGroup) {
      if (input.value.trim()) {
        inputGroup.classList.add("has-value");
      } else {
        inputGroup.classList.remove("has-value");
      }
    }

    if (input.value.trim()) {
      this.debounce(() => this.validateField(input), 300)();
    }
  }

  handleInputBlur(input) {
    this.validateField(input);
    this.trackEvent("field_completed", { field: input.name });
  }

  handleInputFocus(input) {
    this.clearFieldMessage(input);
    this.trackEvent("field_focused", { field: input.name });
  }

  handleKeyDown(input, e) {
    if (e.key === "Enter" && input.type !== "submit") {
      e.preventDefault();
      const nextInput = this.getNextInput(input);
      if (nextInput) {
        nextInput.focus();
      } else {
        this.form.querySelector('button[type="submit"]').focus();
      }
    }
  }

  createPasswordStrengthIndicator() {
    if (!this.passwordInput) return;

    const strengthHTML = `
      <div class="password-strength">
        <div class="strength-bar">
          <div class="strength-fill"></div>
        </div>
        <div class="strength-text">Password Strength: Not Set</div>
      </div>
    `;

    this.passwordInput.insertAdjacentHTML("afterend", strengthHTML);
  }

  createInputLabels() {
    this.inputs.forEach((input) => {
      const inputGroup = input.closest(".input-group") || input.parentElement;

      if (!inputGroup.querySelector(".input-label")) {
        const label = document.createElement("div");
        label.className = "input-label";
        label.textContent = this.capitalize(input.name.replace("_", " "));
        inputGroup.appendChild(label);
      }
    });
  }

  createValidationMessages() {
    this.inputs.forEach((input) => {
      const inputGroup = input.closest(".input-group") || input.parentElement;

      if (!inputGroup.querySelector(".validation-message")) {
        const message = document.createElement("div");
        message.className = "validation-message";
        inputGroup.appendChild(message);
      }
    });
  }

  createFeatureHighlights() {
    const form = document.querySelector(".primal-form");
    if (!form || form.querySelector(".register-features")) return;

    const featuresHTML = `
      <div class="register-features">
        <div class="feature-item"><i class="fas fa-shield-alt"></i><span>Secure Encryption</span></div>
        <div class="feature-item"><i class="fas fa-user-check"></i><span>Verified Account</span></div>
        <div class="feature-item"><i class="fas fa-crown"></i><span>Premium Access</span></div>
      </div>
    `;

    form.insertAdjacentHTML("afterend", featuresHTML);
  }

  setupSecurityFeatures() {
    if (this.passwordInput) {
      this.passwordInput.addEventListener("contextmenu", (e) =>
        e.preventDefault()
      );
    }

    let rapidSubmissionCount = 0;
    const resetRapidSubmission = () => (rapidSubmissionCount = 0);

    if (this.form) {
      this.form.addEventListener("submit", () => {
        rapidSubmissionCount++;
        if (rapidSubmissionCount > 3) {
          this.showErrorMessage(
            "Too many attempts. Please wait before trying again."
          );
          setTimeout(resetRapidSubmission, 60000);
          return false;
        }
        setTimeout(resetRapidSubmission, 10000);
      });
    }
  }

  setupAccessibility() {
    this.inputs.forEach((input) => {
      if (!input.getAttribute("aria-label")) {
        input.setAttribute(
          "aria-label",
          this.capitalize(input.name.replace("_", " "))
        );
      }

      if (
        input.hasAttribute("required") &&
        !input.getAttribute("aria-required")
      ) {
        input.setAttribute("aria-required", "true");
      }
    });

    if (this.submitBtn) {
      this.submitBtn.setAttribute(
        "aria-describedby",
        "register-form-description"
      );
    }
  }

  setupKeyboardNavigation() {
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        this.clearForm();
      }
    });
  }

  loadSavedData() {
    const savedData = localStorage.getItem("primal_register_draft");
    if (savedData) {
      try {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach((key) => {
          if (key !== "password" && key !== "confirm_password") {
            const input = document.querySelector(`input[name="${key}"]`);
            if (input) {
              input.value = data[key];
              this.handleInputChange(input);
            }
          }
        });
      } catch (error) {
        console.error("Failed to load saved data:", error);
      }
    }
  }

  saveDraftData() {
    const formData = new FormData(this.form);
    const data = Object.fromEntries(formData);

    delete data.password;
    delete data.confirm_password;

    localStorage.setItem("primal_register_draft", JSON.stringify(data));
  }

  showFieldError(input, message) {
    input.classList.add("invalid");
    input.classList.remove("valid");
    this.showFieldMessage(input, message, "error");
  }

  showFieldSuccess(input, message = "") {
    input.classList.add("valid");
    input.classList.remove("invalid");
    if (message) {
      this.showFieldMessage(input, message, "success");
    }
  }

  showFieldMessage(input, message, type) {
    const inputGroup = input.closest(".input-group") || input.parentElement;
    const messageEl = inputGroup.querySelector(".validation-message");

    if (messageEl) {
      messageEl.textContent = message;
      messageEl.className = `validation-message ${type} show`;
    }
  }

  clearFieldMessage(input) {
    const inputGroup = input.closest(".input-group") || input.parentElement;
    const messageEl = inputGroup.querySelector(".validation-message");

    if (messageEl) {
      messageEl.classList.remove("show");
    }
  }

  showErrorMessage(message) {
    this.showNotification(message, "error");
  }

  showSuccessMessage(message) {
    this.showNotification(message, "success");
  }

  showNotification(message, type) {
    const notification = document.createElement("div");
    notification.className = `primal-notification ${type}`;
    notification.innerHTML = `
      <i class="fas fa-${
        type === "success" ? "check-circle" : "exclamation-circle"
      }"></i>
      <span>${message}</span>
    `;

    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === "success" ? "var(--primal-green)" : "#e74c3c"};
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      z-index: 10000;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-family: 'Inter', sans-serif;
      font-weight: 500;
      transform: translateX(100%);
      transition: transform 0.3s ease;
    `;

    document.body.appendChild(notification);
    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);
    setTimeout(() => {
      notification.style.transform = "translateX(100%)";
      setTimeout(() => notification.remove(), 300);
    }, 4000);
  }

  setButtonLoading(loading) {
    if (loading) {
      this.submitBtn.classList.add("loading");
      this.submitBtn.disabled = true;
      this.submitBtn.textContent = "";
    } else {
      this.submitBtn.classList.remove("loading");
      this.submitBtn.disabled = false;
      this.submitBtn.textContent = "Create Account";
    }
  }

  clearForm() {
    this.form.reset();
    this.inputs.forEach((input) => {
      input.classList.remove("valid", "invalid");
      this.clearFieldMessage(input);
      const inputGroup = input.closest(".input-group");
      if (inputGroup) {
        inputGroup.classList.remove("has-value");
      }
    });

    this.passwordStrength = 0;
    this.updatePasswordStrengthUI({
      score: 0,
      level: "Not Set",
      color: "#ccc",
      feedback: [],
    });
  }

  getNextInput(input) {
    const index = Array.from(this.inputs).indexOf(input);
    return this.inputs[index + 1];
  }

  capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  debounce(func, delay) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
    };
  }

  trackPageView() {
    console.log("📊 Tracked: Registration page view");
  }

  trackEvent(eventName, data = {}) {
    console.log(`📈 Event: ${eventName}`, data);
  }
}

// Initialize registration logic when DOM is ready
document.addEventListener("DOMContentLoaded", () => new PrimalRegister());
