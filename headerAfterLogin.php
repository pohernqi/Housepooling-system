<?php
//session_start(); // Start the session (assuming you're using sessions for login)

// Check if the user is logged in and their login type is stored in the session
if (isset($_SESSION['user_type'])) {
    $loginType = $_SESSION['user_type'];
}?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Housepool System</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="m.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body>
<div id="content">
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">Housepool System</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      
      <?php
      // Depending on the login type, adjust the header content
      if ($loginType === 'admin') {
          echo '<ul class="nav navbar-nav">';
          echo '<li class="active"><a href="adminMenu.php">Home</a></li>';
          echo '</ul>';
          echo '<ul class="nav navbar-nav navbar-right">';
          echo '<li><a href="adminBooking.php"><span class="glyphicon glyphicon-calendar"></span>Booking</a></li>';
          echo '<li><a href="adminTransaction.php"><span class="glyphicon glyphicon-usd"></span>Transaction</a></li>';
          echo '<li><a href="adminUser.php"><span class="glyphicon glyphicon-list-alt"></span>User</a></li>';
          echo '<li><a href="adminProperty.php"><span class="glyphicon glyphicon-home"></span>Properties</a></li>';
          echo '<li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Profile</a></li>';
          echo '<li><a href="logOut.php"><span class="glyphicon glyphicon-log-in"></span> LogOut</a></li>';
          echo '</ul>';
      } elseif ($loginType === 'owner') {
          echo '<ul class="nav navbar-nav">';
          echo '<li class="active"><a href="index.php">Home</a></li>';
          echo '</ul>';
          echo '<ul class="nav navbar-nav navbar-right">';
          echo '<li><a href="ownerBookings.php"><span class="glyphicon glyphicon-calendar"></span> Bookings</a></li>';
          echo '<li><a href="ownerProperty.php"><span class="glyphicon glyphicon-home"></span> Properties</a></li>';
          echo '<li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Profile</a></li>';
          echo '<li><a href="logOut.php"><span class="glyphicon glyphicon-log-in"></span> LogOut</a></li>';
          echo '</ul>';
      }elseif ($loginType === 'tenant'){
          echo '<ul class="nav navbar-nav">';
          echo '<li class="active"><a href="index.php">Home</a></li>';
          echo '</ul>';
          echo '<ul class="nav navbar-nav navbar-right">';
          echo '<li><a href="roleChange.php"><span class="glyphicon glyphicon-user"></span> Role Change Request</a></li>';
          echo '<li><a href="tenantProperty.php"><span class="glyphicon glyphicon-home"></span> Properties</a></li>';
          echo '<li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Profile</a></li>';
          echo '<li><a href="logOut.php"><span class="glyphicon glyphicon-log-in"></span> LogOut</a></li>';
          echo '</ul>';
          
      }else{
          echo '<ul class="nav navbar-nav">';
          echo '<li class="active"><a href="index.php">Home</a></li>';
          echo '</ul>';
          echo '<ul class="nav navbar-nav navbar-right">';
          echo '<li><a href="searcherBookings.php"><span class="glyphicon glyphicon-user"></span> Bookings</a></li>';
          echo '<li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Profile</a></li>';
          echo '<li><a href="logOut.php"><span class="glyphicon glyphicon-log-in"></span> LogOut</a></li>';
          echo '</ul>';
      }
      ?>
        </li>
      </ul>
      
    </div>
  </div>
</nav>

