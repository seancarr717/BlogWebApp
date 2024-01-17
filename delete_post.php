<?php
session_start();
include 'connect.php';
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

if (isLoggedIn() && isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    //Check if the logged-in owns the post 

    $stmt = $db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);

    // variable to show a confirmation message
    $_SESSION['message'] = 'Post deleted successfully.';
}

header("Location: index.php");
exit();
?>