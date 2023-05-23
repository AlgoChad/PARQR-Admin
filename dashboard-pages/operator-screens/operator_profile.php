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

    $currentID =$_GET['id'];
    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id'])->snapshot()->data();
    $operatorDoc = $firestore->collection('operators')->document($currentID)->snapshot()->data();
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
                <h1 style="color: #213A5C;">Parking Operators</h1>
                <div style="display: flex; flex-direction: row;">
                    <a href="../operators.php"><h4 style="color: #213A5C;">Parking Operators</h4></a>
                    <h4 style="margin-left: 5px; margin-right: 5px;">/</h4>
                    <a href=""><h4 style="color: #213A5C;"> Profile</h4></a>
                </div>
                <div style="margin: 10px;">
                    <div style="display: flex; flex-direction: row; margin-top: 20px;">
                        <div style="flex: 1;">
                            <img src="<?php echo isset($operatorDoc['profile_picture']) ? $operatorDoc['profile_picture'] : '../../assets/PARQR-White.png'; ?>" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 150px; height: 150px;">
                        </div>    
                        <div style="display: flex; flex-direction: column; padding: 25px; width: 100%;">
                            <span style="color: #213A5C; font-size: 24px; font-weight: bold;"><?php echo $operatorDoc['name']; ?></span>
                            <span style="color: #213A5C;"><?php echo $operatorDoc['operator_id']; ?></span>
                            <span style="color: #213A5C; font-weight: bold;">Hired Since <?php echo DateTime::createFromFormat('d/m/Y', $operatorDoc['hired_by'])->format('M d, Y'); ?></span>
                        </div>
                    </div>
                    <div style="margin: 20px;">
                        <a class="btn" style="display: flex; flex-direction: row; align-items: center; width: 100%; text-align: left; background-color: #ebedf0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" href="operator_transactions.php?id=<?php echo $currentID; ?>" title="Edit your profile">
                            <div style="background-color: lightgray; border-radius: 50%; padding: 10px;">
                                <img src="../../assets/profile-icons/EditProfile.png" class="img-responsive" style="width: 30px; height: 30px;">
                            </div>
                            <div style="display: flex; flex-direction: column; margin: 10px; width: 50%">
                                <span style="font-size: 18px; font-weight: bold; color: black;">Activities</span>
                                <span style="color: gray;">View the Activities made by the Operator</span>
                            </div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1;"></div>            
                            <div style="flex: 1;"></div>                          
                            <div style="flex: 1;">
                                <img src="../../assets/profile-icons/right.png" class="img-responsive" style="width: auto; height: 25px;">    
                            </div>
                        </a>
                    </div>
                    <div >
                        <div style="padding-left: 50px;">
                            <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                                <span style="font-size: 24px; color: gray;">Name</span>
                                <span style="font-size: 24px;"><?php echo $operatorDoc['name']; ?></span>
                            </div>
                            <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                                <span style="font-size: 24px; color: gray;">Address</span>
                                <span style="font-size: 24px;"><?php echo $operatorDoc['address']; ?></span>
                            </div>
                            <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                                <span style="font-size: 24px; color: gray;">Contact Number</span>
                                <span style="font-size: 24px;"><?php echo $operatorDoc['phone_number']; ?></span>
                            </div>
                            <div style="display: flex; flex-direction: column; margin-bottom: 25px;">
                                <span style="font-size: 24px; color: gray;">Email Address</span>
                                <span style="font-size: 24px;"><?php echo $operatorDoc['email']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end" style="display: flex; flex-direction: row;">
                        <div style="margin: 30px;">
                            <a class="btn ml-auto" type="submit" style="font-size: 24px; font-weight: bold; background-color: white; color: #213A5C; transform: scale(1.2);" href="edit_operator.php?id=<?php echo $currentID; ?>">Edit</a>
                        </div>
                        <div style="margin: 30px;">
                            <a class="btn ml-auto" type="submit" style="font-size: 24px; font-weight: bold; background-color: red; color: white; transform: scale(1.2);">Archive</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>