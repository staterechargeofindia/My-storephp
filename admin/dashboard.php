<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$today = date("Y-m-d");

$pending_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status='placed'");
$pending = mysqli_fetch_assoc($pending_q)['total'];

$confirmed_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status='confirmed'");
$confirmed = mysqli_fetch_assoc($confirmed_q)['total'];

$dispatched_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status='dispatched'");
$dispatched = mysqli_fetch_assoc($dispatched_q)['total'];

$delivered_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status='delivered'");
$delivered = mysqli_fetch_assoc($delivered_q)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f4f6f9;
            margin:0;
        }
        .header{
            background:#2874f0;
            color:#fff;
            padding:15px;
            text-align:center;
            font-size:20px;
        }
        .container{
            padding:20px;
        }
        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:20px;
        }
        .card{
            background:#fff;
            padding:20px;
            border-radius:6px;
            box-shadow:0 2px 5px rgba(0,0,0,0.1);
            text-align:center;
        }
        .card h3{
            margin:0;
            color:#555;
        }
        .card p{
            font-size:28px;
            margin:10px 0 0;
            color:#2874f0;
        }
        .menu{
            margin-top:30px;
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:15px;
        }
        .menu a{
            display:block;
            text-decoration:none;
            background:#fff;
            padding:15px;
            text-align:center;
            border-radius:5px;
            color:#333;
            box-shadow:0 1px 4px rgba(0,0,0,0.1);
        }
        .menu a:hover{
            background:#e9f0ff;
        }
    </style>
</head>
<body>

<div class="header">
    Admin Dashboard
</div>

<div class="container">

    <div class="cards">
        <div class="card">
            <h3>Pending Orders</h3>
            <p><?php echo $pending; ?></p>
        </div>
        <div class="card">
            <h3>Confirmed Orders</h3>
            <p><?php echo $confirmed; ?></p>
        </div>
        <div class="card">
            <h3>Dispatched Orders</h3>
            <p><?php echo $dispatched; ?></p>
        </div>
        <div class="card">
            <h3>Delivered Orders</h3>
            <p><?php echo $delivered; ?></p>
        </div>
    </div>

    <div class="menu">
        <a href="categories.php">Manage Categories</a>
        <a href="banners.php">Manage Banners</a>
        <a href="merchants.php">Merchants</a>
        <a href="orders.php">All Orders</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>

</div>

</body>
</html>
