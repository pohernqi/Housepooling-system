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

include('connect.php');
if(isset($_POST['search']))
{
    $valueToSearch = $_POST['valueToSearch'];
    // search in all table columns
    // using concat mysql function
    $query = "SELECT distinct property.* FROM property LEFT JOIN agreement ON property.propertyId= agreement.propertyId LEFT JOIN tenant ON agreement.tenantId = tenant.tenantId WHERE CONCAT(property.propertyname, property.location, property.address, property.propertytype, property.description, property.price, property.propertystatus) LIKE '%" . $valueToSearch . "%' AND tenant.tenantId = $user_id AND property.propertystatus <> 'deleted'";
    $search_result = mysqli_query($conn, $query);
}
 else {
    $query = "SELECT distinct property.* from property LEFT JOIN agreement ON property.propertyId= agreement.propertyId LEFT JOIN tenant ON agreement.tenantId = tenant.tenantId where tenant.tenantId= $user_id AND property.propertystatus <> 'deleted'";
    $search_result = mysqli_query($conn, $query);
}

// function to connect and execute the query
function filterTable($query)
{
    include('connect.php');
    $filter_Result = mysqli_query($conn, $query);
    
    return $filter_Result;
}

?>

<div class="main-content">
    <div class="wrapper">
        <h1>Properties Listing</h1>
                <form action="tenantProperty.php" method ="post">
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
                                $price= $row['price'];
                                $description= $row['description'];
                                 
                    ?>
                                    <tr>
                                        <td><?php echo $sn++; ?>. </td>
                                        <td><?php echo $name; ?></td>
                                        <td>
                                        <img src="<?php echo $img; ?>" alt="Image"  width="250">
                                        </td>
                                        <td><?php echo $location; ?></td>
                                        <td><?php echo $address; ?></td>
                                        <td><?php echo $propertytype; ?></td>
                                        <td><?php echo $price; ?></td>
                                        <td><?php echo $description; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td>
                                            <form method="POST" action="tenantReview.php">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-secondary">Review</button>
                                            </form>
                                            <form method="POST" action="tenantLease.php">
                                                <input type="hidden" name="propertyId" value="<?php echo $id; ?>">
                                                <button type="submit" class="btn btn-secondary">Manage Lease</button>
                                            </form>
                                         </td>
                                    </tr>
                        <?php  
                            } 
                         }  else {
                        ?>
                                <tr>
                                    <td colspan="10"><div class="error">No Properties Available.</div></td>
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
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

.tbl-full th {
    background-color: #f2f2f2;
}
</style>
<?php include('footer.html'); ?>
