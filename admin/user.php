<?php
include "../conn.php";
include("../css/sidebar.php");


function createUser($conn, $id_outlet, $nama, $username, $password, $role) {
    $sql = "INSERT INTO tb_user (id_outlet, nama, username, password, role) VALUES ('$id_outlet', '$nama', '$username', '$password', '$role')";
    return $conn->query($sql);
}


function readUsers($conn) {
    $sql = "SELECT tb_user.*, tb_outlet.nama AS outlet_name FROM tb_user JOIN tb_outlet ON tb_user.id_outlet = tb_outlet.id";
    return $conn->query($sql);
}


function getUser($conn, $id) {
    $sql = "SELECT * FROM tb_user WHERE id = '$id'";
    return $conn->query($sql)->fetch_assoc();
}


function updateUser($conn, $id, $id_outlet, $nama, $username, $role, $password = null) {
    if ($password) {
        $sql = "UPDATE tb_user SET id_outlet = '$id_outlet', nama = '$nama', username = '$username', password = '$password', role = '$role' WHERE id = '$id'";
    } else {
        $sql = "UPDATE tb_user SET id_outlet = '$id_outlet', nama = '$nama', username = '$username', role = '$role' WHERE id = '$id'";
    }
    return $conn->query($sql);
}


function deleteUser($conn, $id) {
    $sql = "DELETE FROM tb_user WHERE id = '$id'";
    return $conn->query($sql);
}


function getOutlets($conn) {
    $sql = "SELECT id, nama FROM tb_outlet";
    return $conn->query($sql);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $id_outlet = $_POST['id_outlet'];
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        if (createUser($conn, $id_outlet, $nama, $username, $password, $role)) {
            echo "New user added successfully.<br>";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $id_outlet = $_POST['id_outlet'];
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $password = !empty($_POST['password']) ? $_POST['password'] : null; // Update password if provided
        if (updateUser($conn, $id, $id_outlet, $nama, $username, $role, $password)) {
            echo "User updated successfully.<br>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (deleteUser($conn, $id)) {
        echo "User deleted successfully.<br>";
    }
}


$users = readUsers($conn);
$outlets = getOutlets($conn); 
?>

<head>
    <link rel="stylesheet" href="../css/user.css">
</head>
<body>
<h2>User Management</h2>

<div class="action-buttons">
    <button id="show-form-btn" class="add-user-btn">Add User</button>
</div>

<div id="user-form" class="user-form" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php if (isset($_GET['edit'])) { echo $_GET['edit']; } ?>">
        
        Outlet:
        <select name="id_outlet" required>
            <?php while ($outlet = $outlets->fetch_assoc()): ?>
                <option value="<?php echo $outlet['id']; ?>" 
                    <?php if (isset($_GET['edit']) && getUser($conn, $_GET['edit'])['id_outlet'] == $outlet['id']) echo 'selected'; ?>>
                    <?php echo $outlet['nama']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        Name: <input type="text" name="nama" value="<?php if (isset($_GET['edit'])) { echo getUser($conn, $_GET['edit'])['nama']; } ?>" required><br>
        Username: <input type="text" name="username" value="<?php if (isset($_GET['edit'])) { echo getUser($conn, $_GET['edit'])['username']; } ?>" required><br>
        
        Password: <input type="password" name="password" placeholder="Leave blank to keep the same"><br>

        Role: 
        <select name="role" required>
            <option value="admin" <?php if (isset($_GET['edit']) && getUser($conn, $_GET['edit'])['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="cashier" <?php if (isset($_GET['edit']) && getUser($conn, $_GET['edit'])['role'] == 'cashier') echo 'selected'; ?>>Cashier</option>
            <option value="owner" <?php if (isset($_GET['edit']) && getUser($conn, $_GET['edit'])['role'] == 'owner') echo 'selected'; ?>>Owner</option>
        </select><br>
        
        <?php if (isset($_GET['edit'])): ?>
            <input type="submit" name="update" value="Update User">
            <a href="user.php"><button type="button">Clear</button></a> 
        <?php else: ?>
            <input type="submit" name="create" value="Add User">
            <a href="user.php"><button type="button">Clear</button></a> 
        <?php endif; ?>
    </form>
</div>

<!-- Display list of users -->
<h2>User List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Outlet</th>
        <th>Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $users->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['outlet_name']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
            <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
    document.getElementById('show-form-btn').addEventListener('click', function() {
        var form = document.getElementById('user-form');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    });
</script>
</body>
