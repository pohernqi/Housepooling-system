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
$reviewId = $_POST['reviewId'];
$query = "SELECT r.reviewdetail, r.reviewstar,  DATE_FORMAT(r.reviewdate,'%d/%m/%Y %h:%m:%s %p') AS reviewdate, p.propertyname, p.address, t.name AS tenantname ".
		 "FROM review r LEFT JOIN property p ON p.propertyId = r.propertyId LEFT JOIN tenant t ON t.tenantId = r.tenantId WHERE r.reviewId = ".$reviewId;
if(isset($_POST['search']))
{
	$valueToSearch = $_POST['valueToSearch'];
	$query = $query." AND CONCAT(r.reviewdetail, r.reviewstar, r.reviewdate, p.propertyname, p.address, t.name) LIKE '%" . $valueToSearch . "%' ";
}
include('connect.php');
$search_result = mysqli_query($conn, $query);

?>

<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="main-content">
    <div class="wrapper">
        <h1>Tenant's Review Listing</h1>
                <form action="" method ="post">
                <input type="text" name="valueToSearch" style="float: right;" placeholder="Search by value"><br><br>
                <input type="submit" name="search" style="float: right;" value="Search"><br>
				<input type="hidden" name="reviewId" value="<?php echo $reviewId; ?>">
                </form>
                <br /><br />
                <table class="tbl-full">
                    <tr>
                        <th>No.</th>
                        <th>Property Name</th>
						<th>Property Address</th>
                        <th>Review Rating</th>
                        <th>Review Description</th>
                        <th>Review Date</th>
                        <th>Reviewed By</th>
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
                                $propertyname = $row['propertyname'];                 
                                $address = $row['address'];
                                $reviewstar = $row['reviewstar'];
                                $reviewdetail = $row['reviewdetail'];
	  					        $reviewdate = $row['reviewdate'];
                                $tenantname= $row['tenantname'];
                    ?>
                                    <tr>
                                        <td><?php echo $sn++; ?>. </td>
                                        <td><?php echo $propertyname; ?></td>
                                        <td><?php echo $address; ?></td>
                                        <td><?php echo $reviewstar; ?></td>
                                        <td><?php echo $reviewdetail; ?></td>
                                        <td><?php echo $reviewdate; ?></td>
                                        <td><?php echo $tenantname; ?></td>
                                    </tr>
                            
                        <?php
                            }
                        } else {
                        ?>
                                <tr>
                                    <td colspan="9"><div class="error">No Review Available.</div></td>
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
    border: 1px solid black;
          border-spacing:0 12px;
          padding: 6px;
}

.tbl-full th {
    background-color: #f2f2f2;
}
</style>
<?php include('footer.html'); ?> 