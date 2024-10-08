<?php
// Start the session and connect to the database
session_start();
$conn = mysqli_connect("localhost", "root", "", "laundry_db");
include "../css/sidebar.php";


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Laporan</title>
    <link rel="stylesheet" href="../css/laporan.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<h2>Generate Laporan - View Transactions</h2>

<!-- Table to display transactions -->
<table>
    <thead>
        <tr>
            <th>Invoice Code</th>
            <th>Member</th>
            <th>Outlet</th>
            <th>Date</th>
            <th>Batas Waktu</th> <!-- Adding batas waktu to the table -->
            <th>Status</th>
            <th>Paid</th>
            <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
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
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Button to print the report -->
<button onclick="window.print()">Cetak Laporan</button>

</body>
</html>
