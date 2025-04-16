<?php 
include('header.html'); 
$msg="";
include("connect.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
//required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
 
//Create an instance; passing `true` enables exceptions
$send="";
session_start();
if ((isset($_POST['send']) && is_null($_SESSION['forgotPassword.php'])) || (isset($_POST['email']) && $_POST["email"] != $_SESSION['forgotPassword.php']))
{
    $send = $_POST['send'];
}
else $_SESSION['forgotPassword.php'] = null;

if (!empty($send) ) {
  $email = $_POST["email"];
  $token = bin2hex(random_bytes(16));
  $token_hash = hash("sha256", $token);
   // Define the tables to check
  $tables = ['owner', 'tenant', 'admin', 'searcher'];

   // Flag to indicate if email exists
   $emailExists = false;

   $table='';
   
   // Check if email exists in any table
   foreach ($tables as $table) {
       $query = "SELECT email FROM $table WHERE email = '$email'";
       $result = mysqli_query($conn, $query);

       if (mysqli_num_rows($result) > 0) {
           $emailExists = true;
           break;
       }
   }

  if ($result->num_rows > 0) 
  {

    $mail = new PHPMailer(true);
    $row = $result->fetch_assoc();
    
    $sql2 = "UPDATE $table
    SET reset_token_hash = '$token_hash'
    WHERE email = '$email'";

    mysqli_query($conn, $sql2);


    //Server settings
    $mail->isSMTP();                              //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';       //Set the SMTP server to send through
    $mail->SMTPAuth   = true;             //Enable SMTP authentication
    $mail->Username   = 'pohernqii@gmail.com';   //SMTP write your email
    $mail->Password   = 'htxitaroknulcoer';      //SMTP password
    $mail->SMTPSecure = 'ssl';            //Enable implicit SSL encryption
    $mail->Port       = 465;                                    
 
    //Recipients
    $mail->setFrom($email); // Sender Email and name
    $mail->addAddress('pohernqii@gmail.com');     //Add a recipient email  
    $mail->addReplyTo($email); // reply to sender email
 
    //Content
    $mail->isHTML(true);               //Set email format to HTML
    //$mail->Subject = $_POST["subject"];   // email subject headings
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="resetPassword.php?token=$token">here</a> 
    to reset your password.

    END;
      
    // Success sent message alert
    $mail->send();
    $msg="Message is sent successfully!";
  } else {
    $msg="Email is not yet registered!";
  }
  $_SESSION['forgotPassword.php']=$_POST["email"];
}
?>
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<div class="container" align="middle">
<div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Forgot Password</b></h1>
<form action="" method="post">
<table>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Email :</p></b></td>
        <td>&nbsp;<input type="email" name="email" id="email" size="25" required></td>
    </tr>
<br>    
    </table>
<br>
<input type="submit" name="send" value="Send" id=""><br>
</form>
</div>
</div>
</div>
<?php include('footer.html'); ?>