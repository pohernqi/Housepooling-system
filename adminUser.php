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

// Modify the fetchAllUsers function to accept an optional search string parameter
function fetchAllUsers($conn, $searchString = null)
{
    $users = [];

    // Get the logged-in admin's ID from the session
    $loggedInAdminId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    // List of user types and the corresponding table names
    $userTypes = [
        'admin' => 'admin',
        'searcher' => 'searcher',
        'tenant' => 'tenant',
        'owner' => 'owner'
    ];
    // Fetch users from each table
    foreach ($userTypes as $type => $tableName) {
        // Exclude the logged-in admin from the query
        $sql = "SELECT *, '$type' as usertype FROM $tableName WHERE username LIKE '%$searchString%'";
        if ($type === 'admin') {
            $sql .= " AND adminId != $loggedInAdminId";
        }
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception("Error fetching $type users: " . mysqli_error($conn));
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }

    return $users;
}
$users = fetchAllUsers($conn);
// Handle form submission for searching users
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['searchUser'])) {
    $searchString = $_GET['searchUser'];
    $users = fetchAllUsers($conn, $searchString);
}



// Handle POST request for deleting a user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $userId = $_POST['selectedUser']; // Make sure this is the name of your radio input
    $userType = $_POST['userType'][$userId];

    // Validate that $userType is one of the expected types to prevent SQL injection
    $allowedUserTypes = ['admin', 'searcher', 'tenant', 'owner'];
    if (!in_array($userType, $allowedUserTypes)) {
        $_SESSION['error'] = "Invalid user type specified.";
        header("Location: adminUser.php");
        exit();
    }
    $userIdColumn = $userType . 'Id';
    $sql = "DELETE FROM $userType WHERE $userIdColumn = $userId";
    $result = mysqli_query($conn, $sql);
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    header("Location: adminUser.php"); // Redirect to refresh the user list
    exit();
}


?>
    <div class="container">
        
        <div class="header">
            <h2>Manage User</h2>
            <a href="rolechangerequest.php">Role Change Request</a>
        </div>
        <form action="adminUser.php" method="get">
                <input type="text" name="searchUser" placeholder="Search by User Name" style="margin-bottom:10px;">
                <button type="submit">Search</button>
        </form>
        <form action="adminUser.php" method="post">
            <div class = button>
        <button type="submit" name="delete" class="btn btn-danger">Delete account</button>
        </div>
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Username</th>
                        <th>User Type</th>
                        <th>Email</th>
                        <th>Phone num</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>

                        <tr>
                            <td>

                                <input type="radio" name="selectedUser" value="<?php echo $user[$user['usertype'] . 'Id'] ?>">
                                <input type="hidden" name="userType[<?= htmlspecialchars($user[$user['usertype'] . 'Id']) ?>]" value="<?= htmlspecialchars($user['usertype']) ?>">

                            </td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['usertype']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        </form>

                    </div>
<style>
    body {
        background-color: rgb(255, 250, 205);



    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 10px;
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
    .button{
        margin-bottom: 10px;
    }
</style>
<?php include('footer.html'); ?>