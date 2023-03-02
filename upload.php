<?php

$lf = __DIR__ . '/requests.log';
$dirSuff = '/uploads';
$dir = __DIR__ . $dirSuff;

$log = ['*********************************'];
$log[] = '------- URL -------';
$url = sprintf(
    '%s://%s%s',
    $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    $_SERVER['REQUEST_URI']
);
$log[] = $url;
$log[] = '------- REQUEST -------';
$log[] = sprintf('Method: %s', $_SERVER['REQUEST_METHOD']);
$log[] = sprintf('Headers:');
foreach ($_SERVER as $key => $header) {
    $log[] = sprintf('  - %s: %s', $key, $header);
}
$log[] = '------- GET -------';
foreach ($_GET as $key => $param) {
    $log[] = sprintf(
        '  - %s: %s',
        $key,
        is_scalar($param) ? $param : json_encode($param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}
$log[] = '------- POST -------';
foreach ($_POST as $key => $param) {
    $log[] = sprintf(
        '  - %s: %s',
        $key,
        is_scalar($param) ? $param : json_encode($param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}
$log[] = '------- FILES -------';
foreach ($_FILES as $key => $param) {
    $log[] = sprintf(
        '  - %s: %s',
        $key,
        is_scalar($param) ? $param : json_encode($param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}
$log[] = '------- COOKIES -------';
foreach ($_COOKIE as $key => $param) {
    $log[] = sprintf(
        '  - %s: %s',
        $key,
        is_scalar($param) ? $param : json_encode($param, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}

file_put_contents($lf, implode("\n", $log) . "\n\n", FILE_APPEND);

header('Content-Type: text/plain; charset=utf-8');

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = ($ext = explode('/', $file['type'])[1] ?? false) ? $ext : 'png';
    $rnd = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz', ceil(5 / strlen($x)))), 1, 5);
    $fn = sprintf('%s-%s.%s', date('Ymd'), $rnd, $ext);
    $fna = $dir . '/' . $fn;
    $url = dirname($url) . $dirSuff . '/' . $fn;

    if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
        echo "ERR: The '$ext' file type is unsupported to uploads for security reasons!";
    }

    if (move_uploaded_file($file['tmp_name'], $fna)) {
        echo "SUCCESS: $url";
    } else {
        echo "ERR: Internal error during file saving";
    }
} else {
    echo "ERR: No File provided";
}
