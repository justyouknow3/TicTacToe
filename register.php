<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = cleanInput($_POST["student_id"] ?? "");
    $name = cleanInput($_POST["name"] ?? "");
    $email = cleanInput($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = cleanInput($_POST["role"] ?? "Student");

    // Remove all non-numeric characters from student ID
    $student_id_digits = preg_replace('/\D/', '', $student_id);

    // Validate length and format student ID as XXXX-XXXXX
    if (strlen($student_id_digits) === 9) {
        $student_id = substr($student_id_digits, 0, 4) . '-' . substr($student_id_digits, 4);
    } else {
        $error = "Invalid Student ID format. Must be 9 digits.";
    }

    if ($error === "") {
        if ($student_id === "" || $name === "" || $email === "" || $password === "") {
            $error = "All fields are required.";
        } elseif (!isValidEmail($email)) {
            $error = "Invalid email format.";
        } elseif (!str_ends_with(strtolower($email), "@evsu.edu.ph")) {
            $error = "Email must be an @evsu.edu.ph address.";
        } elseif (!in_array($role, ["Student", "Officer"], true)) {
            $error = "Invalid role selected.";
        } else {
            // Check duplicate student ID or email
            $check = $conn->prepare("SELECT id FROM users WHERE student_id = ? OR email = ?");
            $check->bind_param("ss", $student_id, $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $error = "Student ID or email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $status = "Pending";

                $stmt = $conn->prepare("INSERT INTO users (student_id, name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $student_id, $name, $email, $hashed, $role, $status);

                if ($stmt->execute()) {
                    $success = "Registration submitted. Wait for admin approval.";
                } else {
                    $error = "Registration failed. Try again.";
                }
            }
        }
    }
}

include __DIR__ . "/includes/header.php";
?>

<!-- HTML Form -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Create Account</div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                <form method="post" onsubmit="return validateEmail();">
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" 
                               name="student_id" 
                               class="form-control" 
                               required 
                               placeholder="xxxx-xxxxx e.g 2022-32197"
                               oninput="formatStudentID(this)"
                               value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
                    </div>
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="example@evsu.edu.ph"></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="Student" <?php echo (($_POST['role'] ?? '') === 'Student') ? 'selected' : ''; ?>>Student</option>
                            <option value="Officer" <?php echo (($_POST['role'] ?? '') === 'Officer') ? 'selected' : ''; ?>>Officer</option>
                        </select>
                    </div>
                    <p>Already have an <a href="login.php">Account</a></p>
                    <button class="btn btn-primary" type="submit">Register</button>
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

function validateEmail() {
    const emailInput = document.getElementById('email').value.toLowerCase();
    if (!emailInput.endsWith('@evsu.edu.ph')) {
        alert("Email must be an @evsu.edu.ph address.");
        return false; // prevent form submission
    }
    return true;
}
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>