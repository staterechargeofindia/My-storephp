<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

$settings_q = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
$settings = mysqli_fetch_assoc($settings_q);

if (isset($_POST['save_settings'])) {
    $upi_id = mysqli_real_escape_string($conn, trim($_POST['upi_id']));
    $site_name = mysqli_real_escape_string($conn, trim($_POST['site_name']));

    if ($settings) {
        mysqli_query($conn, "UPDATE settings SET upi_id='$upi_id', site_name='$site_name' WHERE id=".$settings['id']);
    } else {
        mysqli_query($conn, "INSERT INTO settings (upi_id, site_name) VALUES ('$upi_id','$site_name')");
    }
    $msg = "Settings updated successfully";
    $settings = ['upi_id'=>$upi_id,'site_name'=>$site_name];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px}
.box{background:#fff;padding:20px;border-radius:6px;max-width:500px;margin:auto}
input,button{width:100%;padding:10px;margin-top:10px}
button{background:#2874f0;color:#fff;border:none;border-radius:4px}
.success{color:green;margin-bottom:10px;text-align:center}
</style>
</head>
<body>

<div class="header">Settings</div>

<div class="container">
<div class="box">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>

<form method="post">
<label>Site Name</label>
<input type="text" name="site_name" value="<?php echo isset($settings['site_name'])?$settings['site_name']:""; ?>" required>

<label>UPI ID</label>
<input type="text" name="upi_id" value="<?php echo isset($settings['upi_id'])?$settings['upi_id']:""; ?>" required>

<button type="submit" name="save_settings">Save Settings</button>
</form>

</div>
</div>

</body>
</html>
