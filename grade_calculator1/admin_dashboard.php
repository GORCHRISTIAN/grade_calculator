<?php
include 'config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle adding subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_name'])) {
    $subject_name = $conn->real_escape_string($_POST['subject_name']);
    $conn->query("INSERT INTO subjects (name) VALUES ('$subject_name')");
}

// Fetch students
$students = $conn->query("SELECT * FROM students");

// Fetch subjects
$subjects = $conn->query("SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="toggle_theme.js"></script>
</head>
<body class="p-3">
<div class="container">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-danger float-end">Logout</a>

    <hr>
    <h4>Add New Subject</h4>
    <form method="POST" class="mb-4">
        <input type="text" name="subject_name" required class="form-control w-50 d-inline" placeholder="Subject Name" />
        <button class="btn btn-success">Add</button>
    </form>

    <h4>All Subjects</h4>
    <ul>
        <?php while ($row = $subjects->fetch_assoc()): ?>
            <li><?= htmlspecialchars($row['name']) ?></li>
        <?php endwhile; ?>
    </ul>

    <h4>All Students</h4>
    <table class="table table-bordered">
        <thead><tr><th>Username</th><th>Assign Grade</th></tr></thead>
        <tbody>
        <?php while ($student = $students->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($student['username']) ?></td>
                <td><a href="assign_grade.php?student_id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Assign</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
