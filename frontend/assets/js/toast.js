// Toast Notification System
const Toast = {
  init() {
    if (!document.querySelector(".toast-container")) {
      const container = document.createElement("div");
      container.className = "toast-container";
      document.body.appendChild(container);
    }
  },

  show(message, type = "info", duration = 3000) {
    this.init();

    const container = document.querySelector(".toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;

    // Icons
    let icon = "";
    if (type === "success")
      icon = '<i data-feather="check-circle" style="color:#10b981;"></i>';
    else if (type === "error")
      icon = '<i data-feather="alert-circle" style="color:#ef4444;"></i>';
    else if (type === "warning")
      icon = '<i data-feather="alert-triangle" style="color:#f59e0b;"></i>';
    else icon = '<i data-feather="info" style="color:#3b82f6;"></i>';

    toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        `;

    container.appendChild(toast);
    if (typeof feather !== "undefined") feather.replace();

    // Trigger animation
    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    // Auto remove
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    }, duration);
  },
};

// Global shortcuts
window.showAlert = (msg) => Toast.show(msg, "warning");
window.showSuccess = (msg) => Toast.show(msg, "success");
window.showError = (msg) => Toast.show(msg, "error");
window.showInfo = (msg) => Toast.show(msg, "info");
