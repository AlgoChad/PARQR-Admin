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
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Today's Income</span>
                                    <div>
                                        <span id="today-income" style="font-size: 24px;"></span>
                                        <span id="today-income-percentage" style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Income.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Today's Users</span>
                                    <div>
                                        <span id="today-users" style="font-size: 24px;"></span>
                                        <span id="today-users-percentage" style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Users.png" alt="">
                            </div>
                        </div>
                        <div style="background-color: #fef8e6; height: 600px; padding: 20px; margin: 20px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
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
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">New User</span>
                                    <div>
                                        <span id="new-clients"style="font-size: 24px;"></span>
                                        <span id="new-clients-percentage" style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                <div style="flex: 1;"></div>
                                <img src="../assets/home-icons/Clients.png" alt="">
                            </div>
                            <div style="display: flex; flex-direction: row; background-color: #EEEEEE; padding: 20px; margin: 20px; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 10px;">Total Sales</span>
                                    <div>
                                        <span id="total-revenue" style="font-size: 24px;"></span>
                                        <span id="total-revenue-percentage" style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                <div style="flex: 1;"></div>
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
                <div>
                    <div style="display: flex; padding-left: 25px; padding-top: 30px;">
                            <h2 style="color: #213A5C;">Operator</h1>
                            <div style="flex: 1;"></div>
                            <div style="flex: 1.6;"></div>
                            <h2 style="color: #213A5C;">Payment</h1>
                            <div style="flex: 1;"></div>
                            <h2 style="color: #213A5C;">Payment</h1>
                            <div style="flex: 1;"></div>
                    </div>
                    <div style="display: flex; flex-direction: row;">
                        <div style="display: flex-direction: column; flex; align-items: center; padding-left: 30px; margin: 20px; background-color: #ebedf0; border-radius: 15px; width: 225%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="display: flex; flex-direction: row; align-items: flex-end; justify-content: flex-end; padding-top: 20px;">
                                <a class="btn" style="color: white; border-radius: 20px; padding-right: 30px; padding-left: 30px; margin-right: 15px;" href="operators.php">
                                    <img src="../assets/home-icons/Menu.png" alt="">
                                </a>
                            </div>
                            <div style="display: flex; align-items: center;">
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
                        <div style="margin: 20px; padding: 20px; background-color: #ebedf0; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="margin: 10px;">
                                <div class="form-group">
                                    <label>Initial Hours:</label>
                                    <input type="number" class="form-control" id="initial-hours">
                                </div>
                                <div class="form-group">
                                    <label>Initial Hours Payment Amount:</label>
                                    <input type="text" class="form-control" id="initial-hours-payment-amount">
                                </div>
                                <div class="form-group">
                                    <label>Incremental Payment Amount (Per Hour):</label>
                                    <input type="text" class="form-control" id="incremental-payment-amount">
                                </div>
                                <button type="button" class="btn" id="submit-button-payment" style="background-color: #213A5C; color: white;">Update</button>
                            </div>
                        </div>
                        <div style="margin: 20px; padding: 20px; background-color: #ebedf0; border-radius: 15px; width: 100%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                            <div style="margin: 10px;">
                                <div class="form-group">
                                    <label for="discount-type">Discount Type:</label>
                                    <select class="form-control" id="discount-type">
                                        <option value="senior_citizen">Senior Citizen</option>
                                        <option value="student">Student</option>
                                        <option value="pwd">PWD</option>
                                    </select>
                                </div>
                                <div style="display: flex; flex-direction: row;">
                                    <div class="form-group"style=" width: 100%;">
                                        <label for="discount-type">Discount By:</label>
                                        <select class="form-control" id="discount-by">
                                            <option value="Percentage">Percentage</option>
                                            <option value="Deduct">Fee Deduction</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-left: 5px; width: 100%;">
                                        <label>Amount:</label>
                                        <input type="text" class="form-control" id="discount-amount">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Number of Cost free Hours:</label>
                                    <input type="text" class="form-control" id="costfree-amount">
                                </div>
                                <button type="button" class="btn" id="submit-button-discount" style="background-color: #213A5C; color: white;">Update</button>
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
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                label: 'Parking Spaces',
                data: [0, 0],
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
                    callbacks: {
                        label: function (context) {
                            var dataset = context.dataset;
                            var data = dataset.data[context.dataIndex];
                            var total = dataset.data.reduce((acc, curr) => acc + curr);
                            var percentage = ((data / total) * 100).toFixed(2) + "%";
                            return dataset.label + ": " + data + " (" + percentage + ")";
                        }
                    }
                }
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
        const peakParkingRef = ref(database, `peak_parking/${new Date().toISOString().slice(0, 10)}`);

        // Attach an event listener to get the data from parking availability
        onValue(parkingRef, (snapshot) => {
            const data = snapshot.val();
            const occupiedSpaces = data.occupied_spaces;
            displaySpaces(data);

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
            $('#today-income').text(todayRevenue);
            const revenueChangePercentage = calculatePercentageChange(todayRevenue, yesterdayRevenue);
            displayPercentageChange('#today-income-percentage', revenueChangePercentage);
        }

        function displayNewClients(newClients, yesterdayClients){
            $('#new-clients').text(newClients);
            const clientsChangePercentage = calculatePercentageChange(newClients, yesterdayClients);
            displayPercentageChange('#new-clients-percentage', clientsChangePercentage);
        }

        function displayTotalRevenue(totalRevenue, yesterdayTotalRevenue){
            $('#total-revenue').text(totalRevenue);
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

        function displaySpaces(spaces){
            $('#total-space').text(spaces.max_spaces);
            $('#available').text(spaces.max_spaces - spaces.occupied_spaces);
            $('#occupied').text(spaces.occupied_spaces);
            myChart.data.datasets[0].data = [spaces.occupied_spaces, spaces.max_spaces - spaces.occupied_spaces];
            myChart.update();
        }

        const parkingSettingsRef = ref(database, 'parking_payment_settings');
            
        // Retrieve and display the data from Firebase
        onValue(parkingSettingsRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                document.getElementById('initial-hours').value = data['initial_hours'];
                document.getElementById('initial-hours-payment-amount').value = data['initial_payment'];
                document.getElementById('incremental-payment-amount').value = data['incremental_payment'];
            }
        });

        document.getElementById('submit-button-payment').addEventListener('click', () => {
            const initialHours = document.getElementById('initial-hours').value;
            const initialPayment = document.getElementById('initial-hours-payment-amount').value;
            const incrementalPayment = document.getElementById('incremental-payment-amount').value;

            onValue(parkingSettingsRef, (snapshot) => {
                const currentData = snapshot.val() || {};

                const updatedData = {
                    ...currentData,
                    'initial_hours': parseInt(initialHours),
                    'initial_payment': parseInt(initialPayment),
                    'incremental_payment': parseInt(incrementalPayment)
                };

                runTransaction(parkingSettingsRef, (currentData) => {
                    return updatedData;
                })
                    .then(() => {
                        console.log('Data updated successfully!');
                    })
                    .catch((error) => {
                        console.error('Error updating data:', error);
                    });
            });
        });

        
        // Retrieve and display the data from Firebase
        onValue(parkingSettingsRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                const discountType = data['discount_type'] || {};
                document.getElementById('discount-type').value = discountType['type'];
                document.getElementById('discount-by').value = discountType['discount_by'];
                document.getElementById('discount-amount').value = discountType['amount'] ? discountType['amount'] : 0;
                document.getElementById('costfree-amount').value = discountType['costfree_amount'] ? discountType['costfree_amount'] : 0;
            }
        });

        document.getElementById('submit-button-discount').addEventListener('click', () => {
            const discountType = document.getElementById('discount-type').value;
            const discountBy = document.getElementById('discount-by').value;
            const discountAmount = document.getElementById('discount-amount').value;
            const costfreeAmount = document.getElementById('costfree-amount').value;

            onValue(parkingSettingsRef, (snapshot) => {
                const currentData = snapshot.val() || {};

                const updatedData = {
                    ...currentData,
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
                        console.log('Data updated successfully!');
                    })
                    .catch((error) => {
                        console.error('Error updating data:', error);
                    });
            });
        });

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