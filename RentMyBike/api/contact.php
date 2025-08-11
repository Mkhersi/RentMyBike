<?php
// api/contact.php
header('Content-Type: application/json; charset=utf-8');

// Buffer any stray output so we can return clean JSON
ob_start();

try {
    // Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        ob_clean();
        echo json_encode(['success'=>false,'error'=>'This action is not available. Please retry']);
        exit;
    }

    // Inputs
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    $errors = [];
    if ($name === '')  $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'We require your valid email address.';
    if ($message === '') $errors[] = 'The message box is empty. Please type your message.';

    if ($errors) {
        http_response_code(400);
        ob_clean();
        echo json_encode(['success'=>false,'error'=>implode(' ', $errors)]);
        exit;
    }

    // Try to send email (often disabled on dev)
    $to      = 'support@rentmybike.io';
    $subject = "Contact form from {$name}";
    $body    = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
    $headers = "From: {$email}\r\nReply-To: {$email}";
    $sent    = @mail($to, $subject, $body, $headers);

    // Fallback: log to file
    if (!$sent) {
        $dir = __DIR__ . '/../../logs';
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new RuntimeException('There was an error creating the logs dictionary');
            }
        }
        $logFile = $dir . '/contact.log';
        $line = sprintf("[%s] From: %s <%s>\n%s\n\n", date('Y-m-d H:i'), $name, $email, $message);
        if (@file_put_contents($logFile, $line, FILE_APPEND) === false) {
            throw new RuntimeException('There was an error writing to contact.log');
        }
    }

    ob_clean();
    echo json_encode(['success'=>true]);

} catch (Throwable $e) {
    http_response_code(500);
    ob_clean();
    echo json_encode(['success'=>false,'error'=>'There was a server error. Please retry']);
}
