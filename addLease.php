<!--checkSession()-->
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


include("connect.php");
$msg="";
$propertyId="";

if (isset($_POST['propertyId'])) 
	$propertyId= $_POST['propertyId'];
   
if (isset($_POST['submit'])) {
    
    // Process the form data
    $tenantEmail = $_POST['email'];
    $rental = $_POST['rental'];
    $startDate = $_POST['startdate'];
    $endDate = $_POST['enddate'];
    $deposit = $_POST['deposit'];
    $docs = $_FILES['file'];
   
    // Check if all fields are empty (excluding the image)
    if (!empty($tenantEmail) && !empty($rental) && !empty($startDate) && !empty($endDate) && !empty($deposit)) {
       
        //check email format
        if(!filter_var($tenantEmail, FILTER_VALIDATE_EMAIL))
            $msg = "Invalid email format.";
        else {
            if($startDate > $endDate) $msg = "End date must be greater than start date.";
            else 
            {
                 $fileName = "";
                // Check if a file has been uploaded in the 'file' field
                if ($_FILES["file"]["error"] == 0) {
                    $targetDirectory = "docs/"; // Set your desired upload directory
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
                            $msg = "Sorry, your file is too large.";
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

                            //find the tenant using email
                            $sql = "SELECT tenantId FROM tenant WHERE email = '$tenantEmail'";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Fetch the tenant's data
                                $row = $result->fetch_assoc();
                                $tenantId = $row['tenantId'];


                                $addSQL = "INSERT INTO `agreement` (`rental`, `startdate`, `enddate`, `deposit`, `status`, `paystatus`, `tenantId`, `propertyId`, `docs`) 
                                VALUES ('$rental', '$startDate', '$endDate', '$deposit', 'pending', 'unpaid', '$tenantId', $propertyId , '$newFileName')";
                                echo $addSQL;
                                if (mysqli_query($conn, $addSQL)) {
                                    $_SESSION['sucessmsg'] = "<div class='error text-center'>Agreememt is added successfully.</div>";
                                    $_SESSION['propertyId'] = $propertyId;
                                    header('location: ownerLease.php');

                                } else {
                                    $msg= "Error updating profile: " . mysqli_error($conn);
                                }
                            } else {
                                $msg= "Tenant is not registered";
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
        } 
    } else {
        $msg ="No empty fields allowed.";
    }
}

?>

<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="container" align="left">
<div class="card" style="width: 50rem; height: 70rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>Add Lease</b></h1>
<form action="" method="POST" enctype="multipart/form-data">
<table class="tbl-full">
    <tr>
        <td><br>Tenant's email:&nbsp; </td>
        <td>
           <br> <input type="text" name="email" value="" required>
        </td>
    </tr>

    <tr>
        <td><br>Rental: </td>
        <td>
           <br> <input type="number" name="rental" value="" min="0" required>
        </td>
    </tr>

    <tr>
        <td><br>Start Date: </td>
        <td>
           <br> <input type="date" name="startdate" value="" required>
        </td>
    </tr>

    <tr>
        <td><br>End Date: </td>
        <td><br>
        <input type="date" name="enddate" value="" required>
    </td>
    </tr>

    <tr>
        <td><br>Deposit: </td>
        <td>
        <br><input type="number" name="deposit" value="" min="0" required>
        </td>
    </tr>

    <tr>
        <td><br>Docs: </td>
        <td>
        <br><input type="file" name="file" value="" accept=".pdf, .doc, .docx" required>
        </td>
    </tr>

    
    <tr>
        <td><br>
        <input type="hidden" name="propertyId" value="<?php echo $propertyId; ?>">
        <input type="submit" name="submit" value="Add Lease" class="btn-secondary">
        </td>
    </tr>

</table>

</form>
</div>
</div></div>
<?php include('footer.html'); ?>