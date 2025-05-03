<?php
const PWD = "X8v^JQmUx&63')!";
const FROM = 'contact@betterweb.blog';
const TO = 'boumedyen.benboubekeur@gmail.com';

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

// Set headers to prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
];

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $name = isset($_POST['name']) ? filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $subject = isset($_POST['subject']) ? filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING) : '';
    $message = isset($_POST['message']) ? filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING) : '';

    // Validate data
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid';
    }

    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }

    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    // If there are validation errors
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }

    // Set up email recipient (change this to your email)
    $to = 'boumedyen.benboubekeur@gmail.com';

    // Set up email headers
    $headers = "From: $name <$email>"."\r\n";
    $headers .= "Reply-To: $email"."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Compose email content
    $emailContent = "
    <html lang='en-US'>
    <head>
        <title>Contact Form Submission</title>
    </head>
    <body>
        <h2>Contact Form Submission</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong></p>
        <p>".nl2br($message)."</p>
    </body>
    </html>
    ";

    // Attempt to send email
    $mailSent = true;

    // Save to database (optional - add your database code here)
    // Example:
    // $conn = new mysqli('localhost', 'username', 'password', 'database');
    // $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    // $stmt->bind_param("ssss", $name, $email, $subject, $message);
    // $stmt->execute();
    // $stmt->close();
    // $conn->close();

    // Set success response

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.hostinger.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = FROM;
    $mail->Password = PWD;
    $mail->setFrom(FROM, 'Contact Better Web');
    $mail->addAddress(TO, 'Boumedyen BenBoubekeur');
    $mail->Subject = $subject;
    $mail->msgHTML($emailContent);

    try {
        if ($mail->send()) {
            $response['success'] = true;
            $response['message'] = 'Your message has been sent successfully.';
        } else {
            $response['message'] = 'Failed to send email. Please try again later.';
        }
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $response['message'] = 'Failed to send email. Please try again later.';
    }

// Return JSON response
    echo json_encode($response);
} else {
    // If not POST request
    $response['message'] = 'Invalid request method';
}
