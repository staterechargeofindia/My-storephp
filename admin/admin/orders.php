<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['confirm'])) {
    $oid = (int)$_GET['confirm'];
    mysqli_query($conn, "UPDATE orders SET status='confirmed' WHERE id=$oid");
    header("Location: orders.php");
    exit;
}

$orders = mysqli_query($conn, "
    SELECT o.*, u.name AS customer_name, m.shop_name 
    FROM orders o
    JOIN users u ON u.id=o.user_id
    JOIN merchants m ON m.id=o.merchant_id
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:10px;border:1px solid #ddd;text-align:center}
a{padding:6px 10px;text-decoration:none;border-radius:4px;color:#fff}
.confirm{background:#28a745}
.view{background:#17a2b8}
</style>
</head>
<body>

<div class="header">All Orders</div>

<div class="container">
<table>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Shop</th>
<th>Total</th>
<th>Payment</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php while($o=mysqli_fetch_assoc($orders)){ ?>
<tr>
<td><?php echo $o['id']; ?></td>
<td><?php echo htmlspecialchars($o['customer_name']); ?></td>
<td><?php echo htmlspecialchars($o['shop_name']); ?></td>
<td>₹<?php echo $o['total_amount']; ?></td>
<td><?php echo $o['payment_method']; ?></td>
<td><?php echo strtoupper($o['status']); ?></td>
<td>
<a class="view" href="order-details.php?id=<?php echo $o['id']; ?>">View</a>
<?php if($o['status']=='placed'){ ?>
<a class="confirm" href="orders.php?confirm=<?php echo $o['id']; ?>">Confirm</a>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>
