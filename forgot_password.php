<?php
session_start();
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/includes/functions.php";

$error = "";
$success = "";
$showOtp = false;
$showPassword = false;

// STEP 1: VERIFY + SEND OTP
if (isset($_POST['verify'])) {

    $student_id = cleanInput($_POST["student_id"] ?? "");
    $email = cleanInput($_POST["email"] ?? "");

    $student_id_digits = preg_replace('/\D/', '', $student_id);

    if (strlen($student_id_digits) === 9) {
        $student_id = substr($student_id_digits, 0, 4) . '-' . substr($student_id_digits, 4);
    } else {
        $error = "Invalid Student ID format.";
    }

    if ($error === "") {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email.";
        } elseif (!str_ends_with(strtolower($email), "@evsu.edu.ph")) {
            $error = "Email must be @evsu.edu.ph";
        } else {

            $stmt = $conn->prepare("SELECT id FROM users WHERE student_id=? AND email=?");
            $stmt->bind_param("ss", $student_id, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                // SAVE SESSION
                $_SESSION['reset_student_id'] = $student_id;
                $_SESSION['reset_email'] = $email;

                // GENERATE OTP
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;

                // SEND EMAIL (PHPMailer)
                require 'phpmailer/src/Exception.php';
                require 'phpmailer/src/PHPMailer.php';
                require 'phpmailer/src/SMTP.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'elychristian.bucasas@evsu.edu.ph';
                    $mail->Password = 'qpydnbiyqitzeqgi';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('elychristian.bucasas@evsu.edu.ph', 'EVSU System');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "OTP Code";
                    $mail->Body = "Your OTP is: <b>$otp</b>";

                    $mail->send();

                    $success = "OTP sent to your email.";
                    $showOtp = true;

                } catch (Exception $e) {
                    $error = "Mailer Error: {$mail->ErrorInfo}";
                }

            } else {
                $error = "Account not found.";
            }
        }
    }
}

// STEP 2: VERIFY OTP
if (isset($_POST['verify_otp'])) {

    if ($_POST['otp'] == $_SESSION['otp']) {
        $success = "OTP Verified!";
        $showOtp = true;
        $showPassword = true;
    } else {
        $error = "Invalid OTP.";
        $showOtp = true;
    }
}

// STEP 3: RESET PASSWORD
if (isset($_POST['reset_password'])) {

    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $student_id = $_SESSION['reset_student_id'];

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE student_id=?");
    $stmt->bind_param("ss", $new_password, $student_id);

    if ($stmt->execute()) {
        session_destroy();
        $success = "Password updated successfully!";
    } else {
        $error = "Failed to update password.";
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center">
<div class="col-md-5">
<div class="card">
<div class="card-header">Forgot Password</div>
<div class="card-body">

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="post" onsubmit="return formatAndValidateStudentID();">

    <!-- STUDENT ID -->
    <div class="mb-3">
        <label>Student ID</label>
        <input type="text" name="student_id" id="student_id" class="form-control"
               required oninput="formatStudentID(this)"
               value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
    </div>

    <!-- EMAIL -->
    <div class="mb-3">
        <label>EVSU Email</label>
        <input type="email" name="email" class="form-control" required
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
    </div>

    <!-- VERIFY BUTTON -->
    <button class="btn btn-primary" name="verify">Verify</button>

    <!-- OTP FIELD (HIDDEN FIRST) -->
    <?php if ($showOtp): ?>
    <div class="mt-3">
        <label>Enter OTP</label>
        <input type="text" name="otp" class="form-control" required>
        <button class="btn btn-success mt-2" name="verify_otp">Verify OTP</button>
    </div>
    <?php endif; ?>

    <!-- NEW PASSWORD FIELD -->
    <?php if ($showPassword): ?>
    <div class="mt-3">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>
        <button class="btn btn-danger mt-2" name="reset_password">Reset Password</button>
    </div>
    <?php endif; ?>

</form>

<p class="mt-2"><a href="login.php">Back to Login</a></p>

</div>
</div>
</div>
</div>

<script>
function formatStudentID(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.substring(0, 9);
    if (value.length > 4) {
        value = value.substring(0, 4) + '-' + value.substring(4);
    }
    input.value = value;
}

function formatAndValidateStudentID() {
    const input = document.getElementById('student_id');
    let value = input.value.replace(/\D/g, '');

    if (value.length !== 9) {
        alert("Invalid Student ID format.");
        return false;
    }

    input.value = value.substring(0, 4) + '-' + value.substring(4);
    return true;
}
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>