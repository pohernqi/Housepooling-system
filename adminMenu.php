<?php
session_start(); //include in all pages 

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

// Fetch Pending Properties Count
$pendingPropertiesResult = mysqli_query($conn, "SELECT COUNT(*) AS pendingCount FROM property WHERE propertystatus = 'pending'");
$pendingPropertiesRow = mysqli_fetch_assoc($pendingPropertiesResult);
$pendingPropertiesCount = $pendingPropertiesRow['pendingCount'];

// Fetch Listing Properties Count
$listingPropertiesResult = mysqli_query($conn, "SELECT COUNT(*) AS listingCount FROM property WHERE propertystatus = 'approved'");
$listingPropertiesRow = mysqli_fetch_assoc($listingPropertiesResult);
$listingPropertiesCount = $listingPropertiesRow['listingCount'];

// Fetch User Type Counts
$ownerCountResult = mysqli_query($conn, "SELECT COUNT(*) AS ownerCount FROM owner");
$ownerCountRow = mysqli_fetch_assoc($ownerCountResult);
$ownerCount = $ownerCountRow['ownerCount'];

$tenantCountResult = mysqli_query($conn, "SELECT COUNT(*) AS tenantCount FROM tenant");
$tenantCountRow = mysqli_fetch_assoc($tenantCountResult);
$tenantCount = $tenantCountRow['tenantCount'];

$adminCountResult = mysqli_query($conn, "SELECT COUNT(*) AS adminCount FROM admin");
$adminCountRow = mysqli_fetch_assoc($adminCountResult);
$adminCount = $adminCountRow['adminCount'];

$searcherCountResult = mysqli_query($conn, "SELECT COUNT(*) AS searcherCount FROM searcher");
$searcherCountRow = mysqli_fetch_assoc($searcherCountResult);
$searcherCount = $searcherCountRow['searcherCount'];

// Close the connection
mysqli_close($conn);
?>

    <h2 class = welcome><?= $welcomeMessage ?></h2>
    <style>
        .welcome{
            margin:10px;
        }
        body {
            font-family: Arial, sans-serif;
        }

        .menu-container {
            margin: auto;
            overflow: hidden;
            display: flex;
            justify-content: center; /* This will center the menu items horizontally */
            flex-wrap: wrap; 
            padding: 20px;
        }

        .menu-item {
            float: left;
            width: 20%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f2f2f2;
            margin: 10px;
            transition: background-color 0.3s;
        }

        .menu-item:hover {
            background-color: #ddd;
        }

        .menu-item a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .dashboard-statistics {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .statistics-group {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .statistics-item {
            flex: 1;
            /* Ensure that all items have equal width */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 10px;
            /* Space between items */
            transition: all 0.3s ease;
        }

        .statistics-item:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .statistics-item h4 {
            margin-bottom: 10px;
            color: #333;
            font-size: 1.2em;
        }

        .statistics-item p {
            font-size: 2em;
            color: #007bff;
            margin: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .statistics-group {
                flex-direction: column;
            }

            .statistics-item {
                margin-bottom: 20px;
            }
        }
    </style>
<div class = "container">
<h1>Admin Dashboard</h1>
    <div class="menu-container">
        
        <div class="menu-item">
            <a href="adminProperty.php">Manage Pending Properties</a>
        </div>
        <div class="menu-item">
            <a href="manageListings.php">Manage Listing Properties</a>
        </div>
        <div class="menu-item">
            <a href="adminBooking.php">Manage Booking</a>
        </div>
        <div class="menu-item">
            <a href="adminUser.php">Manage Users</a>
        </div>
        <div class="menu-item">
            <a href="adminTransaction.php">View Transaction</a>
        </div>
        <div class="menu-item">
            <a href="Profile.php">Edit Profile</a>
        </div>
        <div class="menu-item">
            <a href="adminLease.php">View Lease</a>
        </div>
        <div class="menu-item">
            <a href="adminReview.php">Manage Review</a>
        </div>
        <div class="menu-item" >
            <a href="logout.php">Logout</a>
        </div>
    </div>
    </div>
    <div class="dashboard-statistics">
        <h2>Statistics</h2>
        <div class="property-count statistics-group">
            <div class="statistics-item">
                <h4>Pending Properties</h4>
                <p><?= $pendingPropertiesCount ?></p>
            </div>
            <div class="statistics-item">
                <h4>Listing Properties</h4>
                <p><?= $listingPropertiesCount ?></p>
            </div>
        </div>
        <div class="user-count statistics-group">
            <div class="statistics-item">
                <h4>Owners</h4>
                <p><?= $ownerCount ?></p>
            </div>
            <div class="statistics-item">
                <h4>Tenants</h4>
                <p><?= $tenantCount ?></p>
            </div>
            <div class="statistics-item">
                <h4>Admins</h4>
                <p><?= $adminCount ?></p>
            </div>
            <div class="statistics-item">
                <h4>Searchers</h4>
                <p><?= $searcherCount ?></p>
            </div>
        </div>
    </div>

 <?php include('footer.html'); ?>