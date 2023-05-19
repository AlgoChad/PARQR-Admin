<?php
require '../vendor/autoload.php';

session_start();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

$factory = (new Factory)->withServiceAccount('../firebase.json');
$auth = $factory->createAuth();

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Storage;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phone_number'];
    $email = $_POST['email'];

    // Update user data in Firestore
    $projectId = 'parqr-8d2fd';
    $databaseId = '(default)';
    $firestore = new FirestoreClient([
        'projectId' => $projectId,
        'databaseId' => $databaseId,
    ]);

    $operatorDoc = $firestore->collection('operators')->document($_POST['id']);

    $operatorDoc->update([
        ['path' => 'name', 'value' => $name],
        ['path' => 'address', 'value' => $address],
        ['path' => 'phone_number', 'value' => $phoneNumber],
        ['path' => 'email', 'value' => $email],
    ]);

    // Handle profile picture upload (if provided)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $storage = $factory->createStorage();

        $bucket = $storage->getBucket();
        $file = $_FILES['file'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $_SESSION['user_id'] . '.' . $fileExtension;
        $object = $bucket->upload(
            fopen($file['tmp_name'], 'r'),
            ['name' => 'operator_profiles/' . $fileName]
        );
        $downloadUrl = $object->signedUrl(new DateTime('+10 years'));

        // Update profile picture URL in Firestore
        $operatorDoc->update([
            ['path' => 'profile_picture', 'value' => $downloadUrl],
        ]);
    } else {
        $downloadUrl = null;
        echo "Error uploading profile picture";
    }

    // Redirect back to the profile page or any other desired location
    header("Location: ../dashboard-pages/operator-screens/operator_profile.php?id=" . $_POST['id']);
    exit;
}
?>