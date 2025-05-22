<?php
include 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$result = $conn->query("SELECT * FROM students WHERE id = $student_id");

if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    // No student found, logout and redirect
    session_destroy();
    header("Location: login.php?error=StudentNotFound");
    exit;
}

// Get grades
$sql = "
    SELECT g.*, s.name AS subject_name
    FROM grades g
    JOIN subjects s ON g.subject_id = s.id
    WHERE g.student_id = $student_id
";

$grades = $conn->query($sql);

$total = 0;
$count = 0;

function getLetterGrade($gwa) {
    if ($gwa >= 97) return "A+";
    elseif ($gwa >= 93) return "A";
    elseif ($gwa >= 90) return "A-";
    elseif ($gwa >= 87) return "B+";
    elseif ($gwa >= 83) return "B";
    elseif ($gwa >= 80) return "B-";
    elseif ($gwa >= 77) return "C+";
    elseif ($gwa >= 73) return "C";
    elseif ($gwa >= 70) return "C-";
    elseif ($gwa >= 60) return "D";
    else return "F";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="toggle_theme.js"></script>
    <style>
        .dark-mode {
            background-color: #121212 !important;
            color: #eee !important;
        }
        .dark-mode input, .dark-mode table {
            background-color: #222 !important;
            color: #eee !important;
            border: 1px solid #555;
        }
    </style>
</head>
<body class="p-3">
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($student['username']) ?>!</h2>
    <a href="logout.php" class="btn btn-danger float-end">Logout</a>

    <hr>
    <h4>Your Grades</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Grade</th>
                <th>Date Assigned</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $grades->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    <td><?= $row['grade'] ?></td>
                    <td><?= date('F j, Y', strtotime($row['date_created'])) ?></td>
                </tr>
                <?php
                    $total += $row['grade'];
                    $count++;
                ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($count > 0): 
        $gwa = round($total / $count, 2);
        $letter = getLetterGrade($gwa);
    ?>
        <h5>Final GWA: <strong><?= $gwa ?></strong></h5>
        <h5>Letter Grade: <strong><?= $letter ?></strong></h5>
    <?php else: ?>
        <div class="alert alert-warning">No grades available yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
