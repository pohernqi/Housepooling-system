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
    $user_type= $_SESSION['user_type'];
    $user_id = $_SESSION['user_id'];
   
    
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}

include("connect.php");
$action = $_POST['submit'] ?? '';

if ($action == "Remove") {
    
    // Process the form data
    $id = $_POST['id'];  //the id is the one that refers the name in form , same for all
    if(!empty($id))
    {
        $sql = "DELETE FROM review  where propertyId = $id AND tenantId = '$user_id'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['updated'] = "<div class='error text-center'>Review is deleted successfully.</div>";
                header('location: tenantProperty.php');

        } else {
            echo "Failed to delete review: " . mysqli_error($conn);
        }
    }
}

$star = '';
$detail = '';
$reviewId = '';
$msg="";
$id = "";
 
$id = $_POST['id'];
$sql = "SELECT * FROM review WHERE propertyId = $id AND tenantId = $user_id";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) > 0)
 {
    $row = mysqli_fetch_assoc($res);
    $star = $row['reviewstar'];
    $detail = $row['reviewdetail'];
    $reviewId = $row['reviewId'];
}

if ($action == "Submit") {
    
    // Process the form data
    $id = $_POST['id'];  //the id is the one that refers the name in form , same for all
    $newStar = $_POST["star"];
    $newDetails = $_POST["details"];
    if(empty($reviewId))
    {
        $sql = "INSERT INTO review (reviewDetail, reviewStar, reviewDate, propertyId, tenantId) VALUES ('$newDetails', '$newStar', Now(), $id, '$user_id')";
                
        if (mysqli_query($conn, $sql)) {
            $_SESSION['updated'] = "<div class='error text-center'>Review is inserted successfully.</div>";
                header('location: tenantProperty.php');

        } else {
            echo "Failed to insert review: " . mysqli_error($conn);
        }
    }
    else
    {
        $sql = "UPDATE review SET reviewDetail = '$newDetails', reviewStar= '$newStar', reviewDate = Now() where propertyId = $id AND tenantId = '$user_id'";
                
        if (mysqli_query($conn, $sql)) {
            $_SESSION['updated'] = "<div class='error text-center'>Review is updated successfully.</div>";
                header('location: tenantProperty.php');

        } else {
            echo "Failed to update the review: " . mysqli_error($conn);
        }
    }
}

?>
<style>
  .star-rating {
    direction: rtl; /* Reverse the order of the stars for easier JS logic */
    font-size: 30px;
    unicode-bidi: bidi-override;
    display: inline-block;
  }
  .star-rating input {
    display: none;
  }
  .star-rating label {
    color: #ccc; /* Color of non-selected stars */
    cursor: pointer;
  }
  .star-rating label:hover,
  .star-rating label:hover ~ label,
  .star-rating input:checked ~ label {
    color: gold; /* Color of selected stars */
  }
</style>
<script>
function confirmDelete() {
    return confirm('Are you sure you want to remove this review?');
}
</script>
<br><div class='error text-center' align="middle"><span><?php echo $msg?></span></div>
<br>
<div class="container" align="middle">
    <div class="card" style="width: 35rem; border-radius: 25px; background-color: #f0f5f5;">
        <br>
        <div class="card-body">
            <h1 class="card-title"><b>&nbsp;Review</b></h1>
            <br>
        <form  method="post">
            <table class="tbl-full" width="100%">
                    <tr>
                    <td><br>Rating: </td>
                    <td>
                    <div class="star-rating">
<?php 
$checked = "";
for ($x = 5; $x >0; $x--) {
    if ($x == $star)
        $checked = "checked";
    else $checked = "";
    echo "<input type=\"radio\" id=\"$x-stars\" name=\"rating\" value=\"$x\" $checked/><label for=\"$x-stars\" title=\"$x stars\">&#9733;</label>";
  }
  
?>
</div>    
<script>
// JavaScript to handle the click and retrieve the value
const stars = document.querySelectorAll('.star-rating input');
stars.forEach(star => {
  star.addEventListener('click', function() {
   // alert('You rated this ' + this.value + ' stars.');
    document.getElementById("star").value = this.value;
  });
});
</script>        
                     <input type="hidden" name="star" id="star" >
                    </td>
                </tr>
             <tr>
                <td>Description: </td>
                <td><input type="text" name="details" value="<?php echo $detail; ?>">
                    </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="submit" name="submit" value="Submit" class="btn-secondary">
                <input type="submit" name="submit" onclick="return confirmDelete()"  value="Remove" class="btn-secondary">
                </td>
            </tr>
    </table><br>
</div></div></div><br>
<?php include('footer.html'); ?>