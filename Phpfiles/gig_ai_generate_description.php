<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_error_local.log');

function json_error($message, $statusCode = 200, $details = '')
{
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
    }

    $result = array('error' => $message);

    if ($details !== '') {
        $result['details'] = $details;
    }

    echo json_encode($result);
    exit;
}

register_shutdown_function(function () {
    $err = error_get_last();

    if ($err && in_array($err['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        error_log(
            'Fatal error: ' . $err['message'] .
            ' in ' . $err['file'] .
            ' on line ' . $err['line']
        );

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode(array(
            'error' => 'Fatal error in PHP endpoint.',
            'details' => $err['message'] . ' (line ' . $err['line'] . ')'
        ));
    }
});

if (
    !isset($_SESSION['emailz']) ||
    !isset($_SESSION['btypez']) ||
    $_SESSION['btypez'] !== 'Designer'
) {
    json_error('Unauthorized. Please log in as Designer again.', 401);
}

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$tone = isset($_POST['tone']) ? trim($_POST['tone']) : 'professional';
$regenCount = isset($_POST['regen_count']) ? (int) $_POST['regen_count'] : 0;

if ($title === '' || $tags === '') {
    json_error('Gig Title and Tags are required.', 400);
}

$apiKey = '';
$configFile = __DIR__ . '/config_openai.php';

if (file_exists($configFile)) {
    require_once $configFile;

    if (defined('OPENAI_API_KEY')) {
        $apiKey = trim(OPENAI_API_KEY);
    }
}

if ($apiKey === '') {
    $envKey = getenv('OPENAI_API_KEY');

    if ($envKey !== false) {
        $apiKey = trim($envKey);
    }
}

if ($apiKey === '') {
    json_error(
        'OpenAI API key not found.',
        500,
        'Create phpfiles/config_openai.php and define OPENAI_API_KEY, or configure OPENAI_API_KEY for the Apache/PHP process.'
    );
}

if (!function_exists('curl_init')) {
    json_error(
        'PHP cURL extension is not enabled.',
        500,
        'Enable the curl extension in the php.ini used by Apache, then restart Apache.'
    );
}

$allowedTones = array('professional', 'friendly', 'premium', 'bold');
if (!in_array($tone, $allowedTones, true)) {
    $tone = 'professional';
}

$prompt =
"You are an expert freelancer marketplace copywriter.
Write only the gig description. Do not return JSON and do not use markdown code fences.
Length: 120-200 words.

Include:
- A 2-3 sentence introduction
- 3-6 concise bullet-point deliverables
- A simple 2-4 step process
- A short call to action

When regen_count is greater than zero, produce a noticeably different variation.

Gig Title: {$title}
Tags: {$tags}
Category: {$category}
Tone: {$tone}
regen_count: {$regenCount}";

$payload = array(
    'model' => 'gpt-4o-mini',
    'input' => $prompt,
    'max_output_tokens' => 500,
    'store' => false
);

$payloadJson = json_encode($payload);

if ($payloadJson === false) {
    $jsonError = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Unknown JSON encoding error';
    json_error('Could not encode the OpenAI request.', 500, $jsonError);
}

$ch = curl_init('https://api.openai.com/v1/responses');

curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ),
    CURLOPT_POSTFIELDS => $payloadJson,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 60
));

$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrNo = curl_errno($ch);
$curlErr = curl_error($ch);

curl_close($ch);

if ($response === false || $curlErrNo !== 0) {
    error_log('OpenAI cURL error ' . $curlErrNo . ': ' . $curlErr);

    $hint = $curlErr;

    if (
        stripos($curlErr, 'certificate') !== false ||
        stripos($curlErr, 'SSL') !== false
    ) {
        $hint .= ' Check the CA certificate/curl.cainfo setting in the php.ini used by Apache.';
    }

    json_error('Could not connect to OpenAI.', 502, $hint);
}

$decoded = json_decode($response, true);

if (!is_array($decoded)) {
    error_log('OpenAI invalid JSON response: ' . $response);
    json_error('OpenAI returned an invalid response.', 502);
}

if ($httpCode < 200 || $httpCode >= 300) {
    error_log('OpenAI HTTP ' . $httpCode . ': ' . $response);

    $apiMessage = '';

    if (
        isset($decoded['error']) &&
        is_array($decoded['error']) &&
        isset($decoded['error']['message'])
    ) {
        $apiMessage = $decoded['error']['message'];
    }

    if ($apiMessage === '') {
        $apiMessage = 'See php_error_local.log for the full OpenAI response.';
    }

    echo json_encode(array(
        'error' => 'OpenAI request failed.',
        'status' => $httpCode,
        'details' => $apiMessage
    ));
    exit;
}

$desc = '';

if (isset($decoded['output']) && is_array($decoded['output'])) {
    foreach ($decoded['output'] as $item) {
        if (
            isset($item['type']) &&
            $item['type'] === 'message' &&
            isset($item['content']) &&
            is_array($item['content'])
        ) {
            foreach ($item['content'] as $content) {
                if (
                    isset($content['type']) &&
                    $content['type'] === 'output_text' &&
                    isset($content['text'])
                ) {
                    $desc .= $content['text'];
                }
            }
        }
    }
}

$desc = trim($desc);

if ($desc === '') {
    error_log('OpenAI response parse failed: ' . $response);
    json_error(
        'AI returned empty content.',
        502,
        'Check php_error_local.log for the returned OpenAI response.'
    );
}

echo json_encode(
    array('description' => $desc),
    JSON_UNESCAPED_UNICODE
);
