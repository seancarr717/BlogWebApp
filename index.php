<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
          crossorigin="anonymous">
</head>
<body>
    <div class="container py-4">
        <?php
	//connects to database
        include 'connect.php';
        session_start();
	//checks if user is logged in
        function isLoggedIn() {
            return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        }
	// Function to display posts
        function displayPosts($db) {
            try {
                $stmt = $db->query("SELECT * FROM posts");
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($posts as $post) {
                    echo "<div class='card my-3'><div class='card-body'>";
                    echo "<h5 class='card-title'>" . htmlspecialchars($post['title']) . "</h5>";
                    echo "<p class='card-text'>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
		    //show delete link for posts
                    if (isLoggedIn()) {
                        echo "<a href='delete_post.php?id=" . $post['id'] . "' class='btn btn-danger'>Delete Post</a>";
                    }
                    echo "</div></div>";
                }
            } catch (PDOException $e) {
                echo "Error fetching posts: " . $e->getMessage();
            }
        }
	// user registration to sqlite database
        if (isset($_POST['register'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];// in secure appi should hash the password
            $query = "INSERT INTO users (username, password) VALUES ('$username', '$password','')";
            $result = $db->query($query);
            echo "<div class='alert alert-success'>Registered successfully!</div>";
        }
	//handle user login
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
                echo "<div class='alert alert-success'>Logged in successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Login failed!</div>";
            }
        }
	//handles creating post submission
        if (isLoggedIn() && isset($_POST['add_post'])) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $stmt = $db->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $content]);
            echo "<div class='alert alert-success'>Post added successfully!</div>";
        }
	// Display logic based on user's login status
        if (isLoggedIn()) {
            displayPosts($db);
            ?>
            <div class="card my-4">
                <div class="card-body">
                    <h5 class="card-title">Add a New Post</h5>
                    <form method="post">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title" placeholder="Post Title" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="content" placeholder="Post Content" required style="resize: none;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="add_post">Add Post</button>
                    </form>
                </div>
            </div>
	    
            <a href="logout.php" class="btn btn-secondary">Logout</a>
            <?php
            //show logout link^
        } else {
            ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card my-4">
                        <div class="card-body">
                            <h5 class="card-title">Login or Register</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="login">Login</button>
                                <button type="submit" class="btn btn-secondary" name="register">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-3C3MYJU9gYR4XCmx5U7Jj9eB3y2mW1bVq78RiZ9nN8Df6I9Jl0wBn0C2VgQZ8kmj" 
            crossorigin="anonymous"></script>
</body>
</html>