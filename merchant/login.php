<?php
session_start();
include "../db.php";

if (isset($_SESSION['merchant_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "All fields are required";
    } else {

        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = trim($_POST['password']);

        $sql = "SELECT * FROM merchants WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {

            $merchant = mysqli_fetch_assoc($result);

            if (password_verify($password, $merchant['password'])) {

                $_SESSION['merchant_id']   = $merchant['id'];
                $_SESSION['merchant_name'] = $merchant['name'];
                $_SESSION['shop_name']     = $merchant['shop_name'];
                $_SESSION['kyc_status']    = $merchant['kyc_status'];

                header("Location: dashboard.php");
                exit;

            } else {
                $error = "Incorrect password";
            }

        } else {
            $error = "Merchant account not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Merchant Login</title>
<style>
body{
    font-family:Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
}
.login-box{
    width:420px;
    margin:100px auto;
    background:#fff;
    padding:30px;
    border-radius:6px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
h2{
    text-align:center;
    margin-bottom:20px;
}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:4px;
}
button{
    width:100%;
    padding:12px;
    background:#2874f0;
    color:#fff;
    border:none;
    border-radius:4px;
    font-size:16px;
    cursor:pointer;
}
button:hover{
    background:#0f5bd3;
}
.error{
    color:red;
    text-align:center;
    margin-bottom:10px;
}
.note{
    text-align:center;
    margin-top:15px;
    font-size:14px;
    color:#555;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Merchant Login</h2>

    <?php if($error!=""){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="post">
        <input type="email" name="email" placeholder="Merchant Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="note">
        Only KYC verified merchants can sell products
    </div>
</div>

</body>
</html>
