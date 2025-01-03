<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Sthom\Kernel\Kernel;

try {
    Kernel::boot();
} catch (Exception $e) {
    if ($_ENV['DEBUG'] === 'false') {
        echo "<h1>Une erreur est survenue</h1>";
        exit();
    } else {
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $code = $e->getCode();
        $trace = $e->getTraceAsString();
        include_once __DIR__ . '/../kernel/Error/debugger.php';
        exit(0);
    }
}
