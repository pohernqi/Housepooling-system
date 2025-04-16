<?php
session_start();

// Check if user is logged in, otherwise redirect
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Include headers based on login status
include('headerAfterLogin.php'); // Assuming the user is always logged in for simplicity

include("connect.php");

// Function to fetch all agreements optionally filtered by status
function fetchAgreements($conn, $status = null)
{
    $sql = "SELECT agreement.*, tenant.name AS tenantname, owner.name AS ownername, owner.email AS owneremail, property.propertyname AS propertyname
            FROM agreement
            INNER JOIN tenant ON agreement.tenantId = tenant.tenantId
            INNER JOIN property ON agreement.propertyId = property.propertyId
            INNER JOIN owner ON property.ownerId = owner.ownerId";
    if ($status) {
        $sql .= " WHERE agreement.status = '$status'";
    }
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Check if a filter has been applied
$filterStatus = isset($_POST['status_filter']) ? $_POST['status_filter'] : null;

// Fetch agreements based on the selected filter
$agreements = fetchAgreements($conn, $filterStatus);
?>
   <div class = "container">
    <div class="table-responsive">
        <h3>Agreement List</h3>

        <!-- Filter Form -->
        <form action="adminLease.php" method="post">
            <select name="status_filter">
                <option value="">All</option>
                <option value="pending" <?= $filterStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="active" <?= $filterStatus == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="terminated" <?= $filterStatus == 'terminated' ? 'selected' : '' ?>>Terminated</option>
            </select>
            <button type="submit">Filter</button>
        </form>

        <!-- Agreements Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Owner Name</th>
                    <th>Property Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Owner Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agreements)) : ?>
                    <tr>
                        <td colspan="9">
                            <div class="error">No Properties Available.</div>
                        </td>
                    </tr>
                <?php else : ?>
                    <!-- Agreements Table -->
            <tbody>
                <?php foreach ($agreements as $agreement) : ?>
                    <tr>
                        <td><?= htmlspecialchars($agreement['tenantname']) ?></td>
                        <td><?= htmlspecialchars($agreement['ownername']) ?></td>
                        <td><?= htmlspecialchars($agreement['propertyname']) ?></td>
                        <td><?= htmlspecialchars($agreement['startdate']) ?></td>
                        <td><?= htmlspecialchars($agreement['enddate']) ?></td>
                        <td><?= htmlspecialchars($agreement['status']) ?></td>
                        <td><?= htmlspecialchars($agreement['owneremail']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
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

    .container{
        padding: 5px;
    }
</style>
<?php include('footer.html'); ?>