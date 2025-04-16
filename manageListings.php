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

if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] != 'admin') {
    header('Location: login.php');
    exit();
}

//to display the updated msg on property page
if (isset($_SESSION['updated'])) {
    echo ($_SESSION['updated']);
    unset($_SESSION['updated']);
}

function fetchListingProperties($conn, $searchTerm = null)
{
    $sql = "SELECT property.*, owner.name AS ownername, owner.email AS owneremail 
            FROM property
            INNER JOIN owner ON property.ownerId = owner.ownerId
            WHERE property.propertystatus = 'approved'";

    // Add a WHERE clause if there is a search term
    if (!is_null($searchTerm)) {
        // Use a prepared statement for security
        $sql .= " AND property.propertyname LIKE ?";
    }
    $stmt = mysqli_prepare($conn, $sql);

    if (!is_null($searchTerm)) {
        $likeTerm = '%' . $searchTerm . '%';
        mysqli_stmt_bind_param($stmt, 's', $likeTerm);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $newstatus = "deleted";
        //$sql = "DELETE FROM property WHERE propertyId = $id";
        $sql = "UPDATE property SET propertystatus = '$newstatus' WHERE propertyId = $id";
        $result = mysqli_query($conn, $sql);
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Property deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
        // Redirect back to manageListings.php after deletion
        header("Location: manageListings.php");
        exit();
    } elseif (isset($_POST['edit']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        header("Location: adminupdateProperty.php?id=$id");
        exit();
    }
}
$searchTerm = isset($_GET['searchProperty']) ? $_GET['searchProperty'] : null;
$ListingProperties = fetchListingProperties($conn, $searchTerm);

?>

    <div class="container">
        <h3>Manage Listing Properties</h3>
        <div class="top-bar">
            <form action="manageListings.php" method="get" style="margin-left: auto" ;>
                <input type="text" name="searchProperty" placeholder="Search by Property Name">
                <button type="submit">Search</button>
            </form>
        </div>
        <form action="manageListings.php" method="post">
            <input type="submit" name="edit" value="Edit" />
            <input type="submit" name="delete" value="Delete" />



            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Picture</th>
                            <th>Property Name</th>
                            <th>Property Address</th>
                            <th>Property Price</th>
                            <th>Property Description</th>
                            <th>Owner Name</th>
                            <th>Owner Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ListingProperties as $property) : ?>
                            <tr>
                                <td><input type="radio" name="id" value="<?php echo $property['propertyId'] ?>">
                                </td>
                                <td><img src="<?= htmlspecialchars($property['pic']) ?>" alt="Property" /></td>
                                <td><?= htmlspecialchars($property['propertyname']) ?></td>
                                <td><?= htmlspecialchars($property['address']) ?></td>
                                <td><?= htmlspecialchars($property['price']) ?></td>
                                <td><?= htmlspecialchars($property['description']) ?></td>
                                <td><?= htmlspecialchars($property['ownername']) ?></td>
                                <td><?= htmlspecialchars($property['owneremail']) ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }
    </style>
<?php include('footer.html'); ?>