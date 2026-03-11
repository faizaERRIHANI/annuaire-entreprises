<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();

$pageTitle = "Ajouter une entreprise";
$errors = [];

$nom = '';
$categorie = '';
$adresse = '';
$telephone = '';
$email = '';
$site_web = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $site_web = trim($_POST['site_web'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom === '') {
        $errors[] = "Le nom de l'entreprise est obligatoire.";
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email est invalide.";
    }

    if ($site_web !== '' && !filter_var($site_web, FILTER_VALIDATE_URL)) {
        $errors[] = "L'URL du site web est invalide.";
    }

    $logoName = '';

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du logo.";
        } else {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $maxSize = 2 * 1024 * 1024;

            $originalName = $_FILES['logo']['name'];
            $tmpName = $_FILES['logo']['tmp_name'];
            $fileSize = (int) $_FILES['logo']['size'];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions, true)) {
                $errors[] = "Le logo doit être au format jpg, jpeg, png, gif ou webp.";
            }

            if ($fileSize > $maxSize) {
                $errors[] = "Le logo ne doit pas dépasser 2 Mo.";
            }

            if (empty($errors)) {
                $logoName = uniqid('logo_', true) . '.' . $extension;
                $destination = __DIR__ . '/../uploads/logos/' . $logoName;

                if (!move_uploaded_file($tmpName, $destination)) {
                    $errors[] = "Impossible d'enregistrer le logo.";
                }
            }
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO entreprises (nom, categorie, adresse, telephone, email, site_web, description, logo)
                VALUES (:nom, :categorie, :adresse, :telephone, :email, :site_web, :description, :logo)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':categorie' => $categorie,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email,
            ':site_web' => $site_web,
            ':description' => $description,
            ':logo' => $logoName
        ]);

        header('Location: /annuaire-entreprises/index.php?success=1');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="mb-4">Ajouter une entreprise</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'entreprise</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <input type="text" class="form-control" id="categorie" name="categorie" value="<?= htmlspecialchars($categorie) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($adresse) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="site_web" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="site_web" name="site_web" value="<?= htmlspecialchars($site_web) ?>" placeholder="https://example.com">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif,.webp">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="/annuaire-entreprises/index.php" class="btn btn-secondary">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>