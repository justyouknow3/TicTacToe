<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Student"]);
require_once __DIR__ . "/../config/db.php";

$user_id = $_SESSION["user_id"];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $survey_id = (int)($_POST["survey_id"] ?? 0);
    $answers = $_POST["answer_option"] ?? [];

    if ($survey_id > 0 && is_array($answers) && count($answers) > 0) {
        $savedCount = 0;
        foreach ($answers as $question_id => $option_id) {
            $question_id = (int)$question_id;
            $option_id = (int)$option_id;
            if ($question_id <= 0 || $option_id <= 0) {
                continue;
            }

            // Validate that the selected option belongs to this question and survey.
            $validate = $conn->prepare("
                SELECT 1
                FROM survey_options o
                JOIN survey_questions q ON q.id = o.question_id
                WHERE q.survey_id = ? AND q.id = ? AND o.id = ?
                LIMIT 1
            ");
            $validate->bind_param("iii", $survey_id, $question_id, $option_id);
            $validate->execute();
            $valid = $validate->get_result();

            if ($valid->num_rows !== 1) {
                continue;
            }

            // Prevent duplicate answers for the same question.
            $check = $conn->prepare("SELECT id FROM survey_answers WHERE user_id = ? AND survey_id = ? AND question_id = ?");
            $check->bind_param("iii", $user_id, $survey_id, $question_id);
            $check->execute();
            $exists = $check->get_result();

            if ($exists->num_rows > 0) {
                continue;
            }

            $insert = $conn->prepare("INSERT INTO survey_answers (user_id, survey_id, question_id, option_id) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiii", $user_id, $survey_id, $question_id, $option_id);
            $insert->execute();
            $savedCount++;
        }

        if ($savedCount > 0) {
            $msg = "Survey answers saved.";
        } else {
            $msg = "No new answers were saved (you may have answered already).";
        }
    }
}

$rows = $conn->query("
    SELECT
        s.id AS survey_id,
        s.title AS survey_title,
        s.created_at,
        q.id AS question_id,
        q.question,
        o.id AS option_id,
        o.option_text
    FROM surveys s
    JOIN survey_questions q ON q.survey_id = s.id
    JOIN survey_options o ON o.question_id = q.id
    ORDER BY s.created_at DESC, q.id ASC, o.id ASC
");

$surveys = [];
while ($r = $rows->fetch_assoc()) {
    $sid = (int)$r["survey_id"];
    if (!isset($surveys[$sid])) {
        $surveys[$sid] = [
            "id" => $sid,
            "title" => $r["survey_title"],
            "created_at" => $r["created_at"],
            "questions" => []
        ];
    }
    $qid = (int)$r["question_id"];
    if (!isset($surveys[$sid]["questions"][$qid])) {
        $surveys[$sid]["questions"][$qid] = [
            "id" => $qid,
            "question" => $r["question"],
            "options" => []
        ];
    }
    $surveys[$sid]["questions"][$qid]["options"][] = [
        "id" => (int)$r["option_id"],
        "text" => $r["option_text"]
    ];
}

include __DIR__ . "/../includes/header.php";
?>
<h3>Available Surveys</h3>
<?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php foreach ($surveys as $survey): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5><?php echo htmlspecialchars($survey["title"]); ?></h5>

            <form method="post">
                <input type="hidden" name="survey_id" value="<?php echo (int)$survey["id"]; ?>">

                <?php foreach ($survey["questions"] as $question): ?>
                    <div class="mt-3">
                        <label class="form-label fw-semibold"><?php echo htmlspecialchars($question["question"]); ?></label>
                        <?php foreach ($question["options"] as $opt): ?>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="answer_option[<?php echo (int)$question["id"]; ?>]"
                                    required
                                    value="<?php echo (int)$opt["id"]; ?>"
                                    id="q_<?php echo (int)$question["id"]; ?>_opt_<?php echo (int)$opt["id"]; ?>"
                                >
                                <label class="form-check-label" for="q_<?php echo (int)$question["id"]; ?>_opt_<?php echo (int)$opt["id"]; ?>">
                                    <?php echo htmlspecialchars($opt["text"]); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <button class="btn btn-sm btn-primary mt-3">Submit Answers</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
<?php include __DIR__ . "/../includes/footer.php"; ?>
