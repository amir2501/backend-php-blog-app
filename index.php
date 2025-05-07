<?php
$postsFile = 'posts.json';

// CORS headers for React Native apps or other frontends
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Handle POST (Create new post)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");

    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';

    if ($title && $body) {
        $posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
        $newPost = [
            'id' => count($posts) + 1,
            'title' => htmlspecialchars($title),
            'body' => htmlspecialchars($body),
        ];
        $posts[] = $newPost;
        file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true, 'post' => $newPost]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Title and body required']);
    }
    exit;
}

// Handle GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    // If it's a request from the app or expects JSON
    if (str_contains($accept, 'application/json')) {
        header("Content-Type: application/json");
        if (file_exists($postsFile)) {
            echo file_get_contents($postsFile);
        } else {
            echo json_encode([]);
        }
    } else {
        // Simple HTML form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Create Post</title>
            <style>
                body {
                    font-family: sans-serif;
                    padding: 2rem;
                    max-width: 500px;
                    margin: auto;
                    background: #f5f5f5;
                }
                input, textarea {
                    width: 100%;
                    margin-bottom: 1rem;
                    padding: 0.5rem;
                    font-size: 1rem;
                }
                button {
                    padding: 0.7rem 1.5rem;
                    font-size: 1rem;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
        <h1>Create New Post</h1>
        <form method="POST">
            <input type="text" name="title" placeholder="Post title" required>
            <textarea name="body" placeholder="Post content" rows="6" required></textarea>
            <button type="submit">Submit</button>
        </form>
        </body>
        </html>
        <?php
    }
    exit;
}
?>