<?php
// Start the session
session_start();
// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: ../login.php");
    exit;
}
?>
<?php
    require_once '../vendor/autoload.php';

    use Google\Cloud\Firestore\FirestoreClient;
    use Google\Cloud\Firestore\Query;

    $projectId = 'parqr-8d2fd';
    $databaseId = '(default)';
    $firestore = new FirestoreClient([
        'projectId' => $projectId,
        'databaseId' => $databaseId,
    ]);

    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id'])->snapshot()->data();
    $email = $adminDoc['email'];
    $_SESSION['admin_email'] = $email;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-pn+gKQ7UwMF6UyV6+uW6NfK7hZ00taJskpxBwU6z/XMZ6o2E6f+1CvGo0ZFXCr9TKVb8myoA5/o0wPIdJKKjzA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .sidebar {
            height: 100vh;
            background-color: #213A5C;
            color: #fff;
        }
        .sidebar .profile {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }
        .sidebar .profile h4 {
            margin-top: 10px;
            font-weight: bold;
        }
        .sidebar .profile p {
            margin-top: 5px;
            font-style: italic;
        }
        .sidebar .nav-link {
            color: #fff;
            font-size: 18px;
            margin-top: 10px;
        }
        .sidebar .company-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .sidebar .company-icon img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="company-icon">
                    <img src="../assets/PARQR-White.png" class="img-fluid" style="width: 100%; height: 100%;" alt="Company Icon">
                </div>
                <div class="profile">
                    <a href="profile.php" class="nav-link d-flex align-items-center">
                    <div style="flex: 1;">
                            <img src="<?php echo $adminDoc['profile_picture'] ? $adminDoc['profile_picture'] : '../assets/PARQR-White.png'; ?>" 
                                class="img-responsive rounded-circle" 
                                style="background-color: #213A5C; width: 100px; height: 100px; border-radius: 50%;">
                        </div>
                        <div style="margin-left: 10px">
                            <span style="font-size: 18px; font-weight: bold;" class="mb-0"><?php echo $adminDoc['name']; ?></span>
                            <p class="mb-0" style="font-size: 16px;">Admin</p>
                        </div>
                    </a>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="home.php">
                            <img src="../assets/nav-icons/home.png" alt="Home Icon" class="mr-3">
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="operators.php">
                            <img src="../assets/nav-icons/operators.png" alt="Operators Icon" class="mr-3">
                            <span>Operators</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="dashboard.php">
                            <img src="../assets/nav-icons/dashboard.png" alt="Dashboard Icon" class="mr-3">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="transactions.php">
                            <img src="../assets/nav-icons/transactions.png" alt="Transactions Icon" class="mr-3">
                            <span>Transactions</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Main Content -->
            <div class="col-md-10" style="overflow-y: scroll; height: calc(100vh);">
                <h2 style="color: #213A5C; padding-left: 30px; padding-top: 30px; font-weight: bold; font-size: 40px; margin-bottom: 30px;">Profile</h2>
                <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; margin-bottom: 40px;">
                    <div style="flex: 1;"></div>    
                    <div class="col-md-3" style="flex: 1;">
                        <img src="<?php echo$adminDoc['profile_picture'] ? $adminDoc['profile_picture'] : '../assets/PARQR-White.png'; ?>" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 200px; height: 200px;">
                    </div>
                    <div style="flex: 1;"></div>
                    <div class="col-md-3" style="flex: 1;">
                        <h2 style="font-weight: bold; font-size: 40px;"><?php echo $adminDoc['name'] ? $adminDoc['name'] : 'Unknown Name'; ?></h2>
                        <h5>ADMINISTRATOR</h3>
                    </div>
                    <div style="flex: 1;"></div>
                </div>
                <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; margin-top: 20px; border-top: 1px solid gray;">
                    <div class="col-md-6" style="border-right: 1px solid gray;">                
                        <a class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left; " href="profile-screens/edit_profile.php" title="Edit your profile">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../assets/profile-icons/EditProfile.png" class="img-responsive" style="width: 30px; height: 30px;">
                            </div>
                            <div style="display: flex; flex-direction: column; margin-left: 30px; width: 50%">
                                <span style="font-size: 18px; font-weight: bold; color: black;">Edit Profile</span>
                                <span style="color: gray;">Make changes to your profile</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>                          
                            <div style="flex: 1;">
                                <img src="../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                        <a id="changePasswordLink" class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left;" title="Change your Password">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../assets/profile-icons/Security.png" class="img-responsive" style="width: 30px; height: 30px;">
                            </div>
                            <div style="display: flex; flex-direction: column; margin-left: 30px; width: 50%">
                                <span style="font-size: 18px; font-weight: bold; color: black;">Security</span>
                                <span style="color: gray;">Change Password</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>                                                                  
                            <div style="flex: 1;">
                                <img src="../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                        <a class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left;" href="profile-screens/register_admin.php" title="Add New Admin">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../assets/profile-icons/AdminAdd.png" class="img-responsive" style="width: 30px; height: 30px;">    
                            </div>
                            <div style="display: flex; flex-direction: column; margin-left: 30px; width: 50%">
                                <span style="font-size: 18px; font-weight: bold; color: black;">Add Admin</span>
                                <span style="color: gray;">Add a new Admin Account</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;">
                                <img src="../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                        <a class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left;" href="profile-screens/feedback_page.php" title="Log out your account">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../assets/profile-icons/Help.png" class="img-responsive" style="width: 30px; height: 30px;">   
                            </div> 
                            <div style="display: flex; flex-direction: column; margin-left: 30px; width: 50%">    
                                <span style="font-size: 18px; font-weight: bold; color: black;">Feedback</span>
                                <span style="color: gray;">User's Feedback</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>                                    
                            <div style="flex: 1;">
                                <img src="../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                        <a class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left;" href="../php/logout.php" title="Log out your account">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../assets/profile-icons/Logout.png" class="img-responsive" style="width: 30px; height: 30px;">   
                            </div> 
                            <div style="display: flex; flex-direction: column; margin-left: 30px; width: 50%">    
                                <span style="font-size: 18px; font-weight: bold; color: black;">Log out</span>
                                <span style="color: gray;">Log out your account</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>                                    
                            <div style="flex: 1;">
                                <img src="../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6" style="padding-left: 50px; padding-top: 20px;">
                        <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                            <span style="font-size: 24px; font-weight: bold;">Name</span>
                            <span><?php echo $adminDoc['name']; ?></span>
                        </div>
                        <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                            <span style="font-size: 24px; font-weight: bold;">Address</span>
                            <span><?php echo $adminDoc['address']; ?></span>
                        </div>
                        <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                            <span style="font-size: 24px; font-weight: bold;">Contact Number</span>
                            <span><?php echo $adminDoc['phone_number']; ?></span>
                        </div>
                        <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                            <span style="font-size: 24px; font-weight: bold;">Email</span>
                            <span><?php echo $adminDoc['email']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
        import { getDatabase } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";
        import { getAuth, sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-auth.js";
        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyBLqTYCYZm0XTxWG0uabY0oolAwb-8XK08",
            authDomain: "parqr-8d2fd.firebaseapp.com",
            databaseURL: "https://parqr-8d2fd-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "parqr-8d2fd",
            storageBucket: "parqr-8d2fd.appspot.com",
            messagingSenderId: "267085407338",
            appId: "1:267085407338:web:4c70ca4740d6a1d8919613"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);
        const auth = getAuth(app);

        document.getElementById('changePasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = "<?php echo $_SESSION['admin_email']; ?>";
            sendPasswordResetEmail(auth, email)
                .then(() => {
                    console.log("Password reset email sent successfully");
                    alert("Password reset email sent successfully");
                    // Optionally, you can display a success message or perform other actions
                })
                .catch((error) => {
                    console.error("Failed to send password reset email:", error);
                    alert("Failed to send password reset email:", error);
                    // Optionally, you can display an error message or perform other error handling
                });
        });
    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->