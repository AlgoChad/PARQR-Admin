<?php
require '../vendor/autoload.php';

session_start();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;


$factory = (new Factory)->withServiceAccount('../firebase.json');
$auth = $factory->createAuth();
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Storage;

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($POST_['submit'])) {

    // Get the user's details from the form
    $first_name     = $_POST['first_name'];
    $last_name      = $_POST['last_name'];
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
    $usersCollection = $firestore->collection('admin');

    $count = $usersCollection->documents()->size();
    $operatorId = $current_date->format("Y").str_pad($count+1, 2, '0', STR_PAD_LEFT);

    // Upload the profile picture to Firebase Storage
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();
        $file = $_FILES['file'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $uid . '.' . $fileExtension;
        $object = $bucket->upload(
            fopen($file['tmp_name'], 'r'),
            ['name' => 'admin_profiles/' . $fileName]
        );
        $downloadUrl = $object->signedUrl(new DateTime('+10 years'));
    } else {
        $downloadUrl = null;
    }
    
    $newUser = [
        'name' => $first_name." ".$last_name,
        'email' => $email,
        'address' => $address,
        'phone_number' => $phoneNumber,
        'profile_picture' => $downloadUrl,
    ];
    $usersCollection->document($uid)->set($newUser);

    $condition = true;
    // Redirect the user to a success page
    if ($condition) {
        // Generate JavaScript code to display an alert and redirect
        echo '<script>alert("Admin Register Success!"); window.location.href="../dashboard-pages/profile.php";</script>';
    }
    exit;
}
?>
