<?php
include "../conn.php";

// Function to create a member
function createMember($conn, $nama, $alamat, $jenis_kelamin, $tlp) {
    $sql = "INSERT INTO tb_member (nama, alamat, jenis_kelamin, tlp) VALUES ('$nama', '$alamat', '$jenis_kelamin', '$tlp')";
    return $conn->query($sql);
}

// Function to read all members
function readMembers($conn) {
    $sql = "SELECT * FROM tb_member";
    return $conn->query($sql);
}

// Function to get a single member's details
function getMember($conn, $id) {
    $sql = "SELECT * FROM tb_member WHERE id = '$id'";
    return $conn->query($sql)->fetch_assoc();
}

// Function to update a member
function updateMember($conn, $id, $nama, $alamat, $jenis_kelamin, $tlp) {
    $sql = "UPDATE tb_member SET nama = '$nama', alamat = '$alamat', jenis_kelamin = '$jenis_kelamin', tlp = '$tlp' WHERE id = '$id'";
    return $conn->query($sql);
}

// Function to delete a member
function deleteMember($conn, $id) {
    $sql = "DELETE FROM tb_member WHERE id = '$id'";
    return $conn->query($sql);
}

// Handle Create, Update, Delete operations based on form submission
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

// Display all members (Read)
$members = readMembers($conn);
?>

<!-- HTML form for creating or updating members -->
<h2>Member Form</h2>
<form method="post" action="">
    <input type="hidden" name="id" value="<?php if (isset($_GET['edit'])) { echo $_GET['edit']; } ?>">
    Name: <input type="text" name="nama" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['nama']; } ?>" required><br>
    Address: <input type="text" name="alamat" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['alamat']; } ?>" required><br>
    Gender: 
    <select name="jenis_kelamin" required>
        <option value="L" <?php if (isset($_GET['edit']) && getMember($conn, $_GET['edit'])['jenis_kelamin'] == 'L') echo 'selected'; ?>>Male</option>
        <option value="P" <?php if (isset($_GET['edit']) && getMember($conn, $_GET['edit'])['jenis_kelamin'] == 'P') echo 'selected'; ?>>Female</option>
    </select><br>
    Phone: <input type="text" name="tlp" value="<?php if (isset($_GET['edit'])) { echo getMember($conn, $_GET['edit'])['tlp']; } ?>" required><br>
    <?php if (isset($_GET['edit'])): ?>
        <input type="submit" name="update" value="Update Member">
    <?php else: ?>
        <input type="submit" name="create" value="Add Member">
    <?php endif; ?>
</form>

<!-- Display list of members -->
<h2>Member List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Address</th>
        <th>Gender</th>
        <th>Phone</th>
        <th>Actions</th>
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
            <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
