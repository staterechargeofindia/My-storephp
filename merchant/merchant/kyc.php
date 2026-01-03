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

$kyc_q = mysqli_query($conn, "SELECT * FROM merchant_kyc WHERE merchant_id=$merchant_id");
$kyc = mysqli_fetch_assoc($kyc_q);

if (isset($_POST['submit_kyc'])) {

    $aadhaar = mysqli_real_escape_string($conn, trim($_POST['aadhaar']));
    $pan = mysqli_real_escape_string($conn, trim($_POST['pan']));
    $gst = mysqli_real_escape_string($conn, trim($_POST['gst']));
    $msme = mysqli_real_escape_string($conn, trim($_POST['msme']));
    $account_name = mysqli_real_escape_string($conn, trim($_POST['account_name']));
    $account_number = mysqli_real_escape_string($conn, trim($_POST['account_number']));
    $ifsc = mysqli_real_escape_string($conn, trim($_POST['ifsc']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $district = mysqli_real_escape_string($conn, trim($_POST['district']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));

    $photo = "";
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = "kyc_" . time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/kyc/" . $photo);
    }

    if ($aadhaar=="" || $pan=="" || $account_name=="" || $account_number=="" || $ifsc=="" || $address=="") {
        $error = "All required fields must be filled";
    } else {

        if ($kyc) {
            mysqli_query($conn, "
                UPDATE merchant_kyc SET
                aadhaar='$aadhaar',
                pan='$pan',
                gst='$gst',
                msme='$msme',
                account_name='$account_name',
                account_number='$account_number',
                ifsc='$ifsc',
                address='$address',
                pincode='$pincode',
                district='$district',
                state='$state',
                photo='$photo'
                WHERE merchant_id=$merchant_id
            ");
        } else {
            mysqli_query($conn, "
                INSERT INTO merchant_kyc
                (merchant_id,aadhaar,pan,gst,msme,account_name,account_number,ifsc,address,pincode,district,state,photo)
                VALUES
                ($merchant_id,'$aadhaar','$pan','$gst','$msme','$account_name','$account_number','$ifsc','$address','$pincode','$district','$state','$photo')
            ");
        }

        mysqli_query($conn, "UPDATE merchants SET kyc_status='pending' WHERE id=$merchant_id");

        $msg = "KYC submitted successfully. Please wait for admin verification.";
        $_SESSION['kyc_status'] = "pending";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Merchant KYC</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0}
.header{background:#2874f0;color:#fff;padding:15px;text-align:center;font-size:20px}
.container{padding:20px;max-width:900px;margin:auto}
.box{background:#fff;padding:20px;border-radius:6px}
input,textarea,select{width:100%;padding:10px;margin-top:10px}
button{margin-top:15px;padding:12px;background:#2874f0;color:#fff;border:none;border-radius:4px;width:100%;font-size:16px}
.success{color:green;margin-bottom:10px;text-align:center}
.error{color:red;margin-bottom:10px;text-align:center}
</style>
</head>
<body>

<div class="header">Merchant KYC</div>

<div class="container">
<div class="box">

<?php if($msg!=""){ ?><div class="success"><?php echo $msg; ?></div><?php } ?>
<?php if($error!=""){ ?><div class="error"><?php echo $error; ?></div><?php } ?>

<form method="post" enctype="multipart/form-data">

<input type="text" name="aadhaar" placeholder="Aadhaar Number" value="<?php echo $kyc['aadhaar']??''; ?>" required>
<input type="text" name="pan" placeholder="PAN Number" value="<?php echo $kyc['pan']??''; ?>" required>
<input type="text" name="gst" placeholder="GST Number (optional)" value="<?php echo $kyc['gst']??''; ?>">
<input type="text" name="msme" placeholder="MSME Number (optional)" value="<?php echo $kyc['msme']??''; ?>">

<input type="text" name="account_name" placeholder="Account Holder Name" value="<?php echo $kyc['account_name']??''; ?>" required>
<input type="text" name="account_number" placeholder="Account Number" value="<?php echo $kyc['account_number']??''; ?>" required>
<input type="text" name="ifsc" placeholder="IFSC Code" value="<?php echo $kyc['ifsc']??''; ?>" required>

<textarea name="address" placeholder="Shop Address" required><?php echo $kyc['address']??''; ?></textarea>
<input type="text" name="pincode" placeholder="Pin Code" value="<?php echo $kyc['pincode']??''; ?>">
<input type="text" name="district" placeholder="District" value="<?php echo $kyc['district']??''; ?>">
<input type="text" name="state" placeholder="State" value="<?php echo $kyc['state']??''; ?>">

<input type="file" name="photo">

<button type="submit" name="submit_kyc">Submit KYC</button>

</form>

</div>
</div>

</body>
</html>
