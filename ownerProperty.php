<?php
session_start();

 // User is not logged in, redirect to the login page to prevent unauthorised access
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['unlog']=  "Please login first.";
    header('Location: login.php');

    exit();
}

//if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in, display the header for logged-in users
   // $userId = $_SESSION["userid"];
    include('headerAfterLogin.php');
    $user_type= $_SESSION['user_type'];
    $user_id = $_SESSION['user_id'];
   
    
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}

   //to display the updated msg on property page
   if(isset($_SESSION['updated']))
   {
       echo ($_SESSION['updated']);
       unset($_SESSION['updated']);
   }

$msg="";
include("connect.php");
// Check if the form has been submitted with a delete request
if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {

    //Remove the physical image file is available
    if (isset($_POST['id'])) 
    {
        $id = $_POST['id'];

            $sql = "UPDATE property SET propertystatus='deleted' WHERE propertyId = $id";
    
            if (mysqli_query($conn, $sql)) {
                // Deletion successful
                $msg="The property is deleted successfully";
            } else {
                // Error occurred during deletion
                $msg= "Error: " . mysqli_error($conn);
            }
    } 
}

if(isset($_POST['search']))
{
    $valueToSearch = $_POST['valueToSearch'];
    // search in all table columns
    // using concat mysql function
    $query = "SELECT p.*, f.feedback FROM property p LEFT JOIN feedback f ON p.propertyId = f.propertyId WHERE CONCAT(p.propertyname, p.location, p.address, p.propertytype, p.rentType, p.price, p.description, p.propertystatus) LIKE '%" . $valueToSearch . "%' AND p.{$user_type}Id = $user_id AND p.propertystatus <> 'deleted'";
    $search_result = filterTable($query);
    
}
else {
    $query = "SELECT p.*, f.feedback FROM property p LEFT JOIN feedback f ON p.propertyId = f.propertyId WHERE  p.{$user_type}Id = $user_id and p.propertystatus <> 'deleted'";
    $search_result = filterTable($query);
}

// function to connect and execute the query
function filterTable($query)
{
    include('connect.php');
    $filter_Result = mysqli_query($conn, $query);
    
    return $filter_Result;
}

$pageToken = bin2hex(random_bytes(16)); // Generate a random token
$_SESSION['pageToken'] = $pageToken;

?>

<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this property?");
}
</script>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="main-content">
    <div class="wrapper">
        <h1>Properties Listing</h1>
                <form method="POST" action="addProperty.php">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-secondary" >Add Property</button>
                </form>

                <form action="" method ="post">
                <input type="text" name="valueToSearch" style="float: right;" placeholder="Search by value"><br><br>
                <input type="submit" name="search" style="float: right;" value="Search"><br>
                </form>
                <br /><br />
                <table class="tbl-full">
                    <tr>
                        <th>No.</th>
                        <th>Property name</th>
                        <th>Image</th>
                        <th>Location</th>
                        <th>Address</th>
                        <th>Property Type</th>
                        <th>Rent Type</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    
                    <?php 
                        //Count Rows
                        $count = mysqli_num_rows($search_result);

                        //Create Serial Number Variable and assign value as 1
                        $sn=1;

                        //Check whether we have data in database or not
                        if($count>0)
                        {
                            //We have data in database
                            //get the data and display
                            while($row=mysqli_fetch_assoc($search_result))
                            {
                                $id = $row['propertyId']; //id
                                $name = $row['propertyname']; //title
                                $location = $row['location'];
                                $img = $row['pic'];                 //festure
                                $address = $row['address'];
                                $status= $row['propertystatus'];
                                $propertytype= $row['propertytype'];
                                $renttype= $row['rentType'];
                                $price= $row['price'];
                                $description= $row['description'];
                                $feedback = $row['feedback'];

                                ?>
                                    <tr>
                                        <td><?php echo $sn++; ?>. </td>
                                        <td><?php echo $name; ?></td>

                                        <td>
           
                                        <img src="<?php echo $img; ?>" alt="Image" width="250" ></td>

                                        <td><?php echo $location; ?></td>
                                        <td><?php echo $address; ?></td>
                                        <td><?php echo $propertytype; ?></td>
                                        <td><?php echo $renttype; ?></td>
                                        <td><?php echo $price; ?></td>
                                        <td><?php echo $description; ?></td>
                                        <td><div class="statusDiv">
                                        <?php 
                                        echo $status;
                                        if ($status == "rejected") 
                                            echo "<img src=\".\img\RejectedComment.PNG\" alt=\"\" width=\"20\" >";
                                        ?>
                                        </div>
                                        <?php 
                                        echo "<div class=\"hideStatus\">";
                                        if ($status == "rejected") {
                                          echo "(".$feedback.")";
                                        }
                                        echo "</div>";
                                        ?>
                                        </td>
                                        <td>
                                      <?php
                                        if ($status === 'pending' || $status === 'rejected' ) {
                                          ?>
                                            <form method="POST" action="updateProperty.php">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <button type="submit" class="btn btn-secondary">Update</button>
                                            </form>

                                            <form method="POST" action="" onsubmit="return confirmDelete()" >
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                                <button type="submit" class="btn btn-secondary">Delete</button>
                                            </form>

                                        </td>
                                    </tr>

                                <?php
                            
                                }else{
                                    ?>
                                    <form method="POST" action="updateProperty.php">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button type="submit" class="btn btn-secondary">Update</button>
                                    </form>

                                    <form method="POST" action="" onsubmit="return confirmDelete()"  >
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                        <button type="submit" class="btn btn-secondary">Delete</button>
                                    </form>

                                    <form method="POST" action="ownerLease.php">
                                        <input type="hidden" name="propertyId" value="<?php echo $id; ?>">
                                        <button type="submit" class="btn btn-secondary">Manage Lease</button>
                                    </form>
                                </td>
                            </tr>
                            <?php    } 
                            
                        }
                        }else{
                            ?>
                                <tr>
                                    <td colspan="11"><div class="error">No Properties Available.</div></td>
                                </tr>

                                <?php
                            }
                    ?>
                </table><br><br><br><br>
    </div>
</div>

<style>
.wrapper{
    padding: 1%;
    width: 80%;
    margin: 0 auto;
}
.tbl-full {
    width: 100%;
    border-collapse: collapse;
}

.tbl-full th,
.tbl-full td {
    border: 1px solid black;
          border-spacing:0 12px;
          padding: 6px;
}

.tbl-full th {
    background-color: #f2f2f2;
}
.hideStatus {
  display: none;
}
    
.statusDiv:hover + .hideStatus {
  display: block;
  color: red;
}
</style>

<?php include('footer.html'); ?>
 