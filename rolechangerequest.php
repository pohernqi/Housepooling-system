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
    include('headerAfterLogin.php');
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}

include("connect.php");

// Function to fetch pending requests
function fetchPendingRequest($conn)
{
    $sql = "SELECT request_role.*, tenant.name AS tenantName, tenant.email AS tenantEmail FROM request_role INNER JOIN tenant ON tenant.tenantId = request_role.userId WHERE request_status = 'pending'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$pendingRequests = fetchPendingRequest($conn);

// Handle approve action
if (isset($_POST['approve']) && isset($_POST['selectedUser'])) {
    $requestId = $_POST['selectedUser'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Fetch tenant data based on requestId
        $tenantSql = "SELECT * FROM tenant WHERE tenantId = (SELECT userId FROM request_role WHERE requestId = ?)";
        $stmt = mysqli_prepare($conn, $tenantSql);
        mysqli_stmt_bind_param($stmt, 'i', $requestId);
        mysqli_stmt_execute($stmt);
        $tenantResult = mysqli_stmt_get_result($stmt);
        $tenantData = mysqli_fetch_assoc($tenantResult);
        mysqli_stmt_close($stmt);

        if ($tenantData) {
            $ownerSql = "INSERT INTO owner (username, password, name, email, phone, pic) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $ownerSql);
            mysqli_stmt_bind_param($stmt, 'sssssb', $tenantData['username'], $tenantData['password'], $tenantData['name'], $tenantData['email'], $tenantData['phone'], $tenantData['pic']);
            mysqli_stmt_send_long_data($stmt, 5, $tenantData['pic']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $null = NULL; // This is required as a placeholder for the blob parameter
           
            // Update tenant to is_deleted=1
            $updateSql = "UPDATE tenant SET is_deleted = 1 WHERE tenantId = (SELECT userId FROM request_role WHERE requestId = ?)";
            $stmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($stmt, 'i', $requestId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Update request status to 'approved'
            $updateSql = "UPDATE request_role SET request_status = 'approved' WHERE requestId = ?";
            $stmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($stmt, 'i', $requestId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Optionally, delete the tenant record or mark it as inactive
            // $deleteSql = "DELETE FROM tenant WHERE tenantId = ?";
            // mysqli_stmt_prepare($conn, $deleteSql);
            // mysqli_stmt_bind_param($stmt, 'i', $tenantData['tenantId']);
            // mysqli_stmt_execute($stmt);
            // mysqli_stmt_close($stmt);

            // Commit transaction
            mysqli_commit($conn);

            // Refresh the page or redirect to avoid form resubmission issues
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            throw new Exception("No tenant data found for the selected request.");
        }
    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_rollback($conn);
        // Error handling logic here
        echo "Error: " . $e->getMessage();
    }
} elseif (isset($_POST['reject']) && isset($_POST['selectedUser'])) {
    $requestId = $_POST['selectedUser'];
    $updateSql = "UPDATE request_role SET request_status = 'rejected' WHERE requestId = ?";
    $stmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($stmt, 'i', $requestId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

?>
    <div class="container">
        <h3>Role Change Request</h3>
        <form method="post" action="">
            <button type="submit" name="approve">Approve</button>
            <button type="submit" name="reject">Reject</button>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Tenant Name</th>
                        <th>Tenant Email</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingRequests)) : ?>
                        <tr>
                            <td colspan="4">No Request Available.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($pendingRequests as $request) : ?>
                            <tr>
                                <td><input type="radio" name="selectedUser" value="<?= $request['requestId'] ?>"></td>
                                <td><?= htmlspecialchars($request['tenantName']) ?></td>
                                <td><?= htmlspecialchars($request['tenantEmail']) ?></td>
                                <td><?= htmlspecialchars($request['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
<style>
        body {
            background-color: rgb(255, 250, 205);
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
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

        img {
            max-width: 300px;
            height: auto;
        }

        .button {
            margin-bottom: 10px;
        }


    </style>
<?php include('footer.html'); ?>