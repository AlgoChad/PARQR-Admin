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

    $adminDoc = $firestore->collection('admin')->document($_SESSION['user_id']);

    $adminDoc->update([
        ['path' => 'name', 'value' => $name],
        ['path' => 'address', 'value' => $address],
        ['path' => 'phone_number', 'value' => $phoneNumber],
        ['path' => 'email', 'value' => $email],
    ]);

     // Update email in Firebase Authentication
    try {
        $userProperties = [
            'email' => $email,
        ];
        $updatedUser = $auth->updateUser($_SESSION['user_id'], $userProperties);
        // Email updated successfully
    } catch (Exception $e) {
        // Handle error
        echo 'Error updating email: ' . $e->getMessage();
        exit;
    }

    // Handle profile picture upload (if provided)
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $storage = $factory->createStorage();

        $bucket = $storage->getBucket();
        $file = $_FILES['file'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $_SESSION['user_id'] . '.' . $fileExtension;
        $object = $bucket->upload(
            fopen($file['tmp_name'], 'r'),
            ['name' => 'admin_profiles/' . $fileName]
        );
        $downloadUrl = $object->signedUrl(new DateTime('+10 years'));

        // Update profile picture URL in Firestore
        $adminDoc->update([
            ['path' => 'profile_picture', 'value' => $downloadUrl],
        ]);
    } else {
        $downloadUrl = null;
        echo "Error uploading profile picture";
    }

    // Redirect back to the profile page or any other desired location
    header('Location: ../dashboard-pages/profile.php');
    exit;
}
?>