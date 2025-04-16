<?php 
include("header.html");

include("connect.php");
session_start();
$msg="";

if(isset($_SESSION['unlog'])){
    $msg= ($_SESSION['unlog']);
    unset ($_SESSION['unlog']);
}
if(isset($_SESSION['msg'])){
    $msg= ($_SESSION['msg']);
    unset ($_SESSION['msg']);
}

if(isset($_POST['submit'])) {

    // Process for Login
    $selectedOption = $_POST['selectedoption']; // Get the selected login option

    // Get the Data from Login form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Initialize the table name based on the selected option
    $table = '';
    $whereclause = "";
    $gotoPage = "";
    if ($selectedOption === 'owner') {
        $table = 'owner';
        $gotoPage = "ownerProperty.php";
    } elseif ($selectedOption === 'tenant') {
        $table = 'tenant';
        $whereclause = " AND is_deleted=0 ";
        $gotoPage = "tenantProperty.php";
    } elseif ($selectedOption === 'admin') {
        $table = 'admin';
        $gotoPage = "adminMenu.php";
    } elseif ($selectedOption === 'searcher') {
        $table = 'searcher';
        $gotoPage = "index.php";
    }

    // Check if the table name is set
    if (!empty($table)) {

        // Check if fields are empty
        if (empty($username) || empty($password)) {
            $msg = "Fields cannot be empty.";

        } else {
            // SQL to get the user with the specified username
            $sql = "SELECT * FROM $table WHERE username='$username'".$whereclause;

            // Execute the Query
            $res = mysqli_query($conn, $sql);

            if ($res) {
                // Check whether the user exists or not
                $count = mysqli_num_rows($res);

                if ($count == 1) {
                    // User exists, now verify the password
                    $row = mysqli_fetch_assoc($res);
                    $hashed_password = $row['password']; // Replace 'password' with your actual password field name

                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, login successful
                        $user_id = $row[$table . 'Id'];
                        $user_type= $table;
                        // Store the user ID in the session for later use
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['logged_in'] = true; // To check whether the user is logged in or not, and logout will unset it
                        $_SESSION['user_type'] = $table;
                       
                        // Redirect to appropriate dashboard
                        header('location: ' . $gotoPage);
                    } else {
                        // Password is incorrect
                        $msg = "Password did not match.";
                    }
                } else {
                    // User not found
                    $msg = "Username does not exist";
                }
            } else {
                // Query execution error
                $msg = "An error occurred while trying to log in.";
            }
        }
    }
}
?>
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<div class="container" align="middle">
<div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="center"><b>Login</b></h1>
<form name="loginform" action="" method="post">
<table>
    <tr>
        <td><b>&nbsp;Login As :  &nbsp;</b></td>
        <td> 
            <select name="selectedoption" size="1">
                <option value="owner">Owner</option>
                <option value="tenant">Tenant</option>
                <option value="admin">Admin</option>
                <option value="searcher">Searcher</option>
            </select>
        </td>
    </tr>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Username :</p></b></td>
        <td>&nbsp;<input type="text" name="username" value="" size="25" required></td>
    </tr>
<br>    
    <tr>
        <td><b><p style="text-align: left;">&nbsp;Password :</p></b></td>
        <td>&nbsp;<input type="password" name="password" value="" size="25" required></td>
    </tr>
   
    </table>
<br>

    <br>
<input type="submit" name="submit" value="Login" id=""><br>

</form><br>
    <a href="forgotPassword.php"><b>Forgot Password?</b></a><br><br>
</div>
</div>
</div>
<?php include('footer.html'); ?>


