<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: merchants.php");
    exit;
}

$merchant_id = (int)$_GET['id'];

$merchant_q = mysqli_query($conn, "SELECT * FROM merchants WHERE id=$merchant_id");
$merchant = mysqli_fetch_assoc($merchant_q);

$kyc_q = mysqli_query($conn, "SELECT * FROM merchant_kyc WHERE merchant_id=$merchant_id");
$kyc = mysqli_fetch_assoc($kyc_q);

$delivered = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status='delivered'"
))['total'];

$dispatched = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status='dispatched'"
))['total'];

$pending = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM orders WHERE merchant_id=$merchant_id AND status IN('placed','confirmed')"
))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Merchant Details</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
.box{background:#fff;padding:20px;border-radius:6px;margin-bottom:20px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px}
.stat{background:#e9f0ff;padding:15px;text-align:center;border-radius:5px}
.stat h3{margin:0;font-size:16px;color:#333}
.stat p{font-size:24px;margin:5px 0 0;color:#2874f0}
table{width:100%;border-collapse:collapse}
td{padding:8px;border:1px solid #ddd}
</style>
</head>
<body>

<div class="header">Merchant Details</div>

<div class="container">

<div class="box">
<h3>Shop Information</h3>
<table>
<tr><td>Name</td><td><?php echo htmlspecialchars($merchant['name']); ?></td></tr>
<tr><td>Shop Name</td><td><?php echo htmlspecialchars($merchant['shop_name']); ?></td></tr>
<tr><td>Email</td><td><?php echo htmlspecialchars($merchant['email']); ?></td></tr>
<tr><td>Phone</td><td><?php echo htmlspecialchars($merchant['phone']); ?></td></tr>
<tr><td>KYC Status</td><td><?php echo strtoupper($merchant['kyc_status']); ?></td></tr>
</table>
</div>

<div class="box">
<h3>Order Statistics</h3>
<div class="stats">
<div class="stat"><h3>Delivered</h3><p><?php echo $delivered; ?></p></div>
<div class="stat"><h3>Dispatched</h3><p><?php echo $dispatched; ?></p></div>
<div class="stat"><h3>Pending</h3><p><?php echo $pending; ?></p></div>
</div>
</div>

<div class="box">
<h3>KYC Details</h3>
<?php if($kyc){ ?>
<table>
<tr><td>Aadhaar</td><td><?php echo htmlspecialchars($kyc['aadhaar']); ?></td></tr>
<tr><td>PAN</td><td><?php echo htmlspecialchars($kyc['pan']); ?></td></tr>
<tr><td>GST</td><td><?php echo htmlspecialchars($kyc['gst']); ?></td></tr>
<tr><td>MSME</td><td><?php echo htmlspecialchars($kyc['msme']); ?></td></tr>
<tr><td>Account Name</td><td><?php echo htmlspecialchars($kyc['account_name']); ?></td></tr>
<tr><td>Account Number</td><td><?php echo htmlspecialchars($kyc['account_number']); ?></td></tr>
<tr><td>IFSC</td><td><?php echo htmlspecialchars($kyc['ifsc']); ?></td></tr>
<tr><td>Address</td><td><?php echo htmlspecialchars($kyc['address']); ?></td></tr>
<tr><td>Pin Code</td><td><?php echo htmlspecialchars($kyc['pincode']); ?></td></tr>
<tr><td>District</td><td><?php echo htmlspecialchars($kyc['district']); ?></td></tr>
<tr><td>State</td><td><?php echo htmlspecialchars($kyc['state']); ?></td></tr>
<tr><td>Photo</td><td>
<?php if($kyc['photo']!=""){ ?>
<img src="../uploads/kyc/<?php echo $kyc['photo']; ?>" style="max-width:150px">
<?php } ?>
</td></tr>
</table>
<?php } else { ?>
<p>No KYC data submitted.</p>
<?php } ?>
</div>

</div>

</body>
</html>
