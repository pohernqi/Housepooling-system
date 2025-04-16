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
$name = '';
$description = '';
$location = '';
$address = '';
$current_image = '';
$propertytype = '';
$status = '';
$propertyId= $_GET['id'];

if (isset($propertyId)) {
   
    //Get the ID and all other details
                //echo "Getting the Data";
                $id = $propertyId;
                //Create SQL Query to get all other details
                $sql = "SELECT * FROM property WHERE propertyId=$id";

                //Execute the Query
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res)==1)
                {
                    //Get all the data
                    $row = mysqli_fetch_assoc($res);
                    $name = $row['propertyname'];
                    $description = $row['description'];
                    $location = $row['location'];
                    $address = $row['address'];
                    $current_image= $row['pic'];
                    $propertytype= $row['propertytype'];
                    $status= $row['propertystatus'];
                    $propertyId=$row['propertyId'];

                }
                else
                {
                    //redirect to manage  with session message
                    $_SESSION['no-property-found'] = "<div class='error'>Property not Found.</div>";
                   header('location: adminProperty.php');
                }

}else{
    header('location: adminProperty.php');
}

?><?php
if (isset($_POST['submit'])) {
    
    // Process the form data
    $id = $_POST['id'];  //the id is the one that refers the name in form , same for all
    $newPropertyName = $_POST["name"];
    $newDescription = $_POST["description"];
    //$newStatus = $_POST["status"];
    $newAddress = $_POST["address"];
    $newLocation = $_POST["location"];
    $propertyType = $_POST["propertytype"];
    
    
    // Handle photo upload if a new photo is selected
   // Check if a file has been uploaded in the 'photo' field
   if (!empty($newPropertyName) && !empty($newDescription) && !empty($newAddress) && !empty($newLocation) && !empty($propertyType)) {
     $newFilePath = "";

    if (isset($_FILES["photo"]) && $_FILES["photo"]["name"]) {
        $targetDirectory = "uploads/"; // Set your desired upload directory
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if the file is an actual image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Check and limit the file size if needed
            if ($_FILES["photo"]["size"] > 5000000) { // 5 MB limit
                echo "Sorry, your file is too large.";
            } else {
                // Generate a unique filename
                $newFilename = uniqid() . '.' . $imageFileType;
                $newFilePath = $targetDirectory . $newFilename;

                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $newFilePath)) {
                    // File uploaded successfully, update the database with the new photo filename
                    $updatePhotoSQL = "UPDATE property SET pic = '$newFilePath' WHERE propertyId = $id";
                    mysqli_query($conn, $updatePhotoSQL);
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            echo "File is not an image.";
        }

    }
    
        //  Update the user's other information
        $updateUserSQL = "UPDATE property SET propertyname = '$newPropertyName', description = '$newDescription', address= '$newAddress', propertytype='$propertyType', location='$newLocation'  WHERE propertyId = $id";
       
        if (mysqli_query($conn, $updateUserSQL)) {
            $_SESSION['updated'] = "<div class='error text-center'>Updated Successfully!.</div>";
            header('location: manageListings.php');

        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }

    } else{
        echo "fields cannot be empty.";
    } 
   
}

?>


<div class="container" align="middle">
<div class="card" style="width: 50%; height: 20%; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>Update Property</b></h1>
   
<form action="" method="POST" enctype="multipart/form-data">
<table class="tbl-full">
    <tr>
        <td><br>Property Name: </td>
        <td>
           <br> <input type="text" name="name" value="<?php echo $name; ?>">
        </td>
    </tr>


    <tr>
        <td><br>Current Image: </td>
        <td><br>
            <?php 
                if($current_image != "")
                {
                    //Display the Image
                    ?>
                   <img src="<?php echo $current_image; ?>" alt="Image">
                    <?php
                }
                else
                {
                    //Display Message
                    echo "<div class='error'>Image Not Added.</div>";
                }
            ?>
        </td>
    </tr>

    <tr>
        <td><br>New Image: </td>
        <td><br>
            <input type="file" name="photo" id="photo" accept="image/*">
        </td>
    </tr>

    <tr>
        <td><br>Location: </td>
        <td>
        <br><input type="text" name="location" value="<?php echo $location; ?>">
        </td>
    </tr>

    <tr>
        <td><br>Address: </td>
        <td>
           <br> <input type="text" name="address" value="<?php echo $address; ?>">
        </td>
    </tr>

    <tr>
        <td><br>Property Type: </td>
        <td>
        <br><input type="text" name="propertytype" value="<?php echo $propertytype; ?>">
        </td>
    </tr>

    <tr>
        <td><br>Description: </td>
        <td><br>
        <input type="text" name="description" value="<?php echo $description; ?>">
            </td>
    </tr><tr>
        <td>&nbsp;</td></tr>
    <tr>
        <td>
        <input type="hidden" name="id" value="<?php echo $propertyId; ?>">
           
            <input type="submit" name="submit" value="Update Property" class="btn-secondary">
        </td>
    </tr><tr>
        <td>&nbsp;</td></tr>

</table>

</form>
</div>
</div>
</div>

<style>
    img {
            max-width: 350px;
            /* Set a fixed width */
            height: auto;
            /* Maintain aspect ratio */
        }
</style>
<?php include('footer.html'); ?>   


