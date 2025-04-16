<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in, display the header for logged-in users
    
    include('headerAfterLogin.php');
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}
?>

   <!-- Prop sEARCH Section Starts Here -->
   <section class="prop-search text-center">
        <div class="container">
        <div class="card">
        <br>
        <div class="card-body">
        <script>
        function validateInput() {
            var proptype = document.getElementById("proptype").options[document.getElementById("proptype").selectedIndex].value;
            var location= document.getElementById("location").options[document.getElementById("location").selectedIndex].value;
            var searchName = document.getElementById("searchName").value;
            var minRental = document.getElementById("minRental").value;
            var maxRental = document.getElementById("maxRental").value;

            if(proptype==0 && location ==0 && searchName=="" && minRental == "" && maxRental=="")
            {
                document.getElementById("errMsg").innerHTML = "Enter any fields before click search button.";
                return false;
            }
            
        }
        function resetInput() {
            document.getElementById("proptype").selectedIndex=0;
            document.getElementById("location").selectedIndex=0;
            document.getElementById("searchName").value="";
            document.getElementById("minRental").value="";
            document.getElementById("maxRental").value="";
            return false;
        }
</script>  
            <form action="index.php" method="POST">
                <input type="searchName" name="searchName" id="searchName" placeholder="Search by Property name."> &nbsp; &nbsp; &nbsp;
                <select name="location" id="location"> 
                    <option value="">Location</option>
         <?php 
            include("connect.php");
            
            //Getting Props from Database that are active and featured
            //SQL Query
            $sql = "SELECT distinct location FROM property WHERE propertystatus='approved' ";

            //Execute the Query
            $res = mysqli_query($conn, $sql);

            //Count Rows
            $count = mysqli_num_rows($res);

            //CHeck whether prop available or not
            if($count>0)
            {
                //Prop Available
                while($row=mysqli_fetch_assoc($res))
                {
                    $selectedLocation = "";
                    if (!empty($_POST['location']) and $row['location'] == $_POST['location'])  
                        $selectedLocation = "selected";
                    ?>                    
                    <option value="<?php echo $row['location']; ?>" <?php echo $selectedLocation; ?>><?php echo $row['location']; ?></option>
                    <?php 
                }
            }
                ?> 
                
                 </select>
                 &nbsp; &nbsp; &nbsp;
                 <select name="proptype" id="proptype">
                    <option value="">Property Type</option>
         <?php 
          
            
            //Getting Props from Database that are active and featured
            //SQL Query
            $sql = "SELECT distinct propertytype FROM property WHERE propertystatus='approved' ";

            //Execute the Query
            $res = mysqli_query($conn, $sql);

            //Count Rows
            $count = mysqli_num_rows($res);

            //CHeck whether prop available or not
            if($count>0)
            {
                //Prop Available
                while($row=mysqli_fetch_assoc($res))
                {
                    $selectedPropType = "";
                    if (!empty($_POST['proptype']) and $row['propertytype'] == $_POST['proptype'])  
                        $selectedPropType = "selected";
                    ?>                    
                    <option value="<?php echo $row['propertytype']; ?>" <?php echo $selectedPropType; ?>><?php echo $row['propertytype']; ?></option>
                    <?php 
                }
            }
            if (!empty($_POST['minRental'])) $minRental =  $_POST['minRental'];
            else $minRental="";
            if (!empty($_POST['maxRental'])) $maxRental =  $_POST['maxRental'];
            else $maxRental="";
                ?> 
                 </select>
                 &nbsp; &nbsp; &nbsp;
                 <span class="input-group-text">RM </span><input placeholder="Min. Monthly Rental"  id="minRental" name="minRental" value="<?php echo $minRental; ?>" size="15">
                 &nbsp; &nbsp; &nbsp;
                 <span class="input-group-text">RM </span><input placeholder="Max. Monthly Rental"  id="maxRental" name="maxRental" value="<?php echo $maxRental; ?>" size="15">
                 &nbsp; &nbsp; &nbsp;
                <input type="submit" name="submit" value="Search" onclick="return validateInput();" class="btn btn-primary">
                &nbsp; 
                <input type="reset" name="Reset" value="Reset" onclick="return resetInput();" class="btn btn-primary">
                <p id="errMsg"></p>
            </form>

        </div></div></div>
    </section>
<section class="prop-menu">
        <div class="container">
            <h2 class="text-center">Properties</h2>

            <?php 
         
            if (!empty($_POST['searchName'])) $searchName = mysqli_real_escape_string($conn, $_POST['searchName']);
            if (!empty($_POST['location'])) $location = mysqli_real_escape_string($conn, $_POST['location']);
            if (!empty($_POST['proptype'])) $proptype = mysqli_real_escape_string($conn, $_POST['proptype']);
            if (!empty($_POST['minRental'])) $minRental = mysqli_real_escape_string($conn, $_POST['minRental']);
            if (!empty($_POST['maxRental'])) $maxRental = mysqli_real_escape_string($conn, $_POST['maxRental']);
            //Getting Props from Database that are active and featured
            //SQL Query
            
            $sql = "SELECT * FROM property WHERE propertystatus='approved' ";
            if (!empty($searchName)) $sql= $sql." AND propertyname LIKE '%$searchName%' ";
            if (!empty($location)) $sql= $sql." AND location = '$location' ";
            if (!empty($proptype)) $sql= $sql." AND propertytype = '$proptype' ";
            if (!empty($minRental)) $sql= $sql." AND price >= $minRental ";
            if (!empty($maxRental)) $sql= $sql." AND price <= $maxRental ";

            //Execute the Query
            //echo $sql;
            $res2 = mysqli_query($conn, $sql);

            //Count Rows
            $count2 = mysqli_num_rows($res2);

            //CHeck whether prop available or not
            if($count2>0)
            {
                //Prop Available
                while($row=mysqli_fetch_assoc($res2))
                {
                    //Get all the values
                    $id = $row['propertyId'];
                    $title = $row['propertyname'];
                    $propertytype = $row['propertytype'];
                    $price = $row['price'];
                    $image_name = $row['pic'];
                    
                    ?>

                    <div class="prop-menu-box">
                        <div class="prop-menu-img">
                            <?php 
                                //Check whether image available or not
                                if($image_name=="")
                                {
                                    //Image not Available
                                    echo "<div class='error'>Image not available.</div>";
                                }
                                else
                                {
                                    //Image Available
                                    ?>
                                   <img src="<?php echo $image_name; ?>"  class="img-responsive img-curve">
                                    <?php
                                }
                            ?>
                            
                        </div>

                        <div class="prop-menu-desc">
                            <h4><?php echo $title; ?></h4>
                            <p class="prop-price">Property Type: <?php echo $propertytype; ?></p>
                            <p class="prop-detail">
                             Rental:   <?php echo $price; ?>
                            </p>
                            <br>

                            <a href="booking.php?propertyId=<?php echo $id; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>

                    <?php
                }
            }
            else
            {
                //Prop Not Available 
                echo "<div class='error'>Property not available.</div>";
            }
            
            ?>

            <div class="clearfix"></div>

            

        </div>
    </section>


<style>
{
    margin: 0 0;
    padding: 0 0;
    font-family: Arial, Helvetica, sans-serif;
}
    .container{
    width: 80%;
    margin: 0 auto;
    padding: 1%;
}
.card{
    width: 100rem; 
    height: 70px;
    border-radius: 25px; 
    background-color: white;
    position: relative;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    margin-top: 30px;
}

.img-responsive{
    width: 100%;
}
.img-curve{
    border-radius: 1px;
}

.text-right{
    text-align: right;
}
.text-center{
    text-align: center;
}
.text-left{
    text-align: left;
}
.text-white{
    color: white;
}

.clearfix{
    clear: both;
    float: none;
}

/* CSS for Categories */
.categories{
    padding: 4% 0;
}

.box-3{
    width: 28%;
    float: left;
    margin: 2%;
}

.prop-search
{
  background-image: url(img/bg.jpg);
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  padding: 7% 0;
  
}

/* CSS for Prop Menu */
.prop-menu{
    background-color: #ececec;
    padding: 4% 0;
    
}
.prop-menu-box{
    width: 28%;
    height: 430px;
    margin: 2%;
    padding: 2%;
    float: left;
    background-color: white;
    border-radius: 15px;
}

.prop-menu-img{
    width: 285px; /* Set your desired width in pixels */
    height: 140px; /* Set your desired height in pixels */
    float: left;
}

.prop-menu-desc{
    padding-top: 65px;
    width: 70%;
    float: left;
    margin-left: 8%;
}

.prop-price{
    font-size: 15px;
    color: #747d8c;
    
}
.prop-detail{
    font-size: 15px;
    color: #747d8c;
    width: 70%;
    word-wrap: break-word; /* Older property for wrapping long words */
    word-break: break-word; /* Ensures that words break to prevent overflow */
    overflow-wrap: break-word; 
}

</style>
<?php include('footer.html'); ?>
    