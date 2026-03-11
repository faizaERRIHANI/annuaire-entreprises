<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

$pageTitle = "Accueil - Annuaire d'Entreprises";
require_once __DIR__ . '/includes/header.php';

$pdo = getPDO();

$nom = trim($_GET['nom'] ?? '');
$categorie = trim($_GET['categorie'] ?? '');

$sqlCategories = "SELECT DISTINCT categorie
                  FROM entreprises
                  WHERE categorie IS NOT NULL AND categorie <> ''
                  ORDER BY categorie ASC";
$stmtCategories = $pdo->query($sqlCategories);
$categories = $stmtCategories->fetchAll();

$sql = "SELECT * FROM entreprises WHERE 1=1";
$params = [];

if ($nom !== '') {
    $sql .= " AND nom LIKE :nom";
    $params[':nom'] = '%' . $nom . '%';
}

if ($categorie !== '') {
    $sql .= " AND categorie = :categorie";
    $params[':categorie'] = $categorie;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entreprises = $stmt->fetchAll();
?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success">
        Entreprise ajoutée avec succès.
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div class="alert alert-success">
        Entreprise supprimée avec succès.
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Liste des entreprises</h1>
    <a href="/annuaire-entreprises/entreprises/create.php" class="btn btn-success">
        Ajouter une entreprise
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <label for="nom" class="form-label">Recherche par nom</label>
                    <input
                        type="text"
                        class="form-control"
                        id="nom"
                        name="nom"
                        value="<?= htmlspecialchars($nom) ?>"
                        placeholder="Ex: Tech Maroc"
                    >
                </div>

                <div class="col-md-5 mb-3">
                    <label for="categorie" class="form-label">Filtrer par catégorie</label>
                    <select class="form-select" id="categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['categorie']) ?>"
                                <?= $categorie === $cat['categorie'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['categorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <div class="w-100 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                        <a href="/annuaire-entreprises/index.php" class="btn btn-outline-secondary">Réinitialiser</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($entreprises)): ?>
    <div class="alert alert-warning">
        Aucune entreprise trouvée.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($entreprises as $entreprise): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-entreprise h-100 shadow-sm">
                    <?php if (!empty($entreprise['logo'])): ?>
                        <img
                            src="/annuaire-entreprises/uploads/logos/<?= htmlspecialchars($entreprise['logo']) ?>"
                            alt="Logo de <?= htmlspecialchars($entreprise['nom']) ?>"
                            class="card-img-top"
                            style="height: 200px; object-fit: contain; padding: 15px;"
                        >
                    <?php endif; ?>

                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($entreprise['nom']) ?></h5>

                        <p class="card-text mb-1">
                            <strong>Catégorie :</strong>
                            <?= htmlspecialchars($entreprise['categorie'] ?? '') ?>
                        </p>

                        <p class="card-text mb-1">
                            <strong>Adresse :</strong>
                            <?= htmlspecialchars($entreprise['adresse'] ?? '') ?>
                        </p>

                        <p class="card-text mb-1">
                            <strong>Téléphone :</strong>
                            <?= htmlspecialchars($entreprise['telephone'] ?? '') ?>
                        </p>

                        <p class="card-text mb-1">
                            <strong>Email :</strong>
                            <?= htmlspecialchars($entreprise['email'] ?? '') ?>
                        </p>

                        <p class="card-text mb-3">
                            <strong>Site web :</strong>
                            <?= htmlspecialchars($entreprise['site_web'] ?? '') ?>
                        </p>

                        <a href="/annuaire-entreprises/entreprises/show.php?id=<?= (int)$entreprise['id'] ?>" class="btn btn-primary">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>