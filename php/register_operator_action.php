<?php
require '../vendor/autoload.php';

session_start();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;


$factory = (new Factory)->withServiceAccount('../firebase.json');
$auth = $factory->createAuth();
use Google\Cloud\Firestore\FirestoreClient;

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the user's details from the form
    $name           = $_POST['name'];
    $email          = $_POST['email'];
    $password       = $_POST['password'];
    $address        = $_POST['address'];
    $phoneNumber    = $_POST['phone_number'];

    // Create a new user in Firebase Authentication
    try {
        $user = $auth->createUserWithEmailAndPassword($email, $password);
        $uid = $user->uid;
    } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {
        echo 'Email already exists';
    }

    // Store the user's details in Firestore
    $projectId = 'parqr-8d2fd';
    $databaseId = '(default)';
    $firestore = new FirestoreClient([
        'projectId' => $projectId,
        'databaseId' => $databaseId,
    ]);

    $current_date = new DateTime();
    $usersCollection = $firestore->collection('operators');

    $count = $usersCollection->documents()->size();
    $operatorId = $current_date->format("Y").str_pad($count+1, 2, '0', STR_PAD_LEFT);

    $newUser = [
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'phone_number' => $phoneNumber,
        'hired_by' => $current_date->format("d/m/Y"),
        'operator_id' => $operatorId,
    ];
    $usersCollection->document($uid)->set($newUser);

    // Redirect the user to a success page
    echo "Signup successful";
    header('Location: ../dashboard-pages/operators.php');
    exit;
}
?>