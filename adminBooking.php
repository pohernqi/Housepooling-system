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

// Function to fetch booking records
function fetchBookingRecords($conn) {
    $sql = "SELECT booking.*, searcher.name AS searcherName, property.propertyname AS propertyName
    FROM booking 
    INNER JOIN searcher ON booking.searcherId = searcher.searcherId
    INNER JOIN property ON property.propertyId = booking.propertyId";

   $result = mysqli_query($conn, $sql);
   if (!$result) {
       throw new Exception("Error fetching bookings: " . mysqli_error($conn));
   }
   return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Call the function to fetch booking records
$bookingRecords = fetchBookingRecords($conn);

// Handle Delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
   $selectedBookings = $_POST['selectedBookings'] ?? array();
   foreach ($selectedBookings as $bookingId) {
       $sql = "DELETE FROM booking WHERE bookingId = ?";
       $stmt = mysqli_prepare($conn, $sql);
       mysqli_stmt_bind_param($stmt, 'i', $bookingId);
       mysqli_stmt_execute($stmt);
   }
   header('Location: adminBooking.php');
}

?>
<div class="container">
<div class="table-responsive">
    <h3>Manage Booking</h3>
    <form action="adminBooking.php" method="post">
    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Searcher Name</th>
                    <th>Property Name</th>
                    <th>Booking Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookingRecords as $booking): ?>
                    <tr>
                        <td><input type="checkbox" name="selectedBookings[]" value="<?= htmlspecialchars($booking['bookingId']) ?>"></td>
                        <td><?= htmlspecialchars($booking['searcherName']) ?></td>
                        <td><?= htmlspecialchars($booking['propertyName']) ?></td>
                        <td><?= htmlspecialchars($booking['bookingdate']) ?></td>
                        <td><?= htmlspecialchars($booking['remarks']) ?></td>
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
            margin-bottom: 10px;
        }
    </style>
<?php include('footer.html'); ?>