<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

if (isset($_POST['add_banner'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $link = mysqli_real_escape_string($conn, trim($_POST['link']));
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/banners/" . $image);
    }

    if ($name != "" && $image != "") {
        mysqli_query($conn, "INSERT INTO banners (name,image,link,status) VALUES ('$name','$image','$link','$status')");
        $msg = "Banner added successfully";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT image FROM banners WHERE id=$id");
    if ($row = mysqli_fetch_assoc($q)) {
        if (file_exists("../uploads/banners/" . $row['image'])) {
            unlink("../uploads/banners/" . $row['image']);
        }
    }
    mysqli_query($conn, "DELETE FROM banners WHERE id=$id");
    header("Location: banners.php");
    exit;
}

if (isset($_POST['update_banner'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $link = mysqli_real_escape_string($conn, trim($_POST['link']));
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/banners/" . $image);
        mysqli_query($conn, "UPDATE banners SET name='$name', link='$link', status='$status', image='$image' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE banners SET name='$name', link='$link', status='$status' WHERE id=$id");
    }

    $msg = "Banner updated successfully";
}

$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Banners</title>
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
img{max-width:120px}
a{color:red;text-decoration:none}
.success{color:green;margin-bottom:10px}
</style>
</head>
<body>

<div class="header">Manage Banners</div>

<div class="container">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>

<div class="box">
<form method="post" enctype="multipart/form-data">
<h3>Add New Banner</h3>
<input type="text" name="name" placeholder="Banner Name" required>
<input type="file" name="image" required>
<input type="text" name="link" placeholder="Banner Link">
<select name="status">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
</select>
<button type="submit" name="add_banner">Add Banner</button>
</form>
</div>

<div class="box">
<h3>All Banners</h3>
<table>
<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Link</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php while($row=mysqli_fetch_assoc($banners)){ ?>
<tr>
<form method="post" enctype="multipart/form-data">
<td><?php echo $row['id']; ?></td>
<td><img src="../uploads/banners/<?php echo $row['image']; ?>"></td>
<td>
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="text" name="name" value="<?php echo $row['name']; ?>">
</td>
<td><input type="text" name="link" value="<?php echo $row['link']; ?>"></td>
<td>
<select name="status">
<option value="active" <?php if($row['status']=="active") echo "selected"; ?>>Active</option>
<option value="inactive" <?php if($row['status']=="inactive") echo "selected"; ?>>Inactive</option>
</select>
</td>
<td>
<input type="file" name="image">
<button type="submit" name="update_banner">Update</button><br><br>
<a href="banners.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this banner?')">Delete</a>
</td>
</form>
</tr>
<?php } ?>
</table>
</div>

</div>
</body>
</html>
