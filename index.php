<?php
//connects to database
include 'connect.php';
//starts session managmnet
session_start();

// user registration to sqlite database
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; // In a secure application, you should hash the password
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    echo "Registered successfully!";
}

// Handle  login to database
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        echo "Logged in successfully!";
    } else {
        echo "Login failed!";
    }
}

// Display posts
$stmt = $db->query("SELECT * FROM posts");
$posts = $stmt->fetchAll();
foreach ($posts as $post) {
    echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
    echo "<p>" . htmlspecialchars($post['content']) . "</p>";
}

// Add a post (only if logged in)
if (isset($_POST['add_post']) && isset($_SESSION['user_id'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $db->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $content]);
    echo "Post added successfully!";
}

// HTML Forms for actions
?>
<form method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="register" value="Register">
    <input type="submit" name="login" value="Login">
</form>

<?php if (isset($_SESSION['user_id'])): ?>
    <form method="post">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea name="content" placeholder="Post Content" required></textarea>
        <input type="submit" name="add_post" value="Add Post">
    </form>
<?php endif; ?>