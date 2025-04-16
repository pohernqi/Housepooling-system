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

$msg=""; 

if (isset($_POST['submit'])) {
    
    // Process the form data
    $newPropertyName = $_POST["name"];
    $newDescription = $_POST["description"];
    $newPrice = $_POST["price"];
    $newStatus = "pending";
    $newAddress = $_POST["address"];
    $newLocation = $_POST["location"];
    $propertyType = $_POST["propertytype"];
    $rentType = $_POST["renttype"];
    // Check if all fields are empty 
    if (!empty($newPropertyName) && !empty($newDescription) && !empty($newAddress) && !empty($newLocation) && !empty($propertyType) && !empty($rentType) && !empty($newPrice)) {
       
        $newFilePath = "";
        // Handle photo upload if a new photo is selected
        // Check if a file has been uploaded in the 'photo' field
        if (isset($_FILES["photo"]) && $_FILES["photo"]["name"]) {
            $targetDirectory = "uploads/"; // Set your desired upload directory
            $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            // Check if the file is an actual image
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if ($check !== false) {
                // Check and limit the file size if needed
                if ($_FILES["photo"]["size"] > 5000000) { // 5 MB limit
                    $msg="Sorry, your file is too large.";
                } else {
                    // Generate a unique filename
                    $newFilename = uniqid() . '.' . $imageFileType;
                    $newFilePath = $targetDirectory . $newFilename;

                    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $newFilePath)) {
                        // File uploaded successfully, update the database with the new photo filename
                    } else {
                        $msg="Sorry, there was an error uploading your file.";
                    }
                }
            } else {
                $msg="File is not an image.";
            }

              //insert
              if(empty($msg)){
                $addSQL = "INSERT INTO property (ownerId, pic, propertyname, description, propertystatus, address, location, propertytype, rentType, price) VALUES ('$user_id', '$newFilePath', '$newPropertyName', '$newDescription', '$newStatus', '$newAddress', '$newLocation', '$propertyType', '$rentType', $newPrice)";
                include("connect.php");
               
                if (mysqli_query($conn, $addSQL)) {
                    $_SESSION['updated'] = "<div class='error text-center'>Property added successfully and pending approval from admin.</div>";
                    header('location: ownerProperty.php');
                }else {
                    $msg="Error updating profile: " . mysqli_error($conn);
                }
              }
               
            
        } else {
            $msg="Please upload a property pic.";
           
        }
    } else{
        $msg="No empty fields allowed.";
    } 

}
  
?> 
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<div class="container" align="middle">
<div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Add Property</b></h1>
<form action="" method="POST" enctype="multipart/form-data">
<table class="tbl-full">
<tr>
        <td><br>Property Name: </td>
        <td>
           <br> <input type="text" name="name" value="" required>
        </td>
    </tr>


    <tr>
        <td><br>Upload Image: </td>
        <td><br>
            <input type="file" name="photo" id="photo" accept="image/*" required>
        </td>
    </tr>

    <tr>
        <td><br>Location: </td>
        <td>
        <br><input type="text" name="location" value="" required>
        </td>
    </tr>

    <tr>
        <td><br>Address: </td>
        <td>
           <br> <input type="text" name="address" value="" required>
        </td>
    </tr>

    <tr>
        <td><br>Property Type: </td>
        <td>
        <br><select name="propertytype" id="propertytype" required>
            <option value="">Please select</option>
            <option value="Condominium">Condominium</option>
            <option value="Apartment">Apartment</option>
            <option value="Flat">Flat</option>
            <option value="Studio Apartment">Studio Apartment</option>
            <option value="Serviced Residence">Serviced Residence</option>
            <option value="Terrace">Terrace</option>
            <option value="Bungalow">Bungalow</option>
            <option value="Semi-Detached">Semi-Detached</option>
            <option value="Townhouse">Townhouse</option>
        </select>
        </td>
    </tr>
    <tr>
        <td><br>Rent Type: </td>
        <td>
        <br><select name="renttype" id="renttype" required>
            <option value="">Please select</option>
            <option value="Room">Room</option>
            <option value="Whole Unit">Whole Unit</option>
        </select>
        </td>
    </tr>
    <tr>
        <td><br>Price: </td>
        <td><br>
        <input type="number" name="price" value=""  step="0.01" required>
            </td>
    </tr>
    <tr>
        <td><br>Description: </td>
        <td><br>
        <input type="text" name="description" value="" required>
            </td>
    </tr>
<br>    
    </table>
<br>
<input type="submit" name="submit" value="Add Property" class="btn-secondary">
</form>
<br><br>
</div>
</div>
</div>
<?php include('footer.html'); ?>