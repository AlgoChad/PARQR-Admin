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
                <div style="display: flex; flex-direction: justify-content: center; row; align-items: center; padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <a href="../profile.php" class="btn">
                        <img src="../../assets/leftArrow.png" style="">
                    </a>
                    <div style="flex: 1;">
                        <h2>Transactions</h1>
                    </div>
                </div>
                <ul id="feedback-list"></ul>
            </div>
        </div>
    </div>
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

        // Get reference to the feedback_messages node
        const feedbackRef = ref(database, 'feedback_messages');

        // Function to display the feedback data as a list
        const displayFeedbackData = (feedbackData) => {
            const feedbackList = document.getElementById('feedback-list');

            // Clear previous list items
            feedbackList.innerHTML = '';

            // Loop through the feedback data
            for (const key in feedbackData) {
                const feedbackItem = feedbackData[key];

                // Create list item element
                const listItem = document.createElement('li');

                // Apply styles to the list item
                listItem.style.display = 'flex';
                listItem.style.flexDirection = 'row';
                // listItem.style.justifyContent = 'center';
                // listItem.style.alignItems = 'center';
                listItem.style.margin = '20px';
                listItem.style.padding = '10px';
                listItem.style.borderRadius = '10px';
                listItem.style.backgroundColor = '#ebedf0';

                // Format the date
                const feedbackDate = new Date(feedbackItem.date);
                const formattedDate = `${feedbackDate.toLocaleDateString()}`;

                // Set innerHTML of the list item
                listItem.innerHTML = `
                <div style="">
                    <div style="flex: 1;"><strong>${feedbackItem.userName}</strong></div>
                    <div style="flex: 1;"><span>Date: ${formattedDate}</span></div>
                    <div style="flex: 1;"><span>Time: ${feedbackDate.toLocaleTimeString()}</span></div>
                    <div style="flex: 1;"><span>Feedback: ${feedbackItem.feedback}</span></div>
                </div>
                `;

                // Append the list item to the feedback list
                feedbackList.appendChild(listItem);
            }
        };

        // Listen for changes in the feedback data
        onValue(feedbackRef, (snapshot) => {
            const feedbackData = snapshot.val();

            // Display the feedback data
            displayFeedbackData(feedbackData);
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>