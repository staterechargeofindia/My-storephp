<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['id'];

$order_q = mysqli_query($conn, "
    SELECT o.*, u.name AS customer_name, u.email, u.phone, m.shop_name
    FROM orders o
    JOIN users u ON u.id=o.user_id
    JOIN merchants m ON m.id=o.merchant_id
    WHERE o.id=$order_id
    LIMIT 1
");

if (mysqli_num_rows($order_q) == 0) {
    header("Location: orders.php");
    exit;
}

$order = mysqli_fetch_assoc($order_q);

$items = mysqli_query($conn, "
    SELECT oi.*, p.name AS product_name
    FROM order_items oi
    JOIN products p ON p.id=oi.product_id
    WHERE oi.order_id=$order_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Details</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
.box{background:#fff;padding:20px;border-radius:6px;margin-bottom:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border:1px solid #ddd;text-align:left}
.total{font-size:20px;font-weight:bold;text-align:right}
</style>
</head>
<body>

<div class="header">Order Details</div>

<div class="container">

<div class="box">
<h3>Customer Information</h3>
<table>
<tr><td>Name</td><td><?php echo htmlspecialchars($order['customer_name']); ?></td></tr>
<tr><td>Email</td><td><?php echo htmlspecialchars($order['email']); ?></td></tr>
<tr><td>Phone</td><td><?php echo htmlspecialchars($order['phone']); ?></td></tr>
<tr><td>Shop</td><td><?php echo htmlspecialchars($order['shop_name']); ?></td></tr>
<tr><td>Payment</td><td><?php echo $order['payment_method']; ?></td></tr>
<tr><td>Status</td><td><?php echo strtoupper($order['status']); ?></td></tr>
</table>
</div>

<div class="box">
<h3>Products</h3>
<table>
<tr>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
</tr>
<?php
$grand = 0;
while($i=mysqli_fetch_assoc($items)){
    $sub = $i['price'] * $i['quantity'];
    $grand += $sub;
?>
<tr>
<td><?php echo htmlspecialchars($i['product_name']); ?></td>
<td><?php echo $i['quantity']; ?></td>
<td>₹<?php echo number_format($sub,2); ?></td>
</tr>
<?php } ?>
<tr>
<td colspan="2" class="total">Total</td>
<td class="total">₹<?php echo number_format($grand,2); ?></td>
</tr>
</table>
</div>

</div>

</body>
</html>
