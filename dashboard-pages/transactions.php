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

$factory = (new Factory)->withServiceAccount('../firebase.json');
$database = $factory->withDatabaseUri('https://parqr-8d2fd-default-rtdb.asia-southeast1.firebasedatabase.app')->createDatabase();

$dataRef = $database->getReference('transactions')->getValue();
$data = array_reverse($dataRef)
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
            <div class="col-md-10" style="overflow-y: auto;">
                <div style="display: flex; flex-direction: justify-content: center; row; align-items: center; padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <div style="flex: 1;">
                        <h2>Transactions</h1>
                    </div>
                </div>
                <div class="row justify-content-center">
                     <div class="col-md-12" style="display: flex; flex-direction: row;">
                        <div id="resizableDiv" class="col-md-12">
                            <div style="padding-left: 30px; padding-right: 30px; padding-top: 20px;">
                                <form method="GET" action="transactions.php" style="display: flex; flex-direction: row; align-items: center;">
                                    <div style="flex-grow: 1; max-width: 80vw; border-radius: 20px; background-color: #ebedf0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                        <input type="text" name="search" style="padding: 10px; border: none; background-color: transparent; width: 100%;" placeholder="Search...">
                                    </div>
                                    <button class="btn" style="margin-left: 10px; background-color: #213A5C; color: white; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" type="submit">
                                        Search
                                    </button>
                                </form>
                            </div>
                            <div style="display: flex; flex-direction: row;  margin: 20px; padding: 10px; border-radius: 10px;">
                                <span style="flex: 1;">Name</span>
                                <div style="flex: 2;"></div>
                                <div style="flex: 2;"></div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1;">
                                    <span>Date</span>
                                </div>
                                <div style="flex: 1.7;"></div>
                                <div style="flex: 1;">
                                    <span>Time</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1;">
                                    <span>Amount</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 0.4;"></div>
                            </div>
                            <div style="overflow-y: scroll; height: calc(80vh);">
                                <?php if ($dataRef !== null) : 
                                        $data = array_reverse($dataRef)?>
                                    <?php foreach ($data as $info) : ?>
                                        <?php if (!empty($info)) : ?>
                                            <?php
                                            // Filter the data based on search query
                                            $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
                                            $name = strtolower($info['user_name']);
                                            if (empty($searchQuery) || strpos($name, strtolower($searchQuery)) !== false) :
                                            ?>
                                            <div>
                                                <div style="display: flex; flex-direction: row; justify-content: center; align-items: center; margin: 20px; padding: 10px; border-radius: 10px; background-color: #ebedf0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                    <div>
                                                        <img src="<?php echo isset($info['profile_picture']) ? $info['profile_picture'] : '../assets/PARQR-White.png'; ?>" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 50px; height: 50px;">
                                                    </div>
                                                    <div style="flex: 1; padding-left: 20px; text-align: start;">
                                                        <h5><?php echo $info['user_name']; ?></h5>
                                                    </div>              
                                                    <div style="flex: 1;"></div>                                      
                                                    <div style="flex: 1;">
                                                        <h5 style="font-size: 18px;"><?php 
                                                        if ($info['top_up']) {                                                            
                                                            echo $info['formattedDate'];
                                                        } else {
                                                            date_default_timezone_set('Asia/Manila');
                                                            echo date('m/d/Y', strtotime($info['date']));
                                                        }
                                                        ?></h5>
                                                    </div>                                                    
                                                    <div style="flex: 1;">
                                                        <h5 style="font-size: 18px;">
                                                            <?php
                                                            if ($info['top_up']) {
                                                                echo $info['formattedTime'];
                                                            } else {
                                                                $start_time_ms = $info['start_time'];
                                                                $duration = $info['duration'] * 1000; // Convert duration from seconds to milliseconds

                                                                // Convert the start time from milliseconds to Unix timestamp
                                                                $start_time_unix = round($start_time_ms / 1000); // Remove milliseconds precision

                                                                // Set the timezone to Philippines
                                                                date_default_timezone_set('Asia/Manila');

                                                                // Calculate the end time by adding the duration (in milliseconds) to the start time
                                                                $end_time_unix = $start_time_unix + ($duration / 1000); // Convert duration back to seconds

                                                                // Format the start time and end time in the desired format (e.g., 8:31 am - 6:51 pm)
                                                                $start_time_formatted = date('g:i A', round($start_time_unix));
                                                                $end_time_formatted = date('g:i A', round($end_time_unix));

                                                                // Print the formatted start time and end time
                                                                echo $start_time_formatted . ' - ' . $end_time_formatted;
                                                            }
                                                            ?>
                                                        </h5>
                                                    </div>                                                    
                                                    <div style="flex: 1;">
                                                        <h5 style="font-size: 18px;"><?php echo "₱" . $info['payment']; ?></h5>
                                                    </div>
                                                    <button class="btn" onclick="toggleDivVisibility(<?php echo htmlspecialchars(json_encode($info), ENT_QUOTES, 'UTF-8'); ?>); resizeDiv();">
                                                        <img src="../assets/home-icons/Menu.png" alt="">
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="toggleDiv" style="display: none; align-items: center; justify-content: center; background-color: #fefcf2; width: 100%; margin-right: 20px; border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleDivVisibility(info) {
            console.log(info);
            var infoDiv = document.getElementById("toggleDiv");
            var profilePicture = document.getElementById("profilePicture");

            if (infoDiv.style.display === "none") {
                // Retrieve the data from the info object and display it in the toggleDiv
                var html;
                if(info.top_up) {
                    console.log('do something')
                    var name = info.user_name;
                    var operator = info.operator_name;
                    var plateNo = info.plate_no;
                    var payment = info.payment;
                    var referenceNumber = info.reference_number;
                    var profilePictureSrc = info.src ? info.src : '../assets/PARQR-White.png';
                    html = `<div style="display: flex; flex-direction: column; align-items: center; justify-content:-top:  center;">
                                <img src="${profilePictureSrc}" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 100px; height: 100px; margin-top: 30px;">
                                <span style="font-size: 24px; font-weight: bold; color: #213A5C; margin-top: 20px;">${name}</span>
                                <span style="font-size: 24px; color: #213A5C;">Transaction Details</span>
                                <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                                    <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                        <span style="font-size: 16px; color: lightgray;">Operator</span>
                                        <span style="font-size: 16px; color: gray;">${operator}</span>
                                    </div>
                                    <div style="display: flex; flex-direction: row; margin-bottom: 20px; justify-content: space-between; width: 90%;">
                                        <span style="font-size: 16px; color: lightgray;">Payment</span>
                                        <span style="font-size: 16px; color: gray;">${"+₱" + payment}</span>
                                    </div>
                                    <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                    <div style="display: flex; flex-direction: row; margin-bottom: 30px; justify-content: space-between; width: 90%;">
                                        <span style="font-size: 16px; color: lightgray;">Reference Number</span>
                                        <span style= "font-size: 16px; color: gray;">${referenceNumber}</span>
                                    </div>
                                </div 
                            <div>`;
                    
                } else {
                    var name = info.user_name;
                    var plateNo = info.plate_no;
                    var date = new Date(info.date).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
                    var time = new Date(info.start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    var operator = info.operator_name;
                    var endTime = new Date(info.start_time + info.duration * 1000).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                    var durationInSeconds = (new Date(info.start_time + info.duration * 1000) - new Date(info.start_time)) / 1000;
                    var durationInMinutes = Math.floor(durationInSeconds / 60);
                    var durationInHours = Math.floor(durationInMinutes / 60);
                    var discount = info.discount;
                    var payment = info.payment;
                    var referenceNumber = info.reference_number;
                    var profilePictureSrc = info.src ? info.src : '../assets/PARQR-White.png';

                    let durationText;
                    if (durationInHours < 1) {
                        const remainingSeconds = Math.round(durationInSeconds % 60);
                        durationText = `0 mins ${remainingSeconds} secs`;
                    } else {
                        durationText = `${durationInHours} hours ${durationInMinutes % 60} min`;
                    }

                    html = `<div style="display: flex; flex-direction: column; align-items: center; justify-content:-top:  center;">
                                    <img src="${profilePictureSrc}" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 100px; height: 100px; margin-top: 30px;">
                                    <span style="font-size: 24px; font-weight: bold; color: #213A5C; margin-top: 20px;">${name}</span>
                                    <span style="font-size: 24px; color: #213A5C;">Transaction Details</span>
                                    <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Plate no</span>
                                            <span style="font-size: 16px; color: gray;">${plateNo}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Date</span>
                                            <span style="font-size: 16px; color: gray;">${date}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Time</span>
                                            <span style="font-size: 16px; color: gray;">${time}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Operator</span>
                                            <span style="font-size: 16px; color: gray;">${operator}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Hours Parked</span>
                                            <span style="font-size: 16px; color: gray;">${time + " - " + endTime}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Duration</span>
                                            <span style="font-size: 16px; color: gray;">${durationText}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Discount</span>
                                            <span style="font-size: 16px; color: gray;">${discount}</span>
                                        </div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 20px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Payment</span>
                                            <span style="font-size: 16px; color: gray;">${"+₱" + payment}</span>
                                        </div>
                                        <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                        <div style="display: flex; flex-direction: row; margin-bottom: 30px; justify-content: space-between; width: 90%;">
                                            <span style="font-size: 16px; color: lightgray;">Reference Number</span>
                                            <span style= "font-size: 16px; color: gray;">${referenceNumber}</span>
                                        </div>
                                    </div 
                                <div>`;
                }
                infoDiv.innerHTML = html;
                infoDiv.style.display = "block";
            } else {
                infoDiv.style.display = "none";
            }
        }

        function resizeDiv() {
            var toggleDiv = document.getElementById("toggleDiv");
            var resizableDiv = document.getElementById("resizableDiv");

            if (toggleDiv.style.display === "none") {
                resizableDiv.classList.remove("col-md-9");
                resizableDiv.classList.add("col-md-12");
            } else {
                resizableDiv.classList.remove("col-md-12");
                resizableDiv.classList.add("col-md-9");
            }
        }
    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->