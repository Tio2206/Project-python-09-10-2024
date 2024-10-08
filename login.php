<?php
include "conn.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // No hashing, using plain text comparison

    $query = "SELECT * FROM tb_user WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        // Successful login, set session variables
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['name'] = $data['nama'];
        $_SESSION['outlet_id'] = $data['id_outlet'];
        $_SESSION['role'] = $data['role'];

        session_regenerate_id(true); // Prevent session fixation

        if ($data['role'] == 'admin') {
            header('Location: admin/');
        } elseif ($data['role'] == 'kasir') {
            header('Location: kasir/');
        } elseif ($data['role'] == 'owner') {
            header('Location: owner/');
        }
        exit;
    } else {
        // Invalid login credentials
        echo "<script>alert('Username or password incorrect');window.location.href = 'index.php';</script>";
    }
}
?>
