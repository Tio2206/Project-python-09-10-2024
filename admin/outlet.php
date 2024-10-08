<?php

$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'laundry_db';
include("../css/sidebar.php");

$conn = new mysqli($host, $user, $password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function createOutlet($conn, $nama, $alamat, $tlp) {
    $sql = "INSERT INTO tb_outlet (nama, alamat, tlp) VALUES ('$nama', '$alamat', '$tlp')";
    return $conn->query($sql);
}


function readOutlets($conn) {
    $sql = "SELECT * FROM tb_outlet";
    return $conn->query($sql);
}


function getOutlet($conn, $id) {
    $sql = "SELECT * FROM tb_outlet WHERE id = '$id'";
    return $conn->query($sql)->fetch_assoc();
}


function updateOutlet($conn, $id, $nama, $alamat, $tlp) {
    $sql = "UPDATE tb_outlet SET nama = '$nama', alamat = '$alamat', tlp = '$tlp' WHERE id = '$id'";
    return $conn->query($sql);
}


function deleteOutlet($conn, $id) {
    $sql = "DELETE FROM tb_outlet WHERE id = '$id'";
    return $conn->query($sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $tlp = $_POST['tlp'];
        if (createOutlet($conn, $nama, $alamat, $tlp)) {
            echo "New outlet added successfully.<br>";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $tlp = $_POST['tlp'];
        if (updateOutlet($conn, $id, $nama, $alamat, $tlp)) {
            echo "Outlet updated successfully.<br>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (deleteOutlet($conn, $id)) {
        echo "Outlet deleted successfully.<br>";
    }
}


$outlets = readOutlets($conn);
?>


<head>
    <link rel="stylesheet" href="../css/outlet.css"> 
</head>
<body>
<h2>Outlet List</h2>


<div class="action-buttons">
    <button id="show-form-btn" class="add-outlet-btn">Tambah Outlet</button>
</div>


<div id="outlet-form" class="outlet-form" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php if (isset($_GET['edit'])) { echo $_GET['edit']; } ?>">
        Name: <input type="text" name="nama" value="<?php if (isset($_GET['edit'])) { echo getOutlet($conn, $_GET['edit'])['nama']; } ?>" required><br>
        Address: <input type="text" name="alamat" value="<?php if (isset($_GET['edit'])) { echo getOutlet($conn, $_GET['edit'])['alamat']; } ?>" required><br>
        Phone: <input type="text" name="tlp" value="<?php if (isset($_GET['edit'])) { echo getOutlet($conn, $_GET['edit'])['tlp']; } ?>" required><br>
        <?php if (isset($_GET['edit'])): ?>
            <input type="submit" name="update" value="Update Outlet">
            <a href="outlet.php"><button type="button">Clear</button></a> 
        <?php else: ?>
            <input type="submit" name="create" value="Add Outlet">
            <a href="outlet.php"><button type="button">Clear</button></a> 
        <?php endif; ?>
    </form>
</div>


<h2>Outlet List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $outlets->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['alamat']; ?></td>
        <td><?php echo $row['tlp']; ?></td>
        <td>
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
            <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
    document.getElementById('show-form-btn').addEventListener('click', function() {
        var form = document.getElementById('outlet-form');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
</script>
</body>
