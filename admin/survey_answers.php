<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Admin"]);
require_once __DIR__ . "/../config/db.php";

$survey_id = (int)($_GET["survey_id"] ?? 0);

$answers = null;
if ($survey_id > 0) {
    $stmt = $conn->prepare("
        SELECT
            a.answered_at,
            u.name AS student_name,
            s.title AS survey_title,
            qo.question,
            o.option_text
        FROM survey_answers a
        JOIN users u ON u.id = a.user_id
        JOIN surveys s ON s.id = a.survey_id
        JOIN survey_options o ON o.id = a.option_id
        JOIN survey_questions qo ON qo.id = o.question_id
        WHERE a.survey_id = ?
        ORDER BY a.answered_at DESC
    ");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $answers = $stmt->get_result();
} else {
    // If no survey selected, show recent answers.
    $answers = $conn->query("
        SELECT
            a.answered_at,
            u.name AS student_name,
            s.title AS survey_title,
            qo.question,
            o.option_text
        FROM survey_answers a
        JOIN users u ON u.id = a.user_id
        JOIN surveys s ON s.id = a.survey_id
        JOIN survey_options o ON o.id = a.option_id
        JOIN survey_questions qo ON qo.id = o.question_id
        ORDER BY a.answered_at DESC
        LIMIT 200
    ");
}

include __DIR__ . "/../includes/header.php";
?>
<h3>Survey Answers</h3>

<?php if ($survey_id > 0): ?>
    <div class="alert alert-info mb-3">
        Showing answers for survey ID: <?php echo (int)$survey_id; ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Answered At</th>
                <th>Student</th>
                <th>Survey</th>
                <th>Question</th>
                <th>Selected Option</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($answers): while ($row = $answers->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["answered_at"]); ?></td>
                <td><?php echo htmlspecialchars($row["student_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["survey_title"]); ?></td>
                <td><?php echo htmlspecialchars($row["question"]); ?></td>
                <td><?php echo htmlspecialchars($row["option_text"]); ?></td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>

