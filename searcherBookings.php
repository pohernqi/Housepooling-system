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
   
    
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}

?>
<?php

if(isset($_POST['search']))
{
    $valueToSearch = $_POST['valueToSearch'];
}

$searcherid = $_SESSION['user_id']; 
$query = "SELECT booking.bookingId, DATE_FORMAT(booking.bookingdate, '%d %b %Y %r') as bookingdate, booking.bookingstatus, booking.remarks, owner.name, owner.phone, owner.email, property.propertyname
FROM `booking`
JOIN `property` AS property ON booking.propertyId = property.propertyId
JOIN `owner` ON property.ownerId = owner.ownerId
WHERE booking.searcherId = '$searcherid' ";

$query2="";
if (!empty($valueToSearch))  
    $query2 = " AND CONCAT(`bookingdate`, `remarks`, `bookingstatus`) LIKE '%" . $valueToSearch . "%' 
    OR CONCAT(name, phone, email) LIKE '%" . $valueToSearch . "%'
    OR propertyname LIKE '%" . $valueToSearch . "%'";

$search_result = filterTable($query.$query2);

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
        <h1>Property Bookings</h1>
                <form action="" method ="post">
                <input type="text" name="valueToSearch" style="float: right;" placeholder="Search by value"><br><br>
                <input type="submit" name="search" style="float: right;" value="Search"><br>
                </form>
                <br /><br />
                <table class="tbl-full">
                    <tr>
                        <th>No.</th>
                        <th>Owner Name</th>
                        <th>Owner Email</th>
                        <th>Owner Phone No.</th>
                        <th>Property Booked</th>
                        <th>Booking Slot</th>
                        <th>Remarks</th>
                        <th>Status</th>
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
                                $id = $row['bookingId']; //id
                                $name = $row['name'];
                                $phone =$row['phone'];
                                $email= $row['email'];
                                $property= $row['propertyname'];
                                $bookingdate = $row['bookingdate']; //title
                                $remarks = $row['remarks'];                
                                $status= $row['bookingstatus'];
                                 
                                $_SESSION['bookingId'] = $id;

                                ?>
                                    <tr>
                                        <td><?php echo $sn++; ?>. </td>
                                        <td><?php echo $name;?></td>
                                        <td><?php echo $email;?></td>
                                        <td><?php echo $phone;?></td>
                                        <td><?php echo $property;?></td>
                                        <td><?php echo $bookingdate;?></td>
                                        <td><?php echo $remarks;?></td>
                                        <td><?php echo $status;?></td>
                                    </tr>

                                <?php
                            }
                        }else{
                                //WE do not have data
                                //We'll display the message inside table
                                ?>
                                <tr>
                                    <td colspan="8"><div class="error">No Properties Available.</div></td>
                                </tr>

                                <?php
                            }
                      ?>
             </table>
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