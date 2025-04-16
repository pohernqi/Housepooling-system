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

include("connect.php");

$msg ="";
if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {
    // Process the form data
    $newUsername = $_POST["username"];
    $newPhone = $_POST["phone"];
    $newEmail = $_POST["email"];
    $currentPwd = $_POST["password"];
    $newPassword = $_POST["newpassword"];
    
    if (empty($newUsername) || empty($newPhone) || empty($newEmail)) {
        $msg = "User Name, Phone Number and Email Fields cannot be empty.";
    }
    if (empty($msg) && ((!empty($newPassword) && empty($currentPwd)) || (empty($newPassword) && !empty($currentPwd)))) {
        $msg = "Please enter current password first.";
    }
    if (empty($msg) && !empty($newPassword)) {
        $number = preg_match('@[0-9]@', $newPassword);
         $uppercase = preg_match('@[A-Z]@', $newPassword);
        $lowercase = preg_match('@[a-z]@', $newPassword);
        //$specialChars = preg_match('@[^\w]@', $password);
        
        if(strlen($newPassword) < 6 || strlen($newPassword) > 12 || !$number || !$uppercase || !$lowercase) {
             $msg = "Password must be at least 6 - 12 characters in length and must contain at least one number, one upper case letter and one lower case letter.";
        } 
    }
    //check email format
    if(empty($msg) && !filter_var($newEmail, FILTER_VALIDATE_EMAIL))
        $msg = "Invalid email format.";
    
    if(empty($msg)) {
        // Define the tables to check
        $tables = ['owner', 'tenant', 'admin', 'searcher'];

        // Flag to indicate if email exists
        $emailExists = false;

        // Check if email exists in any table
        foreach ($tables as $table) {
            $query = "SELECT $table"."Id FROM $table WHERE email = '$newEmail'";
            $result = mysqli_query($conn, $query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc(); // Fetch data
                $foundID = $row[$table."Id"];
                if($foundID != $user_id)
                {
                    $emailExists = true;
                    break;
                }
            }  
        }

        if ($emailExists) {
            // Email already exists in one of the tables
            $msg = "The email address is already registered. Please use a different email.";
        }
    }
    $newFilePath="";
    // Handle photo upload if a new photo is selected
    if (empty($msg) && $_FILES["photo"]["name"]) {
        $targetDirectory = "uploads/"; // Set your desired upload directory
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if the file is an actual image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Check and limit the file size if needed
            if ($_FILES["photo"]["size"] > 5000000) { // 5 MB limit
                $msg= "Sorry, your file is too large.";
            } else {
                // Generate a unique filename
                $newFilename = uniqid() . '.' . $imageFileType;
                $newFilePath = $targetDirectory . $newFilename;
             } 
        } else {
            $msg= "File is not an image.";
       }
    } 
        
    if(empty($msg))
    {
        if (!empty($currentPwd) && !empty($newPassword))
        {
            $checkPasswordSQL = "SELECT password FROM $user_type WHERE {$user_type}Id = $user_id";
            $result = mysqli_query($conn, $checkPasswordSQL);
            $row = mysqli_fetch_assoc($result);
            $storedPasswordHash = $row["password"];
            if(!password_verify($currentPwd, $storedPasswordHash))
                $msg= "Current password is incorrect.";
        }

        // Update the user's other information
        if(empty($msg)) {
            $updateUserSQL = "UPDATE $user_type SET username = '$newUsername', phone = '$newPhone', email = '$newEmail'";
                
            // Check if a new password is provided and add it to the query if not empty
            if (!empty($newPassword)) {
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateUserSQL .= ", password = '$hashedNewPassword'";
            }

            if (!empty($newFilePath)) {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $newFilePath)) {
                     $updateUserSQL .= ", pic = '$newFilePath'";
                }
                else $msg= "However, there was an error uploading your file.";
            } 

            $updateUserSQL .= " WHERE {$user_type}Id = $user_id";

            if (mysqli_query($conn, $updateUserSQL)) {
                $msg= "Profile updated successfully.".$msg;
            } else {
                $msg= "Error updating profile: " . mysqli_error($conn);
            }
        } 
    }
}

  // SQL query to fetch initial user data
  $sql = "SELECT pic, username, phone, email FROM $user_type WHERE {$user_type}Id = $user_id";
  $result = mysqli_query($conn, $sql);

  $pic = "";
  $username = "";
  $phone = "";
  $email = "";

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc(); // Fetch data
      $pic = $row["pic"];
      $username = $row["username"];
      $phone = $row["phone"];
      $email = $row["email"];
  } else {
    $msg= "0 results";
  }
  
  $pageToken = bin2hex(random_bytes(16)); // Generate a random token
  $_SESSION['pageToken'] = $pageToken;
?>
<style>
#profile-image-container {
    text-align: center; /* Center aligns the content */
    width: 150px; /* Adjust the width as needed */
    margin: auto; /* This centers the container in the parent element */
}

#profile-image {
    width: 100%;
    height: auto; /* Maintain aspect ratio */
    border-radius: 50%; /* Circular image */
    object-fit: cover;
    display: block; /* Ensures no inline space around the image */
    margin-bottom: 10px; /* Space between the image and the username */
}
</style>

<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<br>
<div class="container" align="middle">
    <div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
        <br>
        <div class="card-body" align="left">
            <h1 class="card-title"><b>Manage Profile</b></h1>
            <br>
        <form  method="post" enctype="multipart/form-data">
        <div id="profile-image-container">
        <img src="<?php echo $pic ?>" alt="Profile Image" id="profile-image">
        </div>
        <table style="width:100%"><tr><td style="white-space: nowrap;">
        <label for="username">&nbsp;User Name:&nbsp;</label></td><td>
        <input type="text" id="username" name="username" required value="<?php echo $username ?>">
        </td></tr><tr><td style="white-space: nowrap;">
        <label for="password">&nbsp;Current Password:&nbsp;</label></td><td>
        <input type="password" id="password" name="password" ><br>
        </td></tr><tr><td style="white-space: nowrap;">
        <label for="newpassword">&nbsp;New Password:&nbsp;</label></td><td>
        <input type="password" id="password" name="newpassword" ><br>
        </td></tr><tr><td style="white-space: nowrap;">
        <label for="phone">&nbsp;Phone Number:&nbsp;</label></td><td>
        <input type="tel" id="phone" name="phone" pattern="\d{3}-\d{4}\d{4}|\d{3}-\d{4}\d{3}" required value="<?php echo $phone ?>"><br>
        </td></tr><tr><td style="white-space: nowrap;">
        <label for="email">&nbsp;Email:&nbsp;</label></td><td>
        <input type="email" id="email" name="email" required value="<?php echo $email ?>"><br><br>
        </td></tr><tr><td style="white-space: nowrap;">
        <label for="photo">&nbsp;Update Photo:&nbsp;</label></td><td>
        <input type="file" id="photo" name="photo" accept="image/*">
        <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
        </td></tr><tr><td><br>
        <input type="submit" value="Update Profile"></td></tr>
        </form><br>
        </table>
</div></div></div><br>
<?php include('footer.html'); ?>
 