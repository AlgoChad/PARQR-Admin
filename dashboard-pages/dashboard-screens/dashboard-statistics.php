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
            <div class="col-md-10" style="background-color: #203551; overflow-y: scroll; height: calc(100vh);">
                <a href="../dashboard.php" class="btn" style="padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <img src="../../assets/leftArrow.png" style="filter: brightness(0) invert(1);">
                </a>
                <div style="display: flex; flex-direction: row;">
                    <div  style="width: 100%;">
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 530px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Space Occupied</span>
                                <div style="flex: 1;"></div>
                            </div>
                            <div style="height: 90%;">
                                <canvas id="Chart" ></canvas>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 255px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Total Income</span>
                            </div>
                            <div style="height: 80%;">
                                <canvas id="Chart_1"></canvas>
                            </div>
                        </div>
                    </div>
                    <div  style="width: 100%;">
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 530px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Space Available</span>
                                <div style="flex: 1;"></div>
                            </div>
                            <div style="height: 90%;">
                                <canvas id="Chart_0" ></canvas>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; margin: 20px; height: 255px; border-radius: 15px;">
                            <div style="display: flex; flex-direction: row; border-bottom: 1px solid lightgray; padding-bottom: 10px;">
                                <span style="font-size: 26px; font-weight: bold; color: #213A5C;">Average Time</span>
                            </div>
                            <div style="height: 80%;">
                                
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
    var ctx = document.getElementById('Chart').getContext('2d');
    var Bar = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Highest Space Occupied',
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
                    display: true,
                    position: 'bottom' // Adjust the legend position as needed
                }
            }
        }
    });

    var ctx1 = document.getElementById('Chart_0').getContext('2d');
    var Bar_0 = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Available Spaces',
                data: [],
                backgroundColor: 'black',
                borderColor: 'gray',
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
                    display: true,
                    position: 'bottom' // Adjust the legend position as needed
                }
            }
        }
    });

    var ctx2 = document.getElementById("Chart_1").getContext("2d");
    var Line = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Income Per Day',
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
                    display: true,
                    position: 'bottom' // Adjust the legend position as needed
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

    function updateBarChartData(peakList) {
        const labels = Object.keys(peakList).slice(-7); // Get the dates as labels
        const data = Object.values(peakList).slice(-7); // Get the data for each day

        // Update the chart labels and data
        Bar.data.labels = labels;
        Bar.data.datasets[0].data = data;

        // Update the chart
        Bar.update();
    }

    function updateBarChartData1(peakList) {
        const labels = Object.keys(peakList).slice(-7); // Get the dates as labels
        const data = Object.values(peakList).slice(-7); // Get the data for each day

        const parkingRef = ref(database, 'parking_availability');
        onValue(parkingRef, (snapshot) => {
            const parkingData = snapshot.val();
            const maxSpaces = parkingData.max_spaces;

            const updatedData = data.map((peak) => maxSpaces - peak);

            Bar_0.data.labels = labels;
            Bar_0.data.datasets[0].data = updatedData;

            Bar_0.update();
        });
    }

    function updateLineChartData(revenueData) {
        const labels = Object.keys(revenueData).slice(-7);
        const data = Object.values(revenueData).map(obj => obj.revenue).slice(-7);

        // Update the chart labels and data
        Line.data.labels = labels;
        Line.data.datasets[0].data = data;

        // Update the chart
        Line.update();
    }


    const peakListRef = ref(database, 'peak_parking');
    
    onValue(peakListRef, (snapshot) => {
        const peakListData = snapshot.val() || {};
        updateBarChartData(peakListData);
        updateBarChartData1(peakListData);
    })

    const revenueRef = ref(database, 'transaction_count_revenue');

    onValue(revenueRef, (snapshot) => {
        const revenueData = snapshot.val() || {};
        updateLineChartData(revenueData);
    })

    
    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->