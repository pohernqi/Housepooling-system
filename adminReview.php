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

function fetchReviewRecords($conn, $searchProperty = null) {
    $sql = "SELECT review.*, tenant.name AS tenantName, property.propertyname AS propertyName
            FROM review
            INNER JOIN tenant ON review.tenantId = tenant.tenantId
            INNER JOIN property ON property.propertyId = review.propertyId";
    
    // If there's a search term, modify the SQL query
    if ($searchProperty) {
        $sql .= " WHERE property.propertyname LIKE ?";
    }

    $stmt = mysqli_prepare($conn, $sql);

    // If there's a search term, bind the parameter
    if ($searchProperty) {
        $searchTerm = "%" . $searchProperty . "%";
        mysqli_stmt_bind_param($stmt, 's', $searchTerm);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception("Error fetching reviews: " . mysqli_error($conn));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get the search term if it exists
$searchProperty = isset($_GET['searchProperty']) ? $_GET['searchProperty'] : null;

// Fetch the records, possibly with filtering
$ReviewRecords = fetchReviewRecords($conn, $searchProperty);


// Handle Delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $selectedReview = $_POST['selectedReview'] ?? array();
    foreach ($selectedReview as $reviewId) {
        $sql = "DELETE FROM review WHERE reviewId = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $reviewId);
        mysqli_stmt_execute($stmt);
    }
    header('Location: adminReview.php');
}

?>
    <div class="container">
        <div class="table-responsive">
            <h3>Manage Review</h3>
            <form action="adminReview.php" method="get">
                <input type="text" name="searchProperty" placeholder="Search by Property Name">
                <button type="submit">Search</button>
            </form>

            <form action="adminReview.php" method="post">
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Tenant Name</th>
                            <th>Property Name</th>
                            <th>Review Star</th>
                            <th>Review Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ReviewRecords)) : ?>
                            <tr>
                                <td colspan="9">
                                    <div class="error">No Properties Available.</div>
                                </td>
                            </tr>
                        <?php else : ?>
                    <tbody>
                        <?php foreach ($ReviewRecords as $review) : ?>
                            <tr>
                                <td><input type="checkbox" name="selectedReview[]" value="<?= htmlspecialchars($review['reviewId']) ?>"></td>
                                <td><?= htmlspecialchars($review['tenantName']) ?></td>
                                <td><?= htmlspecialchars($review['propertyName']) ?></td>
                                <td><?= htmlspecialchars($review['reviewstar']) ?></td>
                                <td><?= htmlspecialchars($review['reviewdetail']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
                </table>


            </form>
        </div>
    </div>

    <style>
        .booking-container {
            margin: 5%;
        }

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