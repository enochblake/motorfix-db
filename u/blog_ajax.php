<?php
/**
 * Motor Fix - Blog AJAX Backend (Fresh Build)
 * @version 3.0
 * @date October 23, 2025
 */

// === SETUP ===
ini_set('display_errors', 0);
error_reporting(E_ALL);

$base_path = dirname(__DIR__);
require_once $base_path . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers
header('Content-Type: application/json; charset=utf-8');

// Auth check
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;

if (!$user_id || $user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Paths
$upload_dir = __DIR__ . '/media/blog/';
$upload_url = '/u/media/blog/';

// Ensure upload directory
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}
if (!is_writable($upload_dir)) {
    @chmod($upload_dir, 0755);
}

// === ROUTER ===
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    switch ($action) {
        case 'fetch_posts':
            response(fetchPosts());
            break;

        case 'upload_media':
            uploadMedia();
            break;

        case 'get_post_details':
            response(getPostDetails());
            break;

        case 'save_post':
            response(savePost());
            break;

        case 'update_status':
            response(updateStatus());
            break;

        case 'delete_post':
            response(deletePost());
            break;

        default:
            response(['status' => 'error', 'message' => 'Unknown action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    response(['status' => 'error', 'message' => $e->getMessage()]);
}


// === FUNCTIONS ===

function response($data) {
    echo json_encode($data);
    exit;
}

function fetchPosts() {
    global $pdo, $user_id;
    
    $stmt = $pdo->prepare("
        SELECT 
            p.id, p.title, p.status, p.created_at, u.name as author_name,
            (SELECT bm.file_path FROM blog_media bm WHERE bm.post_id = p.id AND bm.is_featured = 1 LIMIT 1) as featured_image
        FROM blog_posts p
        JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    
    return [
        'status' => 'success',
        'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
}

function uploadMedia() {
    global $upload_dir, $upload_url, $user_id;
    
    $file = $_FILES['filepond'] ?? null;
    
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = $file ? getUploadError($file['error']) : 'No file provided';
        http_response_code(400);
        header('Content-Type: text/plain');
        echo $error;
        exit;
    }

    $name = $file['name'];
    $tmp = $file['tmp_name'];
    $size = $file['size'];

    // Validate extension
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov'];
    
    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        header('Content-Type: text/plain');
        echo "Invalid file type: $ext";
        exit;
    }

    // Validate size (50MB)
    if ($size > 50 * 1024 * 1024) {
        http_response_code(413);
        header('Content-Type: text/plain');
        echo "File too large (max 50MB)";
        exit;
    }

    // Validate MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp);
    finfo_close($finfo);
    
    $allowed_mime = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm', 'video/quicktime'
    ];
    
    if (!in_array($mime, $allowed_mime)) {
        http_response_code(400);
        header('Content-Type: text/plain');
        echo "Invalid MIME type: $mime";
        exit;
    }

    // Generate unique name
    $new_name = 'blog_' . uniqid('', true) . '.' . $ext;
    $dest = $upload_dir . $new_name;

    if (!@move_uploaded_file($tmp, $dest)) {
        http_response_code(500);
        header('Content-Type: text/plain');
        echo "Upload failed";
        exit;
    }

    @chmod($dest, 0644);

    // Return filename (FilePond expects plain text)
    http_response_code(200);
    header('Content-Type: text/plain');
    echo $new_name;
    exit;
}

function getPostDetails() {
    global $pdo, $user_id;
    
    $id = (int)($_GET['id'] ?? 0);
    
    if (!$id) {
        return ['status' => 'error', 'message' => 'Invalid post ID'];
    }

    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$id, $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        return ['status' => 'error', 'message' => 'Post not found'];
    }

    $stmt = $pdo->prepare("
        SELECT file_path, media_type, file_size, is_featured 
        FROM blog_media 
        WHERE post_id = ? 
        ORDER BY is_featured DESC, uploaded_at ASC
    ");
    $stmt->execute([$id]);
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'status' => 'success',
        'post' => $post,
        'media' => $media
    ];
}

function savePost() {
    global $pdo, $user_id, $upload_dir;
    
    $id = (int)($_POST['post_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $status = in_array($_POST['status'] ?? '', ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';
    $media_files = $_POST['media_files'] ?? [];
    $featured = $_POST['featured_image'] ?? null;

    if (!$title || !$content) {
        return ['status' => 'error', 'message' => 'Title and content required'];
    }

    $slug = createSlug($title);

    $pdo->beginTransaction();

    try {
        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE blog_posts 
                SET title = ?, slug = ?, content = ?, status = ?, published_at = ?, updated_at = NOW()
                WHERE id = ? AND author_id = ?
            ");
            $stmt->execute([
                $title, $slug, $content, $status,
                ($status === 'published' ? date('Y-m-d H:i:s') : null),
                $id, $user_id
            ]);
        } else {
            // Create
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (author_id, title, slug, content, status, published_at, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $user_id, $title, $slug, $content, $status,
                ($status === 'published' ? date('Y-m-d H:i:s') : null)
            ]);
            $id = $pdo->lastInsertId();
        }

        // Handle media
        if (!empty($media_files)) {
            $pdo->prepare("UPDATE blog_media SET is_featured = 0 WHERE post_id = ?")->execute([$id]);

            foreach ($media_files as $filename) {
                $filename = basename($filename);
                $path = $upload_dir . $filename;

                if (!file_exists($path)) continue;

                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $type = in_array($ext, ['mp4', 'webm', 'mov']) ? 'video' : 'image';
                $size = filesize($path);
                $is_featured = ($filename === $featured) ? 1 : 0;

                // Check if exists
                $check = $pdo->prepare("SELECT id FROM blog_media WHERE post_id = ? AND file_path = ?");
                $check->execute([$id, $filename]);

                if ($check->rowCount() === 0) {
                    $insert = $pdo->prepare("
                        INSERT INTO blog_media (post_id, author_id, media_type, file_path, file_size, is_featured, uploaded_at)
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $insert->execute([$id, $user_id, $type, $filename, $size, $is_featured]);
                } else {
                    if ($is_featured) {
                        $update = $pdo->prepare("UPDATE blog_media SET is_featured = 1 WHERE post_id = ? AND file_path = ?");
                        $update->execute([$id, $filename]);
                    }
                }
            }
        }

        $pdo->commit();

        return [
            'status' => 'success',
            'message' => 'Post saved',
            'post_id' => $id
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function updateStatus() {
    global $pdo, $user_id;
    
    $id = (int)($_POST['id'] ?? 0);
    $status = in_array($_POST['status'] ?? '', ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';

    if (!$id) {
        return ['status' => 'error', 'message' => 'Invalid post ID'];
    }

    $stmt = $pdo->prepare("
        UPDATE blog_posts 
        SET status = ?, published_at = ?, updated_at = NOW()
        WHERE id = ? AND author_id = ?
    ");
    $stmt->execute([
        $status,
        ($status === 'published' ? date('Y-m-d H:i:s') : null),
        $id, $user_id
    ]);

    return ['status' => 'success', 'message' => 'Status updated'];
}

function deletePost() {
    global $pdo, $user_id, $upload_dir;
    
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        return ['status' => 'error', 'message' => 'Invalid post ID'];
    }

    $pdo->beginTransaction();

    try {
        // Get media files
        $stmt = $pdo->prepare("SELECT file_path FROM blog_media WHERE post_id = ? AND author_id = ?");
        $stmt->execute([$id, $user_id]);
        $files = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete files
        foreach ($files as $file) {
            $path = $upload_dir . basename($file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        // Delete post
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ? AND author_id = ?");
        $stmt->execute([$id, $user_id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Post not found');
        }

        $pdo->commit();

        return ['status' => 'success', 'message' => 'Post deleted'];

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function createSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

function getUploadError($code) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file provided',
        UPLOAD_ERR_NO_TMP_DIR => 'Temp directory missing',
        UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
        UPLOAD_ERR_EXTENSION => 'Extension blocked'
    ];
    return $errors[$code] ?? 'Unknown error';
}