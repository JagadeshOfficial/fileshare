<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - FileShare</title>
    <link rel="stylesheet" href="login.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1><a href="index.html" class="logo">FileSharePro</a></h1>
            <nav>
                <a href="login.html">Login</a>
            </nav>
        </div>
    </header>

    <!-- Reset Password Form -->
    <main class="login-container">
        <div class="login-form">
            <h2>Reset Password</h2>
            <form id="resetPasswordForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <div class="input-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                </div>
                <button type="submit" class="login-btn">Reset Password</button>
            </form>
            <p class="register-link">
                Back to <a href="login.html">Login</a>
            </p>
        </div>
    </main>

    <script>
        document.getElementById("resetPasswordForm").addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const password = formData.get("password");
            const confirmPassword = formData.get("confirm_password");

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }

            fetch("reset_password_handler.php", {
                method: "POST",
                body: formData,
            })
                .then((res) => res.text())
                .then((response) => {
                    if (response.trim() === "success") {
                        alert("Password reset successfully. You can now log in.");
                        window.location.href = "login.html";
                    } else if (response.trim() === "invalid_token") {
                        alert("Invalid or expired reset link.");
                    } else {
                        alert("Something went wrong. Please try again.");
                        console.error("Server response:", response);
                    }
                })
                .catch((err) => {
                    console.error("Error:", err);
                    alert("An error occurred. Please try again.");
                });
        });
    </script>
</body>
</html>