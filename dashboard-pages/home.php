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

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth;

// Initialize the Firebase service account and database instance
$factory = (new Factory)->withServiceAccount('../firebase.json');
$database = $factory->withDatabaseUri('https://parqr-8d2fd-default-rtdb.asia-southeast1.firebasedatabase.app')->createDatabase();

// Check if the parking availability table exists
if (!$database->getReference('parking_availability')->getSnapshot()->exists()) {
    // Create the parking availability table with default values if it doesn't exist
    $database->getReference('parking_availability')->set([
        'max_spaces' => 100,
        'occupied_spaces' => 0
    ]);
}

// Retrieve the parking availability data from the database
$data = $database->getReference('parking_availability')->getValue();

// Handle form submission to edit the maximum parking spaces
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maxSpaces = $_POST['max_spaces'] ?? null;

    if (is_numeric($maxSpaces) && $maxSpaces >= 0) {
        $database->getReference('parking_availability/max_spaces')->set(intval($maxSpaces));
        $data['max_spaces'] = intval($maxSpaces);
    }
}

// Display the parking availability data and form to edit the maximum parking spaces
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
                                style="background-color: #213A5C; width: 50px; height: 50px; border-radius: 50%;">
                        </div>
                        <div style="margin-left: 10px">
                            <span style="font-size: 14; font-weight: bold;" class="mb-0"><?php echo $adminDoc['name']; ?></span>
                            <p class="mb-0">Admin</p>
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
                <div style="display: flex; padding-left: 30px; padding-top: 30px;">
                        <h2 style="color: #213A5C;">Home</h1>
                </div>
                <div style="display: flex; flex-direction: row;">
                    <div style=" width: 100%;">
                        <div style="display: flex; flex-direction: row;">
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Today's Income</span>
                                    <span style="font-size: 24px;">+3,052</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Income.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Today's Users</span>
                                    <span style="font-size: 24px;">+3,052</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Users.png" alt="">
                            </div>
                        </div>
                        <div style="background-color: #fef8e6; height: 600px; padding: 20px; margin: 20px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row; padding-bottom: 10px;">
                                <span style="font-size: 26px; color: #213A5C;">Parking Spaces</span>
                            </div>
                            <div style="height: 90%;">
                                <canvas id="Chart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div style="width: 100%;">
                        <div style="display: flex; flex-direction: row;">
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">New Clients</span>
                                    <span style="font-size: 24px;">+3,052</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Clients.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Total Sales</span>
                                    <span style="font-size: 24px;">+3,052</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Sales.png" alt="">
                            </div>
                        </div>
                        <div style="background-color: #ebedf0; height: 600px; padding: 20px; margin: 20px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row;">
                                <span style="font-size: 26px; color: #213A5C;">Total Number of Parking Spaces</span>
                                <div style="flex: 1;"></div>
                                <a class="btn"  href="operators.php">
                                    <img src="../assets/home-icons/Menu.png" alt="">
                                </a>
                            </div>
                            <div style="text-align: center;">
                                <span style="font-size: 150px; font-weight: bold;">100</span>
                            </div>
                            <div style="display: flex; flex-direction: row; justify-content: center;">
                                <div style="display: flex; flex-direction: column; background-color: #F3BB01; padding: 40px; margin: 20px; border-radius: 15px; text-align: center;">
                                    <span style="color: white;">Available</span>
                                    <span style="font-size: 100px; font-weight: bold;">80</span>
                                </div>
                                <div style="display: flex; flex-direction: column; background-color: #213A5C; padding: 40px; margin: 20px; border-radius: 15px; text-align: center;">
                                    <span style="color: white;">Available</span>
                                    <span style="font-size: 100px; color: white; font-weight: bold;">20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; padding-left: 30px; padding-top: 30px;">
                            <h2 style="color: #213A5C;">Operator</h1>
                    </div>
                    <div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var ctx = document.getElementById("Chart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                label: 'Occupancy',
                data: [75, 25],
                backgroundColor: ['#F3BB01', '#213A5C'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toFixed(0) + '%';
                        }
                    }
                },
                outlabels: {
                    text: "%l %p",
                    color: "white",
                    stretch: 45,
                    font: {
                        resizable: true,
                        minSize: 12,
                        maxSize: 18,
                    },
                }
            }
        }
    });
    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->