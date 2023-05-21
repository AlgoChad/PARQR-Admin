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
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Query;

$factory = (new Factory)->withServiceAccount('../firebase.json');
$database = $factory->withDatabaseUri('https://parqr-8d2fd-default-rtdb.asia-southeast1.firebasedatabase.app')->createDatabase();

$transactionsData = $database->getReference('transactions')->getValue();
if ($transactionsData) {
    $data = reset($transactionsData);
}
$spaces = $database->getReference('parking_availability')->getValue();
?>
<?php
    require_once '../vendor/autoload.php';

    $projectId = 'parqr-8d2fd';
    $databaseId = '(default)';
    $firestore = new FirestoreClient([
        'projectId' => $projectId,
        'databaseId' => $databaseId,
    ]);

    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id'])->snapshot()->data();
    $collection = $firestore->collection('operators');
    $operatorDoc = $collection->document('W1NPHituB1VZehbJoZXoqxx2GMF3')->snapshot()->data();
    $profilePicture = isset($operatorDoc['profile_picture']) ? $operatorDoc['profile_picture'] : '../assets/PARQR-White.png';
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
            <div class="col-md-10" style="background-color: #203551; overflow-y: scroll; height: calc(100vh);">
                <div style="display: flex; flex-direction: justify-content: center; row; align-items: center; padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                        <h2 style="color: white;">Dashboard</h1>
                </div>
                <div style="display: flex; flex-direction: row;">
                    <div  style="width: 100%;">
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 255px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Today's Activity</span>
                            </div>
                            <div style="display: flex; flex-direction: row;">
                                <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                    <span id="occupied" style="font-size: 52px; font-weight: bold; padding-top: 10px; padding-bottom: 10px; color: #213A5C;"></span>
                                    <span style="font-weight: bold; font-size: 16px; font-style: italic; color: #213A5C;" >Occupied</span>
                                    <span style="font-weight: bold; font-size: 16px; color: #213A5C;">Space</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                    <span id="available" style="font-size: 52px; font-weight: bold; padding-top: 10px; padding-bottom: 10px; color: #213A5C;"></span>
                                    <span style="font-weight: bold; font-size: 16px; font-style: italic; color: #213A5C;">Available</span>
                                    <span style="font-weight: bold; font-size: 16px; font-style: italic; color: #213A5C;">Space</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                                    <span id="today-income" style="font-size: 52px; font-weight: bold; padding-top: 10px; padding-bottom: 10px; color: #213A5C;"></span>
                                    <span style="font-weight: bold; font-size: 16px; font-style: italic; color: #213A5C;">Revenue</span>
                                </div>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 255px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Transactions</span>
                                <div style="flex: 1;"></div>
                                <a class="btn" style="background-color: #213A5C; color: white; border-radius: 20px;" href="transactions.php">
                                    <span>View All</span>
                                </a>
                            </div>
                            <div id="transactions-container">
                                <div style="display: flex; flex-direction: row; justify-content: center; border-bottom: 1px solid lightgray; padding-top: 20px; padding-bottom: 20px;">
                                    <div style="display: flex; flex-direction: column; text-align: center;">
                                        <span id="user-name" style="font-weight: bold;"></span>
                                        <span id="car-plate"></span>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: row; padding-top: 30px;">
                                    <span id="date"></span>
                                    <div style="flex: 1;"></div>
                                    <span id="time"></span>
                                    <div style="flex: 1;"></div>
                                    <span id="payment"></span>
                                </div>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 255px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Operators</span>
                                <div style="flex: 1;"></div>
                                <a class="btn" style="background-color: #213A5C; color: white; border-radius: 20px;" href="operators.php">
                                    <span>View All</span>
                                </a>
                            </div>
                            <div style="display: flex; flex-direction: row;">
                                <div style="padding: 20px;">
                                    <img src="<?php echo $profilePicture; ?>" class="img-responsive" style="background-color: #213A5C; height: 150px; width: 150px; border-radius: 15px;">
                                </div>
                                <div style="display: flex; flex-direction: column; padding: 40px; justify-content: center;">
                                    <span style="font-size: 20px; font-weight: bold;"><?php echo $operatorDoc['name'] ?></span>
                                    <span><?php echo $operatorDoc['phone_number'] ?></span>
                                    <span style="padding-top: 10px; font-size: 10px;">Hired since <?php echo $operatorDoc['hired_by'] ?></span> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7" style="width: 100%;">
                        <div style="background-color: white; padding: 20px; margin-top: 20px; margin-bottom: 20px; height: 530px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Statistics</span>
                                <div style="flex: 1;"></div>
                                <a class="btn" style="background-color: #213A5C; color: white; border-radius: 20px;" href="dashboard-screens/dashboard-statistics.php">
                                    <span>View All</span>
                                </a>
                            </div>
                            <div style="height: 90%;">
                                <canvas id="Chart" ></canvas>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; height: 255px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Total Customers</span>
                            </div>
                            <div style="height: 80%;">
                                <canvas id="Chart_1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var ctx1 = document.getElementById('Chart').getContext('2d');
    var chart1 = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: '#213A5C',
                borderColor: '#F3BB01',
                borderWidth: 1
            }]
        }, 
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y:{
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                },
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    </script>
    <script type="text/javascript">
    var ctx = document.getElementById("Chart_1").getContext("2d");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                data: [],
                    backgroundColor: 'transparent',
                    borderColor: '#213A5C',
                    borderWidth: 2,
                    lineTension: 0.4,
                    pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y:{
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                },
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
         }
    });
    </script>
    <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getDatabase, ref, onValue, runTransaction } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

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

    const today = new Date().toISOString().slice(0, 10);
    const transactionsCountAndRevenue = ref(database, 'transaction_count_revenue');

    const parkingRef = ref(database, 'parking_availability');

        // Attach an event listener to get the data
    onValue(parkingRef, (snapshot) => {
        const data = snapshot.val();
        displaySpaces(data);
    });

    function displaySpaces(spaces){
        $('#available').text(spaces.max_spaces - spaces.occupied_spaces);
        $('#occupied').text(spaces.occupied_spaces);
    }

    onValue(transactionsCountAndRevenue, (snapshot) => {
        const transactionCountAndRevenueData = snapshot.val() || {};
        const todayRevenue = transactionCountAndRevenueData[today]?.revenue || 0;

        displayTodayrevenue(todayRevenue ? todayRevenue : 0);
    })

    const transactionList = ref(database, 'transactions');

    onValue(transactionList, (snapshot) => {
        const transactionListData = snapshot.val() || {};
        const transactionArray = Object.values(transactionListData);
        const lastTransaction = transactionArray[transactionArray.length - 1];
        displayTransaction(lastTransaction);
    })

    function updateBarChartData(peakList) {
        const labels = Object.keys(peakList).slice(-7); // Get the dates as labels
        const data = Object.values(peakList).slice(-7); // Get the data for each day

        // Update the chart labels and data
        chart1.data.labels = labels;
        chart1.data.datasets[0].data = data;

        // Update the chart
        chart1.update();
    }

    function updateLineChartData(registerCount) {
        const labels = Object.keys(registerCount).slice(-7);
        const data = Object.values(registerCount).slice(-7);

        // Update the chart labels and data
        myChart.data.labels = labels;
        myChart.data.datasets[0].data = data;

        // Update the chart
        myChart.update();
    }

    const peakListRef = ref(database, 'peak_parking');
    
    onValue(peakListRef, (snapshot) => {
        const peakListData = snapshot.val() || {};
        updateBarChartData(peakListData);
    })

    const registerCountRef = ref(database, 'user_register_count');

    onValue(registerCountRef, (snapshot) => {
        const registerCountData = snapshot.val() || {};
        updateLineChartData(registerCountData);
    })

    function displayTodayrevenue(todayRevenue){
        $('#today-income').text(todayRevenue);
    }
    // Function to display the transaction data
    function displayTransaction(transaction) {
        var formattedDate, formattedStartTime, formattedEndTime;
        if (transaction.date !== undefined) {
            var date = new Date(transaction.date);
            formattedDate = date.toLocaleDateString('en-US', {month: '2-digit', day: '2-digit', year: 'numeric'});
            formattedStartTime = new Date(transaction.start_time).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
            formattedEndTime = new Date(transaction.start_time + transaction.duration * 1000).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
            $('#date').text(formattedDate);
            $('#time').text(formattedStartTime + ' - ' + formattedEndTime);
        } else {
            formattedDate = transaction.formattedDate;
            formattedStartTime = transaction.formattedTime;
            $('#date').text(formattedDate);
            $('#time').text(formattedStartTime);
        }
        $('#user-name').text(transaction.user_name);
        $('#car-plate').text(transaction.plate_no);
        $('#payment').text(transaction.payment);
    }
    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->