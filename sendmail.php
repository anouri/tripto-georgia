<?php
// Prevent direct access via browser GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit;
}

// Set JSON header response for AJAX
header('Content-Type: application/json');

// 1. Sanitize and validate input fields
$name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(["status" => "error", "message" => "Please fill in all fields correctly."]);
    exit;
}

// 2. Setup Email Headers
$to      = "info@tripto-georgia.com";
$cc      = "r.rezam@yahoo.com";
$from    = "Trip to Georgia <info@tripto-georgia.com>";

// Standard security and formatting headers (HTML format)
$headers   = array();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "From: {$from}";
$headers[] = "Cc: {$cc}";
$headers[] = "Reply-To: {$name} <{$email}>";
$headers[] = "X-Mailer: PHP/" . phpversion();

// 3. Create a clean HTML Email Body
$email_subject = "New Inquiry: " . $subject;

$email_body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .header { background-color: #2c3e50; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .label { font-weight: bold; color: #e74c3c; }
        .footer { background-color: #f8f9fa; text-align: center; padding: 10px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Website Inquiry</h2>
        </div>
        <div class='content'>
            <div class='field'><span class='label'>Name:</span><br>{$name}</div>
            <div class='field'><span class='label'>Email:</span><br>{$email}</div>
            <div class='field'><span class='label'>Subject:</span><br>{$subject}</div>
            <div class='field'><span class='label'>Message:</span><br>" . nl2br($message) . "</div>
        </div>
        <div class='footer'>
            Sent from tripto-georgia.com
        </div>
    </div>
</body>
</html>
";

// 4. Send the Email using the secure envelope sender parameter (-f)
$success = mail($to, $email_subject, $email_body, implode("\r\n", $headers), "-finfo@tripto-georgia.com");

if ($success) {
    echo json_encode(["status" => "success", "message" => "Message sent successfully."]);
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["status" => "error", "message" => "Mail server failed to send message."]);
}
?>
