<?php
session_start();

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ['id' => 1, 'student_number' => '24174771', 'name' => 'Mark Jarden', 'section' => 'IT3K'],
        ['id' => 2, 'student_number' => '24174972', 'name' => 'Venice Canlas', 'section' => 'IT3K'],
        ['id' => 3, 'student_number' => '24174911', 'name' => 'Jerwin Esparza', 'section' => 'IT3K'],
        ['id' => 4, 'student_number' => '24174757', 'name' => 'Christine Espiritu', 'section' => 'IT3K'],
        ['id' => 5, 'student_number' => '24174661', 'name' => 'Eduard Bagangan', 'section' => 'IT3K'],
        ['id' => 6, 'student_number' => '24174973', 'name' => 'Elmer Cornelio', 'section' => 'IT3K'],
        ['id' => 7, 'student_number' => '24174770', 'name' => 'Bloom Diaz', 'section' => 'IT3K']
    ];
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if ($action === 'get_users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode($_SESSION['users']);
        exit;
    }

    if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentNumber = trim($_POST['student_number'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $section = trim($_POST['section'] ?? '');

        if (!empty($studentNumber) && !empty($name) && !empty($section)) {
            $newId = count($_SESSION['users']) > 0 ? max(array_column($_SESSION['users'], 'id')) + 1 : 1;
            $newUser = [
                'id' => $newId,
                'student_number' => $studentNumber,
                'name' => $name,
                'section' => $section
            ];
            $_SESSION['users'][] = $newUser;
            echo json_encode(['success' => true, 'message' => 'User added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student number, name, and section are required.']);
        }
        exit;
    }

    if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $studentNumber = trim($_POST['student_number'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $section = trim($_POST['section'] ?? '');

        if (empty($studentNumber) || empty($name) || empty($section)) {
            echo json_encode(['success' => false, 'message' => 'Student number, name, and section are required.']);
            exit;
        }

        $updated = false;
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] === $id) {
                $user['student_number'] = $studentNumber;
                $user['name'] = $name;
                $user['section'] = $section;
                $updated = true;
                break;
            }
        }
        unset($user);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? 'User updated successfully!' : 'User not found.'
        ]);
        exit;
    }

    if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $_SESSION['users'] = array_values(array_filter($_SESSION['users'], function($user) use ($id) {
            return $user['id'] !== $id;
        }));
        echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP CRUD System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
         .instructions { background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 12px 15px; margin: 20px 0; border-radius: 4px; }
         .instructions h3 { margin: 0 0 8px; color: #333; }
         .instructions ol { margin: 0; padding-left: 22px; line-height: 1.6; }
         .instructions p { margin: 10px 0 0; color: #666; font-size: 14px; }
         .secondary-btn { background-color: #6c757d; margin-left: 5px; }
         .secondary-btn:hover { background-color: #5a6268; }
         .edit-btn { background-color: #007bff; padding: 5px 10px; margin-right: 5px; }
         .edit-btn:hover { background-color: #0069d9; }
         .action-buttons { white-space: nowrap; }
