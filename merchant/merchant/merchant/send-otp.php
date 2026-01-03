<?php
session_start();
include "../db.php";

if (!isset($_SESSION['merchant_id'])) {
    header("Location: login.php");
    exit;
}

$merchant_id = $_SESSION['merchant_id'];
$msg = "";
$error = "";

if (isset($_POST['send_otp'])) {

    $otp = rand(100000,999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    mysqli_query($conn, "UPDATE admin_otp SET is_used='yes' WHERE merchant_id=$merchant_id");

    $insert = mysqli_query($conn, "
        INSERT INTO admin_otp (merchant_id, otp, expires_at)
        VALUES ($merchant_id, '$otp', '$expiry')
    ");

    if ($insert) {
        $msg = "OTP generated successfully. Please contact admin to get OTP.";
    } else {
        $error = "Failed to generate OTP. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Send OTP</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px;max-width:500px;margin:auto}
.box{background:#fff;padding:25px;border-radius:6px}
button{width:100%;padding:12px;background:#2874f0;color:#fff;border:none;border-radius:4px;font-size:16px}
.success{color:green;text-align:center;margin-bottom:10px}
.error{color:red;text-align:center;margin-bottom:10px}
.info{text-align:center;color:#555;margin-bottom:15px}
</style>
</head>
<body>

<div class="header">OTP Request</div>

<div class="container">
<div class="box">

<div class="info">
Click below to generate OTP. Admin will share OTP with you.
</div>

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>
<?php if($error!=""){ ?><div class="error"><?php echo $error; ?></div><?php } ?>

<form method="post">
<button type="submit" name="send_otp">Send OTP</button>
</form>

</div>
</div>

</body>
</html>
