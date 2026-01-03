<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    if ($name != "") {
        mysqli_query($conn, "INSERT INTO categories (name,status) VALUES ('$name','active')");
        $msg = "Category added successfully";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: categories.php");
    exit;
}

if (isset($_POST['update_category'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE categories SET name='$name', status='$status' WHERE id=$id");
    $msg = "Category updated successfully";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
.box{background:#fff;padding:20px;border-radius:6px;margin-bottom:20px}
input,select,button{padding:10px;width:100%;margin-top:10px}
button{background:#2874f0;color:#fff;border:none;border-radius:4px;cursor:pointer}
button:hover{background:#0f5bd3}
table{width:100%;border-collapse:collapse;margin-top:20px;background:#fff}
th,td{padding:10px;border:1px solid #ddd;text-align:center}
a{color:red;text-decoration:none}
.success{color:green;margin-bottom:10px}
</style>
</head>
<body>

<div class="header">Manage Categories</div>

<div class="container">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>

<div class="box">
<form method="post">
<h3>Add New Category</h3>
<input type="text" name="name" placeholder="Category Name" required>
<button type="submit" name="add_category">Add Category</button>
</form>
</div>

<div class="box">
<h3>All Categories</h3>
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php while($row=mysqli_fetch_assoc($categories)){ ?>
<tr>
<form method="post">
<td><?php echo $row['id']; ?></td>
<td>
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="text" name="name" value="<?php echo $row['name']; ?>">
</td>
<td>
<select name="status">
<option value="active" <?php if($row['status']=="active") echo "selected"; ?>>Active</option>
<option value="inactive" <?php if($row['status']=="inactive") echo "selected"; ?>>Inactive</option>
</select>
</td>
<td>
<button type="submit" name="update_category">Update</button><br><br>
<a href="categories.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this category?')">Delete</a>
</td>
</form>
</tr>
<?php } ?>
</table>
</div>

</div>
</body>
</html>
