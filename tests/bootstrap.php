<?php
$frameworkPath = __DIR__ . '/../framework';
$frameworkDir = basename($frameworkPath);
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname($frameworkPath));
}
require_once 'Core/Core.php';
