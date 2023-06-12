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
                <a href="dashboard-statistics.php" class="btn" style="padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <img src="../../assets/leftArrow.png" style="filter: brightness(0) invert(1);">
                </a>
                <div style="background-color: white; padding: 20px; margin-bottom: 30px; margin-top: 20px; margin-left: 15px; margin-right: 15px; height: 800px; border-radius: 15px;">
                    <h2>Longest Time Running</h2>
                    <div id="list" style="padding: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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

        var html = '<div style="overflow-y: scroll; height: calc(70vh);">';
        html += '<div style="overflow-x: auto;">';
        html += '<table>';

        // Generate the HTML content for the table header
        html += '<thead>';
        html += '<tr>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">User Name</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Operator Name</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Plate No</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Date</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Start Time</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Duration</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Discount</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Payment</th>';
        html += '<th style="border: 1px solid black; padding: 20px; font-weight: bold;">Reference Number</th>';
        html += '</tr>';
        html += '</thead>';

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

                let discountText;

                const discountList = {
                    "pwd": "Pwd",
                    "none": "None",
                    "student": "Student",
                    "pregnant": "Pregnant",
                    "senior_citizen": "Senior Citizen",
                };

                for (const key in discountList) {
                    if (key === user.longestHistory.discount) {
                        discountText = discountList[key];
                        break;
                    } else {
                        discountText = "None"
                    }
                }

                html += '<tr>';
                html += '<td style="border: 1px solid black; padding: 20px; color: #213A5C; font-weight: bold; font-size: 16px;">' + user.userName + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: black; font-weight: bold; font-size: 16px;">' + user.longestHistory.operator_name + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: gray;">' + user.longestHistory.plate_no + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: gray;">' + new Date(user.longestHistory.start_time).toLocaleDateString([], { year: "numeric", month: "2-digit", day: '2-digit' }) + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: gray;">' + new Date(user.longestHistory.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: gray;">' + durationText + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: gray;">' + discountText + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px; color: #F3BB01; font-weight: bold;">' + "₱" + user.longestHistory.payment + '</td>';
                html += '<td style="border: 1px solid black; padding: 20px;">' + user.longestHistory.reference_number + '</td>';
                html += '</tr>';
            }
        });

        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        html += '</div>';

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