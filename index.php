<?php
//connects to database
include 'connect.php';
//starts session managmnet
session_start();
//checks if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// user registration to sqlite database
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; // In a secure application, you should hash the password
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    echo "Registered successfully!";
}
// Function to display posts
function displayPosts($db) {
    try {
        $stmt = $db->query("SELECT * FROM posts");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $post) {
            echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
        }
    } catch (PDOException $e) {
        echo "Error fetching posts: " . $e->getMessage();
    }
}
// Handle user registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; // In a secure application, you should hash the password
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    echo "Registered successfully!";
}

// Handle user login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        echo "Logged in successfully!";
    } else {
        echo "Login failed!";
    }
}

// Handle post submission
if (isLoggedIn() && isset($_POST['add_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $db->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $content]);
    echo "Post added successfully!";
}

// Display logic based on user's login status
if (isLoggedIn()) {
    // Display posts and post submission form
    displayPosts($db);
    ?>
    <form method="post">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea name="content" placeholder="Post Content" required></textarea>
        <input type="submit" name="add_post" value="Add Post">
    </form>
    <?php
} else {
    // Display login and registration forms
    ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Login">
        <input type="submit" name="register" value="Register">
    </form>
    <?php
}
?>