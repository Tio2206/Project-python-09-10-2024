<?php
include "../conn.php";
include("../css/sidebar.php");

function createPaket($conn, $id_outlet, $jenis, $nama_paket, $harga) {
    $sql = "INSERT INTO tb_paket (id_outlet, jenis, nama_paket, harga) VALUES ('$id_outlet', '$jenis', '$nama_paket', '$harga')";
    return $conn->query($sql);
}


function readPakets($conn) {
    $sql = "SELECT tb_paket.*, tb_outlet.nama AS outlet_name FROM tb_paket JOIN tb_outlet ON tb_paket.id_outlet = tb_outlet.id";
    return $conn->query($sql);
}


function getPaket($conn, $id) {
    $sql = "SELECT * FROM tb_paket WHERE id = '$id'";
    return $conn->query($sql)->fetch_assoc();
}


function updatePaket($conn, $id, $id_outlet, $jenis, $nama_paket, $harga) {
    $sql = "UPDATE tb_paket SET id_outlet = '$id_outlet', jenis = '$jenis', nama_paket = '$nama_paket', harga = '$harga' WHERE id = '$id'";
    return $conn->query($sql);
}


function deletePaket($conn, $id) {
    $sql = "DELETE FROM tb_paket WHERE id = '$id'";
    return $conn->query($sql);
}


function getOutlets($conn) {
    $sql = "SELECT id, nama FROM tb_outlet";
    return $conn->query($sql);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $id_outlet = $_POST['id_outlet'];
        $jenis = $_POST['jenis'];
        $nama_paket = $_POST['nama_paket'];
        $harga = $_POST['harga'];
        if (createPaket($conn, $id_outlet, $jenis, $nama_paket, $harga)) {
            echo "New package added successfully.<br>";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $id_outlet = $_POST['id_outlet'];
        $jenis = $_POST['jenis'];
        $nama_paket = $_POST['nama_paket'];
        $harga = $_POST['harga'];
        if (updatePaket($conn, $id, $id_outlet, $jenis, $nama_paket, $harga)) {
            echo "Package updated successfully.<br>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (deletePaket($conn, $id)) {
        echo "Package deleted successfully.<br>";
    }
}

$pakets = readPakets($conn);
$outlets = getOutlets($conn); // Fetching outlets for the dropdown
?>


<head>
    <link rel="stylesheet" href="../css/paket.css"> 
</head>
<body>
<h2>Package List</h2>


<div class="action-buttons">
    <button id="show-form-btn" class="add-package-btn">Tambah Paket</button>
</div>


<div id="package-form" class="package-form" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php if (isset($_GET['edit'])) { echo $_GET['edit']; } ?>">
        
        Outlet:
        <select name="id_outlet" required>
            <?php while ($outlet = $outlets->fetch_assoc()): ?>
                <option value="<?php echo $outlet['id']; ?>" 
                    <?php if (isset($_GET['edit']) && getPaket($conn, $_GET['edit'])['id_outlet'] == $outlet['id']) echo 'selected'; ?>>
                    <?php echo $outlet['nama']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>
        
        Type: 
        <select name="jenis" required>
            <option value="kiloan" <?php if (isset($_GET['edit']) && getPaket($conn, $_GET['edit'])['jenis'] == 'kiloan') echo 'selected'; ?>>Kiloan</option>
            <option value="selimut" <?php if (isset($_GET['edit']) && getPaket($conn, $_GET['edit'])['jenis'] == 'selimut') echo 'selected'; ?>>Selimut</option>
            <option value="bed_cover" <?php if (isset($_GET['edit']) && getPaket($conn, $_GET['edit'])['jenis'] == 'bed_cover') echo 'selected'; ?>>Bed Cover</option>
            <option value="kaos" <?php if (isset($_GET['edit']) && getPaket($conn, $_GET['edit'])['jenis'] == 'kaos') echo 'selected'; ?>>Kaos</option>
        </select><br>
        
        Package Name: <input type="text" name="nama_paket" value="<?php if (isset($_GET['edit'])) { echo getPaket($conn, $_GET['edit'])['nama_paket']; } ?>" required><br>
        Price: <input type="text" name="harga" value="<?php if (isset($_GET['edit'])) { echo getPaket($conn, $_GET['edit'])['harga']; } ?>" required><br>
        
        <?php if (isset($_GET['edit'])): ?>
            <input type="submit" name="update" value="Update Package">
            <a href="paket.php"><button type="button">Clear</button></a> 
        <?php else: ?>
            <input type="submit" name="create" value="Add Package">
            <a href="paket.php"><button type="button">Clear</button></a> 
        <?php endif; ?>
    </form>
</div>


<h2>Package List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Outlet</th>
        <th>Type</th>
        <th>Package Name</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $pakets->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['outlet_name']; ?></td>
        <td><?php echo $row['jenis']; ?></td>
        <td><?php echo $row['nama_paket']; ?></td>
        <td><?php echo $row['harga']; ?></td>
        <td>
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
            <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
    document.getElementById('show-form-btn').addEventListener('click', function() {
        var form = document.getElementById('package-form');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
</script>
</body>
