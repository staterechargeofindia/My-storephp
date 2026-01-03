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

$merchant_id = $_SESSION['merchant_id'];
$msg = "";
$error = "";

$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status='active' ORDER BY name ASC");

if (isset($_POST['add_product'])) {

    $category_id = (int)$_POST['category_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $mrp = floatval($_POST['mrp']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    if ($mrp <= 0 || $price <= 0 || $price > $mrp) {
        $error = "Invalid price or MRP";
    } elseif ($name == "" || $category_id == 0) {
        $error = "All required fields must be filled";
    } else {

        $discount = round((($mrp - $price) / $mrp) * 100);

        mysqli_query($conn, "
            INSERT INTO products 
            (merchant_id, category_id, name, mrp, price, discount_percent, description, status)
            VALUES 
            ($merchant_id, $category_id, '$name', '$mrp', '$price', '$discount', '$description', 'active')
        ");

        $product_id = mysqli_insert_id($conn);

        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $img_name) {

                if ($img_name == "") continue;

                $ext = pathinfo($img_name, PATHINFO_EXTENSION);
                $img = "prod_" . time() . "_" . rand(1000,9999) . "." . $ext;

                move_uploaded_file(
                    $_FILES['images']['tmp_name'][$key],
                    "../uploads/products/" . $img
                );

                mysqli_query($conn, "
                    INSERT INTO product_images (product_id, image)
                    VALUES ($product_id, '$img')
                );
            }
        }

        $msg = "Product added successfully";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px;max-width:900px;margin:auto}
.box{background:#fff;padding:25px;border-radius:6px}
input,select,textarea{width:100%;padding:10px;margin-top:10px}
button{margin-top:15px;padding:12px;background:#2874f0;color:#fff;border:none;border-radius:4px;width:100%;font-size:16px}
.success{color:green;text-align:center;margin-bottom:10px}
.error{color:red;text-align:center;margin-bottom:10px}
.note{font-size:13px;color:#555;margin-top:10px}
</style>
</head>
<body>

<div class="header">Add Product</div>

<div class="container">
<div class="box">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>
<?php if($error!=""){ ?><div class="error"><?php echo $error; ?></div><?php } ?>

<form method="post" enctype="multipart/form-data">

<select name="category_id" required>
<option value="">Select Category</option>
<?php while($c=mysqli_fetch_assoc($categories)){ ?>
<option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
<?php } ?>
</select>

<input type="text" name="name" placeholder="Product Name" required>
<input type="number" step="0.01" name="mrp" placeholder="MRP" required>
<input type="number" step="0.01" name="price" placeholder="Selling Price" required>

<textarea name="description" placeholder="Product Description"></textarea>

<input type="file" name="images[]" multiple required>
<div class="note">Minimum 1 image required. You can upload multiple images.</div>

<button type="submit" name="add_product">Add Product</button>

</form>

</div>
</div>

</body>
</html>
