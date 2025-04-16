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
    include('headerAfterLogin.php');
} else {
    // User is not logged in, display the header for non-logged-in users
    include('header.html');
}

    $msg="";
    $description="";  
    $userid=$_SESSION['user_id'];  

    
    if (isset($_POST['pageToken']) && $_POST['pageToken'] === $_SESSION['pageToken']) {
        $description=$_POST['description'];
        if($description == "State the reason here.")
        {
            $msg= "Please enter a reason.";
        }
        else
        {
            include("connect.php");
            $sql = "SELECT requestid FROM request_role WHERE userid=$userid AND request_status='pending' ";
            $result = mysqli_query($conn, $sql);
        
            if ($result->num_rows > 0) {
                $msg= "New request is not allowed. A request was submitted previously and currently pending review." ;
            }
            else
            {
                $sql = "INSERT INTO request_role (userid, request_status,request_date, description) values ($userid, 'pending', SYSDATE(), '$description')";
            
                if (mysqli_query($conn, $sql)) {
                    $msg= "Your request is submitted successfully.";
                } else {
                    $msg= "Unable to save the request: " . mysqli_error($conn);
                }
            }
        }
    }
    $pageToken = bin2hex(random_bytes(16)); // Generate a random token
    $_SESSION['pageToken'] = $pageToken;
?>
<script>
function confirmSubmit() {
    if(document.getElementById("description").value == "State the reason here.")
    {
        alert("Please enter a reason.");
        return false;
    }
    return confirm("Are you sure you want to submit the request to convert to an owner? Your tenant profile will be deleted.");
}
</script>
    <div class="container">
    <br><div style="margin-left: 200px;" class='error text-left' align="left"><?php echo $msg?></span></div>
        <h3>Become an Owner</h3>
        <form method="post" action="" onsubmit="return confirmSubmit()" >
         <input type="hidden" name="pageToken" value="<?php echo $pageToken; ?>">
              <table class="table table-striped">
                <thead>
                    <tr><th></th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td colspan="4">Submit a request to convert your tenant role to an owner role.</td>
                        </tr>
                        <tr><td colspan="4">
                        <textarea id="description" name="description" rows="4" cols="50" maxlength="200" required>State the reason here.</textarea>
                        </td></tr>
                            <tr>
                                <td><button type="submit" name="submit">Submit</button>
                            </tr>

                </tbody>
                
            </table>
        </form>
    </div>
<style>
        body {
            background-color: white;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: black;
            Color: white;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-spacing: 0 15px;
            padding: 6px;
        }

        img {
            max-width: 300px;
            height: auto;
        }

        .button {
            margin-bottom: 10px;
        }


    </style>
<?php include('footer.html'); ?>
    