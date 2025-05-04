/**
 * Main JavaScript for Cafe Website
 */

document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");

  if (hamburger) {
    hamburger.addEventListener("click", function () {
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");
    });
  }

  // Close mobile menu when clicking a nav link
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      hamburger.classList.remove("active");
      navMenu.classList.remove("active");
    });
  });

  // Menu category filtering
  const categoryFilters = document.querySelectorAll(".category-filter");
  const menuItems = document.querySelectorAll(".menu-item");

  if (categoryFilters.length > 0) {
    categoryFilters.forEach((filter) => {
      filter.addEventListener("click", function () {
        // Remove active class from all filters
        categoryFilters.forEach((item) => {
          item.classList.remove("active");
        });

        // Add active class to clicked filter
        this.classList.add("active");

        const category = this.getAttribute("data-category");

        // Show/hide menu items based on category
        menuItems.forEach((item) => {
          if (category === "all") {
            item.style.display = "block";
          } else if (item.getAttribute("data-category") === category) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      });
    });
  }

  // Contact form validation
  const contactForm = document.getElementById("contactForm");

  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      const nameInput = document.getElementById("name");
      const emailInput = document.getElementById("email");
      const messageInput = document.getElementById("message");
      let isValid = true;

      // Reset error messages
      const errorElements = document.querySelectorAll(".error-message");
      errorElements.forEach((element) => {
        element.textContent = "";
      });

      // Validate name
      if (nameInput.value.trim() === "") {
        document.getElementById("nameError").textContent = "Name is required";
        isValid = false;
      }

      // Validate email
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailInput.value)) {
        document.getElementById("emailError").textContent =
          "Valid email is required";
        isValid = false;
      }

      // Validate message
      if (messageInput.value.trim() === "") {
        document.getElementById("messageError").textContent =
          "Message is required";
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }

  // Admin panel order status update
  const statusSelects = document.querySelectorAll(".status-select");

  if (statusSelects.length > 0) {
    statusSelects.forEach((select) => {
      select.addEventListener("change", function () {
        const orderId = this.getAttribute("data-order-id");
        const status = this.value;

        // Send AJAX request to update order status
        const formData = new FormData();
        formData.append("order_id", orderId);
        formData.append("status", status);

        fetch("update_order_status.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Show success message
              const row = select.closest("tr");
              const statusCell = row.querySelector(".status-cell");

              // Remove old status badge
              const oldBadge = statusCell.querySelector(".status-badge");
              if (oldBadge) {
                oldBadge.remove();
              }

              // Create new status badge
              const newBadge = document.createElement("span");
              newBadge.className = `status-badge status-${status}`;
              newBadge.textContent =
                status.charAt(0).toUpperCase() + status.slice(1);
              statusCell.prepend(newBadge);

              showNotification("Order status updated successfully", "success");
            } else {
              showNotification("Failed to update order status", "error");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showNotification("An error occurred", "error");
          });
      });
    });
  }

  // Show notification
  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.classList.add("show");
    }, 10);

    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }

  // Menu item delete confirmation
  const deleteButtons = document.querySelectorAll(".delete-btn");

  if (deleteButtons.length > 0) {
    deleteButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        if (!confirm("Are you sure you want to delete this item?")) {
          e.preventDefault();
        }
      });
    });
  }
});
