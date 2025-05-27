<?php
// Database connection
include 'db.php'; // Ensure this file sets up $conn properly

if (isset($_GET['token']) && !empty(trim($_GET['token']))) {
    $token = trim($_GET['token']);

    // Validate the token in admin_password_resets table
    $stmt = $conn->prepare("SELECT email FROM admin_password_resets WHERE token = ? AND expires > NOW()");
    if (!$stmt) {
        echo "Database error: " . $conn->error;
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Debugging: Check why the token is invalid
        $debugStmt = $conn->prepare("SELECT expires FROM admin_password_resets WHERE token = ?");
        $debugStmt->bind_param("s", $token);
        $debugStmt->execute();
        $debugResult = $debugStmt->get_result();
        if ($debugResult->num_rows > 0) {
            $row = $debugResult->fetch_assoc();
            error_log("Admin token found but expired. Expires: " . $row['expires'] . ", Current time: " . date('Y-m-d H:i:s'));
            echo "Token has expired.";
        } else {
            error_log("Admin token not found in admin_password_resets table: $token");
            echo "Invalid token.";
        }
        $debugStmt->close();
        exit;
    }

    $stmt->close();
} else {
    echo "No token provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FileSharePro</title>
    <link rel="stylesheet" href="index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Style the container */
.container {
    background: #ffffff;
    padding: 170px 50px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0 realist, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
    display: block;
    transition: transform 0.3s ease;
}

/* Slight hover effect on container */
.container:hover {
    transform: translateY(-5px);
}

/* Style the heading */
.container h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #1e3a8a; /* Dark blue */
    font-size: 28px;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Style the form */
#forgotPasswordForm {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Style the form group */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Style the label */
.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #4b5563; /* Gray */
}

/* Style the email input */
.form-control {
    width: 93%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    color: #1f2937;
    background: #f9fafb;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Input focus state */
.form-control:focus {
    outline: none;
    border-color: #3b82f6; /* Blue */
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.2);
}

/* Placeholder style */
.form-control::placeholder {
    color: #9ca3af;
}

/* Style the submit button */
.btn {
    padding: 12px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa); /* Gradient button */
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.1s ease, box-shadow 0.3s ease;
}

/* Button hover state */
.btn:hover {
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

/* Button active state */
.btn:active {
    transform: scale(0.98);
}

/* Button loading state */
.btn.loading {
    background: #9ca3af;
    cursor: not-allowed;
    box-shadow: none;
}

/* Style the response message */
#responseMessage {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    padding: 10px;
    border-radius: 5px;
    transition: opacity 0.3s ease;
}

/* Success message */
#responseMessage.success {
    background: #ecfdf5;
    color: #16a34a;
}

/* Error message */
#responseMessage.error {
    background: #fef2f2;
    color: #dc2626;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .container {
        padding: 25px 20px;
        max-width: 90%;
    }

    .container h2 {
        font-size: 24px;
    }

    .form-control, .btn {
        font-size: 14px;
        padding: 10px;
    }

    .form-group label {
        font-size: 13px;
    }
}
/* Style the container */
.container {
    background: #ffffff;
    
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    transition: transform 0.3s ease;
}

/* Hover effect for container */
.container:hover {
    transform: translateY(-3px);
}

/* Style the heading */
.container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1e40af;
    font-size: 24px;
    font-weight: 600;
}

/* Style the form */
#resetPasswordForm {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Style the form group */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Style the password input */
.form-group input[type="password"] {
    width: 92%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 16px;
    color: #1f2937;
    background: #f9fafb;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Input focus state */
.form-group input[type="password"]:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 6px rgba(59, 130, 246, 0.2);
}

/* Placeholder style */
.form-group input[type="password"]::placeholder {
    color: #9ca3af;
}

/* Style the submit button */
#resetPasswordForm button {
    padding: 12px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.1s ease, box-shadow 0.3s ease;
}

/* Button hover state */
#resetPasswordForm button:hover {
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Button active state */
#resetPasswordForm button:active {
    transform: scale(0.98);
}

/* Style the response message */
#responseMessage {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    padding: 8px;
    border-radius: 5px;
    transition: opacity 0.3s ease;
}

/* Success message */
#responseMessage.success {
    background: #ecfdf5;
    color: #16a34a;
}

/* Error message */
#responseMessage.error {
    background: #fef2f2;
    color: #dc2626;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .container {
        padding: 20px;
        max-width: 90%;
    }

    .container h2 {
        font-size: 20px;
    }

    .form-group input[type="password"],
    #resetPasswordForm button {
        font-size: 14px;
        padding: 10px;
    }
}
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <div class="container">
            <h1><a href="index.html" class="logo">FileSharePro</a></h1>
            <nav>
               
                <div class="dropdown">
                    <button class="dropbtn">Login</button>
                    <div class="dropdown-content">
                        <a href="login.html">User Login</a>
                        <a href="Admin_login.html">Admin Login</a>
                    </div>
                </div>
                <a href="register.html">Sign Up</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2>Reset Password</h2>
        <form id="resetPasswordForm" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
            <div class="form-group">
                <input type="password" name="new_password" placeholder="Enter new password" required />
            </div>
            <button type="submit">Reset Password</button>
        </form>
        <div id="responseMessage"></div>
    </div>

    <script>
    document.getElementById('resetPasswordForm').onsubmit = function(event) {
        event.preventDefault();

        const button = this.querySelector('button');
        button.classList.add('loading');
        button.disabled = true;

        const formData = new FormData(this);
        fetch('owner_reset_password_process.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            button.classList.remove('loading');
            button.disabled = false;

            const responseDiv = document.getElementById('responseMessage');
            responseDiv.innerHTML = `<p>${data.message}</p>`;
            if (data.success) {
                responseDiv.classList.add('success');
                responseDiv.classList.remove('error');

                const loginButton = document.createElement('button');
                loginButton.innerText = 'Go to Login';
                loginButton.className = 'btn btn-secondary';
                loginButton.onclick = function() {
                    window.location.href = 'Owner_login.html'; // Change to your actual login page
                };
                responseDiv.appendChild(loginButton);
            } else {
                responseDiv.classList.add('error');
                responseDiv.classList.remove('success');
            }
        })
        .catch(error => {
            button.classList.remove('loading');
            button.disabled = false;
            const responseDiv = document.getElementById('responseMessage');
            responseDiv.innerText = 'Error occurred: ' + error;
            responseDiv.classList.add('error');
            responseDiv.classList.remove('success');
        });
    };
    </script>
</body>
</html>