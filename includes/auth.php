<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../config/database.php';

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $stmt = db()->prepare('SELECT u.*, COALESCE(s.plan, "free") AS plan FROM users u LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = "active" WHERE u.id = ?');
    $stmt->execute([(int)$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}
function require_auth(): array {
    $user = current_user();
    if (!$user) { header('Location: ?page=login'); exit; }
    return $user;
}
function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Недействительный CSRF-токен'); } }
function plan_label(string $plan): string { return ['free'=>'Free','standard'=>'Standard','pro'=>'Pro'][$plan] ?? 'Free'; }
function build_limit(string $plan): ?int { return $plan === 'free' ? 3 : null; }
