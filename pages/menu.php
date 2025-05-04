<?php
// Include header
require_once '../includes/header.php';

// Get all categories
$categories = getAllCategories();

// Get all menu items
$menuItems = getMenuItems();

// Group menu items by category
$itemsByCategory = [];
foreach ($menuItems as $item) {
    if (!isset($itemsByCategory[$item['category_id']])) {
        $itemsByCategory[$item['category_id']] = [];
    }
    $itemsByCategory[$item['category_id']][] = $item;
}
?>

<!-- Menu Banner -->
<section class="hero" style="height: 50vh; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/menu-banner.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Our Menu</h1>
            <p class="hero-subtitle">Discover our delicious offerings made with fresh, organic ingredients</p>
        </div>
    </div>
</section>

<!-- Shopping Cart Icon -->
<div class="cart-icon-container">
    <div class="cart-icon" id="cartIcon">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>
</div>

<!-- Shopping Cart Modal -->
<div class="cart-modal" id="cartModal">
    <div class="cart-modal-content">
        <div class="cart-modal-header">
            <h2>Your Order</h2>
            <span class="cart-close">&times;</span>
        </div>
        <div class="cart-modal-body">
            <div id="cartItems">
                <!-- Cart items will be displayed here -->
                <p class="empty-cart-message">Your cart is empty</p>
            </div>
            
            <!-- Customer Information Form -->
            <div id="customerInfoForm" style="display: none; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
                <h3 style="margin-bottom: 15px; color: var(--green);">Your Information</h3>
                <form id="orderForm">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="customerName">Name*</label>
                        <input type="text" id="customerName" class="form-control" placeholder="Your name" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="customerEmail">Email*</label>
                        <input type="email" id="customerEmail" class="form-control" placeholder="Your email" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="customerPhone">Phone*</label>
                        <input type="tel" id="customerPhone" class="form-control" placeholder="Your phone number" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="specialInstructions">Special Instructions</label>
                        <textarea id="specialInstructions" class="form-control" placeholder="Any special requests or instructions" rows="3"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="cart-modal-footer">
            <div class="cart-total">
                <h3>Total: <span id="cartTotal">$0.00</span></h3>
            </div>
            <button class="btn btn-primary checkout-btn" id="checkoutBtn">Checkout</button>
            <button class="btn btn-primary place-order-btn" id="placeOrderBtn" style="display: none;">Place Order</button>
        </div>
    </div>
</div>

<!-- Menu Section -->
<section class="section">
    <div class="container">
        <!-- Category Filters -->
        <div class="menu-categories">
            <div class="category-filter active" data-category="all">All</div>
            <?php foreach ($categories as $category): ?>
                <div class="category-filter" data-category="<?php echo $category['id']; ?>">
                    <?php echo $category['name']; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- All Menu Items - Catalog Style -->
        <div class="catalog-grid" id="allItems">
            <?php foreach ($menuItems as $item): ?>
                <div class="catalog-item" data-category="<?php echo $item['category_id']; ?>" data-id="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="catalog-item-img">
                        <?php if (!empty($item['image'])): ?>
                            <img src="../assets/images/menu/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <?php else: ?>
                            <img src="../assets/images/menu/default.jpg" alt="<?php echo $item['name']; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="catalog-item-content">
                        <h3 class="catalog-item-title"><?php echo $item['name']; ?></h3>
                        <p class="catalog-item-category"><?php echo $item['category_name']; ?></p>
                        <p class="catalog-item-desc"><?php echo $item['description']; ?></p>
                        <div class="catalog-item-footer">
                            <p class="catalog-item-price"><?php echo formatPrice($item['price']); ?></p>
                            <button class="btn btn-primary add-to-cart-btn">
                                <i class="fas fa-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Menu Categories Sections -->
<?php foreach ($categories as $category): ?>
    <?php if (isset($itemsByCategory[$category['id']])): ?>
        <section class="section category-section" id="category-<?php echo $category['id']; ?>" style="background-color: <?php echo $category['id'] % 2 == 0 ? 'var(--light-gray)' : 'var(--white)'; ?>;">
            <div class="container">
                <!-- Add Category Filters to each category section -->
                <div class="menu-categories category-filters-sticky">
                    <div class="category-filter" data-category="all">All</div>
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-filter <?php echo $cat['id'] == $category['id'] ? 'active' : ''; ?>" data-category="<?php echo $cat['id']; ?>">
                            <?php echo $cat['name']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title">
                    <h2><?php echo $category['name']; ?></h2>
                </div>
                <p style="text-align: center; margin-bottom: 40px;"><?php echo $category['description']; ?></p>
                
                <div class="catalog-grid">
                    <?php foreach ($itemsByCategory[$category['id']] as $item): ?>
                        <div class="catalog-item" data-id="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="catalog-item-img">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="../assets/images/menu/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <?php else: ?>
                                    <img src="../assets/images/menu/default.jpg" alt="<?php echo $item['name']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="catalog-item-content">
                                <h3 class="catalog-item-title"><?php echo $item['name']; ?></h3>
                                <p class="catalog-item-desc"><?php echo $item['description']; ?></p>
                                <div class="catalog-item-footer">
                                    <p class="catalog-item-price"><?php echo formatPrice($item['price']); ?></p>
                                    <button class="btn btn-primary add-to-cart-btn">
                                        <i class="fas fa-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Cart JavaScript -->
<script src="../assets/js/cart.js"></script>

<!-- Enhanced Cart and Checkout Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality (keep existing code)
    document.querySelectorAll('.category-filter').forEach(filter => {
        filter.addEventListener('click', function() {
            // Get the selected category
            const category = this.getAttribute('data-category');
            
            // Update all instances of category filters to show the active state
            document.querySelectorAll('.category-filter').forEach(f => {
                if (f.getAttribute('data-category') === category) {
                    f.classList.add('active');
                } else {
                    f.classList.remove('active');
                }
            });
        });
    });
    
    // Note: Checkout process is now handled by cart.js
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?> 