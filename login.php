<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = cleanInput($_POST["student_id"] ?? "");
    $password = $_POST["password"] ?? "";

    // Remove all non-numeric characters from student ID
    $student_id_digits = preg_replace('/\D/', '', $student_id);

    // Validate length and format student ID as XXXX-XXXXX
    if (strlen($student_id_digits) === 9) {
        $student_id = substr($student_id_digits, 0, 4) . '-' . substr($student_id_digits, 4);
    } else {
        $error = "Invalid Student ID format. Must be 9 digits.";
    }

    if ($error === "") {
        if ($student_id === "" || $password === "") {
            $error = "Student ID and password are required.";
        } else {
            // Query using student_id in correct format
            $stmt = $conn->prepare("SELECT id, name, student_id, email, password, role, status FROM users WHERE student_id = ?");
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (!password_verify($password, $user["password"])) {
                    $error = "Invalid credentials.";
                } elseif ($user["status"] !== "Approved") {
                    $error = "Account is not approved or already deactivated.";
                } else {
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["name"] = $user["name"];
                    $_SESSION["role"] = $user["role"];
                    $_SESSION["student_id"] = $user["student_id"]; // Optional for later use

                    redirectByRole($user["role"]);
                }
            } else {
                $error = "Invalid credentials.";
            }
        }
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <form method="post" onsubmit="return formatAndValidateStudentID();">
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="form-control" required placeholder="xxxx-xxxxx e.g 2022-32197" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" oninput="formatStudentID(this)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <p>
                        Forgot 
                        <a href="forgot_password.php"
                            style="color: red; text-decoration: none; font-size: 0.9rem; transition: 0.5s;"
                            onmouseover="this.style.color='black'; this.style.textDecoration='underline'; this.style.fontSize='1rem';"
                            onmouseout="this.style.color='red'; this.style.textDecoration='none'; this.style.fontSize='0.9rem';">
                            Password?
                        </a>
                    </p>
                    <button class="btn btn-primary" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formatStudentID(input) {
    // Remove all non-numeric characters
    let value = input.value.replace(/\D/g, '');

    // Limit to 9 digits
    value = value.substring(0, 9);

    // Format as XXXX-XXXXX
    if (value.length > 4) {
        value = value.substring(0, 4) + '-' + value.substring(4);
    }

    input.value = value;
}

function formatAndValidateStudentID() {
    const input = document.getElementById('student_id');
    let value = input.value.replace(/\D/g, '');
    
    if (value.length !== 9) {
        alert("Invalid Student ID format. Must be 9 digits.");
        return false;
    }

    // Format properly before submitting
    input.value = value.substring(0, 4) + '-' + value.substring(4);
    return true;
}
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>