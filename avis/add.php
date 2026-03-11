<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Méthode non autorisée.');
}

$entrepriseId = filter_input(INPUT_POST, 'entreprise_id', FILTER_VALIDATE_INT);
$auteur = trim($_POST['auteur'] ?? '');
$note = filter_input(
    INPUT_POST,
    'note',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 5]]
);
$commentaire = trim($_POST['commentaire'] ?? '');

if (!$entrepriseId || $auteur === '' || !$note) {
    header('Location: /annuaire-entreprises/index.php');
    exit;
}

$sql = "SELECT id FROM entreprises WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $entrepriseId]);
$entreprise = $stmt->fetch();

if (!$entreprise) {
    header('Location: /annuaire-entreprises/index.php');
    exit;
}

$sql = "INSERT INTO avis (entreprise_id, auteur, note, commentaire)
        VALUES (:entreprise_id, :auteur, :note, :commentaire)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':entreprise_id' => $entrepriseId,
    ':auteur' => $auteur,
    ':note' => $note,
    ':commentaire' => $commentaire
]);

$sql = "SELECT COUNT(*) AS total, COALESCE(AVG(note), 0) AS moyenne
        FROM avis
        WHERE entreprise_id = :entreprise_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':entreprise_id' => $entrepriseId]);
$stats = $stmt->fetch();

$total = (int)($stats['total'] ?? 0);
$moyenne = round((float)($stats['moyenne'] ?? 0), 1);

$sql = "UPDATE entreprises
        SET note_moyenne = :moyenne, nombre_avis = :total
        WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':moyenne' => $moyenne,
    ':total' => $total,
    ':id' => $entrepriseId
]);

header('Location: /annuaire-entreprises/entreprises/show.php?id=' . $entrepriseId . '&review=1');
exit;