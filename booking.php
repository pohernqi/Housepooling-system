<?php
session_start();

//if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in, display the header for logged-in users
   // $userId = $_SESSION["userid"];
   $_SESSION['unlog']=  "Please login first.";
    include('headerAfterLogin.php');
   
   
    
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}


include("connect.php");

$msg="";
if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {
   
     // User is not logged in, redirect to the login page to prevent unauthorised access
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

        header('Location: login.php');
        $_SESSION['msg'] = "Please login first before booking.";
        exit();
    }
    // Process the form data
    $newid = $_POST['propertyId'];
    $newbookingDT = $_POST["bookingdate"];
    $newremarks = $_POST["remarks"];
    $newstatus = "pending";
    $searcherid = $_SESSION['user_id'];
    $toInsert = 1;
    // Check if all fields are empty (excluding the image)
    if (!empty($newbookingDT) && !empty($newremarks)) {
        $checkExistSQL = "SELECT bookingid from booking where bookingdate = '$newbookingDT' and propertyid = '$newid' and bookingstatus <> 'rejected' ";
        // Execute the Query
        $res = mysqli_query($conn, $checkExistSQL);

        if ($res) {
            // Check whether the user exists or not
            $count = mysqli_num_rows($res);
            if ($count > 0) {
                $msg="This date time slot has been booked. Please choose another date time.";
                $toInsert = 0;
            }
        }
        if($toInsert == 1)
        {
            $addSQL = "INSERT INTO booking (bookingdate,remarks,bookingstatus,searcherid, propertyId) VALUES ('$newbookingDT', '$newremarks','$newstatus', '$searcherid', '$newid')";

            if (mysqli_query($conn, $addSQL)) {
                $msg="Property booked successfully.";
            } else {
                $msg="Error booking property: " . mysqli_error($conn);
            }
        }
    }else{
        $msg="Some fields are blank. Please fill in.";
    } 
}

include("connect.php");

function Properties($conn) {
    if (isset($_GET['propertyId'])) {
        $propertyId = $_GET['propertyId'];}

    $sql = "SELECT property.*, owner.name AS ownername, owner.email AS owneremail 
    FROM property 
    INNER JOIN owner ON property.ownerId = owner.ownerId
    WHERE property.propertystatus != 'deleted' AND property.propertyId = $propertyId";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
$Properties = Properties($conn);

$pageToken = bin2hex(random_bytes(16)); // Generate a random token
$_SESSION['pageToken'] = $pageToken;

if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] == "owner") || (isset($_SESSION['user_type']) && $_SESSION['user_type'] == "tenant") )
   echo "<!--";
?>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="container" align="left">
<div class="card" style="width: 45rem; height: 28rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Book Property</b></h1>

<form action="" method="POST">
<table>
    <tr>
        <td><br>Date & Time: </td>
        <td>
           <br> <input type="datetime-local" name="bookingdate" value="" required>
        </td>
    </tr>

    <tr>
        <td><br>Remarks: </td>
        <td><br>
        <input type="text" name="remarks" value="" required>
            </td>
    </tr>

    <tr>
        <td>
        <input type="hidden" name="propertyId" value="<?= $_GET['propertyId']?>">
        <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
            <input type="submit" name="submit" value="Book Property" class="btn-secondary">
        </td>
    </tr>

</table>
</form>
</div>
</div></div>
<?php
if ((isset($_SESSION['user_type']) && $_SESSION['user_type'] == "owner") || (isset($_SESSION['user_type']) && $_SESSION['user_type'] == "tenant") )
   echo "-->";

?>

<div class="container">
      <h3>Selected Property</h3>
      </form>
          <table>
              <thead>
                  <tr>
                      <th>Property Picture</th>
                      <th>Property Name</th>
                      <th>Location</th>
                      <th> Address </th>
                      <th>Property Type</th>
                      <th>Rent Type</th>
                      <th>Property Description</th> 
                      <th>Price</th>
                      <th>Owner Email</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($Properties as $property): ?>
              <tr>
              <td><img src="<?= htmlspecialchars($property['pic']) ?>" alt="Property"  width="250"/></td>
              <td><?= htmlspecialchars($property['propertyname']) ?></td>
              <td><?= htmlspecialchars($property['location']) ?></td>
              <td><?= htmlspecialchars($property['address']) ?></td>
              <td><?= htmlspecialchars($property['propertytype']) ?></td>
              <td><?= htmlspecialchars($property['rentType']) ?></td>
              <td><?= htmlspecialchars($property['description']) ?></td>
              <td><?=htmlspecialchars($property['price']) ?></td>
              <td><?= htmlspecialchars($property['owneremail']) ?></td>
              </tr>
                   <?php endforeach; ?>
              </tbody>
          </table>
          <br>
  </div>
 
<style>
    table {
          border-collapse: collapse; 
      }
      th {
          background-color: #f2f2f2;
          Color:black;
      }
      table, th, td {
          border: 1px solid black;
          border-spacing:0 12px;
          padding: 6px;
      }
      .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
     
      }

      
</style>
<?php include('footer.html'); ?>