<?php
session_start();
include "../db.php";

if (!isset($_SESSION['merchant_id'])) {
    header("Location: login.php");
    exit;
}

$merchant_id = $_SESSION['merchant_id'];

$products_q = mysqli_query($conn, "SELECT COUNT(*) total FROM products WHERE merchant_id=$merchant_id");
$total_products = mysqli_fetch_assoc($products_q)['total'];

$delivered_q = mysqli_query($conn, "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status='delivered'");
$delivered = mysqli_fetch_assoc($delivered_q)['total'];

$dispatched_q = mysqli_query($conn, "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status='dispatched'");
$dispatched = mysqli_fetch_assoc($dispatched_q)['total'];

$pending_q = mysqli_query($conn, "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status IN('placed','confirmed')");
$pending = mysqli_fetch_assoc($pending_q)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Merchant Dashboard</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px}
.card{background:#fff;padding:20px;border-radius:6px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
.card h3{margin:0;color:#555}
.card p{font-size:28px;margin-top:10px;color:#2874f0}
.menu{margin-top:30px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px}
.menu a{display:block;background:#fff;padding:15px;text-align:center;border-radius:5px;text-decoration:none;color:#333;box-shadow:0 1px 4px rgba(0,0,0,0.1)}
.menu a:hover{background:#e9f0ff}
.notice{background:#fff3cd;color:#856404;padding:15px;border-radius:5px;margin-bottom:20px}
</style>
</head>
<body>

<div class="header">Merchant Dashboard</div>

<div class="container">

<?php if($_SESSION['kyc_status']!='verified'){ ?>
<div class="notice">
Your KYC is <b><?php echo strtoupper($_SESSION['kyc_status']); ?></b>.  
You must complete and verify KYC before adding products.
</div>
<?php } ?>

<div class="cards">
<div class="card"><h3>Total Products</h3><p><?php echo $total_products; ?></p></div>
<div class="card"><h3>Delivered Orders</h3><p><?php echo $delivered; ?></p></div>
<div class="card"><h3>Dispatched Orders</h3><p><?php echo $dispatched; ?></p></div>
<div class="card"><h3>Pending Orders</h3><p><?php echo $pending; ?></p></div>
</div>

<div class="menu">
<a href="products.php">My Products</a>
<a href="add-product.php">Add Product</a>
<a href="orders.php">My Orders</a>
<a href="kyc.php">KYC</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>

</div>

</body>
</html>
