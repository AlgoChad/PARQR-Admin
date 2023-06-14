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
                <div style="display: flex; flex-direction: row; margin-top: 20px; margin-bottom: 20px;">
                    <div class="col-md-7">
                        <div>
                            <div style="display: flex; flex-direction: row;">
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Revenue</h5>
                                    <h3 id="total-revenue"></h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-left: 20px; margin-right: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Income</h5>
                                    <h3 id="total-income"></h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Amount of Discounts</h5>
                                    <h3 id="total-discount"></h3>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: row;">
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Top-up Amount</h5>
                                    <h3 id="total-topup"></h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-left: 20px; margin-right: 20px; margin-bottom: 20px;  height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Car Parking Income</h5>
                                    <h3 id="carparking-income"></h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px;  height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Motorcycle Parking Income</h5>
                                    <h3 id="motorcycle-income"></h3>
                                </div>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; height: 785px; width: 100%; border-radius: 15px;">
                            <div style="height: 90%;">
                                <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                    <h4>Total Summary of Parking Spaces</h4>
                                    <div id="barChartSettings" style="background-color: #f0f0f0; padding: 5px; border-radius: 15px;">
                                        <button class="btn" id="weekBtn">Week</button>
                                        <button class="btn" id="monthBtn">Month</button>
                                        <button class="btn" id="yearBtn">Year</button>
                                        <button class="btn" id="customRangeBtn">Custom</button>
                                    </div>
                                    <dialog id="customRangeDialog" style="display: none; border: none; background-color: #f0f0f0; border-radius: 15px; ">
                                        <input class="btn" type="date" id="startDateBar">
                                        <input class="btn" type="date" id="endDateBar">
                                        <button class="btn" id="applyBtn">Apply</button>
                                        <button class="btn" id="cancelBtn">Cancel</button>
                                    </dialog>
                                </div>
                                <canvas id="Chart_0"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div style="background-color: white; padding: 20px; margin-bottom: 20px; margin-right: 20px; height: 47.9%; width: 100%; border-radius: 15px;">
                            <span style="color: #213A5C; font-weight: bold; font-size: 24px; padding-bottom: 10px;">Longest Time Running</span>
                            <div style="display: flex; flex-direction: row; justify-content: space-between; padding: 10px;">
                                <span style="flex: 2;">Name</span>
                                <span style="flex: 1.5;">Start Time</span>
                                <span style="flex: 1.5;">Duration</span>
                                <span style="flex: 0.8;">Fee</span>
                            </div>
                            <div id="list" style="overflow-y: scroll; height: calc(30vh); padding: 10px;"></div>
                            <a class="btn" style="width: 100%; align-items: center; color: white; background-color: #213A5C; padding: 10px; margin-top: 10px;" href="dashboard-statistics-table.php">Full View</a>
                        </div>
                        <div style="background-color: white; padding: 20px; margin-right: 20px; height: 50%; width: 100%; border-radius: 15px;">
                            <h2>Parked Users Discount Percentage</h2>
                            <span style="padding: 20px; font-size: 20px; font-weight: bold;"id="total-users"></span>
                            <div style="height: 90%;">
                                <canvas id="Chart_2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="background-color: white; padding: 20px; margin-bottom: 30px; margin-left: 15px; margin-right: 15px; height: 500px; border-radius: 15px;" >
                    <div style="height: 90%;">
                        <div style="display: flex; flex-direction: row; justify-content: space-between;">
                            <h2>Total Sales</h2>
                            <div id="lineChartSettings" style="background-color: #f0f0f0; padding: 5px; border-radius: 15px;">
                                <button class="btn" id="weekBtnLineChart">7 Days</button>
                                <button class="btn" id="monthBtnLineChart">30 Days</button>
                                <button class="btn" id="yearBtnLineChart">12 Months</button>
                                <button class="btn" id="customRangeBtnLineChart">Custom</button>
                            </div>
                            <dialog id="customRangeDialogLineChart" style="display: none; border: none; background-color: #f0f0f0; border-radius: 15px;">
                                <input class="btn" type="date" id="startDate">
                                <input class="btn" type="date" id="endDate">
                                <button class="btn" id="applyBtnLineChart">Apply</button>
                                <button class="btn" id="cancelBtnLineChart">Cancel</button>
                            </dialog>
                        </div>
                        <canvas id="Chart_1"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

    var ctx1 = document.getElementById("Chart_0").getContext("2d");
    var Bar = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            datasets: [{
                label: 'Cars',
                data: [100, 150, 90, 80, 120, 70, 120], // Placeholder data values for the first color (7 days)
                backgroundColor: '#f3bb01', // Color for the first dataset
                borderColor: '#f3bb01',
                borderWidth: 1,
                borderRadius: 15
            }, {
                label: 'Motorcycle',
                data: [80, 120, 70, 60, 100, 50, 100], // Placeholder data values for the second color (7 days)
                backgroundColor: '#92a2b8', // Color for the second dataset
                borderColor: '#92a2b8',
                borderWidth: 1,
                borderRadius: 15
            }, {
                label: 'Vacant',
                data: [120, 70, 60, 100, 50, 100, 150], // Placeholder data values for the third color (7 days)
                backgroundColor: '#3b414b', // Color for the third dataset
                borderColor: '#3b414b',
                borderWidth: 1,
                borderRadius: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
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
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, // Display labels as circles
                        boxWidth: 10, // Size of the circle
                        padding: 20
                    }
                }
            }
        }
    });

    
    var ctx2 = document.getElementById("Chart_1").getContext("2d");
    var Line = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            datasets: [{
                label: 'Discount',
                data: [100, 150, 90, 80, 120, 70, 120], // Placeholder data values for the first color (7 days)
                backgroundColor: 'transparent',
                borderColor: '#f3bb01', // Color for the first dataset
                borderWidth: 2,
                lineTension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: 'black',
                borderDash: [5, 5] // Make the line broken with dashes
            }, {
                label: 'Car Parking',
                data: [80, 120, 70, 60, 100, 50, 100], // Placeholder data values for the second color (7 days)
                backgroundColor: 'transparent',
                borderColor: '#92a2b8', // Color for the second dataset
                borderWidth: 2,
                lineTension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: 'black',
                borderDash: [5, 5] // Make the line broken with dashes
            }, {
                label: 'Motorcycle Parking',
                data: [120, 70, 60, 100, 50, 100, 150], // Placeholder data values for the third color (7 days)
                backgroundColor: 'transparent',
                borderColor: '#3b414b', // Color for the third dataset
                borderWidth: 2,
                lineTension: 0.4,
                pointRadius: 2,
                pointBackgroundColor: 'black',
                borderDash: [5, 5] // Make the line broken with dashes
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
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
                    position: 'top', // Adjust the legend position as needed
                    labels: {
                        usePointStyle: true, // Display labels as circles
                        
                    }
                }
            }
        }
    });

    var ctx3 = document.getElementById('Chart_2').getContext('2d');
    var HalfDonut = new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: ['Senior', 'PWD', 'Student', 'Pregnant', 'None'],
            datasets: [{
                data: [0, 0, 0, 0, 0], // Placeholder data values
                backgroundColor: ['#2475e2', '#a6cafa', '#5c7699', '#104388', '#90a0b7'], // Colors for each dataset
                hoverBackgroundColor: ['#2475e2', '#a6cafa', '#5c7699', '#104388', '#90a0b7'], // Hover colors for each dataset
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '50%',
            circumference: 180,
            rotation: 270,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true, // Display labels as circles
                        boxWidth: 10,
                        padding: 30,
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
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed) {
                                var percentage = (context.parsed / context.dataset.data.reduce((a, b) => a + b) * 100).toFixed(2);
                                label += percentage + '%';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });



    </script>
    <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
    import { getDatabase, ref, onValue, runTransaction, off } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-database.js";
    import { getFirestore, collection, getDocs  } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-firestore.js";


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
    const db = getFirestore(app);

    const today = new Date().toISOString().slice(0, 10);
    const yesterday = new Date(Date.now() - 864e5).toISOString().slice(0, 10);

    const usersRef = ref(database, 'users');

    onValue(usersRef, (snapshot) => {
        const usersData = snapshot.val() || {};
        showUsersData(usersData)
    })

    function showUsersData(usersData) {
        var divElement = document.querySelector('#list');

        // Create an array of user objects
        var usersArray = Object.entries(usersData).map(function([userId, userData]) {
            var userName;
            var longestHistory;

            if (userData.parking_time_history) {
                Object.values(userData.parking_time_history).forEach(function(data) {
                    if (!longestHistory || data.duration > longestHistory.duration) {
                        longestHistory = data;
                        userName = data.user_name || ""; // Default value if user_name is undefined or null
                    }
                });
            }

            return {
                userId: userId,
                userName: userName,
                longestHistory: longestHistory
            };
        });

        console.log(usersArray);

        // Sort the users based on the longest durations
        usersArray.sort(function(a, b) {
            var aDuration = a.longestHistory ? a.longestHistory.duration : 0;
            var bDuration = b.longestHistory ? b.longestHistory.duration : 0;
            return bDuration - aDuration;
        });

        var html = '<table>';
       
        html += '<tbody>';

        // Generate the HTML content for each user and their longest history
        usersArray.forEach(function(user) {
            if (user && user.userName && user.longestHistory) {
                var durationInSeconds = (new Date(user.longestHistory.start_time + user.longestHistory.duration * 1000) - new Date(user.longestHistory.start_time)) / 1000;
                var durationInMinutes = Math.floor(durationInSeconds / 60);
                var durationInHours = Math.floor(durationInMinutes / 60);
                let durationText;

                if (durationInHours < 1) {
                    const remainingSeconds = Math.round(durationInSeconds % 60);
                    durationText = `${durationInMinutes} mins ${remainingSeconds} secs`;
                } else {
                    durationText = `${durationInHours} hours ${durationInMinutes % 60} min`;
                }

                html += '<tr>';
                html += '<td style="padding-right: 20px; font-weight: bold; color: #213A5C;">' + user.userName + '</td>';
                html += '<td style="padding-right: 20px; padding-left: 20px; color: gray;">' + new Date(user.longestHistory.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + '</td>';
                html += '<td style="padding-right: 20px; padding-left: 20px; color: gray;">' + durationText + '</td>';
                html += '<td style="padding-left: 20px; color: #F3BB01;">' + "₱" + user.longestHistory.payment + '</td>';
                html += '</tr>';
            }
        });

        html += '</tbody>';
        html += '</table>';


        // Update the innerHTML of the divElement
        divElement.innerHTML = html;
    }

    let totalIncomeVal;
    let totalRevenueVal;

    function calculateTotalPaymentAmount(callback) {
        let totalPaymentAmount = 0;

        const transactionsRef = ref(database, "transactions");
        const onValueCallback = (snapshot) => {
            const transactions = snapshot.val() || {};

            for (const key in transactions) {
                const transaction = transactions[key];
                if (!transaction.top_up) {
                    const paymentAmount = transaction.payment || 0;
                    totalPaymentAmount += paymentAmount;
                }
            }

            callback(totalPaymentAmount);
        };

        // Remove the previous event listener (if any)
        off(transactionsRef, "value", onValueCallback);

        // Add the new event listener
        onValue(transactionsRef, onValueCallback, (error) => {
            console.log('Error retrieving transactions: ', error);
        });
    }

    function displayTotalIncome(totalIncome) {
        var formattedIncome = totalIncome.toLocaleString('en-US');
        var formattedIncomeWithCurrency = '₱ ' + formattedIncome;

        totalIncomeVal = totalIncome;
        $('#total-income').text(formattedIncomeWithCurrency);
        displayTotalDiscount();
    }

    calculateTotalPaymentAmount(displayTotalIncome);


    function calculateTotalRevenue(callback) {
        const paymentSettingsRef = ref(database, 'parking_payment_settings');
        const transactionsRef = ref(database, 'transactions');
        let transactionsListener;

        onValue(paymentSettingsRef, (paymentSettingsSnapshot) => {
            const paymentSettings = paymentSettingsSnapshot.val();
            const initialHours = paymentSettings.initial_hours || 0;
            const initialPayment = paymentSettings.initial_payment || 0;
            const incrementalPayment = paymentSettings.incremental_payment || 0;

            let totalPaymentAmount = 0;

            if (transactionsListener) {
                // Detach the previous listener to avoid duplicate calculations
                off(transactionsRef, 'value', transactionsListener);
            }

            transactionsListener = onValue(transactionsRef, (transactionsSnapshot) => {
            const transactions = transactionsSnapshot.val();

            for (const transactionId in transactions) {
                const transaction = transactions[transactionId];
                const topUp = transaction.top_up || false;
                const duration = transaction.duration || 0;

                if (!topUp) {
                    let paymentAmount = parseInt(initialPayment);

                    const durationInHours = Math.floor(duration / (60 * 60));
                    const additionalHours = durationInHours - parseInt(initialHours);

                    if (additionalHours > 0) {
                        paymentAmount += additionalHours * parseInt(incrementalPayment);
                    }

                    totalPaymentAmount += paymentAmount;
                }
            }

            // Invoke the callback function with the total revenue
            callback(totalPaymentAmount);
            }, (error) => {
                console.log('Error fetching transactions: ', error);
            });
        });
    }

    function displayTotalRevenue(totalRevenue) {
    // Format the total revenue with currency symbol and commas
    var formattedTotalRevenue = '₱ ' + totalRevenue.toLocaleString('en-US');
    totalRevenueVal = totalRevenue;
    // Update the text content of the element with the formatted total revenue
    $('#total-revenue').text(formattedTotalRevenue);
    displayTotalDiscount();
    }

    calculateTotalRevenue(displayTotalRevenue);


    function displayTotalDiscount() {
        if (typeof totalRevenueVal !== 'undefined' && typeof totalIncomeVal !== 'undefined') {
            var formattedTotalDiscount = '₱ ' + (totalRevenueVal - totalIncomeVal).toLocaleString('en-US');
            $('#total-discount').text(formattedTotalDiscount);
        }
    }

    displayTotalDiscount(); //

    async function calculateTotalTopUpAmount(callback) {
        let totalAmount = 0;

        try {
            const querySnapshot = await getDocs(collection(db, "users"));

            querySnapshot.forEach((doc) => {
                const topUpHistory = doc.data().top_up_history || [];

                topUpHistory.forEach((entry) => {
                    const amount = entry.amount || 0;
                    totalAmount += amount;
                });
            });

            // Invoke the callback function with the updated total amount
            callback(totalAmount);
        } catch (error) {
            console.log('Error retrieving top-up history: ', error);
        }
    }

    function displayTotalTopUpAmount(totalAmount) {
        var formattedTotalAmount = '₱ ' + totalAmount.toLocaleString('en-US');
        $('#total-topup').text(formattedTotalAmount);
    }

    calculateTotalTopUpAmount(displayTotalTopUpAmount);

    function calculateTotalCarPaymentAmount(callback) {
        let totalPaymentAmount = 0;

        const transactionsRef = ref(database, "transactions");
        const onValueCallback = (snapshot) => {
            const transactions = snapshot.val() || {};

            for (const key in transactions) {
            const transaction = transactions[key];
            if (!transaction.top_up && (!transaction.vehicle_type || transaction.vehicle_type === "car")) {
                const paymentAmount = transaction.payment || 0;
                totalPaymentAmount += paymentAmount;
            }
            }

            callback(totalPaymentAmount);
        };

        // Remove the previous event listener (if any)
        off(transactionsRef, "value", onValueCallback);

        // Add the new event listener
        onValue(transactionsRef, onValueCallback, (error) => {
            console.log('Error retrieving transactions: ', error);
        });
    }

    function displayTotalCarPaymentAmount(totalPaymentAmount) {
        var formattedTotalAmount = '₱ ' + totalPaymentAmount.toLocaleString('en-US');
        $('#carparking-income').text(formattedTotalAmount);
    }

    calculateTotalCarPaymentAmount(displayTotalCarPaymentAmount);


    function calculateTotalMotorPaymentAmount(callback) {
        let totalPaymentAmount = 0;

        const transactionsRef = ref(database, "transactions");
        const onValueCallback = (snapshot) => {
            const transactions = snapshot.val() || {};

            for (const key in transactions) {
            const transaction = transactions[key];
            if (!transaction.top_up && transaction.vehicle_type === "motorcycle") {
                const paymentAmount = transaction.payment || 0;
                totalPaymentAmount += paymentAmount;
            }
            }

            callback(totalPaymentAmount);
        };

        // Remove the previous event listener (if any)
        off(transactionsRef, "value", onValueCallback);

        // Add the new event listener
        onValue(transactionsRef, onValueCallback, (error) => {
            console.log('Error retrieving transactions: ', error);
        });
    }

    function displayTotalMotorPaymentAmount(totalPaymentAmount) {
        var formattedTotalAmount = '₱ ' + totalPaymentAmount.toLocaleString('en-US');
        $('#motorcycle-income').text(formattedTotalAmount);
    }

    calculateTotalMotorPaymentAmount(displayTotalMotorPaymentAmount);


    // Create an empty array to store the discount counts
    const discountCounts = [0, 0, 0, 0, 0]; // Order: ['Senior', 'PWD', 'Student', 'Pregnant', 'None']

    function calculateDiscountCounts(callback) {
        const transactionsRef = ref(database, "transactions");
        onValue(transactionsRef, (snapshot) => {
            const transactions = snapshot.val() || {};

            for (const key in transactions) {
                const transaction = transactions[key];
                const discount = transaction.discount || 'none';
                const index = ['senior_citizen', 'pwd', 'student', 'pregnant', 'none'].indexOf(discount.toLowerCase());

                if (index !== -1) {
                    discountCounts[index] += 1;
                }
            }

            callback(discountCounts);
        }, (error) => {
            console.log('Error retrieving transactions: ', error);
        });
    }

    calculateDiscountCounts((discountCounts) => {
        let counter = 0;
        console.log(discountCounts);

        for (let i = 0; i < discountCounts.length; i++) {
            counter += discountCounts[i];
        }
        console.log(counter);
        $('#total-users').text("Total Parked Users: " + counter);

        HalfDonut.data.datasets[0].data = discountCounts;
        HalfDonut.update();
    });

    function calculateVehicleCounts(callback) {
        const transactionsRef = ref(database, "transactions");
        const maxSpacesRef = ref(database, "parking_availability/max_spaces");

        onValue(maxSpacesRef, (maxSpacesSnapshot) => {
            const maxSpaces = maxSpacesSnapshot.val() || 0;

            onValue(transactionsRef, (snapshot) => {
                const transactions = snapshot.val() || {};

                const vehicleCounts = {};

                const today = new Date();
                const oneDay = 24 * 60 * 60 * 1000;

                for (const key in transactions) {
                const transaction = transactions[key];

                // Check if the transaction has a 'top_up' field and its value is false
                if ('top_up' in transaction && !transaction.top_up) {
                    const vehicleType = transaction.vehicle_type || 'car';
                    const date = new Date(transaction.date);
                    const dayIndex = Math.floor((today - date) / oneDay);

                    const formattedDate = formatDate(date); // Format the date as needed (e.g., 06/13/2024)

                    if (!(formattedDate in vehicleCounts)) {
                        vehicleCounts[formattedDate] = {
                            vacant: 0,
                            car: 0,
                            motorcycle: 0,
                        };
                    }

                    if (vehicleType === 'car') {
                        vehicleCounts[formattedDate].car += 1;
                    } else if (vehicleType === 'motorcycle') {
                        vehicleCounts[formattedDate].motorcycle += 1;
                    }
                }
            }

            // Calculate vacant spaces for each day
            for (const date in vehicleCounts) {
                const occupiedSpaces = vehicleCounts[date].car + vehicleCounts[date].motorcycle;
                vehicleCounts[date].vacant = Math.max(maxSpaces - occupiedSpaces, 0);
            }

            callback(vehicleCounts);
            }, (error) => {
                console.log('Error retrieving transactions: ', error);
            });
        })
    }

    function formatDate(date) {
        const options = { month: '2-digit', day: '2-digit', year: 'numeric' };
        return date.toLocaleDateString('en-US', options); // Adjust the locale and options as needed
    }

    calculateVehicleCounts((vehicleCounts) => {
        const labels = Object.keys(vehicleCounts).slice(-7);
        const carsData = [];
        const motorcyclesData = [];
        const vacantData = [];

        for (const date of labels) {
            carsData.push(vehicleCounts[date].car);
            motorcyclesData.push(vehicleCounts[date].motorcycle);
            vacantData.push(vehicleCounts[date].vacant);
        }

        Bar.data.labels = labels;
        Bar.data.datasets[0].data = carsData;
        Bar.data.datasets[1].data = motorcyclesData;
        Bar.data.datasets[2].data = vacantData;

        Bar.update();
    });

    function updateChartBar(range) {
        calculateVehicleCounts((vehicleCounts) => {
            let labels = [];
            let carsData = [];
            let motorcyclesData = [];
            let vacantData = [];

            switch (range) {
                case 'week':
                    labels = Object.keys(vehicleCounts).slice(-7);
                    break;
                case 'month':
                    labels = Object.keys(vehicleCounts).slice(-30);
                    break;
                case 'year':
                    labels = Object.keys(vehicleCounts).slice(-365);
                    break;
                case "custom":
                    labels = Object.keys(vehicleCounts)
                    const startDate = document.getElementById("startDate").value;
                    const endDate = document.getElementById("endDate").value;
                    let formattedStartDate = new Date(startDate);
                    let formattedEndDate = new Date(endDate);
                    console.log("Test" + startDate);

                    let filteredLabels = [];
                    for (let i = 0; i < labels.length; i++) {
                        const currentDate = new Date(labels[i]);
                        if (currentDate >= formattedStartDate && currentDate <= formattedEndDate) {
                            console.log("Test");
                            console.log(currentDate);
                            filteredLabels.push(labels[i]);
                        }
                    }

                    labels = filteredLabels;
                    console.log(labels);
                    break;
                default:
                    break;
            }

            for (const date of labels) {
                carsData.push(vehicleCounts[date].car);
                motorcyclesData.push(vehicleCounts[date].motorcycle);
                vacantData.push(vehicleCounts[date].vacant);
            }

            Bar.data.labels = labels;
            Bar.data.datasets[0].data = carsData;
            Bar.data.datasets[1].data = motorcyclesData;
            Bar.data.datasets[2].data = vacantData;

            Bar.update();
        });
    }

    function calculateTotalSales(callback) {
        const paymentSettingsRef = ref(database, 'parking_payment_settings');
        const transactionsRef = ref(database, 'transactions');

        onValue(paymentSettingsRef, (paymentSettingsSnapshot) => {
            const paymentSettings = paymentSettingsSnapshot.val();
            const initialHours = paymentSettings.initial_hours || 0;
            const initialPayment = paymentSettings.initial_payment || 0;
            const incrementalPayment = paymentSettings.incremental_payment || 0;

            onValue(transactionsRef, (transactionsSnapshot) => {
                const transactions = transactionsSnapshot.val() || {};

                const salesCounts = {};

                const today = new Date();
                const oneDay = 24 * 60 * 60 * 1000;

                for (const key in transactions) {
                    const transaction = transactions[key];

                    // Check if the transaction has a 'top_up' field and its value is false
                    if ('top_up' in transaction && !transaction.top_up) {
                        const vehicleType = transaction.vehicle_type || 'car';
                        const date = new Date(transaction.date);
                        const dayIndex = Math.floor((today - date) / oneDay);

                        const formattedDate = formatDate(date); // Format the date as needed (e.g., 06/13/2024)

                        if (!(formattedDate in salesCounts)) {
                            salesCounts[formattedDate] = {
                                paymentAmounts: {
                                    car: 0,
                                    motorcycle: 0
                                },
                                discounts: 0,
                                revenue: 0
                            };
                        }

                        const duration = transaction.duration || 0;
                        let paymentAmount = parseInt(initialPayment);
                        const durationInHours = Math.ceil(duration / (60 * 60));
                        const durationInMinutes = Math.ceil((durationInHours % 3600) / 60);
                        const additionalHours = durationInHours - parseInt(initialHours);

                        if (additionalHours > 0) {
                            paymentAmount += additionalHours * parseInt(incrementalPayment);
                        }

                        if (vehicleType === 'car') {
                            salesCounts[formattedDate].paymentAmounts.car += transaction.payment;
                            salesCounts[formattedDate].revenue += paymentAmount;
                        } else if (vehicleType === 'motorcycle') {
                            salesCounts[formattedDate].paymentAmounts.motorcycle += transaction.payment;
                            salesCounts[formattedDate].revenue += paymentAmount;
                        }
                    }
                }

                callback(salesCounts);
            }, (error) => {
                console.log('Error retrieving transactions: ', error);
            });
        }, (error) => {
            console.log('Error retrieving payment settings: ', error);
        });
    }

    const customRangeBtnLine = document.getElementById("customRangeBtnLineChart");
    const customRangeDialogLine = document.getElementById("customRangeDialogLineChart");
    const lineChartSettings = document.getElementById("lineChartSettings");
    const applyBtnLine = document.getElementById("applyBtnLineChart");
    const cancelBtnLine = document.getElementById("cancelBtnLineChart");

    const customRangeBtnBar = document.getElementById("customRangeBtn");
    const customRangeDialogBar = document.getElementById("customRangeDialog");
    const barChartSettings = document.getElementById("barChartSettings");
    const applyBtnBar = document.getElementById("applyBtn");
    const cancelBtnBar = document.getElementById("cancelBtn");

    document.addEventListener("DOMContentLoaded", function() {
        const weekBtnLine = document.getElementById("weekBtnLineChart");
        const monthBtnLine = document.getElementById("monthBtnLineChart");
        const yearBtnLine = document.getElementById("yearBtnLineChart");

        const weekBtnBar = document.getElementById("weekBtn");
        const monthBtnBar = document.getElementById("monthBtn");
        const yearBtnBar = document.getElementById("yearBtn");

        weekBtnLine.addEventListener("click", function() {
            updateChart("week");
        });

        monthBtnLine.addEventListener("click", function() {
            updateChart("month");
        });

        yearBtnLine.addEventListener("click", function() {
            updateChart("month");
        });


        weekBtnBar.addEventListener("click", function() {
            updateChartBar("week");
        });

        monthBtnBar.addEventListener("click", function() {
            updateChartBar("month");
        });

        yearBtnBar.addEventListener("click", function() {
            updateChartBar("year");
        });

        // Call the updateChart function with the initial range
        updateChart("week");
    });

    function hideCustomRange() {
        lineChartSettings.style.display = 'block';
        customRangeDialogLine.style.display = "none";
    }

    function showCustomRange() {
        lineChartSettings.style.display = 'none';
        customRangeDialogLine.style.display = 'block';
    }

    function applyCustomRange() {
        updateChart("custom");
        hideCustomRange();
    }

    function hideCustomRangeBar() {
        barChartSettings.style.display = 'block';
        customRangeDialogBar.style.display = "none";
    }

    function showCustomRangeBar() {
        barChartSettings.style.display = 'none';
        customRangeDialogBar.style.display = 'block';
    }

    function applyCustomRangeBar() {
        updateChartBar("custom");
        hideCustomRangeBar();
    }

    customRangeBtnLine.addEventListener("click", showCustomRange);
    cancelBtnLine.addEventListener("click", hideCustomRange);
    applyBtnLine.addEventListener("click", applyCustomRange);

    customRangeBtn.addEventListener("click", showCustomRangeBar);
    cancelBtn.addEventListener("click", hideCustomRangeBar);
    applyBtn.addEventListener("click", applyCustomRangeBar);

    function updateChart(range) {
        calculateTotalSales((salesCounts) => {
            
            let labels = [];
            let salesCarData = [];
            let salesMotorcycleData = [];
            let combinedDiscountsData = [];

            switch (range) {
                case 'week':
                    labels = Object.keys(salesCounts).slice(-7);
                    break;
                case 'month':
                    labels = Object.keys(salesCounts).slice(-30);
                    break;
                case 'month':
                    labels = Object.keys(salesCounts).slice(-365);
                    break;
                case "custom":
                    labels = Object.keys(salesCounts)
                    const startDate = document.getElementById("startDate").value;
                    const endDate = document.getElementById("endDate").value;
                    let formattedStartDate = new Date(startDate);
                    let formattedEndDate = new Date(endDate);
                    console.log("Test" + startDate);

                    let filteredLabels = [];
                    for (let i = 0; i < labels.length; i++) {
                        const currentDate = new Date(labels[i]);
                        if (currentDate >= formattedStartDate && currentDate <= formattedEndDate) {
                            console.log("Test");
                            console.log(currentDate);
                            filteredLabels.push(labels[i]);
                        }
                    }

                    labels = filteredLabels;
                    console.log(labels);
                    break;
                default:
                    break;
            }

            for (const date of labels) {
                salesCarData.push(salesCounts[date].paymentAmounts.car);
                salesMotorcycleData.push(salesCounts[date].paymentAmounts.motorcycle);
                combinedDiscountsData.push(
                    Math.max(salesCounts[date].revenue - (salesCounts[date].paymentAmounts.motorcycle + salesCounts[date].paymentAmounts.car), 0)
                );
            }

            Line.data.labels = labels;
            Line.data.datasets[0].data = combinedDiscountsData;
            Line.data.datasets[1].data = salesCarData;
            Line.data.datasets[2].data = salesMotorcycleData;

            Line.update();
        });
    }

    // Call the updateChart function with the initial range
    updateChart('week');

    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->