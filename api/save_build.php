<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
$user = current_user();
if (!$user) { http_response_code(401); echo json_encode(['error'=>'Войдите в аккаунт, чтобы сохранять сборки.'], JSON_UNESCAPED_UNICODE); exit; }
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$items = $data['items'] ?? [];
$limit = build_limit($user['plan']);
$count = (int)db()->query('SELECT COUNT(*) FROM builds WHERE user_id=' . (int)$user['id'])->fetchColumn();
if ($limit !== null && $count >= $limit) { http_response_code(403); echo json_encode(['error'=>'Лимит Free достигнут. Перейдите на Standard или Pro.'], JSON_UNESCAPED_UNICODE); exit; }
if (!$items) { http_response_code(422); echo json_encode(['error'=>'Добавьте комплектующие.'], JSON_UNESCAPED_UNICODE); exit; }
$stmt = db()->prepare('INSERT INTO builds(user_id,name,components_json,total_price) VALUES(?,?,?,?)');
$stmt->execute([$user['id'], $data['name'] ?? 'Моя сборка', json_encode($items, JSON_UNESCAPED_UNICODE), (float)($data['total'] ?? 0)]);
echo json_encode(['ok'=>true, 'id'=>db()->lastInsertId()]);
