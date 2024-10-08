<?php
session_start();
// If already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        header('Location: admin/');
    } elseif ($role == 'kasir') {
        header('Location: kasir/');
    } elseif ($role == 'owner') {
        header('Location: owner/');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Link ke CSS -->
</head>
<body>
    <form action="login.php" method="post">
        <table>
            <tr>
                <td>Username</td>
                <td>:</td>
                <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="text-align: right;"><input type="submit" value="Login"></td>
            </tr>
        </table>
    </form>
</body>
</html>
