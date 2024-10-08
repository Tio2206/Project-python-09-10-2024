<?php
include "../conn.php";
include "../css/sidebar.php";

function createMember($conn, $nama, $alamat, $jenis_kelamin, $tlp) {
    $sql = "INSERT INTO tb_member (nama, alamat, jenis_kelamin, tlp) VALUES ('$nama', '$alamat', '$jenis_kelamin', '$tlp')";
    return $conn->query($sql);
}

function readMembers($conn) {
    $sql = "SELECT * FROM tb_member";
    return $conn->query($sql);
}

function getMember($conn, $id) {
    $sql = "SELECT * FROM tb_member WHERE id = '$id'";
    return $conn->query($sql)->fetch_assoc();
}

function updateMember($conn, $id, $nama, $alamat, $jenis_kelamin, $tlp) {
    $sql = "UPDATE tb_member SET nama = '$nama', alamat = '$alamat', jenis_kelamin = '$jenis_kelamin', tlp = '$tlp' WHERE id = '$id'";
    return $conn->query($sql);
}

function deleteMember($conn, $id) {
    $sql = "DELETE FROM tb_member WHERE id = '$id'";
    return $conn->query($sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tlp = $_POST['tlp'];
        if (createMember($conn, $nama, $alamat, $jenis_kelamin, $tlp)) {
            echo "New member added successfully.<br>";
        }
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tlp = $_POST['tlp'];
        if (updateMember($conn, $id, $nama, $alamat, $jenis_kelamin, $tlp)) {
            echo "Member updated successfully.<br>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (deleteMember($conn, $id)) {
        echo "Member deleted successfully.<br>";
    }
}

$members = readMembers($conn);
?>

<head>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>
<h2>Data Pelanggan</h2>


<div class="action-buttons">
    <button id="show-form-btn" class="add-member-btn">Tambah Pelanggan</button>
</div>


<div id="member-form" class="member-form" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="id_member" value="<?php if (isset($_GET['edit'])) { echo $_GET['edit']; } ?>">
        Nama: <input type="text" name="nama" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['nama']; } ?>" required><br>
        Alamat: <input type="text" name="alamat" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['alamat']; } ?>" required><br>
        Jenis Kelamin: 
        <select name="jenis_kelamin" required>
            <option value="L" <?php if (isset($_GET['edit']) && getMember($conn, $_GET['edit'])['jenis_kelamin'] == 'L') echo 'selected'; ?>>Laki Laki</option>
            <option value="P" <?php if (isset($_GET['edit']) && getMember($conn, $_GET['edit'])['jenis_kelamin'] == 'P') echo 'selected'; ?>>Perempuan</option>
        </select><br>
        Nomor Telphone: <input type="text" name="tlp" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['tlp']; } ?>" required><br>
        <?php if (isset($_GET['edit'])): ?>
            <input type="submit" name="update" value="Update Member" class="reset">
            <a href="member.php"><button type="button">Reset</button></a> <!-- Clear button -->
        <?php else: ?>
            <input type="submit" name="create" value="Add Member" class="reset">
            <a href="member.php"><button type="button">Reset</button></a> <!-- Clear button -->
        <?php endif; ?>
    </form>
</div>

<h2>List Daftar Pengguna</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Jenis Kelamin</th>
        <th>NO Telp</th>
        <th>Aksi</th>
    </tr>
    <?php while($row = $members->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['alamat']; ?></td>
        <td><?php echo $row['jenis_kelamin']; ?></td>
        <td><?php echo $row['tlp']; ?></td>
        <td>
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
            <a href="?delete=<?php echo $row['id']; ?>">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
    document.getElementById('show-form-btn').addEventListener('click', function() {
        var form = document.getElementById('member-form');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
</script>
</body>
