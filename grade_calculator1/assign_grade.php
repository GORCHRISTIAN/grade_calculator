<?php
include 'config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$student_id = intval($_GET['student_id']);
$student = $conn->query("SELECT * FROM students WHERE id=$student_id")->fetch_assoc();
$subjects = $conn->query("SELECT * FROM subjects");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['grades'] as $subject_id => $grade) {
        $grade = floatval($grade);
        // Check if grade exists
        $check = $conn->query("SELECT * FROM grades WHERE student_id=$student_id AND subject_id=$subject_id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE grades SET grade=$grade WHERE student_id=$student_id AND subject_id=$subject_id");
        } else {
            $conn->query("INSERT INTO grades (student_id, subject_id, grade) VALUES ($student_id, $subject_id, $grade)");
        }
    }
    header("Location: admin_dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
    <h3>Assign Grades to <?= htmlspecialchars($student['username']) ?></h3>
    <form method="POST">
        <?php while ($subj = $subjects->fetch_assoc()): ?>
            <div class="mb-2">
                <label><?= htmlspecialchars($subj['name']) ?></label>
                <input type="number" name="grades[<?= $subj['id'] ?>]" step="0.01" class="form-control w-25" />
            </div>
        <?php endwhile; ?>
        <button class="btn btn-success">Save Grades</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
