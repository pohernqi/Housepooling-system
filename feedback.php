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

// Get the property ID from the URL query string
$propertyId = isset($_GET['propertyId']) ? $_GET['propertyId'] : '';

function fetchPendingProperties($conn)
{
    $sql = "SELECT property.*, owner.name AS ownername, owner.email AS owneremail 
            FROM property 
            INNER JOIN owner ON property.ownerId = owner.ownerId
            WHERE property.propertystatus = 'pending'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submitFeedback'])) {
        // Get the property ID and feedback from the form
        $propertyId = $_POST['propertyId'];
        $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
        $adminId = $_SESSION['user_id'];
        // Insert feedback into the database
        $stmt = mysqli_prepare($conn, "INSERT INTO feedback (propertyId, feedback, adminId) VALUES (?, ?,?)");
        mysqli_stmt_bind_param($stmt, 'isi', $propertyId, $feedback, $adminId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $sql = "UPDATE Property SET propertystatus = 'rejected' WHERE propertyId = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $propertyId);
        mysqli_stmt_execute($stmt);

        // Redirect to a confirmation page or back to the dashboard
        header("Location: adminProperty.php");
        exit;
    } else if (isset($_POST['cancel'])) {
        // Get the property ID and feedback from the form
        $propertyId = $_POST['propertyId'];
        // $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

        // $sql = "UPDATE Property SET propertystatus = 'pending' WHERE propertyId = ?";
        // $stmt = mysqli_prepare($conn, $sql);
        // mysqli_stmt_bind_param($stmt, 'i', $id);
        // mysqli_stmt_execute($stmt);

        // Redirect to a confirmation page or back to the dashboard
        header("Location: adminProperty.php");
        exit;
    }
}
?>


    <div class="container">
        <h2>Status: rejected</h2>
        <form action="feedback.php" method="post">
            <label for="feedback">Feedback:</label><br>
            <textarea name="feedback" rows="4" cols="60" placeholder="Add your comment..."></textarea><br>

            <!-- Hidden input to pass the property ID -->
            <input type="hidden" name="propertyId" value="<?= htmlspecialchars($propertyId) ?>">

            <!-- Submit button -->
            <input type="submit" name="submitFeedback" value="Submit">

            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
<style>
    body {
        background-color: rgb(255, 250, 205);
    }
</style>
<?php include('footer.html'); ?>
