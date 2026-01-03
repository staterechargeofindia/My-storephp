<?php
session_start();
include "../db.php";

if (!isset($_SESSION['merchant_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['kyc_status'] != 'verified') {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$merchant_id = $_SESSION['merchant_id'];
$product_id = (int)$_GET['id'];

$product_q = mysqli_query($conn, "
    SELECT * FROM products 
    WHERE id=$product_id AND merchant_id=$merchant_id
    LIMIT 1
");

if (mysqli_num_rows($product_q) == 0) {
    header("Location: products.php");
    exit;
}

$product = mysqli_fetch_assoc($product_q);
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status='active' ORDER BY name ASC");
$images = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id=$product_id");

$msg = "";
$error = "";

if (isset($_POST['update_product'])) {

    $category_id = (int)$_POST['category_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $mrp = floatval($_POST['mrp']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($mrp <= 0 || $price <= 0 || $price > $mrp) {
        $error = "Invalid price or MRP";
    } elseif ($name == "" || $category_id == 0) {
        $error = "All required fields must be filled";
    } else {

        $discount = round((($mrp - $price) / $mrp) * 100);

        mysqli_query($conn, "
            UPDATE products SET
            category_id=$category_id,
            name='$name',
            mrp='$mrp',
            price='$price',
            discount_percent='$discount',
            description='$description',
            status='$status'
            WHERE id=$product_id AND merchant_id=$merchant_id
        ");

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $img_name) {

                if ($img_name == "") continue;

                $ext = pathinfo($img_name, PATHINFO_EXTENSION);
                $img = 'prod_' . time() . '_' . rand(1000,9999) . '.' . $ext;

                move_uploaded_file(
                    $_FILES['images']['tmp_name'][$key],
                    "../uploads/products/" . $img
                );

                mysqli_query($conn, "
                    INSERT INTO product_images (product_id, image)
                    VALUES ($product_id, '$img')
                ");
            }
        }

        $msg = "Product updated successfully";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px;max-width:900px;margin:auto}
.box{background:#fff;padding:25px;border-radius:6px}
input,select,textarea{width:100%;padding:10px;margin-top:10px}
button{margin-top:15px;padding:12px;background:#2874f0;color:#fff;border:none;border-radius:4px;width:100%;font-size:16px}
.success{color:green;text-align:center;margin-bottom:10px}
.error{color:red;text-align:center;margin-bottom:10px}
.images{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
.images img{max-width:120px;border:1px solid #ddd;padding:5px}
</style>
</head>
<body>

<div class="header">Edit Product</div>

<div class="container">
<div class="box">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>
<?php if($error!=""){ ?><div class="error"><?php echo $error; ?></div><?php } ?>

<form method="post" enctype="multipart/form-data">

<select name="category_id" required>
<option value="">Select Category</option>
<?php while($c=mysqli_fetch_assoc($categories)){ ?>
<option value="<?php echo $c['id']; ?>" <?php if($product['category_id']==$c['id']) echo "selected"; ?>>
<?php echo htmlspecialchars($c['name']); ?>
</option>
<?php } ?>
</select>

<input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
<input type="number" step="0.01" name="mrp" value="<?php echo $product['mrp']; ?>" required>
<input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>

<textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>

<select name="status">
<option value="active" <?php if($product['status']=="active") echo "selected"; ?>>Active</option>
<option value="inactive" <?php if($product['status']=="inactive") echo "selected"; ?>>Inactive</option>
</select>

<div class="images">
<?php while($img=mysqli_fetch_assoc($images)){ ?>
<img src="../uploads/products/<?php echo $img['image']; ?>">
<?php } ?>
</div>

<input type="file" name="images[]" multiple>
<button type="submit" name="update_product">Update Product</button>

</form>

</div>
</div>

</body>
</html>
