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

function getCreditCardType($creditCardNumber) {
     $creditCardNumber = str_replace(array(' ', '-'), '', $creditCardNumber);

    // Array of credit card types and their corresponding first digits
    $cardTypes = array(
        'Visa' => '/^4/',                  // Visa starts with 4
        'MasterCard' => '/^5[1-5]/',    // MasterCard starts with 51 to 55
        'American Express' => '/^3[47]/' // American Express starts with 34 or 37
    );

    // Loop through the array of card types and check the first digit
    foreach ($cardTypes as $cardType => $pattern) {
        if (preg_match($pattern, $creditCardNumber)) {
            return $cardType; // Return the card type if it matches
        }
    }

    return 'Unknown'; // If no matches found, return "Unknown"
}

$msg="";
$rental=$_POST['rental'];
$agreementId = $_POST['agreementId'];
$propertyId = $_POST['propertyId'];

$startCountDown="N";
if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {
   $paymentType = getCreditCardType($_POST['cardNumber']);
   if($paymentType != "Unknown")
   {
        include("connect.php");
        $sql = "INSERT INTO transaction (paymentDate, amount, paymentType, transactionStatus, agreementId) 
        values (NOW(), $rental, '$paymentType', 'paid', $agreementId)";

        if (mysqli_query($conn, $sql)) {
            $sql = "UPDATE agreement SET paystatus='paid' where agreementId=$agreementId";
            if (mysqli_query($conn, $sql)) {
                $msg = "Payment is successfully paid. Redirecting in 5 seconds..";
                $startCountDown="Y";
            } else {
                $msg= "Error updating agreement: " . mysqli_error($conn);
            }
        } else {
            $msg= "Error inserting payment transaction: " . mysqli_error($conn);
        }
    } else $msg= "Invalid card number. ";
}
$pageToken = bin2hex(random_bytes(16)); // Generate a random token
$_SESSION['pageToken'] = $pageToken;
?>    
<br><div class='error text-center' align="middle"><span id="countdown"><?php echo $msg?></span></div>

<script>
// Set the initial countdown value
var seconds = 5;

// Function to update the countdown label
function updateCountdown() {
    document.getElementById('countdown').innerHTML = 'Payment is successfully paid.Redirecting in ' + seconds + ' seconds...';
}

// Function to decrement the countdown and handle redirection
function countdown() {
    // Update the countdown label
    updateCountdown();
    
    // Decrement the seconds
    seconds--;

    // Check if countdown has finished
    if (seconds <= 0) {
        // Redirect to another page
        window.location.href = 'tenantLease.php';
    } else {
        // Call this function again after 1 second
        setTimeout(countdown, 1000);
    }
}

// Start the countdown
<?php 
if($startCountDown == "Y")
    echo "countdown();";
    $_SESSION['propertyId'] = $propertyId;
?>
</script>
<div class="container" align="middle">
<div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
<br>
 <div class="card-body">
<h1 class="card-title" align="left"><b>&nbsp;Secure Payment</b></h1>
<form action="" method="post">
<input type="hidden" name="rental" value="<?php echo $_POST['rental']?>">
<input type="hidden" name="agreementId" value="<?php echo $_POST['agreementId']?>">
<input type="hidden" name="propertyId" value="<?php echo $_POST['propertyId']?>">
<table>
<tr><td colspan="2" align="center"><img src="img\creditcards.png" alt="Image"  width="200"></td></tr>
    <tr><tr> <td>&nbsp;</td> </tr>
    <td><b><p style="text-align: left;">&nbsp;Payment : RM <?php echo $_POST['rental']?></p></b></td>
        <td>&nbsp;</td>
    </tr> <tr> <td>&nbsp;</td> </tr>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Card Number :</p></b></td>
        <td>&nbsp;<input type="text" id="cardNumber" name="cardNumber" required></td>
    </tr>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Card Holder Name :</p></b></td>
        <td>&nbsp;<input type="cardHolder" id="cardHolder"  name="cardHolder" required></td>
    </tr>
    <tr>
    <td><b><p style="text-align: left;">&nbsp;Expiry Date :</p></b></td>
        <td>&nbsp;<select name="expiryMonth" id="expiryMonth" required>
            <option value="">Month</option>
            <option value="1">01</option>
            <option value="2">02</option>
            <option value="3">03</option>
            <option value="4">04</option>
            <option value="5">05</option>
            <option value="6">06</option>
            <option value="7">07</option>
            <option value="8">08</option>
            <option value="9">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>&nbsp;<select name="expiryYear" id="expiryYear" required>
            <option value="">Year</option>
<?php
            $currentYear = date("Y"); // Get the current year

            for ($i = 0; $i < 5; $i++) {
                $year = $currentYear + $i;
                echo "<option value=\"$year\">$year</option>";
            }
?>
        </select></td>
    </tr>
    <tr>
    <td><p style="text-align: left;">&nbsp;<b>Security Code</b> <br> ("CVC" / "CVV") :</p></td>
        <td>&nbsp;<input type="password" id="securityCode"  name="securityCode" required maxlength="3"></td>
    </tr>
<br>    
    </table>
<br>
<input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
<input type="submit" name="submit" value="Pay Now"><br><br>
</form>
</div>
</div>
</div>
<?php include('footer.html'); ?>


