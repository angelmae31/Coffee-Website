/**
 * Cart Functionality for Cafe Website
 * Handles shopping cart operations and UI updates
 */

class CafeCart {
  constructor() {
    // Initialize cart elements
    this.cartIcon = document.getElementById("cartIcon");
    this.cartModal = document.getElementById("cartModal");
    this.cartClose = document.querySelector(".cart-close");
    this.cartItems = document.getElementById("cartItems");
    this.cartTotal = document.getElementById("cartTotal");
    this.cartCount = document.getElementById("cartCount");
    this.addToCartButtons = document.querySelectorAll(".add-to-cart-btn");
    this.categoryFilters = document.querySelectorAll(".category-filter");
    this.allItemsSection = document
      .getElementById("allItems")
      ?.closest(".section");
    this.categorySections = document.querySelectorAll(".category-section");

    // Load cart from localStorage - try both potential keys to ensure compatibility
    const cafeCartData = localStorage.getItem("cafeCart");
    const cartData = localStorage.getItem("cart");

    // Use cafeCart if it exists, otherwise try cart, or default to empty array
    this.cart = [];
    if (cafeCartData) {
      try {
        this.cart = JSON.parse(cafeCartData);
        console.log("Loaded cart from 'cafeCart'");
      } catch (e) {
        console.error("Error parsing cafeCart data", e);
      }
    } else if (cartData) {
      try {
        this.cart = JSON.parse(cartData);
        console.log("Loaded cart from 'cart'");
        // Migrate to the new key
        localStorage.setItem("cafeCart", cartData);
        localStorage.removeItem("cart");
      } catch (e) {
        console.error("Error parsing cart data", e);
      }
    }

    // Initialize
    this.init();
  }

  init() {
    // Update cart UI on load
    this.updateCart();

    // Set up event listeners
    this.setupEventListeners();

    // Hide all category sections initially
    this.categorySections.forEach((section) => {
      section.style.display = "none";
    });
  }

  setupEventListeners() {
    // Category filtering
    this.categoryFilters.forEach((filter) => {
      filter.addEventListener("click", this.handleCategoryFilter.bind(this));
    });

    // Cart icon click - open modal
    this.cartIcon.addEventListener("click", () => {
      this.openCartModal();
    });

    // Close button click - close modal
    this.cartClose.addEventListener("click", () => {
      this.closeCartModal();
    });

    // Close when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === this.cartModal) {
        this.closeCartModal();
      }
    });

    // Add to cart buttons
    this.addToCartButtons.forEach((button) => {
      button.addEventListener("click", this.handleAddToCart.bind(this));
    });

    // Checkout button
    const checkoutBtn = document.querySelector(".checkout-btn");
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", this.handleCheckout.bind(this));
    }

    // Place order button
    const placeOrderBtn = document.getElementById("placeOrderBtn");
    if (placeOrderBtn) {
      placeOrderBtn.addEventListener("click", this.handlePlaceOrder.bind(this));
    }
  }

  // Open cart modal
  openCartModal() {
    // First set display to block
    this.cartModal.style.display = "block";

    // Force reflow to ensure the transition works
    this.cartModal.offsetHeight;

    // Then add the show class for the opacity transition
    this.cartModal.classList.add("show");

    // Prevent background scrolling when modal is open
    document.body.style.overflow = "hidden";
  }

  // Close cart modal
  closeCartModal() {
    // First remove the show class to trigger the opacity transition
    this.cartModal.classList.remove("show");

    // Wait for the transition to finish before hiding the modal
    setTimeout(() => {
      this.cartModal.style.display = "none";
      // Re-enable background scrolling
      document.body.style.overflow = "";
    }, 300); // Match this with the CSS transition time
  }

  handleCategoryFilter(event) {
    const category = event.currentTarget.getAttribute("data-category");

    // Update active filter - update all filter instances
    this.categoryFilters.forEach((f) => f.classList.remove("active"));
    document
      .querySelectorAll(`.category-filter[data-category="${category}"]`)
      .forEach((f) => {
        f.classList.add("active");
      });

    if (category === "all") {
      // Show all items section, hide category sections
      if (this.allItemsSection) {
        this.allItemsSection.style.display = "block";
      }

      this.categorySections.forEach((section) => {
        section.style.display = "none";
      });

      // Show all items in the "All" section
      const allItems = document.querySelectorAll("#allItems .catalog-item");
      allItems.forEach((item) => {
        item.style.display = "block";
      });
    } else {
      // Hide all items section, show only relevant category section
      if (this.allItemsSection) {
        this.allItemsSection.style.display = "none";
      }

      this.categorySections.forEach((section) => {
        if (section.id === `category-${category}`) {
          section.style.display = "block";
        } else {
          section.style.display = "none";
        }
      });
    }

    // Scroll to top of the menu section with a small offset to show the sticky filter
    const targetSection =
      category === "all"
        ? document.querySelector(".menu-categories")
        : document.querySelector(
            `#category-${category} .category-filters-sticky`
          );

    if (targetSection) {
      window.scrollTo({
        top: targetSection.offsetTop - 100,
        behavior: "smooth",
      });
    }
  }

  handleAddToCart(event) {
    const button = event.currentTarget;
    const item = button.closest(".catalog-item");
    const itemId = item.dataset.id;
    const itemName = item.dataset.name;
    const itemPrice = parseFloat(item.dataset.price);

    // Add item animation
    button.classList.add("adding");
    setTimeout(() => button.classList.remove("adding"), 300);

    // Check if item is already in cart
    const existingItem = this.cart.find((cartItem) => cartItem.id === itemId);

    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      this.cart.push({
        id: itemId,
        name: itemName,
        price: itemPrice,
        quantity: 1,
      });
    }

    // Save cart and update UI
    this.saveCart();
    this.updateCart();
    this.showNotification(`${itemName} added to cart!`);
  }

  // Format price with peso symbol
  formatPrice(price) {
    return '₱' + price.toFixed(2);
  }

  // Update cart display
  updateCart() {
    let total = 0;
    this.cartItems.innerHTML = '';

    if (this.cart.length === 0) {
      this.cartItems.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
      this.cartTotal.textContent = this.formatPrice(0);
      this.cartCount.textContent = '0';
      return;
    }

    this.cart.forEach(item => {
      const subtotal = item.price * item.quantity;
      total += subtotal;
      
      const itemElement = document.createElement('div');
      itemElement.className = 'cart-item';
      itemElement.innerHTML = `
        <div class="cart-item-details">
          <h4>${item.name}</h4>
          <p>${this.formatPrice(item.price)} × ${item.quantity}</p>
        </div>
        <div class="cart-item-quantity">
          <button class="quantity-btn minus" onclick="cafeCart.decreaseQuantity('${item.id}')">-</button>
          <span>${item.quantity}</span>
          <button class="quantity-btn plus" onclick="cafeCart.increaseQuantity('${item.id}')">+</button>
        </div>
        <div class="cart-item-subtotal">
          ${this.formatPrice(subtotal)}
        </div>
        <button class="remove-item" onclick="cafeCart.removeItem('${item.id}')">
          <i class="fas fa-trash"></i>
        </button>
      `;
      
      this.cartItems.appendChild(itemElement);
    });

    this.cartTotal.textContent = this.formatPrice(total);
    this.cartCount.textContent = this.cart.reduce((sum, item) => sum + item.quantity, 0);
  }

  decreaseQuantity(itemId) {
    const index = this.cart.findIndex((item) => item.id === itemId);

    if (index !== -1) {
      if (this.cart[index].quantity > 1) {
        this.cart[index].quantity -= 1;
      } else {
        this.cart.splice(index, 1);
      }

      this.saveCart();
      this.updateCart();
    }
  }

  increaseQuantity(itemId) {
    const index = this.cart.findIndex((item) => item.id === itemId);

    if (index !== -1) {
      this.cart[index].quantity += 1;
      this.saveCart();
      this.updateCart();
    }
  }

  removeItem(itemId) {
    this.cart = this.cart.filter((item) => item.id !== itemId);
    this.saveCart();
    this.updateCart();
  }

  saveCart() {
    localStorage.setItem("cafeCart", JSON.stringify(this.cart));
  }

  showNotification(message) {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll(".notification");
    existingNotifications.forEach((notification) => {
      notification.remove();
    });

    // Create new notification
    const notification = document.createElement("div");
    notification.className = "notification";
    notification.textContent = message;

    document.body.appendChild(notification);

    // Use requestAnimationFrame for smoother transitions
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        notification.classList.add("show");
      });
    });

    // Remove notification after delay
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 2000);
  }

  handleCheckout() {
    // Check if cart is empty
    if (this.cart.length === 0) {
      alert("Your cart is empty. Please add items before checking out.");
      return;
    }

    // Show customer info form
    const customerInfoForm = document.getElementById("customerInfoForm");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const placeOrderBtn = document.getElementById("placeOrderBtn");

    if (customerInfoForm && checkoutBtn && placeOrderBtn) {
      customerInfoForm.style.display = "block";
      checkoutBtn.style.display = "none";
      placeOrderBtn.style.display = "block";
    } else {
      // If we can't find the form elements (because we're not on the menu page)
      // Simply redirect to menu page
      window.location.href = "menu.php";
    }
  }

  handlePlaceOrder() {
    // Validate form
    const customerName = document.getElementById("customerName")?.value;
    const customerEmail = document.getElementById("customerEmail")?.value;
    const customerPhone = document.getElementById("customerPhone")?.value;

    if (!customerName || !customerEmail || !customerPhone) {
      alert("Please fill in all required fields");
      return;
    }

    // Get cart items - Use this.cart directly since it's already loaded in the class
    if (this.cart.length === 0) {
      alert("Your cart is empty");
      return;
    }

    // Show loading indicator or disable button to prevent multiple submissions
    const placeOrderBtn = document.getElementById("placeOrderBtn");
    if (placeOrderBtn) {
      placeOrderBtn.disabled = true;
      placeOrderBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }

    // Transform cart items to match server expectations
    const cartItems = this.cart.map((item) => ({
      id: item.id,
      quantity: item.quantity,
      price: item.price,
      name: item.name,
    }));

    // Collect order data
    const orderData = {
      customer: {
        name: customerName,
        email: customerEmail,
        phone: customerPhone,
        specialInstructions:
          document.getElementById("specialInstructions")?.value || "",
      },
      items: cartItems,
      totalAmount: this.cart.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      ),
    };

    console.log("Sending order data:", orderData);

    // Send order to server
    fetch("../api/place_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(orderData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Clear cart
          this.cart = [];
          this.saveCart();
          this.updateCart();

          // Show success message and redirect
          window.location.href =
            "order_confirmation.php?order_id=" + data.order_id;
        } else {
          // Re-enable button
          if (placeOrderBtn) {
            placeOrderBtn.disabled = false;
            placeOrderBtn.innerHTML = "Place Order";
          }

          // Check for database structure error
          if (data.message && data.message.includes("Unknown column 'price'")) {
            if (
              confirm(
                "There's a database issue that needs to be fixed. Would you like to go to the fix page?"
              )
            ) {
              window.location.href = "../api/fix_error.php";
              return;
            }
          }

          alert("Error placing order: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        // Re-enable button
        if (placeOrderBtn) {
          placeOrderBtn.disabled = false;
          placeOrderBtn.innerHTML = "Place Order";
        }
        alert("There was an error processing your order. Please try again.");
      });
  }
}

// Initialize cart when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Make cart instance globally accessible
  window.cafeCart = new CafeCart();
});
