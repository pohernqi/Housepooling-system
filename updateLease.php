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

?>

<?php 
include("connect.php");
$rental = '';
$startDate = '';
$endDate = '';
$deposit = '';
$status = '';
$payStatus = '';
$docs = '';
$email='';
$agreementId= '';
$propertyId = "";
$msg = "";
if (isset($_POST['agreementId'])) {
                //Create SQL Query to get all other details
                $agreementId = intval($_POST['agreementId']);
                $sql = " SELECT a.*, t.email AS tenant_email
                FROM agreement AS a
                JOIN tenant AS t ON a.tenantId = t.tenantId
                WHERE a.agreementId = $agreementId ";
                
                //Execute the Query
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res)==1)
                {
                    //Get all the data
                    $row = mysqli_fetch_assoc($res);
                    
                    $email= $row['tenant_email'];
                    $rental = $row['rental'];
                    $startDate = $row['startdate'];
                    $endDate = $row['enddate'];
                    $deposit = $row['deposit'];
                    $status = $row['status'];
                    $payStatus= $row['paystatus'];
                    $agreementId= $row['agreementId'];
                    $propertyId= $row['propertyId'];
                    $docs = $row['docs'];

                }
                

}

if (isset($_POST['submit'])) {
    $agreementId = $_POST['agreementId'];
    $rental = $_POST['rental'];
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    $deposit = $_POST['deposit'];

    if (!empty($rental) && !empty($startDate) && !empty($endDate) && !empty($deposit)) {
        if($startDate > $endDate) $msg = "End date must be greater than start date.";
        else {
            $targetDirectory = "docs/";
            $fileUploaded = !empty($_FILES["file"]["name"]);
            $newFileName = "";

            if ($fileUploaded) {
                $fileName = basename($_FILES["file"]["name"]);
                $targetFile = $targetDirectory . $fileName;
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                $allowedTypes = ['docx', 'doc', 'pdf'];
                if (in_array($fileType, $allowedTypes)) {
                    if ($_FILES["file"]["size"] > 10000000) { // 10 MB limit
                        $msg = "Sorry, your file is too large.";
                    }
                    else
                    {
                        $fileCounter = 1;
                        if(file_exists($targetFile)){
                            while (file_exists($targetFile)) {
                                $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_" . $fileCounter . "." . $fileType;
                                $targetFile = $targetDirectory . $newFileName;
                                $fileCounter++;
                            }
                        }else{
                            $newFileName = basename($_FILES["file"]["name"]);
                        }

                        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
                    }
                } else {
                    $msg= "File is not an allowed document type.";
                }
            }

            if(empty($msg))
            {

                    $updateSQL = "UPDATE `agreement` SET 
                                    `rental` = '$rental', 
                                    `startdate` = '$startDate', 
                                    `enddate` = '$endDate', 
                                    `deposit` = '$deposit', 
                                    `status` = 'pending', 
                                    `paystatus` = 'unpaid'";

                    if ($fileUploaded) {
                        $updateSQL .= ", `docs` = '$newFileName'";
                    }

                    $updateSQL .= " WHERE `agreementId` = $agreementId";

                    if (mysqli_query($conn, $updateSQL)) {
                        $_SESSION['sucessmsg'] = "<div class='error text-center'>Agreememt is updated successfully.</div>";
                        $_SESSION['propertyId'] = $propertyId;
                        header('location: ownerLease.php');
                    } else {
                        $msg = "Error updating agreement: " . mysqli_error($conn);
                    }
                //}
            }
        }
    } else {
        $msg = "No empty fields allowed.";
    }
}
?>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="container" align="left">
<div class="card" style="width: 50rem; height: 70rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b><?php
        if ($status === 'pending') {
            echo "Update Lease";
        } else {
            echo "Renew Lease";
        }
        ?></b></h1>
   
<form action="" method="POST" enctype="multipart/form-data">
<table class="tbl-full">
<tr>
    <td>Tenant Email:</td>
    <td><span name="email"><?php echo $email; ?></span></td>
</tr>


    <tr>
        <td><br>Rental: </td>
        <td>
           <br> <input type="number" name="rental" value="<?php echo $rental; ?>" min="0" required>
        </td>
    </tr>

    <tr>
        <td><br>Start Date: </td>
        <td>
           <br> <input type="date" name="startdate" value="<?php echo $startDate; ?>" required>
        </td>
    </tr>

    
    <tr>
        <td><br>End Date: </td>
        <td>
           <br> <input type="date" name="enddate" value="<?php echo $endDate; ?>" required>
        </td>
    </tr>

    <tr>
        <td><br>Deposit: </td>
        <td>
           <br> <input type="number" name="deposit" value="<?php echo $deposit; ?>" min="0" required>
        </td>
    </tr>

    <tr>
    <td><br>Status : </td>
    <td>
        <br><span><?php echo $status; ?></span>
    </td>
</tr>

<tr>
    <td><br>Payment Status : </td>
    <td>
        <br><span><?php echo $payStatus; ?></span>
    </td>
</tr>
<?php
        $fileLink = "docs/" .$row["docs"];
        $fileName = basename($fileLink);
?>
<tr>
    <td><br>Current Doc : </td>
    <td><br><?php echo "<a href='$fileLink' download='$fileName'>$fileName</a>"; ?>
     </td>
</tr>

    <tr>
        <td><br>Update Doc: <br>(pdf, doc, docx)</td>
        <td>
        <br>&nbsp;<input type="file" name="file" accept=".pdf, .doc, .docx"  value="<?php echo $docs; ?> ">
        </td>
    </tr>

   
    
    <tr>
        <td>
            <br>
            <input type="hidden" name="agreementId" value="<?php echo $agreementId;?>">
            <input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
            <input type="submit" name="submit" value="Update Lease" class="btn-secondary">
        </td>
    </tr>

</table>

</form>
</div>
</div></div>
<?php include('footer.html'); ?>