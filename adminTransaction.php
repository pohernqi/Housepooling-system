<?php
session_start();

// User is not logged in, redirect to the login page to prevent unauthorised access
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

   header('Location: login.php');

   exit();
}

//if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
   // User is logged in, display the header for logged-in users
  // $userId = $_SESSION["userid"];
   include('headerAfterLogin.php');
   
   
} else {
   // User is not logged in, display the header for non-logged-in users
   include('header.html');
}

include("connect.php");

function fetchTransactionRecords($conn) {
    $sql = "SELECT tenant.name AS TenantName, tenant.email AS TenantEmail, 
    owner.name AS OwnerName, owner.email AS OwnerEmail, 
    property.propertyname, transaction.amount, transaction.paymentDate, transaction.paymentType
    FROM transaction
    INNER JOIN agreement ON transaction.agreementId = agreement.agreementId
    INNER JOIN tenant ON agreement.tenantId = tenant.tenantId
    INNER JOIN property ON agreement.propertyId = property.propertyId
    INNER JOIN owner ON property.ownerId = owner.ownerId";

   $result = mysqli_query($conn, $sql);
   if (!$result) {
       throw new Exception("Error fetching transactions: " . mysqli_error($conn));
   }
   return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Call the function to fetch transaction records
$transactionRecords = fetchTransactionRecords($conn);
?>

<div class="container">
    <h3>View Transactions</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Tenant Email</th>
                <th>Owner Name</th>
                <th>Owner Email</th>
                <th>Property Name</th>
                <th>Amount</th>
                <th>Pay Date</th>
                <th>Pay Type</th>
            </tr>
        </thead>
        <tbody>
                        <?php if (empty($transactionRecords)) : ?>
                            <tr>
                                <td colspan="9">
                                    <div class="error">No Transaction Available.</div>
                                </td>
                            </tr>
                        <?php else : ?>
        <tbody>
            <?php foreach ($transactionRecords as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['TenantName']) ?></td>
                    <td><?= htmlspecialchars($transaction['TenantEmail']) ?></td>
                    <td><?= htmlspecialchars($transaction['OwnerName']) ?></td>
                    <td><?= htmlspecialchars($transaction['OwnerEmail']) ?></td>
                    <td><?= htmlspecialchars($transaction['propertyname']) ?></td>
                    <td><?= htmlspecialchars($transaction['amount']) ?></td>
                    <td><?= htmlspecialchars($transaction['paymentDate']) ?></td>
                    <td><?= htmlspecialchars($transaction['paymentType']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php endif; ?>
    </table>
</div>

<style>
        body {
            background-color: rgb(255, 250, 205);
        }

        table {
            border-collapse: collapse;
        }

        th {
            background-color: black;
            Color: white;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-spacing: 0 15px;
            padding: 6px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px;
        }

        button {
            margin-top: 10px;
        }
    </style>
<?php include('footer.html'); ?>