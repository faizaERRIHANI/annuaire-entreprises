<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Méthode non autorisée.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: /annuaire-entreprises/index.php');
    exit;
}

$sql = "SELECT id, nom FROM entreprises WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$entreprise = $stmt->fetch();

if (!$entreprise) {
    header('Location: /annuaire-entreprises/index.php');
    exit;
}

$sql = "DELETE FROM entreprises WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

header('Location: /annuaire-entreprises/index.php?deleted=1');
exit;