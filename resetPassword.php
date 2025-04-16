<?php
include("header.html");
$msg="";

if(isset($_POST['password'])) {
    $password = $_POST['password'];
    $number = preg_match('@[0-9]@', $password);
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    //$specialChars = preg_match('@[^\w]@', $password);
   
    if(strlen($password) < 6 || strlen($password) > 12 || !$number || !$uppercase || !$lowercase) {
      $msg = "Password must be at least 6 - 12 characters in length and must contain at least one number, one upper case letter and one lower case letter.";
    } 
}

if (empty($msg) && isset($_POST['submit'])) {
    include("connect.php");
    $token = $_GET["token"];
    $token_hash = hash("sha256", $token);
    $newPassword = $_POST['password'];
    $passwordConfirmation = $_POST['password_confirmation'];

    if (empty($newPassword) || empty($passwordConfirmation)) {
        $msg = "Please enter both new password and password confirmation.";

    } elseif ($newPassword != $passwordConfirmation) {
        $msg = "Password and password confirmation do not match.";

    } else {
        // Password validation passed, proceed with updating the password
        // Hash the new password before storing it in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

       // Define the tables to check
        $tables = ['owner', 'tenant', 'admin', 'searcher'];

        // Flag to indicate if email exists
        $tokenExists = false;

        $table='';
        // Check if email exists in any table
        foreach ($tables as $table) {
            $sql = "UPDATE $table SET password = '$hashedPassword', reset_token_hash = NULL WHERE reset_token_hash = '$token_hash'";
            if(mysqli_query($conn, $sql))
            {
                if (mysqli_affected_rows($conn) > 0) 
                { 
                    $msg="Password is updated Successfully.";
                    $tokenExists = true;
                    break;
                }
                else $tokenExists = false;
            } else {
                $msg="Error updating password. Please try again.";
            }
        }
        if (!$tokenExists) $msg="token is not found!";
    }
}
?>    
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<div class="container" align="middle">
<div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Reset Password</b></h1>
<form action="" method="post">
<table>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;New Password :</p></b></td>
        <td>&nbsp;<input type="password" id="password" name="password" required></td>
    </tr>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Repeat Password :</p></b></td>
        <td>&nbsp;<input type="password" id="password_confirmation"  name="password_confirmation" required></td>
    </tr>
<br>    
    </table>
<br>
<input type="submit" name="submit" value="Send"><br>
</form>
</div>
</div>
</div>
<?php include('footer.html'); ?>

