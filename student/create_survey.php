<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole(["Student"]);
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

$error = "";
$success = "";
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = cleanInput($_POST["title"] ?? "");
    $questions = $_POST["questions"] ?? [];

    // Limit: one survey per day for student.
    $limitCheck = $conn->prepare("SELECT id FROM surveys WHERE created_by = ? AND DATE(created_at) = CURDATE()");
    $limitCheck->bind_param("i", $user_id);
    $limitCheck->execute();
    $limitResult = $limitCheck->get_result();

    if ($limitResult->num_rows > 0) {
        $error = "You can only create one survey per day.";
    } elseif ($title === "" || !is_array($questions) || count($questions) < 1) {
        $error = "Title and at least one question are required.";
    } else {
        $validQuestions = [];
        foreach ($questions as $q) {
            $qText = cleanInput($q["text"] ?? "");
            $opts = $q["options"] ?? [];

            if (!is_array($opts)) {
                $opts = [];
            }

            $cleanOptions = [];
            foreach ($opts as $opt) {
                $optText = cleanInput($opt ?? "");
                if ($optText !== "") {
                    $cleanOptions[] = $optText;
                }
            }

            // Keep only valid question blocks.
            if ($qText !== "" && count($cleanOptions) >= 1) {
                $validQuestions[] = [
                    "text" => $qText,
                    "options" => $cleanOptions
                ];
            }
        }

        if (count($validQuestions) < 1) {
            $error = "Please add at least one valid question with at least one option.";
        } else {
        $conn->begin_transaction();
        try {
            $surveyStmt = $conn->prepare("INSERT INTO surveys (title, created_by) VALUES (?, ?)");
            $surveyStmt->bind_param("si", $title, $user_id);
            $surveyStmt->execute();
            $survey_id = $conn->insert_id;

            foreach ($validQuestions as $vq) {
                $questionStmt = $conn->prepare("INSERT INTO survey_questions (survey_id, question) VALUES (?, ?)");
                $questionStmt->bind_param("is", $survey_id, $vq["text"]);
                $questionStmt->execute();
                $question_id = $conn->insert_id;

                $optionStmt = $conn->prepare("INSERT INTO survey_options (question_id, option_text) VALUES (?, ?)");
                foreach ($vq["options"] as $optText) {
                    $optionStmt->bind_param("is", $question_id, $optText);
                    $optionStmt->execute();
                }
            }

            $conn->commit();
            $success = "Survey created successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to create survey.";
        }
        }
    }
}

include __DIR__ . "/../includes/header.php";
?>
<h3>Create Survey</h3>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<form method="post" class="card card-body gf-survey-editor" id="surveyForm">
    <div class="gf-editor-top">
        <label class="form-label gf-editor-top-label">Form Title</label>
        <input name="title" class="form-control gf-form-title-input" placeholder="Untitled form" required>
    </div>

    <div id="questionsContainer" class="gf-questions">
        <div class="gf-question-card" data-q-index="0">
            <div class="gf-question-head">
                <div class="gf-question-number">Question 1</div>
                <button type="button" class="btn btn-sm btn-outline-danger gf-mini-btn" onclick="removeQuestionBlock(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <input
                class="form-control gf-question-input"
                name="questions[0][text]"
                placeholder="Untitled Question"
                required
            >

            <div class="gf-options mt-3" id="options_0">
                <div class="gf-option-row">
                    <span class="gf-radio-preview"></span>
                    <input class="form-control gf-option-input" name="questions[0][options][]" placeholder="Option 1" required>
                </div>
                <div class="gf-option-row mt-2">
                    <span class="gf-radio-preview"></span>
                    <input class="form-control gf-option-input" name="questions[0][options][]" placeholder="Option 2" required>
                </div>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mt-3 gf-add-option-btn" onclick="addOption(0)">
                + Add option
            </button>
        </div>
    </div>

    <div class="mt-3">
        <button type="button" class="btn btn-primary w-100 gf-add-question-btn" onclick="addQuestionBlock()">
            <i class="bi bi-plus-circle"></i> Add question
        </button>
    </div>

    <button class="btn btn-primary w-100 mt-3">Create Survey</button>
</form>

<script>
    let questionIndex = 1;

    function addQuestionBlock() {
        const container = document.getElementById("questionsContainer");
        if (questionIndex >= 5) {
            alert("Maximum 5 questions for this demo UI.");
            return;
        }

        const qIndex = questionIndex;
        const div = document.createElement("div");
        div.className = "gf-question-card";
        div.setAttribute("data-q-index", qIndex);
        div.innerHTML = `
            <div class="gf-question-head">
                <div class="gf-question-number">Question ${qIndex + 1}</div>
                <button type="button" class="btn btn-sm btn-outline-danger gf-mini-btn" onclick="removeQuestionBlock(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <input
                class="form-control gf-question-input"
                name="questions[${qIndex}][text]"
                placeholder="Untitled Question"
                required
            >

            <div class="gf-options mt-3" id="options_${qIndex}">
                <div class="gf-option-row">
                    <span class="gf-radio-preview"></span>
                    <input class="form-control gf-option-input" name="questions[${qIndex}][options][]" placeholder="Option 1" required>
                </div>
                <div class="gf-option-row mt-2">
                    <span class="gf-radio-preview"></span>
                    <input class="form-control gf-option-input" name="questions[${qIndex}][options][]" placeholder="Option 2" required>
                </div>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mt-3 gf-add-option-btn" onclick="addOption(${qIndex})">
                + Add option
            </button>
        `;

        container.appendChild(div);
        questionIndex++;
    }

    function removeQuestionBlock(btn) {
        const block = btn.closest(".gf-question-card");
        if (block) block.remove();
    }

    function addOption(qIndex) {
        const container = document.getElementById("options_" + qIndex);
        const optionInputs = container.querySelectorAll(".gf-option-input");
        const optNum = optionInputs.length + 1;

        const row = document.createElement("div");
        row.className = "gf-option-row gf-option-row-extra mt-2";
        row.innerHTML = `
            <span class="gf-radio-preview"></span>
            <input class="form-control gf-option-input" name="questions[${qIndex}][options][]" placeholder="Option ${optNum}">
            <button type="button" class="btn btn-sm btn-outline-danger gf-remove-option-btn" onclick="removeOptionRow(this)">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(row);
    }

    function removeOptionRow(btn) {
        const row = btn.closest(".gf-option-row");
        if (row) row.remove();
    }
</script>
<?php include __DIR__ . "/../includes/footer.php"; ?>
