<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['verify'])) {
    $id = (int)$_GET['verify'];
    mysqli_query($conn, "UPDATE merchants SET kyc_status='verified' WHERE id=$id");
    header("Location: merchants.php");
    exit;
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE merchants SET kyc_status='rejected' WHERE id=$id");
    header("Location: merchants.php");
    exit;
}

$merchants = mysqli_query($conn, "SELECT * FROM merchants ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Merchants</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:10px;border:1px solid #ddd;text-align:center}
a{padding:6px 10px;text-decoration:none;border-radius:4px;color:#fff}
.verify{background:#28a745}
.reject{background:#dc3545}
.view{background:#17a2b8}
</style>
</head>
<body>

<div class="header">Merchants</div>

<div class="container">
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Shop Name</th>
<th>Email</th>
<th>Phone</th>
<th>KYC Status</th>
<th>Action</th>
</tr>
<?php while($row=mysqli_fetch_assoc($merchants)){ ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['shop_name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['phone']); ?></td>
<td><?php echo strtoupper($row['kyc_status']); ?></td>
<td>
<a class="view" href="merchant-details.php?id=<?php echo $row['id']; ?>">View</a>
<?php if($row['kyc_status']!='verified'){ ?>
<a class="verify" href="merchants.php?verify=<?php echo $row['id']; ?>">Verify</a>
<a class="reject" href="merchants.php?reject=<?php echo $row['id']; ?>">Reject</a>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>
