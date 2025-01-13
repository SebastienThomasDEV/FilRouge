<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Sthom\Kernel\Kernel;
use Sthom\Kernel\Error\ErrorHandler;

// Activate error handling
ErrorHandler::handle();

// Start the session
session_start();

// Boot the application
Kernel::boot();
