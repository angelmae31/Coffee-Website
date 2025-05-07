<?php
// Include header
require_once 'includes/header.php';

// Get featured menu items
$featuredItems = getFeaturedItems(3);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to Green Cafe</h1>
            <p class="hero-subtitle">Where fresh ingredients meet exceptional taste in a cozy environment</p>
            <div class="hero-buttons">
                <a href="pages/menu.php" class="btn btn-primary">Explore Menu</a>
                <a href="pages/contact.php" class="btn btn-secondary">Find Us</a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>About Us</h2>
        </div>
        <div class="about-content">
            <div class="about-img">
                <img src="assets/images/9.jpg" alt="Green Cafe Interior">
            </div>
            <div class="about-text">
                <h3>Our Story</h3>
                <p>Green Cafe was founded with a simple mission: to provide a welcoming space where people can enjoy fresh, organic food and drinks. We carefully source ingredients from local producers, supporting sustainable farming practices.</p>
                <p>Our dedicated team is committed to delivering exceptional service in a relaxing atmosphere. Whether you're stopping by for a quick coffee or settling in for a meal, we're honored to be part of your day.</p>
                <a href="pages/contact.php" class="btn btn-primary">Visit Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Menu Section -->
<section class="section" style="background-color: var(--light-gray);">
    <div class="container">
        <div class="section-title">
            <h2>Featured Items</h2>
        </div>
        <div class="featured-items">
            <?php if (count($featuredItems) > 0): ?>
                <?php foreach ($featuredItems as $item): ?>
                    <div class="menu-item">
                        <div class="menu-item-img">
                            <?php if (!empty($item['image'])): ?>
                                <img src="assets/images/menu/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                            <?php else: ?>
                                <img src="assets/images/menu/default.jpg" alt="<?php echo $item['name']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="menu-item-content">
                            <h3 class="menu-item-title"><?php echo $item['name']; ?></h3>
                            <p class="menu-item-category"><?php echo $item['category_name']; ?></p>
                            <p><?php echo $item['description']; ?></p>
                            <p class="menu-item-price"><?php echo formatPrice($item['price']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured items available.</p>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="pages/menu.php" class="btn btn-primary">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Us</h2>
        </div>
        <div class="featured-items">
            <div class="menu-item">
                <div class="menu-item-content" style="text-align: center;">
                    <i class="fas fa-leaf" style="color: var(--green); font-size: 3rem; margin-bottom: 20px;"></i>
                    <h3 class="menu-item-title">Fresh & Organic</h3>
                    <p>We source only the freshest organic ingredients from local farmers, ensuring the highest quality in every dish.</p>
                </div>
            </div>
            <div class="menu-item">
                <div class="menu-item-content" style="text-align: center;">
                    <i class="fas fa-coffee" style="color: var(--green); font-size: 3rem; margin-bottom: 20px;"></i>
                    <h3 class="menu-item-title">Specialty Coffee</h3>
                    <p>Our expert baristas craft exceptional coffee using ethically sourced beans, creating the perfect cup every time.</p>
                </div>
            </div>
            <div class="menu-item">
                <div class="menu-item-content" style="text-align: center;">
                    <i class="fas fa-heart" style="color: var(--green); font-size: 3rem; margin-bottom: 20px;"></i>
                    <h3 class="menu-item-title">Cozy Atmosphere</h3>
                    <p>Relax in our welcoming space designed for comfort, whether you're working, meeting friends, or just unwinding.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?> 