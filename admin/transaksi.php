<?php
// Start the session and connect to the database
session_start();

include "../conn.php";
include "../css/sidebar.php";

// Insert New Transaction
if (isset($_POST['submit'])) {
    $id_outlet = $_POST['id_outlet'];
    $kode_invoice = $_POST['kode_invoice'];
    $id_member = $_POST['id_member'];
    $tgl = date('Y-m-d');
    $batas_waktu = $_POST['batas_waktu']; // Adding batas_waktu field
    $biaya_tambahan = $_POST['biaya_tambahan'];
    $diskon = $_POST['diskon'];
    $pajak = $_POST['pajak'];
    $status = 'baru'; // default status
    $dibayar = 'belum_dibayar'; // default unpaid

    // Insert into tb_transaksi
    $query = "INSERT INTO tb_transaksi (id_outlet, kode_invoice, id_member, tgl, batas_waktu, biaya_tambahan, diskon, pajak, status, dibayar, id_user) 
              VALUES ('$id_outlet', '$kode_invoice', '$id_member', '$tgl', '$batas_waktu', '$biaya_tambahan', '$diskon', '$pajak', '$status', '$dibayar', '$_SESSION[user_id]')";

    if (mysqli_query($conn, $query)) {
        $id_transaksi = mysqli_insert_id($conn); // Get the last inserted transaction ID
        
        // Insert the related transaction details (items)
        $paket_ids = $_POST['id_paket']; // Array of package IDs
        $qtys = $_POST['qty']; // Array of quantities

        for ($i = 0; $i < count($paket_ids); $i++) {
            $id_paket = $paket_ids[$i];
            $qty = $qtys[$i];

            $query_detail = "INSERT INTO tb_detail_transaksi (id_transaksi, id_paket, qty) VALUES ('$id_transaksi', '$id_paket', '$qty')";
            mysqli_query($conn, $query_detail);
        }
        
        echo "Transaction added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Update Transaction Payment Status
if (isset($_POST['pay'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $dibayar = 'dibayar'; // Mark as paid

    $query = "UPDATE tb_transaksi SET dibayar = '$dibayar', tgl_bayar = NOW() WHERE id = '$id_transaksi'";
    mysqli_query($conn, $query);
    
    echo "Transaction has been marked as paid.";
}

// Update Transaction Status
if (isset($_POST['update_status'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $new_status = $_POST['status']; // Get new status from dropdown

    $query = "UPDATE tb_transaksi SET status = '$new_status' WHERE id = '$id_transaksi'";
    mysqli_query($conn, $query);
    
    echo "Transaction status updated to $new_status.";
}

// Delete Transaction and Its Details
if (isset($_POST['delete'])) {
    $id_transaksi = $_POST['id_transaksi'];

    // Delete details first due to foreign key constraint
    $query_details = "DELETE FROM tb_detail_transaksi WHERE id_transaksi = '$id_transaksi'";
    mysqli_query($conn, $query_details);

    // Delete the main transaction
    $query_transaksi = "DELETE FROM tb_transaksi WHERE id = '$id_transaksi'";
    mysqli_query($conn, $query_transaksi);
    
    echo "Transaction deleted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction Management</title>
</head>
<body>

<!-- Add New Transaction Form -->
<h2>Add New Transaction</h2>
<form method="POST" action="">
    <label>Outlet:</label>
    <select name="id_outlet">
        <!-- Populate options from tb_outlet -->
        <?php
        $outlet_query = "SELECT * FROM tb_outlet";
        $outlet_result = mysqli_query($conn, $outlet_query);
        while ($outlet = mysqli_fetch_assoc($outlet_result)) {
            echo "<option value='{$outlet['id']}'>{$outlet['nama']}</option>";
        }
        ?>
    </select>

    <label>Invoice Code:</label>
    <input type="text" name="kode_invoice">

    <label>Member:</label>
    <select name="id_member">
        <!-- Populate options from tb_member -->
        <?php
        $member_query = "SELECT * FROM tb_member";
        $member_result = mysqli_query($conn, $member_query);
        while ($member = mysqli_fetch_assoc($member_result)) {
            echo "<option value='{$member['id']}'>{$member['nama']}</option>";
        }
        ?>
    </select>

    <label>Batas Waktu:</label>
    <input type="date" name="batas_waktu"> <!-- Changed to date input type -->

    <label>Additional Cost:</label>
    <input type="number" name="biaya_tambahan">

    <label>Discount:</label>
    <input type="number" name="diskon">

    <label>Tax:</label>
    <input type="number" name="pajak">

    <!-- Items/Packages for this transaction -->
    <div id="package-items">
        <label>Package:</label>
        <select name="id_paket[]">
            <!-- Populate options from tb_paket -->
            <?php
            $paket_query = "SELECT * FROM tb_paket";
            $paket_result = mysqli_query($conn, $paket_query);
            while ($paket = mysqli_fetch_assoc($paket_result)) {
                echo "<option value='{$paket['id']}'>{$paket['nama_paket']}</option>";
            }
            ?>
        </select>

        <label>Quantity:</label>
        <input type="number" name="qty[]">
    </div>

    <button type="button" id="add-item">Add Another Item</button>

    <input type="submit" name="submit" value="Submit">
</form>

<hr>

<!-- Display Transactions -->
<h2>Transactions List</h2>
<table border="1">
    <tr>
        <th>Invoice Code</th>
        <th>Member</th>
        <th>Outlet</th>
        <th>Date</th>
        <th>Batas Waktu</th> <!-- Adding batas waktu to the table -->
        <th>Status</th>
        <th>Paid</th>
        <th>Total Price</th>
        <th>Actions</th>
    </tr>

    <?php
    // Fetch transaction with total price
    $query = "
        SELECT t.*, m.nama AS member_name, o.nama AS outlet_name, SUM(d.qty * p.harga) AS total_price
        FROM tb_transaksi t
        JOIN tb_member m ON t.id_member = m.id
        JOIN tb_outlet o ON t.id_outlet = o.id
        JOIN tb_detail_transaksi d ON t.id = d.id_transaksi
        JOIN tb_paket p ON d.id_paket = p.id
        GROUP BY t.id
    ";
    
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['kode_invoice'] . "</td>";
        echo "<td>" . $row['member_name'] . "</td>";
        echo "<td>" . $row['outlet_name'] . "</td>";
        echo "<td>" . $row['tgl'] . "</td>";
        echo "<td>" . $row['batas_waktu'] . "</td>"; // Display batas waktu
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['dibayar'] . "</td>";
        echo "<td>" . $row['total_price'] . "</td>";
        echo "<td>
                <form method='POST' action=''>
                    <input type='hidden' name='id_transaksi' value='{$row['id']}'>
                    
                    <!-- Dropdown to edit status -->
                    <select name='status'>
                        <option value='baru' " . ($row['status'] == 'baru' ? 'selected' : '') . ">Baru</option>
                        <option value='proses' " . ($row['status'] == 'proses' ? 'selected' : '') . ">Proses</option>
                        <option value='selesai' " . ($row['status'] == 'selesai' ? 'selected' : '') . ">Selesai</option>
                        <option value='diambil' " . ($row['status'] == 'diambil' ? 'selected' : '') . ">Diambil</option>
                    </select>

                    <input type='submit' name='update_status' value='Update Status'>
                    <input type='submit' name='pay' value='Mark as Paid'>
                    <input type='submit' name='delete' value='Delete'>
                </form>
              </td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
