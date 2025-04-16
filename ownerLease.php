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

 //to display the updated msg on property page
if(isset($_SESSION['sucessmsg']))
{   echo ($_SESSION['sucessmsg']);
    unset($_SESSION['sucessmsg']);
}


$msg="";
include("connect.php"); 

if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) 
{
    if (isset($_POST['agreementId'])) 
    {
        $agreementId = $_POST['agreementId'];

        $sql = "UPDATE agreement SET status='deleted' WHERE agreementId = $agreementId";

        if (mysqli_query($conn, $sql)) {
            // Deletion successful
            $msg="Agreement is deleted successfully";
        } else {
            // Error occurred during deletion
            $msg= "Error: " . mysqli_error($conn);
        }
    } 
}


$search_result = "";
$valueToSearch = "";
$propertyId = "";

if (isset($_POST["propertyId"])) {
    $propertyId = $_POST["propertyId"];
}
else $propertyId= $_SESSION['propertyId'];

    $query =  "SELECT a.*, t.name, t.phone FROM agreement a JOIN property p ON a.propertyId = p.propertyId JOIN tenant t ON 
	a.tenantId = t.tenantId WHERE SUBSTRING_INDEX(a.docs, '.', -1) IN ('docx', 'pdf', 'doc') AND p.propertyId = $propertyId AND a.status <> 'deleted'";

	if(isset($_POST['valueToSearch']))
		$valueToSearch = $_POST['valueToSearch'];
	
	if (!empty($valueToSearch)) {
		$query = $query."
		AND (CONCAT(a.rental, a.startdate, a.enddate, a.deposit, a.status, a.paystatus, a.docs, t.name, t.phone) LIKE '%" . $valueToSearch . "%'
        OR t.name LIKE '%" . $valueToSearch . "%' OR t.phone LIKE '%" . $valueToSearch . "%') ";
    }
   $search_result = mysqli_query($conn, $query);

$pageToken = bin2hex(random_bytes(16)); // Generate a random token
$_SESSION['pageToken'] = $pageToken;

?>
<script>
function confirmDelete(action) {
    return confirm("Are you sure you want to "+ action+ " this lease?");
}
</script>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="main-content">
    <div class="wrapper">
        <h1>Agreements Listing</h1>
                <form method="POST" action="addLease.php">
  					<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                    <button type="submit" class="btn btn-secondary">Add Lease</button>
                </form>

                <form action="ownerLease.php" method ="post">
				<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                <input type="text" name="valueToSearch" style="float: right;" placeholder="Search by value"><br><br>
                <input type="submit" name="search" style="float: right;" value="Search"><br>
                </form>
                <br /><br />
                <table class="tbl-full">
                    <tr>
                        <th>No.</th>
                        <th>Tenant Name</th>
                        <th>Tenant Phone</th>
                        <th>Rental</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Agreements</th>
                        <th>Received Agreeements</th>
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
                               $fileLink = "docs/" .$row["docs"];
                               $fileName = basename($fileLink);
                               $agreementId= $row['agreementId'];
                               $tenantName= $row['name'];
                               $tenantPhone= $row['phone'];
                               $rental= $row['rental'];
                               $startDate=$row['startdate'];
                               $endDate= $row['enddate'];
                               $deposit= $row['deposit'];
                               $status= $row['status'];
                               $payStatus= $row['paystatus'];
                               $receivedFileLink = "accepted_docs/" .$row["receivedDocs"];
                               $receivedFileName = basename($receivedFileLink);

                                ?>
                                    <tr>
                                        <td><?php echo $sn++; ?>. </td>
                                        <td><?php echo $tenantName; ?></td>
                                        <td><?php echo $tenantPhone; ?></td>
                                        <td><?php echo $rental; ?></td>
                                        <td><?php echo $startDate; ?></td>
                                        <td><?php echo $endDate; ?></td>
                                        <td><?php echo $deposit; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <td><?php echo $payStatus; ?></td>
                                        <td><?php echo "<a href='$fileLink' download='$fileName'>$fileName</a><br>"; ?></td>
                                        <td><?php if ($status == 'accepted') {
                                                    echo "<a href='$receivedFileLink' download='$receivedFileName'>$receivedFileName</a><br>";
                                                } else {
                                                    echo "None";
                                                } ?></td>
                                          <td>
                                      <?php
                                         if ($status == 'pending' || $status == 'rejected') {
                                          ?>
                                              <form method="POST" action="updateLease.php">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <button type="submit" class="btn btn-secondary">Update</button>
                                            </form>
                                            <form method="POST" action=""  onsubmit="return confirmDelete('delete')">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                                <button type="submit" class="btn btn-secondary">Delete</button>
                                            </form>
                                        <?php 
                                        } if ($status == 'accepted') {
                                        ?>
                                      <form method="POST" action="updateLease.php">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <button type="submit" class="btn btn-secondary">Renew</button>
                                            </form>
                                            <form method="POST" action=""  onsubmit="return confirmDelete('terminate')">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                                <button type="submit" class="btn btn-secondary">Terminate</button>
                                            </form>
                                <?php    } ?>
                                </td>
                            </tr>
                            <?php     
                            }
                        }
                        else
                        {
                            //WE do not have data
                            //We'll display the message inside table
                            ?>

                            <tr>
                                <td colspan="12"><div class="error">No Lease Available.</div></td>
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