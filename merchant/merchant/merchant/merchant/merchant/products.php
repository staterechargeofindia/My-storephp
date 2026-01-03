<?php
session_start();
include "../db.php";

if (!isset($_SESSION['merchant_id'])) {
    header("Location: login.php");
    exit;
}

$merchant_id = $_SESSION['merchant_id'];

if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];

    $imgs = mysqli_query($conn, "SELECT image FROM product_images WHERE product_id=$pid");
    while ($img = mysqli_fetch_assoc($imgs)) {
        if (file_exists("../uploads/products/" . $img['image'])) {
            unlink("../uploads/products/" . $img['image']);
        }
    }

    mysqli_query($conn, "DELETE FROM product_images WHERE product_id=$pid");
    mysqli_query($conn, "DELETE FROM products WHERE id=$pid AND merchant_id=$merchant_id");

    header("Location: products.php");
    exit;
}

$products = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name
    FROM products p
    JOIN categories c ON c.id=p.category_id
    WHERE p.merchant_id=$merchant_id
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Products</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:10px;border:1px solid #ddd;text-align:center}
a{padding:6px 10px;text-decoration:none;border-radius:4px;color:#fff}
.edit{background:#17a2b8}
.delete{background:#dc3545}
.add{background:#28a745;display:inline-block;margin-bottom:15px}
</style>
</head>
<body>

<div class="header">My Products</div>

<div class="container">
<a class="add" href="add-product.php">Add New Product</a>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>MRP</th>
<th>Price</th>
<th>Discount %</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php while($p=mysqli_fetch_assoc($products)){ ?>
<tr>
<td><?php echo $p['id']; ?></td>
<td><?php echo htmlspecialchars($p['name']); ?></td>
<td><?php echo htmlspecialchars($p['category_name']); ?></td>
<td>₹<?php echo $p['mrp']; ?></td>
<td>₹<?php echo $p['price']; ?></td>
<td><?php echo $p['discount_percent']; ?>%</td>
<td><?php echo strtoupper($p['status']); ?></td>
<td>
<a class="edit" href="edit-product.php?id=<?php echo $p['id']; ?>">Edit</a>
<a class="delete" href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
</td>
</tr>
<?php } ?>
</table>

</div>

</body>
</html>
