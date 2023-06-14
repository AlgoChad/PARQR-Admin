<?php
// Start the session
session_start();
// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: ../../login.php");
    exit;
}
?>
<?php 
require_once '../../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth;

$factory = (new Factory)->withServiceAccount('../../firebase.json');
$database = $factory->withDatabaseUri('https://parqr-8d2fd-default-rtdb.asia-southeast1.firebasedatabase.app')->createDatabase();

// $data = $database->getReference('transactions')->getValue();
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

    $currentID =$_GET['id'];
    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id'])->snapshot()->data();
    $operatorDoc = $firestore->collection('operators')->document($currentID)->snapshot()->data();
    $path = 'operators/'. $currentID.'/transactions';
    $dataRef = $database->getReference($path)->getValue();   
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
            <div class="col-md-10" style="overflow-y: scroll; height: calc(100vh);">
                <div style="display: flex; flex-direction: justify-content: center; row; align-items: center; padding-right: 30px; padding-left: 30px; padding-top: 30px;">
                    <div style="flex: 1;">
                        <h1 style="color: #213A5C;">Parking Operators</h1>
                        <div style="display: flex; flex-direction: row;">
                            <a href="../operators.php"><h4 style="color: #213A5C;">Parking Operators</h4></a>
                            <h4 style="margin-left: 5px; margin-right: 5px; color: #213A5C;">/</h4>
                            <a href="operator_profile.php?id=<?php echo $currentID; ?>"><h4 style="color: #213A5C;"> Profile</h4></a>
                            <h4 style="margin-left: 5px; margin-right: 5px; color: #213A5C;">/</h4>
                            <a href=""><h4 style="color: #213A5C;">Operator Activities</h4></a>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div style="width: 100%; border-radius: 20px; background-color: #ebedf0; 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                                <form method="GET" action="operator_transactions.php">
                                    <input type="hidden" name="id" value="<?php echo $currentID; ?>">
                                    <input type="text" name="search" style="margin: 10px; border: none; background-color: #ebedf0; width: 95%;" placeholder="Search...">
                                    <button type="submit" style="display: none;">Search</button>
                                </form>
                            </div>
                            <div style="display: flex; flex-direction: row;  margin: 20px; padding: 10px; border-radius: 10px;">
                                <span style="flex: 1;">Name</span>
                                <div style="flex: 2;"></div>
                                <div style="flex: 1.8;"></div>
                                <div >
                                    <span>Transaction Type</span>
                                </div>
                                <div style="flex: 1.3;"></div>
                                <div>
                                    <span>Discount</span>
                                </div>
                                <div style="flex: 1.3;"></div>
                                <div style="flex: 1;">
                                    <span>Date</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1;">
                                    <span>Time</span>
                                </div>
                                <div style="flex: 1;"></div>
                                <div style="flex: 1.8;">
                                    <span>Amount</span>
                                </div>
                            </div>
                            <div>
                                <?php if ($dataRef !== null) : 
                                    $data = array_reverse($dataRef);?>
                                    <?php foreach ($data as $key => $info) : ?>
                                        <?php if (!empty($info)) : ?>
                                            <?php
                                            // Filter the data based on search query
                                            $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
                                            $name = strtolower($info['user_name']);
                                            if (empty($searchQuery) || strpos($name, strtolower($searchQuery)) !== false) :
                                            ?>
                                                <div>
                                                    <div class="btn" style="display: flex; flex-direction: row; justify-content: center; align-items: center; margin: 20px; padding: 10px; border-radius: 10px; background-color: #ebedf0;">
                                                        <div>
                                                            <img src="<?php echo isset($info['profile_picture']) ? $info['profile_picture'] : '../../assets/PARQR-White.png'; ?>" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 50px; height: 50px;">
                                                        </div>
                                                        <div style="flex: 1; padding-left: 20px; text-align: start;">
                                                            <h5><?php echo $info['user_name']; ?></h5>
                                                        </div>
                                                        <div style="flex: .8;"></div>
                                                        <div style="flex: 1;">
                                                        <h5>
                                                            <?php
                                                                if ($info['top_up']) {
                                                                    echo "Top-up";
                                                                } else {
                                                                    echo "Parking Pay";
                                                                }
                                                             ?>
                                                        </h5>
                                                        </div>
                                                        <div style="flex: 1;">
                                                            <h5>
                                                                <?php
                                                                    if (!$info['top_up']) {
                                                                        if(isset($info['discount'])) {
                                                                            if ($info['discount'] == "pwd") {
                                                                                echo "PWD";
                                                                            } elseif ($info['discount'] == "senior_citizen") {
                                                                                echo "Senior Citizen";
                                                                            } elseif ($info['discount'] == "pregnant") {
                                                                                echo "Pregnant";
                                                                            } elseif ($info['discount'] == "student") {
                                                                                echo "Student";
                                                                            } else {
                                                                                echo "None";
                                                                            }
                                                                        } else {
                                                                            echo "None";
                                                                        }
                                                                    } else {
                                                                            echo "None";
                                                                    }
                                                                ?>        
                                                            </h5>
                                                        </div>
                                                        <div style="flex: 1;">
                                                            <h5><?php 
                                                            if ($info['top_up']) {                                                            
                                                                echo $info['formattedDate'];
                                                            } else {
                                                                date_default_timezone_set('Asia/Manila');
                                                                echo date('m/d/Y', strtotime($info['date']));
                                                            }
                                                            ?></h5>
                                                        </div>
                                                        <div style="flex: 1;">
                                                            <h5>
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
                                                        <div></div>
                                                        <div style="flex: 0.85;">
                                                            <h5><?php echo "₱" . $info['payment']; ?></h5>
                                                        </div>
                                                        <button class="btn" data-open-modal="<?php echo $key; ?>"  onclick="toggleDivVisibility(<?php echo htmlspecialchars(json_encode($info), ENT_QUOTES, 'UTF-8'); ?>, 'toggleDiv-<?php echo $key; ?>');">
                                                            <img src="../../assets/home-icons/Menu.png" alt="">
                                                        </button>
                                                        <dialog data-modal style="display: none; border: none; border-radius: 10px">  
                                                            <div id="toggleDiv-<?php echo $key; ?>" style="display: none; width: 700px;">
                                                            <?php
                                                                if (!$info['top_up']) {
                                                                    $name = $info['user_name'];
                                                                    $plateNo = $info['plate_no'];

                                                                    $date = date('F d, Y', strtotime($info['date']));
                                                                    $time = date('h:i A', strtotime($info['start_time']));
                                                                   
                                                                    $start_time_ms = $info['start_time'];
                                                                    $duration = $info['duration'] * 1000; // Convert duration from seconds to milliseconds

                                                                    // Convert the start time from milliseconds to Unix timestamp
                                                                    $start_time_unix = round($start_time_ms / 1000); // Remove milliseconds precision

                                                                    // Set the timezone to Philippines
                                                                    date_default_timezone_set('Asia/Manila');

                                                                    // Calculate the end time by adding the duration (in milliseconds) to the start time
                                                                    $end_time_unix = $start_time_unix + ($duration / 1000); // Convert duration back to seconds

                                                                    // Format the start time and end time in the desired format (e.g., 8:31 am - 6:51 pm)
                                                                    $startTime = date('g:i A', round($start_time_unix));
                                                                    $endTime = date('g:i A', round($end_time_unix));

                                                                // Print the formatted start time and end time
            

                                                                    $discount = $info['discount'];
                                                                    $payment = $info['payment'];
                                                                    $referenceNumber = $info['reference_number'];
                                                                    $profilePictureSrc = isset($info['profile_picture']) ? $info['profile_picture'] : '../../assets/PARQR-White.png';

                                                                    $durationInSeconds = round($end_time_unix - $start_time_unix);
                                                                    $durationInMinutes = floor($durationInSeconds / 60);
                                                                    $durationInHours = floor($durationInMinutes / 60);

                                                                    // Calculate the duration text based on the duration in hours and minutes
                                                                    if ($durationInHours < 1) {
                                                                        $remainingSeconds = round($durationInSeconds % 60);
                                                                        $durationText = "0 mins $remainingSeconds secs";
                                                                    } else {
                                                                        $durationText = "$durationInHours hours " . ($durationInMinutes % 60) . " min";
                                                                    }

                                                                    $vehicleText = 'Car';
                                                                    $vehicleTypeTable = array(
                                                                        "car" => "Car",
                                                                        "motorcycle" => "Motorcycle"
                                                                    );

                                                                    foreach ($vehicleTypeTable as $key => $value) {
                                                                        if ($key === isset($info["vehicle_type"])) {
                                                                            $vehicleText = $value;
                                                                            break;
                                                                        }
                                                                    }

                                                                    $discountText;
                                                                    $discountsTable = array(
                                                                        "pwd" => "Pwd",
                                                                        "none" => "None",
                                                                        "student" => "Student",
                                                                        "pregnant" => "Pregnant",
                                                                        "senior_citizen" => "Senior Citizen"
                                                                    );

                                                                    foreach ($discountsTable as $key => $value) {
                                                                        if ($key === $discount) {
                                                                            $discountText = $value;
                                                                            break;
                                                                        }
                                                                    }

                                                                    $eWalletText = isset($info['e_wallet']) ? "E-wallet" : "Cash";

                                                                    echo '
                                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content:-top:  center;">
                                                                        <img src="'.$profilePictureSrc.'" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 100px; height: 100px; margin-top: 30px;">
                                                                        <span style="font-size: 24px; font-weight: bold; color: #213A5C; margin-top: 20px;">'.$name.'</span>
                                                                        <span style="font-size: 24px; color: #213A5C;">Transaction Details</span>
                                                                        <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Transaction Type</span>
                                                                                <span style="font-size: 16px; color: gray;">Parking</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Plate no</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$plateNo.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Vehicle Type</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$vehicleText.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Date</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$date.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Time</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$startTime.'</span>
                                                                            </div>                                                                           
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Hours Parked</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$startTime.' - '.$endTime.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Duration</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$durationText.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Discount</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$discountText.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 20px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Payment Type</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$eWalletText.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 20px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Payment</span>
                                                                                <span style="font-size: 16px; color: gray;">+₱'.$payment.'</span>
                                                                            </div>
                                                                            <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 30px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Reference Number</span>
                                                                                <span style= "font-size: 16px; color: gray;">'.$referenceNumber.'</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>';
                                                                } else {
                                                                    $name = $info['user_name'];
                                                                    $operator = $info['operator_name'];
                                                                    $plateNo = $info['plate_no'];
                                                                    $payment = $info['payment'];
                                                                    $referenceNumber = $info['reference_number'];
                                                                    $profilePictureSrc = isset($info['profile_picture']) ? $info['profile_picture'] : '../../assets/PARQR-White.png';
                                                                    echo '
                                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content:-top:  center;">
                                                                        <img src="'.$profilePictureSrc.'" class="img-responsive" style="background-color: #213A5C; border-radius: 50%; width: 100px; height: 100px; margin-top: 30px;">
                                                                        <span style="font-size: 24px; font-weight: bold; color: #213A5C; margin-top: 20px;">'.$name.'</span>
                                                                        <span style="font-size: 24px; color: #213A5C;">Transaction Details</span>
                                                                        <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Transaction Type</span>
                                                                                <span style="font-size: 16px; color: gray;">Top-up</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 15px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Operator</span>
                                                                                <span style="font-size: 16px; color: gray;">'.$operator.'</span>
                                                                            </div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 20px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Payment</span>
                                                                                <span style="font-size: 16px; color: gray;">+₱'.$payment.'</span>
                                                                            </div>
                                                                            <div style="width: 90%; border-top: 1px solid gray; margin-bottom: 20px;"></div>
                                                                            <div style="display: flex; flex-direction: row; margin-bottom: 30px; justify-content: space-between; width: 90%;">
                                                                                <span style="font-size: 16px; color: lightgray;">Reference Number</span>
                                                                                <span style= "font-size: 16px; color: gray;">'.$referenceNumber.'</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;">
                                                                <button data-close-modal style="border: none; width: 80%; background-color: #213A5C; color: white;" class="btn">Close</button>                                    
                                                            </div>
                                                        </dialog>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const openButtons = document.querySelectorAll('[data-open-modal]');
        const closeButton = document.querySelectorAll('[data-close-modal]');
        const modals = document.querySelectorAll('[data-modal]');

        // Show dialog when the button is clicked
        openButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-open-modal');
                const toggleDiv = document.querySelector(`#toggleDiv-${modalId}`);
                const modal = toggleDiv.closest('[data-modal]');

                toggleDiv.style.display = 'block';
                modal.style.display = 'block';
                modal.showModal();
            });
        });

        // Close dialog when the close button is clicked
        closeButton.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('[data-modal]');
                const toggleDiv = modal.querySelector('[id^="toggleDiv-"]');
                
                toggleDiv.style.display = 'none';
                modal.style.display = 'none';
                modal.close();
            });
        });


    </script>
    <!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/XvoETpP5MPhJ6Ml" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> 
<!-- You can add your own content for the main section after the closing div of the col-md-10 element -->