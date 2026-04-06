<?php
// ── Headers ───────────────────────────────────────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db.php';

// ── Router ────────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':    handleGet($id);    break;
    case 'POST':   handlePost();      break;
    case 'PUT':    handlePut($id);    break;
    case 'DELETE': handleDelete($id); break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}


// ── GET – Fetch all books or a single book ────────────────────────────────────
function handleGet(?int $id): void {
    $conn = getConnection();

    if ($id) {
        // GET /api.php?id=1 → single book
        $stmt = $conn->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book   = $result->fetch_assoc();

        if ($book) {
            echo json_encode(['success' => true, 'data' => $book]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
        }
        $stmt->close();
    } else {
        // GET /api.php → all books (optional filters: ?genre=Fiction&available=1)
        $where  = [];
        $params = [];
        $types  = '';

        if (!empty($_GET['genre'])) {
            $where[]  = 'genre = ?';
            $params[] = $_GET['genre'];
            $types   .= 's';
        }
        if (isset($_GET['available'])) {
            $where[]  = 'available = ?';
            $params[] = (int)$_GET['available'];
            $types   .= 'i';
        }
        if (!empty($_GET['search'])) {
            $where[]  = '(title LIKE ? OR author LIKE ?)';
            $params[] = '%' . $_GET['search'] . '%';
            $params[] = '%' . $_GET['search'] . '%';
            $types   .= 'ss';
        }

        $sql = 'SELECT * FROM books';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY created_at DESC';

        $stmt = $conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'count' => count($books), 'data' => $books]);
        $stmt->close();
    }

    $conn->close();
}


// ── POST – Add a new book ─────────────────────────────────────────────────────
function handlePost(): void {
    $body = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($body['title']) || empty($body['author'])) {
        http_response_code(400);
        echo json_encode(['error' => 'title and author are required']);
        return;
    }

    $title     = trim($body['title']);
    $author    = trim($body['author']);
    $genre     = trim($body['genre']     ?? 'Unknown');
    $year      = !empty($body['year'])   ? (int)$body['year'] : null;
    $available = isset($body['available']) ? (int)$body['available'] : 1;

    $conn = getConnection();
    $stmt = $conn->prepare(
        'INSERT INTO books (title, author, genre, year, available) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('sssii', $title, $author, $genre, $year, $available);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Book added successfully',
            'id'      => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add book']);
    }

    $stmt->close();
    $conn->close();
}


// ── PUT – Update an existing book ─────────────────────────────────────────────
function handlePut(?int $id): void {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'id is required for update — use ?id=1']);
        return;
    }

    $body = json_decode(file_get_contents('php://input'), true);

    if (empty($body)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields provided to update']);
        return;
    }

    // Only update fields that are provided
    $allowed = ['title', 'author', 'genre', 'year', 'available'];
    $set     = [];
    $params  = [];
    $types   = '';

    foreach ($allowed as $field) {
        if (array_key_exists($field, $body)) {
            $set[]    = "$field = ?";
            $params[] = $body[$field];
            $types   .= ($field === 'year' || $field === 'available') ? 'i' : 's';
        }
    }

    if (empty($set)) {
        http_response_code(400);
        echo json_encode(['error' => 'No valid fields to update']);
        return;
    }

    $params[] = $id;
    $types   .= 'i';

    $conn = getConnection();
    $stmt = $conn->prepare('UPDATE books SET ' . implode(', ', $set) . ' WHERE id = ?');
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found or no changes made']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update book']);
    }

    $stmt->close();
    $conn->close();
}


// ── DELETE – Remove a book ────────────────────────────────────────────────────
function handleDelete(?int $id): void {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'id is required for delete — use ?id=1']);
        return;
    }

    $conn = getConnection();
    $stmt = $conn->prepare('DELETE FROM books WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete book']);
    }

    $stmt->close();
    $conn->close();
}
