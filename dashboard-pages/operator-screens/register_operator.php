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
    require_once '../../vendor/autoload.php';

    use Google\Cloud\Firestore\FirestoreClient;
    use Google\Cloud\Firestore\Query;

    $projectId = 'parqr-8d2fd';
    $databaseId = '(default)';
    $firestore = new FirestoreClient([
        'projectId' => $projectId,
        'databaseId' => $databaseId,
    ]);

    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id'])->snapshot()->data();
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
                    <img src="../../assets/PARQR-White.png" class="img-fluid" style="width: 100%; height: 100%" alt="Company Icon">
                </div>
                <div class="profile">
                    <a href="../profile.php" class="nav-link d-flex align-items-center">
                        <div style="flex: 1;">
                            <img src="<?php echo $adminDoc['profile_picture'] ? $adminDoc['profile_picture'] : '../../assets/PARQR-White.png'; ?>" 
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
                        <a class="nav-link d-flex align-items-center" href="../home.php">
                            <img src="../../assets/nav-icons/home.png" alt="Home Icon" class="mr-3">
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="../operators.php">
                            <img src="../../assets/nav-icons/operators.png" alt="Operators Icon" class="mr-3">
                            <span>Operators</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="../dashboard.php">
                            <img src="../../assets/nav-icons/dashboard.png" alt="Dashboard Icon" class="mr-3">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="../transactions.php">
                            <img src="../../assets/nav-icons/transactions.png" alt="Transactions Icon" class="mr-3">
                            <span>Transactions</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Main Content -->
            <div class="col-md-10 py-4 px-5" style="overflow-y: scroll; height: calc(100vh);">
                <h1 style="color: #213A5C;">Add Parking Operators</h1>
                    <div style="display: flex; flex-direction: row;">
                        <a href="../operators.php"><h4 style="color: #213A5C;">Parking Operators</h4></a>
                        <h4 style="margin-left: 5px; margin-right: 5px; color: #213A5C;">/</h4>
                        <a href=""><h4 style="color: #213A5C;"> Add Parking Operator</h4></a>

                    </div> 
                <form method="POST" action="/php/register_operator_action.php" enctype="multipart/form-data">
                    <div class="py-4">
                        <div style="display: flex; flow-direction: row; width: 100%;">
                            <div class="form-group" style="flex: 1; margin-right: 100px;">
                                <label style="font-size: 24px; color: #213A5C;">First Name</label>
                                <input type="text" name="first_name" class="form-control py-3" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label style="font-size: 24px; color: #213A5C;">Last Name</label>    
                                <input type="text" name="last_name" class="form-control py-3" required> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 24px; color: #213A5C;">Address</label>
                            <input type="text" name="address" class="form-control py-3" required>
                        </div>
                        <div style="display: flex; flow-direction: row; flex: 1;">
                            <div class="form-group" style="flex: 1; margin-right: 100px;">
                                <label style="font-size: 24px; color: #213A5C;">Contact Number</label>
                                <input type="text" name="phone_number" class="form-control py-3" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label style="font-size: 24px; color: #213A5C;">Profile Picture</label>
                                <input type="file" name="file" class="form-control py-3">
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 24px; color: #213A5C;">Email Address</label>
                            <input type="email" name="email" class = "file form-control py-3" required>
                        </div>
                        <div style="display: flex; flow-direction: row; flex: 1;">
                            <div class="form-group" style="flex: 1; margin-right: 100px;">
                                <label style="font-size: 24px; color: #213A5C;">Password</label>
                                <input type="password" name="password" class = "form-control py-3" required>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label style="font-size: 24px; color: #213A5C;">Verify Password</label>
                                <input type="password" name="verify_password" class = "form-control py-3" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end" style="margin-top: 10px;">
                        <button class="btn ml-auto" type="submit" style="font-size: 24px; font-weight: bold; background-color: #213A5C; color: white; transform: scale(1.2);">ADD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>