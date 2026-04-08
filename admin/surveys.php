<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Admin"]);
require_once __DIR__ . "/../config/db.php";

$surveys = $conn->query("
    SELECT
        s.id,
        s.title,
        s.created_at,
        u.name AS creator_name,
        (SELECT COUNT(*) FROM survey_questions q WHERE q.survey_id = s.id) AS question_count,
        (SELECT COUNT(*) FROM survey_options o
            JOIN survey_questions q ON q.id = o.question_id
            WHERE q.survey_id = s.id
        ) AS option_count,
        (SELECT COUNT(*) FROM survey_answers a WHERE a.survey_id = s.id) AS answer_count
    FROM surveys s
    LEFT JOIN users u ON u.id = s.created_by
    ORDER BY s.created_at DESC
");

include __DIR__ . "/../includes/header.php";
?>
<h3>Surveys & Answers</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Survey Title</th>
                <th>Created At</th>
                <th>Creator</th>
                <th>Questions</th>
                <th>Options</th>
                <th>Answers</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($s = $surveys->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($s["title"]); ?></td>
                <td><?php echo htmlspecialchars($s["created_at"]); ?></td>
                <td><?php echo htmlspecialchars($s["creator_name"]); ?></td>
                <td><?php echo (int)$s["question_count"]; ?></td>
                <td><?php echo (int)$s["option_count"]; ?></td>
                <td><?php echo (int)$s["answer_count"]; ?></td>
                <td>
                    <a class="btn btn-sm btn-primary" href="/feeds/admin/survey_answers.php?survey_id=<?php echo (int)$s["id"]; ?>">
                        View Answers
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>

