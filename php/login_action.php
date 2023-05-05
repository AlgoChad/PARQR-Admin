<?php 
require 'vendor/autoload.php';

session_start();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;


$factory = (new Factory)->withServiceAccount('firebase.json');
$auth = $factory->createAuth();

if (isset($_SESSION['user_id'])) {
    // Redirect the user to the dashboard page
    header("Location: ../dashboard-pages/home.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    try {
        // Sign in the user
        $signInResult = $auth->signInWithEmailAndPassword($email, $password);
        $_SESSION['user_id'] = $signInResult->data()['localId'];
        echo "Sign-in successful!";
        // Redirect the user to the dashboard page
        if (isset($_SESSION['user_id'])) {
            // Redirect the user to the dashboard page
            header("Location: ../dashboard-pages/home.php");
            exit;
        }
        exit;
    } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
        echo "Invalid password";
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        echo "User not found";
    } catch (\Exception $e) {
        echo $e;
    }
}
?>