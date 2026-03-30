<?php
session_start();
header('Content-Type: application/json');

// user must be logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

require_once 'db.php';

$user_id = (int) $_SESSION['user_id'];
$action  = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    //List tasks
    case 'list':
        $stmt = $conn->prepare(
            "SELECT id, subject, title, completed, created_at 
             FROM tasks 
             WHERE user_id = ? 
             ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = [
                'id'        => (int)$row['id'],
                'subject'   => $row['subject'],
                'title'     => $row['title'],
                'completed' => (bool)$row['completed'],
                'created_at'=> $row['created_at']
            ];
        }
        $stmt->close();
        echo json_encode(['success' => true, 'tasks' => $tasks]);
        break;

    // Add task
    case 'add':
        $subject = trim($_POST['subject'] ?? '');
        $title   = trim($_POST['title'] ?? '');

        if (empty($subject) || empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Subject and task name are required.']);
            break;
        }

        $stmt = $conn->prepare(
            "INSERT INTO tasks (user_id, subject, title) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $subject, $title);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'task' => [
                    'id'        => (int)$stmt->insert_id,
                    'subject'   => $subject,
                    'title'     => $title,
                    'completed' => false
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add task.']);
        }
        $stmt->close();
        break;

    // Toggle task completion
    case 'toggle':
        $task_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$task_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
            break;
        }

        // Flip completed: 0→1, 1→0 
        $stmt = $conn->prepare(
            "UPDATE tasks SET completed = NOT completed 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $task_id, $user_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied.']);
        }
        $stmt->close();
        break;

    // delete task
    case 'delete':
        $task_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$task_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
            break;
        }

        $stmt = $conn->prepare(
            "DELETE FROM tasks WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $task_id, $user_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied.']);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}

$conn->close();
?>
