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
require '../vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

$projectId = 'parqr-8d2fd';
$database = '(default)';

// Initialize Firestore Client
$db = new FirestoreClient([
    'projectId' => $projectId,
]);

// Retrieve data from Firestore
$collection = $db->collection('operators');
$docs = $collection->documents();
?>
<?php
    require_once '../vendor/autoload.php';

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
                    <img src="../assets/PARQR-White.png" class="img-fluid" style="width: 100%; height: 100%" alt="Company Icon">
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
            <div class="col-md-10" style="overflow-y: auto; height: calc(100vh);">
                <div style="display: flex; flex-direction: justify-content: center; row; align-items: center; padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <div style="flex: 1;">
                        <h2>Parking Operators</h1>
                    </div>
                    <div style="flex: 1;"></div>
                    <a class="btn" style="background-color: #213A5C; color: white; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" href="operator-screens/register_operator.php">
                        <span>New operator</span>
                    </a>
                </div>
                <div style="padding-left: 30px; padding-right: 30px; padding-top: 20px;">
                    <form method="GET" action="operators.php" style="display: flex; flex-direction: row; align-items: center;">
                        <div style="flex-grow: 1; max-width: 80vw; border-radius: 20px; background-color: #ebedf0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <input type="text" name="search" style="padding: 10px; border: none; background-color: transparent; width: 100%;" placeholder="Search...">
                        </div>
                        <button class="btn" style="margin-left: 10px; background-color: #213A5C; color: white; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" type="submit">
                            Search
                        </button>
                    </form>
                </div>
                <div style="display: flex; flex-direction: row;  margin: 20px; padding: 10px; border-radius: 10px;">
                                <span style="flex: 1; font-weight: bold; font-size: 24px; color: #213A5C;">Name</span>
                                <div style="flex: 2;"></div>
                                <div style="flex: 2;"></div>
                                <div style="flex: 1;">
                                    <span style="font-weight: bold; font-size: 24px; color: #213A5C;">Operator ID</span>
                                </div>
                                <div style="flex: 0.8;"></div>
                                <div style="flex: 1;"> 
                                </div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1.5;">
                                    <span style="font-weight: bold; font-size: 24px; color: #213A5C;">Hire Date</span>
                                </div>
                            </div>
                <div class="row justify-content-center">
                     <div class="col-md-12">
                        <div class="col-md-12">
                            <div>
                                <?php foreach ($docs as $doc) : ?>
                                    <?php if ($doc->exists()) : ?>
                                        <?php
                                        // Filter the data based on search query
                                        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
                                        $name = strtolower($doc['name']);
                                        if (empty($searchQuery) || strpos($name, strtolower($searchQuery)) !== false) :
                                        ?>
                                            <?php
                                                $currentID = $doc->id();
                                                $profilePicture = isset($doc['profile_picture']) ? $doc['profile_picture'] : null;
                                            ?>
                                            <div>
                                                <a class="btn" style="display: flex; flex-direction: row; justify-content: center; align-items: center; margin: 20px; background-color: #ebedf0; padding: 10px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" href="operator-screens/operator_profile.php?id=<?php echo $currentID; ?>">
                                                    <div>
                                                        <img src="<?php echo $profilePicture ?? '../assets/PARQR-White.png'; ?>" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 50px; height: 50px;">
                                                    </div>
                                                    <div style="flex: 1; margin-left: 20px">
                                                        <h5><?php echo $doc->get('name'); ?></h5>
                                                    </div>
                                                    <div style="flex: 1;"></div>
                                                    <div style="flex: 1;">
                                                        <h5 style="font-size: 16;"><?php echo $doc->get('operator_id'); ?></h5>
                                                    </div>
                                                    <div style="flex: 1;"></div>
                                                    <div style="flex: 1;">
                                                        <h5 style="font-size: 16;"><?php echo $doc->get('hired_by'); ?></h5>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!-- CSS file -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

<!-- JavaScript files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->