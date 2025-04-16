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

$agreementId="";   
$action="";
$msg = "";

if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) 
{
    include("connect.php");
	$agreementId = $_POST['agreementId'];
    $action = $_POST['action'];
    if ("accept" == $action) 
    {
        $docs = $_FILES['file'];
    
        $fileName = "";
            // Check if a file has been uploaded in the 'file' field
            if ($_FILES["file"]["error"] == 0) {
                $targetDirectory = "accepted_docs/"; // Set your desired upload directory
                $fileName = basename($_FILES["file"]["name"]);
                $targetFile = $targetDirectory . $fileName;
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                
                // Set allowed file types
                $allowedTypes = ['docx', 'doc', 'pdf'];
            
                //echo $fileName;
                // Check if the file is of a valid document type
                if (in_array($fileType, $allowedTypes)) {
                    // Check and limit the file size if needed
                    if ($_FILES["file"]["size"] > 10000000) { // 10 MB limit
                        $msg= "Sorry, your file is too large.";
                    } else {
                        // to check if the file in folder has same name
                        $fileCounter = 1;
                        if(file_exists($targetFile)){
                            while(file_exists($targetFile)) {
                                // Append a number to the file name if it already exists
                                $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_" . $fileCounter . "." . $fileType;
                                $targetFile = $targetDirectory . $newFileName;
                                $fileCounter++;
                            }
                        }else{
                            $newFileName = basename($_FILES["file"]["name"]);
                        }
                    
                        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);

                        $sql = "UPDATE agreement set status='accepted', receivedDocs = '$newFileName' where agreementid = $agreementId";
                            
                        if (mysqli_query($conn, $sql)) {
                            $msg= "Agreement is accepted.";
                        } else {
                            $msg= "Error updating agreement: " . mysqli_error($conn);
                        }

                    }
                } else {
                    $msg= "File is not an allowed document type.";
                }
            } else {
                // File not uploaded
                $msg= "Please upload an agreement document in docx, doc, or pdf format.";
            }
    }
    if ("reject" == $action) {
        include("connect.php");

        $sql = "UPDATE agreement set status='rejected' where agreementid = $agreementId";
                            
        if (mysqli_query($conn, $sql)) {
            $msg= "Agreement is rejected.";
        } else {
            $msg= "Error updating agreement: " . mysqli_error($conn);
        }  
    }
}
$search_result="";
$valueToSearch = "";
$propertyId = "";

if (isset($_POST["propertyId"])) {
    $propertyId = $_POST["propertyId"];
}
else $propertyId= $_SESSION['propertyId'];
  
$user_id = $_SESSION['user_id'];
    $query =  "SELECT a.*, t.name, t.phone FROM agreement a JOIN property p ON a.propertyId = p.propertyId JOIN tenant t ON 
	a.tenantId = t.tenantId WHERE a.tenantId = $user_id AND SUBSTRING_INDEX(a.docs, '.', -1) IN ('docx', 'pdf', 'doc') AND p.propertyId = $propertyId AND a.status <> 'deleted'";
 
	if(isset($_POST['valueToSearch']))
		$valueToSearch = $_POST['valueToSearch'];
	
	if (!empty($valueToSearch)) {
		$query = $query."
		AND (CONCAT(a.rental, a.startdate, a.enddate, a.deposit, a.status, a.paystatus, a.docs, t.name, t.phone) LIKE '%" . $valueToSearch . "%'
        OR t.name LIKE '%" . $valueToSearch . "%' OR t.phone LIKE '%" . $valueToSearch . "%') ";
    }

   include("connect.php"); 
   $search_result = mysqli_query($conn, $query);


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
function confirmWindow(action) {
    return confirm("Are you sure you want to " + action + " this lease?");
}
</script>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="main-content">
    <div class="wrapper">
        <h1>Agreements Listing</h1>
                <form action="" method ="post">
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
                        <th>Uploaded Agreeements</th>
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
                                        <?php 
											if ($status == 'pending') {
                                          ?>
                                        <form method="POST" action="" enctype="multipart/form-data">
                                        <?php 
											}
                                          ?>
                                        <td><?php if ($status == 'accepted') {
                                                   echo "<a href='$receivedFileLink' download='$receivedFileName'>$receivedFileName</a><br>";
                                                } else {
                                                    echo "<input type=\"file\" id=\"file\" name=\"file\" accept=\".pdf, .doc, .docx\">";
                                                } ?></td>
                                         <td>
                                         <?php 
											if ($status == 'pending') {
                                          ?>
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                                <input type="hidden" id="action" name="action" value="accept">
                                                <input type="submit" onclick="return confirmWindow('accept')" class="btn btn-secondary" value="Upload & Accept">
                                                </form>
                                                <form method="POST" action="">
                                                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
												<input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <input type="hidden" id="action" name="action" value="reject">
                                                <input type="submit" onclick="return confirmWindow('reject')" class="btn btn-secondary" value="Reject">
                                                </form>
                                                <?php 
											}
											if (($status == 'pending' || $status == 'accepted') && $payStatus == 'unpaid' ) {
                                          ?>
                                               <form method="POST" action="securePayment.php">
                                                <input type="hidden" name="agreementId" value="<?php echo $agreementId; ?>">
                                                <input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
                                                <input type="hidden" id="rental" name="rental" value="<?php echo $deposit; ?>">
                                                <input type="submit" class="btn btn-secondary" value="Pay">
                                                </form>
										<?php 
											}
                                          ?>
                                        </td>
                                    </tr>
                             <?php
                            }
                        }
                        else
                        {
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

