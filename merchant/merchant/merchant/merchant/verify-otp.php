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

if (isset($_POST['verify_otp'])) {

    $otp = mysqli_real_escape_string($conn, trim($_POST['otp']));
    $now = date("Y-m-d H:i:s");

    if ($otp == "") {
        $error = "OTP is required";
    } else {

        $q = mysqli_query($conn, "
            SELECT * FROM admin_otp 
            WHERE merchant_id=$merchant_id 
              AND otp='$otp' 
              AND is_used='no' 
              AND expires_at >= '$now'
            ORDER BY id DESC
            LIMIT 1
        ");

        if (mysqli_num_rows($q) == 1) {

            $row = mysqli_fetch_assoc($q);

            mysqli_query($conn, "
                UPDATE admin_otp 
                SET is_used='yes' 
                WHERE id=".$row['id']
            );

            mysqli_query($conn, "
                UPDATE merchants 
                SET kyc_status='verified' 
                WHERE id=$merchant_id
            ");

            $_SESSION['kyc_status'] = 'verified';

            $msg = "OTP verified successfully. Your KYC is now verified.";

        } else {
            $error = "Invalid or expired OTP";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px;max-width:450px;margin:auto}
.box{background:#fff;padding:25px;border-radius:6px}
input{width:100%;padding:12px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px}
button{width:100%;padding:12px;background:#2874f0;color:#fff;border:none;border-radius:4px;font-size:16px}
.success{color:green;text-align:center;margin-bottom:10px}
.error{color:red;text-align:center;margin-bottom:10px}
.note{text-align:center;color:#555;margin-top:15px}
</style>
</head>
<body>

<div class="header">Verify OTP</div>

<div class="container">
<div class="box">

<?php if($msg!=""){ ?>
    <div class="success"><?php echo $msg; ?></div>
<?php } ?>

<?php if($error!=""){ ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="post">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit" name="verify_otp">Verify OTP</button>
</form>

<div class="note">
After successful verification, you can upload products.
</div>

</div>
</div>

</body>
</html>
