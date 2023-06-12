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
                                    <h3>$66,000</h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-left: 20px; margin-right: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Income</h5>
                                    <h3>$66,000</h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Amount of Discounts</h5>
                                    <h3>$66,000</h3>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: row;">
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px; height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Total Top-up Amount</h5>
                                    <h3>$66,000</h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-left: 20px; margin-right: 20px; margin-bottom: 20px;  height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Car Parking Income</h5>
                                    <h3>$66,000</h3>
                                </div>
                                <div style="background-color: white; padding: 20px; margin-bottom: 20px;  height: 100%; width: 100%; border-radius: 15px;">
                                    <h5 style="font-size: 16px;">Motorcycle Parking Income</h5>
                                    <h3>$66,000</h3>
                                </div>
                            </div>
                        </div>
                        <div style="background-color: white; padding: 20px; height: 700px; width: 100%; border-radius: 15px;">
                            <div style="height: 90%;">
                                <h2>Total Summary of Parking Spaces</h2>
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
                            <h2>Parked Users</h2>
                            <div style="height: 90%;">
                                <canvas id="Chart_2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="background-color: white; padding: 20px; margin-bottom: 30px; margin-left: 15px; margin-right: 15px; height: 500px; border-radius: 15px;" >
                    <div style="height: 90%;">
                        <h2>Total Sales</h2>
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
                data: [20, 30, 20, 10, 20], // Placeholder data values
                backgroundColor: ['#2475e2', '#a6cafa', '#5c7699', '#104388', '#90a0b7'], // Colors for each dataset
                hoverBackgroundColor: ['#2475e2', '#a6cafa', '#5c7699', '#104388', '#90a0b7'], // Hover colors for each dataset
                borderWidth: 0
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
                        padding: 30
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
                                label += context.parsed.toFixed(2) + '%';
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
                html += '<td style="padding-right: 20px; font-weight: bold;">' + user.userName + '</td>';
                html += '<td style="padding-right: 20px; padding-left: 20px;">' + new Date(user.longestHistory.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + '</td>';
                html += '<td style="padding-right: 20px; padding-left: 20px;">' + durationText + '</td>';
                html += '<td style="padding-left: 20px; color: gray;">' + "₱" + user.longestHistory.payment + '</td>';
                html += '</tr>';
            }
        });

        html += '</tbody>';
        html += '</table>';


        // Update the innerHTML of the divElement
        divElement.innerHTML = html;
    }


    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->