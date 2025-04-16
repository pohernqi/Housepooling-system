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
$welcomeMessage = "Welcome back!";
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT name FROM admin WHERE adminId = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the $username variable to the prepared statement as a string
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        // Execute the query
        mysqli_stmt_execute($stmt);

        // Bind the result from the query
        mysqli_stmt_bind_result($stmt, $actualName);

        // Fetch the result
        if (mysqli_stmt_fetch($stmt)) {
            // If a result is found, display the welcome message with the actual name
            $welcomeMessage = "Welcome back, " . htmlspecialchars($actualName) . "!";
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    }
} else {
    // Handle errors with preparing the statement, if any
    echo "Error preparing statement: " . htmlspecialchars(mysqli_error($conn));
}

// Check for admin user session
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

//to display the updated msg on property page
if (isset($_SESSION['updated'])) {
    echo ($_SESSION['updated']);
    unset($_SESSION['updated']);
}

// Function to fetch pending properties
function fetchPendingProperties($conn, $rentType = 'all')
{
    $sql = "SELECT property.*, owner.name AS ownername, owner.email AS owneremail 
            FROM property 
            INNER JOIN owner ON property.ownerId = owner.ownerId
            WHERE property.propertystatus = 'pending'";

    if ($rentType != 'all') {
        $sql .= " AND rentType = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);
    
    if ($rentType != 'all') {
        mysqli_stmt_bind_param($stmt, 's', $rentType);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle Approve/Reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id'])) {
        echo "<div class='error'>Select any property to proceed.</div>";
    }
    else
    {
        if (isset($_POST['approve'])) {
            $id = $_POST['id'];
            // SQL to approve the property
            $sql = "UPDATE Property SET propertystatus = 'approved' WHERE propertyId = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
        } elseif (isset($_POST['reject'])) {
            $id = $_POST['id'];
            // SQL to reject the property
            // $sql = "UPDATE Property SET propertystatus = 'rejected' WHERE propertyId = ?";
            // $stmt = mysqli_prepare($conn, $sql);
            // mysqli_stmt_bind_param($stmt, 'i', $id);
            // mysqli_stmt_execute($stmt);
            header("Location: feedback.php?propertyId=" . urlencode($id));
        }
    }   
}
$rentType = isset($_GET['rentType']) && in_array($_GET['rentType'], ['room', 'whole']) ? $_GET['rentType'] : 'all';

$pendingProperties = fetchPendingProperties($conn, $rentType);
?>

    <div class="header">
        <h2><?= $welcomeMessage ?></h2>
        <!-- Add your navigation menu here -->
        <nav>
            <!-- Your menu items -->
        </nav>
        <!-- The link to manage listings -->
        <form action="adminProperty.php" method="get">
            <div class="managelistings">
                <a href="manageListings.php" class="listings-link">Manage Listing Properties</a>
            </div>
        </form>
    </div>
    <div class="container">

        <h3>Pending Property List</h3>
        <form action="adminProperty.php" method="get">
            <label for="rentType">Choose a Rent Type:</label>
            <select name="rentType" id="rentType">
                <option value="all">All</option>
                <option value="room">Room</option>
                <option value="Whole Unit">Whole Unit</option>
            </select>
            <button type="submit">Filter</button>
        </form>
        <form action="adminProperty.php" method="post">
            <div class="button">
                <button type="submit" name="approve" class="btn btn-success">Approve</button>
                <button type="submit" name="reject" class="btn btn-danger">Reject</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Picture</th>
                            <th>Property Name</th>
                            <th>Property Address</th>
                            <th>Property Type</th>
                            <th>Property Price</th>
                            <th>Property Description</th>
                            <th>Owner Name</th>
                            <th>Owner Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingProperties as $property) : ?>
                            <tr>
                                <td><input type="radio" name="id" value="<?= htmlspecialchars($property['propertyId']) ?>"></td>
                                <td><img src="<?= htmlspecialchars($property['pic']) ?>" alt="Property-image" /></td>
                                <td><?= htmlspecialchars($property['propertyname']) ?></td>
                                <td><?= htmlspecialchars($property['address']) ?></td>
                                <td><?= htmlspecialchars($property['propertytype']) ?></td>
                                <td><?= htmlspecialchars($property['price']) ?></td>
                                <td><?= htmlspecialchars($property['description']) ?></td>
                                <td><?= htmlspecialchars($property['ownername']) ?></td>
                                <td><?= htmlspecialchars($property['owneremail']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

        </form>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px;
        }

        .managelistings {
            text-decoration: underline;
            cursor: pointer;
            color: black;
            font-size: 20px;
        }

        .button {
            margin-bottom: 10px;
        }

        img {
            max-width: 350px;
            /* Set a fixed width */
            height: auto;
            /* Maintain aspect ratio */
        }
    </style>
<?php include('footer.html'); ?>