<?php
// Include header
require_once '../includes/header.php';

// Check if order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    redirect('menu.php');
}

$orderId = (int)$_GET['order_id'];

// Get database connection
$conn = getDBConnection();

// Get order details
$sql = "SELECT o.*, c.name as customer_name, c.email, c.phone
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('menu.php');
}

$order = $result->fetch_assoc();

// Get order items
$sql = "SELECT oi.*, m.name, m.image, m.price as menu_price 
        FROM order_items oi 
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id 
        WHERE oi.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

$orderItems = [];
while ($row = $result->fetch_assoc()) {
    // Add a calculated price if it doesn't exist
    if (!isset($row['price'])) {
        $row['price'] = isset($row['menu_price']) ? $row['menu_price'] : 
                      ($row['quantity'] > 0 ? $row['subtotal'] / $row['quantity'] : 0);
    }
    $orderItems[] = $row;
}
?>

<!-- Order Confirmation Banner -->
<section class="hero" style="height: 40vh; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/order-confirmation.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Order Confirmed</h1>
            <p class="hero-subtitle">Thank you for your order!</p>
        </div>
    </div>
</section>

<!-- Order Confirmation -->
<section class="section">
    <div class="container">
        <div class="row" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            <!-- Order Success Message -->
            <div class="col" style="flex: 1; min-width: 300px; max-width: 800px;">
                <div style="background-color: #d4edda; border-radius: 8px; padding: 20px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                    <h2 style="margin-bottom: 10px; color: #28a745;">Order Placed Successfully!</h2>
                    <p style="font-size: 18px; margin-bottom: 5px;">Your order number is: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
                    <p>We've received your order and will begin preparing it shortly.</p>
                    <p style="margin-top: 10px;">You'll receive a confirmation email with your order details.</p>
                </div>
                
                <!-- Order Details Card -->
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                    <h3 style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px;">Order Details</h3>
                    
                    <!-- Order Status Timeline -->
                    <div style="margin-bottom: 30px;">
                        <div style="display: flex; justify-content: space-between; position: relative; margin-bottom: 10px;">
                            <div style="text-align: center; position: relative; z-index: 2;">
                                <div style="width: 30px; height: 30px; background-color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                                    <i class="fas fa-check" style="color: white; font-size: 14px;"></i>
                                </div>
                                <div style="font-size: 0.8rem; font-weight: 500;">Order Placed</div>
                            </div>
                            
                            <div style="text-align: center; position: relative; z-index: 2;">
                                <div style="width: 30px; height: 30px; background-color: <?php echo $order['status'] != 'pending' ? '#28a745' : '#f8f9fa'; ?>; border: 2px solid <?php echo $order['status'] != 'pending' ? '#28a745' : '#adb5bd'; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                                    <?php if ($order['status'] != 'pending'): ?>
                                        <i class="fas fa-check" style="color: white; font-size: 14px;"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.8rem; font-weight: 500; color: <?php echo $order['status'] != 'pending' ? '#28a745' : '#6c757d'; ?>">Processing</div>
                            </div>
                            
                            <div style="text-align: center; position: relative; z-index: 2;">
                                <div style="width: 30px; height: 30px; background-color: <?php echo $order['status'] == 'completed' ? '#28a745' : '#f8f9fa'; ?>; border: 2px solid <?php echo $order['status'] == 'completed' ? '#28a745' : '#adb5bd'; ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                                    <?php if ($order['status'] == 'completed'): ?>
                                        <i class="fas fa-check" style="color: white; font-size: 14px;"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.8rem; font-weight: 500; color: <?php echo $order['status'] == 'completed' ? '#28a745' : '#6c757d'; ?>">Completed</div>
                            </div>
                            
                            <!-- Progress bar line -->
                            <div style="position: absolute; top: 15px; left: 15px; right: 15px; height: 2px; background-color: #adb5bd; z-index: 1;">
                                <div style="height: 100%; width: <?php 
                                    if ($order['status'] == 'pending') echo '0%';
                                    elseif ($order['status'] == 'processing') echo '50%';
                                    else echo '100%';
                                ?>; background-color: #28a745;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Info -->
                    <div style="margin-bottom: 20px;">
                        <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                        <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['order_date'])); ?></p>
                        <p><strong>Status:</strong> 
                            <span style="display: inline-block; padding: 4px 12px; font-size: 14px; font-weight: 600; border-radius: 20px; 
                                <?php 
                                switch($order['status']) {
                                    case 'pending':
                                        echo 'background-color: #fff3cd; color: #856404;';
                                        break;
                                    case 'processing':
                                        echo 'background-color: #cce5ff; color: #004085;';
                                        break;
                                    case 'completed':
                                        echo 'background-color: #d4edda; color: #155724;';
                                        break;
                                    case 'cancelled':
                                        echo 'background-color: #f8d7da; color: #721c24;';
                                        break;
                                    default:
                                        echo 'background-color: #e2e3e5; color: #383d41;';
                                }
                                ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                        <p><strong>Total Amount:</strong> <span style="color: var(--green); font-weight: bold;"><?php echo formatPrice($order['total_amount']); ?></span></p>
                    </div>
                    
                    <!-- Customer Info (if available) -->
                    <?php if (!empty($order['customer_name'])): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px;">Customer Information</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <?php if (!empty($order['email'])): ?>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($order['phone'])): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Special Instructions (if any) -->
                    <?php if (!empty($order['special_instructions'])): ?>
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px;">Special Instructions</h4>
                        <p style="background-color: #fff3cd; padding: 15px; border-radius: 4px;">
                            <?php echo nl2br(htmlspecialchars($order['special_instructions'])); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Order Items -->
                    <div>
                        <h4 style="margin-bottom: 15px;">Order Items</h4>
                        <div style="border: 1px solid #dee2e6; border-radius: 4px; overflow: hidden;">
                            <?php foreach ($orderItems as $index => $item): ?>
                                <div style="display: flex; padding: 15px; <?php echo $index % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: #fff;'; ?> border-bottom: 1px solid #dee2e6;">
                                    <div style="flex: 0 0 60px; margin-right: 15px;">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../assets/images/menu/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background-color: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-utensils" style="color: #aaa;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="font-weight: bold;"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div style="color: #6c757d; font-size: 0.9em;">
                                            <?php echo formatPrice(isset($item['price']) ? $item['price'] : 0); ?> × <?php echo $item['quantity']; ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; font-weight: bold;">
                                        <?php echo formatPrice($item['subtotal']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Total -->
                            <div style="display: flex; justify-content: space-between; padding: 15px; background-color: #e9ecef; font-weight: bold;">
                                <div>Total</div>
                                <div><?php echo formatPrice($order['total_amount']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div style="text-align: center; margin-top: 30px;">
                    <p style="margin-bottom: 20px;">What would you like to do next?</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="menu.php" class="btn btn-primary">
                            <i class="fas fa-utensils"></i> Browse More Items
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Return to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once '../includes/footer.php';
?> 