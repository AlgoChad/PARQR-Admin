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
$spaces = $database->getReference('parking_availability')->getValue();

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
    $collection = $firestore->collection('operators');
    $operatorsData = [];

    // Query the documents
    $operatorQuery = $collection->orderBy('hired_by', 'DESC')->limit(1)->documents();

    // Loop through the documents and store the data in an array
    foreach ($operatorQuery as $document) {
        $operatorsData[] = $document->data();
    }

    $operatorDoc = end($operatorsData);
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
            <div class="col-md-10" style="overflow-y: scroll; height: calc(100vh);">
                <div style="display: flex; padding-left: 30px; padding-top: 30px;">
                        <h2 style="color: #213A5C;">Home</h1>
                </div>
                <div style="display: flex; flex-direction: row;">
                    <div style=" width: 100%;">
                        <div style="display: flex; flex-direction: row;">
                            <div style="display: flex; flex-direction: row; justify-content: space-between; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px;">Today's Income</span>
                                    <div>
                                        <span id="today-income" style="font-size: 26px;"></span>
                                        <span id="today-income-percentage" style="font-size: 14px;"></span>
                                    </div>
                                </div>
                                <img src="../assets/home-icons/Income.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; justify-content: space-between; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px;">Today's Users</span>
                                    <div>
                                        <span id="today-users" style="font-size: 26px;"></span>
                                        <span id="today-users-percentage" style="font-size: 14px;"></span>
                                    </div>
                                </div>
                                <img src="../assets/home-icons/Users.png" alt="">
                            </div>
                        </div>
                        <div style="background-color: #fef8e6; height: 600px; padding: 20px; margin: 20px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; padding-bottom: 10px;">
                                <span style="font-size: 26px; color: #213A5C;">Parking Spaces</span>
                            </div>
                            <div style="height: 100%; width: 100%; position: relative;">
                                <canvas style="padding: 60px;" id="Chart"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-140%, -50%); text-align: center; z-index: 999;">
                                    <span id="occupied-percentage" style="font-size: 28px; font-weight: bold;">%</span><br>
                                    <span style="font-size: 20px;">Occupied</span>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div style="width: 100%;">
                        <div style="display: flex; flex-direction: row;">
                            <div style="display: flex; flex-direction: row; justify-content: space-between; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px;">New User</span>
                                    <div>
                                        <span id="new-clients"style="font-size: 26px;"></span>
                                        <span id="new-clients-percentage" style="font-size: 14px;"></span>
                                    </div>
                                </div>
                                <img src="../assets/home-icons/Clients.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; justify-content: space-between; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px;">Total Sales</span>
                                    <div>
                                        <span id="total-revenue" style="font-size: 26px;"></span>
                                        <span id="total-revenue-percentage" style="font-size: 14px;"></span>
                                    </div>
                                </div>
                                <img src="../assets/home-icons/Sales.png" alt="">
                            </div>
                        </div>
                        <div style="background-color: #ebedf0; height: 600px; padding: 20px; margin: 20px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row;">
                                <span style="font-size: 26px; color: #213A5C;">Total Number of Parking Spaces</span>
                            <div style="flex: 1;"></div>
                            <button data-open-modal class="btn">
                                <img src="../assets/home-icons/Menu.png" alt="">
                            </button>
                            <dialog data-modal style="display: none; border: none; border-radius: 10px;">    
                                <form id="editMaxSpacesForm" method="post">
                                    <div class="form-group">
                                        <label for="max_spaces">Edit Maximum Spaces:</label>
                                        <input type="number" id="max_spaces" class="form-control" name="max_spaces" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="slot_spaces">Change the Number of Occupied parking space:</label>
                                        <input type="number" id="slot_spaces" class="form-control" name="slot_spaces" value="">
                                    </div>
                                    <div style="display: flex; flex-direction: row;">
                                        <button data-close-modal style="border: none;" class="btn">Close</button>
                                        <div style="flex: 1;"></div>
                                        <button style="border: none;" type="submit" class="btn">Save</button>
                                    </div>
                                </form>
                            </dialog>
                            </div>
                            <div style="text-align: center;">
                                <span id="total-space"style="font-size: 150px; font-weight: bold;"></span>
                            </div>
                            <div style="display: flex; flex-direction: row; justify-content: center;">
                                <div style="display: flex; flex-direction: column; background-color: #F3BB01; padding: 40px; margin: 20px; border-radius: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                    <span style="color: white;">Occupied</span>
                                    <span id="occupied" style="font-size: 100px; font-weight: bold;"></span>
                                </div>
                                <div style="display: flex; flex-direction: column; background-color: #213A5C; padding: 40px; margin: 20px; border-radius: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                    <span style="color: white;">Available</span>
                                    <span id="available" style="font-size: 100px; color: white; font-weight: bold;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row;">
                    <div style="width: 100%;">
                        <div style="display: flex-direction: column; flex; align-items: center; margin: 20px;">
                            <div style="display: flex; flex-direction: row; justify-content: space-between; padding-top: 20px;">
                                <h2 style="color: #213A5C;">Operator</h1>
                                <a class="btn" style="padding: 10px; background-color: #213A5C; color: white; border-radius: 20px;" href="operators.php">
                                     <span>View All</span>
                                </a>
                            </div>
                            <div style="display: flex; align-items: center; padding: 60px;">
                                <div style="padding: 20px;">
                                    <img src="<?php echo $profilePicture; ?>" class="img-responsive" style="background-color: #213A5C; height: 200px; width: 200px; border-radius: 15px;">
                                </div>
                                <div style="display: flex; flex-direction: column; padding: 40px; justify-content: center;">
                                    <span style="font-size: 32px; font-weight: bold;"><?php echo $operatorDoc['name'] ?></span>
                                    <span style="font-size: 24px;"><?php echo $operatorDoc['phone_number'] ?></span>
                                    <span style="padding-top: 10px; font-size: 16px;">Hired since <?php echo $operatorDoc['hired_by'] ?></span> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="width: 100%;">
                        <div style="display: flex; flex-direction: row;">
                            <div style="width: 100%; margin: 20px; padding-top: 20px">
                                <h2 style="color: #213A5C;">Payment</h1>
                                <div style="width: 100%; padding: 20px; background-color: #ebedf0; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                    <div style="margin: 10px;">
                                        <div style="display: flex; flex-direction: row;">
                                            <div class="form-group">
                                                <label style="font-size: 14px;">Initial Hours:</label>
                                                <input type="number" class="form-control" id="initial-hours">
                                            </div>
                                            <div class="form-group" style="margin-left: 5px;">
                                                <label style="font-size: 14px;">Motorcycle Deduct:</label>
                                                <input type="number" class="form-control" id="motorcycle-deduct">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 14px;">Initial Hours Payment:</label>
                                            <input type="number" class="form-control" id="initial-hours-payment-amount">
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 14px;">Incremental Payment (Per Hour):</label>
                                            <input type="number" class="form-control" id="incremental-payment-amount">
                                        </div>
                                        <button type="button" class="btn" id="submit-button-payment" style="background-color: #213A5C; color: white;">Update</button>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 100%; margin: 20px; padding-top: 20px">
                                <h2 style="color: #213A5C;">Discount</h1>
                                <div style="width: 100%; padding: 20px; background-color: #ebedf0; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                    <div style="margin: 10px;">
                                        <div class="form-group">
                                            <label style="font-size: 14px;" for="discount-type">Discount Type:</label>
                                            <select class="form-control" id="discount-type">
                                                <option value="senior_citizen">Senior Citizen</option>
                                                <option value="pwd">PWD</option>
                                                <option value="pregnant">Pregnant</option>
                                                <option value="student">Student</option>
                                            </select>
                                        </div>
                                        <div style="display: flex; flex-direction: row;">
                                            <div class="form-group"style=" width: 100%;">
                                                <label style="font-size: 14px;" for="discount-type">Discount By:</label>
                                                <select class="form-control" id="discount-by">
                                                    <option value="Percentage">Percentage</option>
                                                    <option value="Deduct">Fee Deduction</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="margin-left: 5px; width: 100%;">
                                                <label style="font-size: 14px;">Amount:</label>
                                                <input type="number" class="form-control" id="discount-amount">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 14px;">Number of Cost free Hours:</label>
                                            <input type="number" class="form-control" id="costfree-amount">
                                        </div>
                                        <button type="button" class="btn" id="submit-button-discount" style="background-color: #213A5C; color: white;">Update</button>
                                    </div>
                                </div>
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
    var ctx = document.getElementById("Chart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Car', 'Motorcycle', 'Vacant'],
            datasets: [{
                label: 'Vehicle Distribution',
                data: [0, 0, 0],
                backgroundColor: ['#F3BB01', '#90a0b7', '#213A5C'],
                borderWidth: 0,
                borderRadius: 25,
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        generateLabels: function (chart) {
                            var data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map(function (label, i) {
                                    var meta = chart.getDatasetMeta(0);
                                    var style = meta.controller.getStyle(i);
                                    var value = data.datasets[0].data[i];

                                    // Add your custom text to the label
                                    var labelText = label + ' (' + value + ')';

                                    return {
                                        text: labelText,
                                        fillStyle: style.backgroundColor,
                                        hidden: !chart.getDataVisibility(i),
                                        lineCap: style.borderCapStyle,
                                        lineDash: style.borderDash,
                                        lineDashOffset: style.borderDashOffset,
                                        lineJoin: style.borderJoinStyle,
                                        lineWidth: style.borderWidth,
                                        strokeStyle: style.borderColor,
                                        pointStyle: style.pointStyle,
                                        rotation: style.rotation
                                    };
                                });
                            }
                            return [];
                        },
                        usePointStyle: true, // Display labels as circles
                        boxWidth: 10,
                        padding: 30,
                        font: {
                            size: 20 // Increase the font size to 14 pixels
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            console.log(context);
                            var dataset = context.dataset;
                            var data = dataset.data[context.dataIndex];
                            var total = dataset.data.reduce((acc, curr) => acc + curr);
                            var percentage = ((data / total) * 100).toFixed(2) + "%";
                            return context.label + ": " + data + " (" + percentage + ")";
                        }
                    }
                },
                
            }
        }
    });
    </script>
    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
        import { getDatabase, ref, onValue, runTransaction } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";

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

        const today = new Date().toISOString().slice(0, 10);
        const yesterday = new Date(Date.now() - 864e5).toISOString().slice(0, 10);

        // Get a reference to the data you want to retrieve
        const parkingRef = ref(database, 'parking_availability');
        const activeRef = ref(database, 'activeCustomer');
        const peakParkingRef = ref(database, `peak_parking/${new Date().toISOString().slice(0, 10)}`);

        // Attach an event listener to get the data from parking availability
        onValue(parkingRef, (snapshot) => {
            const data = snapshot.val();
            const occupiedSpaces = data.occupied_spaces;
            const activeSnapshot = onValue(activeRef, (snapshot) => {
                const activeData = snapshot.val();
                displaySpaces(data, activeData);
            });
            

            // Update the peak parking ref if current occupied spaces is greater than previous peak
            runTransaction(peakParkingRef, (currentPeak) => {
                if (currentPeak === null || occupiedSpaces > currentPeak) {
                    return occupiedSpaces;
                } else {
                    return currentPeak;
                }
            });
        });

        const transactionsCountAndRevenue = ref(database, 'transaction_count_revenue');

        // Update total revenue for each day
        onValue(transactionsCountAndRevenue, (snapshot) => {
            const transactionsCountAndRevenue = ref(database, 'transaction_count_revenue');
            let totalRevenue = 0;
            

                    // Update the total revenue for each day, including today
            onValue(transactionsCountAndRevenue, (snapshot) => {
                const transactionCountAndRevenueData = snapshot.val() || {};
                const todayRevenue = transactionCountAndRevenueData[today]?.revenue || 0;
                const yesterdayRevnue = transactionCountAndRevenueData[yesterday]?.revenue || 0;

                displayTodayUsers(transactionCountAndRevenueData[today]?.count ? transactionCountAndRevenueData[today]?.count : 0, transactionCountAndRevenueData[yesterday]?.count);
                

                // Calculate the total revenue for all past days
                let totalRevenue = Object.keys(transactionCountAndRevenueData)
                    .filter((date) => date !== today) // exclude today's revenue
                    .reduce((acc, date) => {
                        const revenue = transactionCountAndRevenueData[date].revenue || 0;
                        return acc + revenue;
                }, 0);

                // Add today's revenue to the total revenue
                const newTotalRevenue = totalRevenue + todayRevenue;

                // Get the reference to the node for today's total revenue
                const todayTotalRevenueRef = ref(database, `total_revenue/${today}`);
                const yesterdayTotalRevenueRef = ref(database, `total_revenue/${yesterday}`);

                // Get the current total revenue for today
                onValue(todayTotalRevenueRef, (snapshot) => {
                const todayTotalRevenue = snapshot.val() || 0;

                    // If the new total revenue is greater than the current one, update the node
                    if (newTotalRevenue > todayTotalRevenue) {
                        runTransaction(todayTotalRevenueRef, (currentRevenue) => {
                            return newTotalRevenue;
                        });
                    }


                    displayTodayrevenue(todayRevenue ? todayRevenue : 0, yesterdayRevnue);
                    onValue(yesterdayTotalRevenueRef, (snapshot) => {
                        const yesterdayTotalRevenue = snapshot.val() || 0;
                        displayTotalRevenue(newTotalRevenue, yesterdayTotalRevenue);
                    })
                });
            });
        });

        const userRegisterCountToday = ref(database, `user_register_count/${today}`);
        const userRegisterCountYesterday = ref(database, `user_register_count/${yesterday}`);
         onValue(userRegisterCountToday, (snapshot) => {
            const userRegisterCount = snapshot.val();
            onValue(userRegisterCountYesterday, (snapshot) => {
                const userRegistercountYesterday = snapshot.val();
                displayNewClients(userRegisterCount ? userRegisterCount : 0, userRegistercountYesterday);
            })
        });

        function displayTodayrevenue(todayRevenue, yesterdayRevenue){
            var formattedRevenue = todayRevenue.toLocaleString('en-US');
            var formattedRevenueWithCurrency = '₱ ' + formattedRevenue;

            $('#today-income').text(formattedRevenueWithCurrency);
            const revenueChangePercentage = calculatePercentageChange(todayRevenue, yesterdayRevenue);
            displayPercentageChange('#today-income-percentage', revenueChangePercentage);
        }

        function displayNewClients(newClients, yesterdayClients){
            $('#new-clients').text(newClients);
            const clientsChangePercentage = calculatePercentageChange(newClients, yesterdayClients);
            displayPercentageChange('#new-clients-percentage', clientsChangePercentage);
        }

        function displayTotalRevenue(totalRevenue, yesterdayTotalRevenue){
            var formattedRevenue = totalRevenue.toLocaleString('en-US');
            var formattedRevenueWithCurrency = '₱ ' + formattedRevenue;

            $('#total-revenue').text(formattedRevenueWithCurrency);
            const totalRevenueChangePercentage = calculatePercentageChange(totalRevenue, yesterdayTotalRevenue);
            displayPercentageChange('#total-revenue-percentage', totalRevenueChangePercentage);
        }

        function displayTodayUsers(todayUsers, yesterdayUsers){
            $('#today-users').text(todayUsers);
            const usersChangePercentage = calculatePercentageChange(todayUsers, yesterdayUsers);
            displayPercentageChange('#today-users-percentage', usersChangePercentage);
        }

        function calculatePercentageChange(currentValue, previousValue) {
            if (previousValue === 0 || 
                currentValue === 0 || 
                previousValue === null || 
                currentValue === null || 
                previousValue === undefined || 
                currentValue === undefined) {
                return 0;
            }
            return ((currentValue - previousValue) / previousValue) * 100;
        }

        function displayPercentageChange(selector, percentageChange) {
            const percentageChangeText = percentageChange.toFixed(2) + "%";
            const percentageChangeColor = percentageChange >= 0 ? "green" : "red";
            $(selector).text(percentageChange >= 0 ? "+" + percentageChangeText : percentageChangeText ).css("color", percentageChangeColor);
        }

        function displaySpaces(spaces, activeSpaces){
            const percentage = 100 * (spaces.occupied_spaces / spaces.max_spaces)
            let carCount = 0;
            let motorcycleCount = 0;
            console.log(spaces)
            console.log(activeSpaces)
            for (const key in activeSpaces) {
                const space = activeSpaces[key];
                if (space.vehicle_type === "car") {
                    carCount++;
                } else if (space.vehicle_type === "motorcycle") {
                    motorcycleCount++;
                }
            }

            console.log(carCount);
            $('#occupied-percentage').text(percentage.toFixed(0) +" %")
            $('#total-space').text(spaces.max_spaces);
            $('#available').text(spaces.max_spaces - spaces.occupied_spaces);
            $('#occupied').text(spaces.occupied_spaces);
            myChart.data.datasets[0].data = [carCount, motorcycleCount, spaces.max_spaces - spaces.occupied_spaces];
            myChart.update();
        }

        const parkingSettingsRef = ref(database, 'parking_payment_settings');
        let paymentDataSubmitted = false;
        let discountDataSubmitted = false;
        let data;

        // Retrieve and display the data from Firebase
        onValue(parkingSettingsRef, (snapshot) => {
        data = snapshot.val();
            if (data) {
                document.getElementById('initial-hours').value = data['initial_hours'];
                document.getElementById('motorcycle-deduct').value = data['motorcycle_deduct'];
                document.getElementById('initial-hours-payment-amount').value = data['initial_payment'];
                document.getElementById('incremental-payment-amount').value = data['incremental_payment'];
            }
        });

        // Function to handle the change event of the discount-type dropdown picker
        function handleDiscountTypeChange() {
            const discountTypeValue = document.getElementById('discount-type').value;
            const discountType = data[discountTypeValue] || {};

            document.getElementById('discount-by').value = discountType['discount_by'];
            document.getElementById('discount-amount').value = discountType['amount'] ? discountType['amount'] : 0;
            document.getElementById('costfree-amount').value = discountType['costfree_amount'] ? discountType['costfree_amount'] : 0;
        }

        // Attach the event listener to the discount-type dropdown picker
        document.getElementById('discount-type').addEventListener('change', handleDiscountTypeChange);

        const submitPaymentButton = document.getElementById('submit-button-payment');
        submitPaymentButton.addEventListener('click', submitPaymentSettings);

        function submitPaymentSettings() {
            if (paymentDataSubmitted) {
                return; // Data has already been submitted, return early
            }

            const initialHours = document.getElementById('initial-hours').value;
            const initialPayment = document.getElementById('initial-hours-payment-amount').value;
            const incrementalPayment = document.getElementById('incremental-payment-amount').value;
            const motorcycleDeduct = document.getElementById('motorcycle-deduct').value;

            paymentDataSubmitted = true;
            const updatedData = {
                ...data,
                'initial_hours': parseInt(initialHours),
                'initial_payment': parseInt(initialPayment),
                'incremental_payment': parseInt(incrementalPayment),
                'motorcycle_deduct' : parseInt(motorcycleDeduct)
            };

            runTransaction(parkingSettingsRef, (currentData) => {
                return updatedData;
            }).then(() => {
                paymentDataSubmitted = false; // Clear the data submission flag
            }).catch((error) => {
                console.error('Error updating data:', error);
                paymentDataSubmitted = false; // Clear the data submission flag
            });
            alert("Parking Payment Settings updated successfully!");
        }

        const submitDiscountButton = document.getElementById('submit-button-discount');
        submitDiscountButton.addEventListener('click', submitDiscountSettings);

        function submitDiscountSettings() {
            if (discountDataSubmitted) {
                return; // Data has already been submitted, return early
            }

            const discountType = document.getElementById('discount-type').value;
            const discountBy = document.getElementById('discount-by').value;
            const discountAmount = document.getElementById('discount-amount').value;
            const costfreeAmount = document.getElementById('costfree-amount').value;

            discountDataSubmitted = true;

            const updatedData = {
                ...data,
                [discountType]: {
                    'discount_by': discountBy,
                    'amount': parseInt(discountAmount),
                    'costfree_amount': parseInt(costfreeAmount)
                }
            };

            runTransaction(parkingSettingsRef, (currentData) => {
                return updatedData;
            })
            .then(() => {
                discountDataSubmitted = false; // Clear the data submission flag
            })
            .catch((error) => {
                console.error('Error updating data:', error);
                discountDataSubmitted = false; // Clear the data submission flag
            });
            alert("Parking Discount Settings updated successfully!");
        }


        function updateMaxSpaces(newMaxSpaces, newSlotSpaces) {
            runTransaction(parkingRef, (currentData) => {
                if (currentData) {
                    currentData.max_spaces = newMaxSpaces;
                    currentData.occupied_spaces = newSlotSpaces;
                }
                return currentData;
            })
            .then(() => {
                console.log('Maximum spaces updated successfully');
            })
            .catch((error) => {
                console.error('Failed to update maximum spaces:', error);
            });
        }

        // Handle form submission
        $('#editMaxSpacesForm').on('submit', function(e) {
            e.preventDefault();
            const newMaxSpaces = parseInt($('#max_spaces').val());
            const newSlotSpaces = parseInt($('#slot_spaces').val());

            if (newSlotSpaces > newMaxSpaces) {
                alert('Slot spaces cannot exceed the maximum spaces');
                return;
            }

            $('#maxSpaces').text('Maximum Spaces: ' + newMaxSpaces);
            $('#slotSpaces').text('Slot Spaces: ' + newSlotSpaces);
            updateMaxSpaces(newMaxSpaces, newSlotSpaces);
        });
    </script>
    <script>
    const openButton = document.querySelector('[data-open-modal]');
    const closeButton = document.querySelector('[data-close-modal]');
    const modal = document.querySelector('[data-modal]');

    // Show dialog when the button is clicked
    openButton.addEventListener('click', () => {
        modal.style.display = 'block';
        modal.showModal();
    });

    // Close dialog when the close button is clicked
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none';
        modal.close();
    });
    </script>
        <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->