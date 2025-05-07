<?php
// File: index.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$postsFile = 'posts.json';

// Handle POST (create new post)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';

    if ($title && $body) {
        $posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
        $newPost = [
            'id' => count($posts) + 1,
            'title' => $title,
            'body' => $body,
        ];
        $posts[] = $newPost;
        file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'post' => $newPost]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Title and body required']);
    }
    exit;
}

// Handle GET (return posts)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($postsFile)) {
        echo file_get_contents($postsFile);
    } else {
        echo json_encode([]);
    }
    exit;
}
?>