<?php 
include("header.html");


$msg="";
$success= true;
if(isset($_POST['password'])) {
  $password = $_POST['password'];
  $number = preg_match('@[0-9]@', $password);
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  //$specialChars = preg_match('@[^\w]@', $password);
  
 
  if(strlen($password) < 6 || strlen($password) > 12 || !$number || !$uppercase || !$lowercase) {
    $msg = "Password must be at least 6 - 12 characters in length and must contain at least one number, one upper case letter and one lower case letter.";
    $success=false;
} 
}

include("connect.php");
session_start();
// Check if the form is submitted
if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {
    // Collect and sanitize input data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['selectrole']); // 'selectrole' is the name attribute of the select field

    $defaultProfilePicture = 'img/profile.png'; // Replace with the actual file path
    
    //check got empty fields or not
    if (empty($username) || empty($password) || empty($name) || empty($phone) || empty($email)) {
        $msg = "Fields cannot be empty.";
    
    //check email format
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $msg = "Invalid email format.";
        $success=false;

    }else {
        // Define the tables to check
        $tables = ['owner', 'tenant', 'admin', 'searcher'];

        // Flag to indicate if email exists
        $emailExists = false;

        // Check if email exists in any table
        foreach ($tables as $table) {
            $query = "SELECT email FROM $table WHERE email = '$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            // Email already exists in one of the tables
            $msg = "The email address is already registered. Please use a different email.";
            $success=false;
      
        } else {
            // Email does not exist, proceed with registration
            // Encrypt the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Determine the table for insertion based on the role
            switch ($role) {
                case "owner":
                    $insertTable = "owner";
                    break;
                case "tenant":
                    $insertTable = "tenant";
                    break;
                case "admin":
                    $insertTable = "admin";
                    break;
                case "searcher":
                    $insertTable = "searcher";
                    break;
                default:
                    $insertTable = "none"; // default table if role is not matched
            }

           
            if($success){
                 // Insert the new user into the respective table
                $insertQuery = "INSERT INTO $insertTable (username, password, name, phone, email, pic) VALUES ('$username', '$hashed_password', '$name', '$phone', '$email', '$defaultProfilePicture')";
            
                if (mysqli_query($conn, $insertQuery)) {
                    $msg = "Registration is successful.";
                } else {
                    $msg = "Error: " . mysqli_error($conn);
                }
            }
           
           

            
        }
    }
  }

  $pageToken = bin2hex(random_bytes(16)); // Generate a random token
  $_SESSION['pageToken'] = $pageToken;
?>
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<br>
<div class="container" align="middle">
    <div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
        <br>
        <div class="card-body">
            <h1 class="card-title"><b>Sign Up Here</b></h1>
            <br>
            <form name="registerform" action="" method="post">
                <b>Sign Up as :</b>
                <select name="selectrole" size="1">
                    <option value="owner">Owner</option>
                    <option value="tenant">Tenant</option>
                    <option value="admin">Admin</option>
                    <option value="searcher">Searcher</option>
                </select>
                <br><br>
                <table>
                <tr>
                <td ><b><p style="text-align: left;">&nbsp;Username :</p></b></td>
                <td> <input type="text" name="username" value="" size="25" required></td>
            </tr>
           <br>    
            <tr>
                <td><b><p style="text-align: left;">&nbsp;Password :</p></b></td>
                <td> <input type="password" name="password" value="" size="25" required></td>
            </tr>
            <tr>

            <tr>
                <td><b><p style="text-align: left;">&nbsp;Name :</p></b></td>
                <td> <input type="text" name="name" value="" size="25" required></td>
            </tr>

                <td><b><p style="text-align: left;">&nbsp;Phone :</p></b></td>
                <td> <input type="tel" pattern="\d{3}-\d{4}\d{4}|\d{3}-\d{4}\d{3}" placeholder="Ex: 012-1234567" name="phone" value="" size="25" required></td>
            </tr>

            <tr>
                <td><b><p style="text-align: left;">&nbsp;Email :</p></b></td>
                <td> <input type="email" name="email" value="" size="25" required></td>
            </tr>
</table>
            
                <br><br>
                <center>
                <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
                    <input type="submit" name='submit' value="Submit">
                </center>
            </form>
            <center>
                <a href="login.php"><b>Have An account? Sign in here</b></a>
            </center>
            <br><br><br>
        </div>
    </div> 
</div>
<?php include('footer.html'); ?>
