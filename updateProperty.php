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
   $_SESSION['unlog']=  "Please login first.";
    include('headerAfterLogin.php');
   
    
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}


include("connect.php");
$name = '';
$description = '';
$price = '';
$location = '';
$address = '';
$current_image = '';
$propertytype = '';
$renttype = '';
$status = '';
$propertyId='';
$msg="";

if (isset($_POST['id'])) {
   
    //Get the ID and all other details
                $id = $_POST['id'];
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
                    $price = $row['price'];
                    $location = $row['location'];
                    $address = $row['address'];
                    $current_image= $row['pic'];
                    $propertytype= $row['propertytype'];
                    $renttype= $row['rentType'];
                    $status= $row['propertystatus'];
                    $propertyId=$row['propertyId'];

                }
                else
                {
                    //redirect to manage  with session message
                    $_SESSION['updated'] = "<div class='error'>Property not Found.</div>";
                   header('location: ownerProperty.php');
                }

}else{
    header('location: ownerProperty.php');
}

if (isset($_POST['submit'])) {
    
    // Process the form data
    $id = $_POST['id'];  //the id is the one that refers the name in form , same for all
    $newPropertyName = $_POST["name"];
    $newDescription = $_POST["description"];
    $newPrice = $_POST["price"];
    //$newStatus = $_POST["status"];
    $newAddress = $_POST["address"];
    $newLocation = $_POST["location"];
    $propertytype = $_POST["propertytype"];
    $renttype = $_POST["renttype"];
    
    // Handle photo upload if a new photo is selected
   // Check if a file has been uploaded in the 'photo' field
   if (!empty($newPropertyName) && !empty($newDescription) && !empty($newPrice) && !empty($newAddress) && !empty($newLocation) && !empty($propertytype) && !empty($renttype)) {
     $newFilePath = "";

    if (isset($_FILES["photo"]) && $_FILES["photo"]["name"]) {
        $targetDirectory = "uploads/"; // Set your desired upload directory
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if the file is an actual image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Check and limit the file size if needed
            if ($_FILES["photo"]["size"] > 10000000) { // 10 MB limit
                $msg="Sorry, your file is too large.";
            } else {
                // Generate a unique filename
                $newFilename = uniqid() . '.' . $imageFileType;
                $newFilePath = $targetDirectory . $newFilename;

                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $newFilePath)) {
                    // File uploaded successfully, update the database with the new photo filename
                    $updatePhotoSQL = "UPDATE property SET pic = '$newFilePath' WHERE propertyId = $id";
                    mysqli_query($conn, $updatePhotoSQL);
                } else {
                    $msg= "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $msg= "File is not an image.";
        }

    }

       if(empty($msg)){
          //  Update the user's other information
        $updateUserSQL = "UPDATE property SET propertyname = '$newPropertyName', price = '$newPrice', description = '$newDescription', propertystatus = 'pending', address= '$newAddress', propertytype='$propertytype', renttype='$renttype', location='$newLocation'  WHERE propertyId = $id";
       
        if (mysqli_query($conn, $updateUserSQL)) {
            $_SESSION['updated'] = "<div class='error text-center'>Updated, pending approval from admin.</div>";
            header('location: ownerProperty.php');

        } else {
            $msg= "Error updating property: " . mysqli_error($conn);
        }
       }
    
    } else{
        $msg= "fields cannot be empty.";
    } 
   
}

?>
<br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div><br>
<div class="container" align="middle">
<div class="card" style="width: 50rem; height: 68rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Update Property</b></h1>
<form action="" method="POST" enctype="multipart/form-data">
<table class="tbl-full">
    <tr>
        <td><br>Property Name: </td>
        <td>
           <br> <input type="text" name="name" value="<?php echo $name; ?>" required>
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
                   <img src="<?php echo $current_image; ?>" alt="Image" width="250">
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
        <br><input type="text" name="location" value="<?php echo $location; ?>" required>
        </td>
    </tr>

    <tr>
        <td><br>Address: </td>
        <td>
           <br> <input type="text" name="address" value="<?php echo $address; ?>" required>
        </td>
    </tr>
    <tr>
        <td><br>Property Type: </td>
        <td>
        <br>
        <?php
        $options = array(
            'Condominium',
            'Apartment',
            'Flat',
            'Studio Apartment',
            'Serviced Residence',
            'Terrace',
            'Bungalow',
            'Semi-Detached',
            'Townhouse'
        );

        echo '<select id="propertytype" name="propertytype" required>';
        echo "<option value=\"\">Please select</option>";
        foreach ($options as $option) {
            $selected = ($propertytype == $option) ? 'selected' : '';
            echo "<option value=\"$option\" $selected>$option</option>";
        }

        echo '</select>';
        ?>
    </td>
    </tr>
    <tr>
        <td><br>Rent Type: </td>
        <td>
        <br>
        <?php
        $options = array(
            'Room',
            'Whole Unit'
        );

        echo '<select id="renttype" name="renttype" required>';
        echo "<option value=\"\">Please select</option>";
        foreach ($options as $option) {
            $selected = ($renttype == $option) ? 'selected' : '';
            echo "<option value=\"$option\" $selected>$option</option>";
        }

        echo '</select>';
        ?>
    </td>
    </tr>
    <tr>
        <td><br>Price: </td>
        <td><br>
        <input type="text" name="price" value="<?php echo $price; ?>" required>
            </td>
    </tr>
    <tr>
        <td><br>Description: </td>
        <td><br>
        <input type="text" name="description" value="<?php echo $description; ?>" required>
            </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td><br>
        <input type="hidden" name="id" value="<?php echo $propertyId; ?>">
            <input type="submit" name="submit" value="Update Property" class="btn-secondary">
        </td>
    </tr>

</table>

</form>
</div>
</div>&nbsp;</div>
<?php include('footer.html'); ?>
    


