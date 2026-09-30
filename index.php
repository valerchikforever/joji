<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
$page = $_GET['page'] ?? 'configurator';
if ($page === 'logout') { session_destroy(); header('Location: ?page=configurator'); exit; }
$allowed = ['configurator','login','register','cabinet','builds'];
if (!in_array($page, $allowed, true)) $page = 'configurator';
require __DIR__ . '/pages/' . $page . '.php';
