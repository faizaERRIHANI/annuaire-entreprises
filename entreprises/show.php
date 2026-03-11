<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    die("Entreprise introuvable.");
}

$sql = "SELECT * FROM entreprises WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$entreprise = $stmt->fetch();

if (!$entreprise) {
    http_response_code(404);
    die("Entreprise introuvable.");
}

$sql = "SELECT * FROM avis WHERE entreprise_id = :id ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$avisList = $stmt->fetchAll();

$pageTitle = $entreprise['nom'] . " - Fiche entreprise";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="alert alert-success">
                Entreprise mise à jour avec succès.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['review']) && $_GET['review'] == 1): ?>
            <div class="alert alert-success">
                Avis ajouté avec succès.
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0"><?= htmlspecialchars($entreprise['nom']) ?></h1>

            <div class="d-flex gap-2">
                <a href="/annuaire-entreprises/entreprises/edit.php?id=<?= (int)$entreprise['id'] ?>" class="btn btn-warning">
                    Modifier
                </a>

                <form method="POST" action="/annuaire-entreprises/entreprises/delete.php" onsubmit="return confirm('Voulez-vous vraiment supprimer cette entreprise ?');">
                    <input type="hidden" name="id" value="<?= (int)$entreprise['id'] ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>

                <a href="/annuaire-entreprises/index.php" class="btn btn-secondary">
                    Retour
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <?php if (!empty($entreprise['logo'])): ?>
                    <div class="mb-3">
                        <img
                            src="/annuaire-entreprises/uploads/logos/<?= htmlspecialchars($entreprise['logo']) ?>"
                            alt="Logo de <?= htmlspecialchars($entreprise['nom']) ?>"
                            style="max-width: 180px; height: auto;"
                        >
                    </div>
                <?php endif; ?>

                <p><strong>Catégorie :</strong> <?= htmlspecialchars($entreprise['categorie'] ?? '') ?></p>
                <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($entreprise['adresse'] ?? '')) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($entreprise['telephone'] ?? '') ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($entreprise['email'] ?? '') ?></p>

                <p>
                    <strong>Site web :</strong>
                    <?php if (!empty($entreprise['site_web'])): ?>
                        <a href="<?= htmlspecialchars($entreprise['site_web']) ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($entreprise['site_web']) ?>
                        </a>
                    <?php else: ?>
                        Non renseigné
                    <?php endif; ?>
                </p>

                <p><strong>Description :</strong></p>
                <div class="border rounded p-3 bg-light">
                    <?= !empty($entreprise['description']) ? nl2br(htmlspecialchars($entreprise['description'])) : 'Aucune description.' ?>
                </div>

                <hr>

                <p>
                    <strong>Note moyenne :</strong>
                    <?= htmlspecialchars((string)($entreprise['note_moyenne'] ?? '0')) ?>/5
                </p>

                <p>
                    <strong>Nombre d’avis :</strong>
                    <?= htmlspecialchars((string)($entreprise['nombre_avis'] ?? '0')) ?>
                </p>

                <?php if (!empty($entreprise['latitude']) && !empty($entreprise['longitude'])): ?>
                    <p>
                        <strong>Coordonnées :</strong>
                        <?= htmlspecialchars((string)$entreprise['latitude']) ?>,
                        <?= htmlspecialchars((string)$entreprise['longitude']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3">Ajouter un avis</h2>

                <form method="POST" action="/annuaire-entreprises/avis/add.php">
                    <input type="hidden" name="entreprise_id" value="<?= (int)$entreprise['id'] ?>">

                    <div class="mb-3">
                        <label for="auteur" class="form-label">Votre nom</label>
                        <input type="text" class="form-control" id="auteur" name="auteur" required>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <select class="form-select" id="note" name="note" required>
                            <option value="">Choisir une note</option>
                            <option value="1">1 étoile</option>
                            <option value="2">2 étoiles</option>
                            <option value="3">3 étoiles</option>
                            <option value="4">4 étoiles</option>
                            <option value="5">5 étoiles</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Envoyer l’avis</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4 mb-3">Liste des avis</h2>

                <?php if (empty($avisList)): ?>
                    <div class="alert alert-warning mb-0">
                        Aucun avis pour le moment.
                    </div>
                <?php else: ?>
                    <?php foreach ($avisList as $avis): ?>
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong><?= htmlspecialchars($avis['auteur']) ?></strong>
                                <small><?= htmlspecialchars($avis['date_creation']) ?></small>
                            </div>

                            <p class="mb-2">
                                <strong>Note :</strong>
                                <?= str_repeat('★', (int)$avis['note']) . str_repeat('☆', 5 - (int)$avis['note']) ?>
                                (<?= (int)$avis['note'] ?>/5)
                            </p>

                            <p class="mb-0">
                                <?= !empty($avis['commentaire']) ? nl2br(htmlspecialchars($avis['commentaire'])) : 'Aucun commentaire.' ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>