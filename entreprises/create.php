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
$horaires = '';
$latitude = '';
$longitude = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $site_web = trim($_POST['site_web'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $horaires = trim($_POST['horaires'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');

    if ($nom === '') {
        $errors[] = "Le nom de l'entreprise est obligatoire.";
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email est invalide.";
    }

    if ($site_web !== '' && !filter_var($site_web, FILTER_VALIDATE_URL)) {
        $errors[] = "L'URL du site web est invalide.";
    }

    if ($latitude !== '' && !is_numeric($latitude)) {
        $errors[] = "La latitude doit être un nombre valide.";
    }

    if ($longitude !== '' && !is_numeric($longitude)) {
        $errors[] = "La longitude doit être un nombre valide.";
    }

    $logoName = '';

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du logo.";
        } else {
            $maxSize = 2 * 1024 * 1024; // 2 Mo
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp'
            ];

            $originalName = $_FILES['logo']['name'];
            $tmpName = $_FILES['logo']['tmp_name'];
            $fileSize = (int) $_FILES['logo']['size'];

            if ($fileSize > $maxSize) {
                $errors[] = "Le logo ne doit pas dépasser 2 Mo.";
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = $finfo ? finfo_file($finfo, $tmpName) : false;
            if ($finfo) {
                finfo_close($finfo);
            }

            if ($mimeType === false || !array_key_exists($mimeType, $allowedMimeTypes)) {
                $errors[] = "Le logo doit être une image valide (jpg, png, gif ou webp).";
            }

            if (empty($errors)) {
                $extension = $allowedMimeTypes[$mimeType];
                $logoName = uniqid('logo_', true) . '.' . $extension;

                $uploadDir = __DIR__ . '/../uploads/logos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destination = $uploadDir . $logoName;

                if (!move_uploaded_file($tmpName, $destination)) {
                    $errors[] = "Impossible d'enregistrer le logo.";
                }
            }
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO entreprises (
                    nom,
                    categorie,
                    adresse,
                    telephone,
                    email,
                    site_web,
                    description,
                    horaires,
                    latitude,
                    longitude,
                    logo
                ) VALUES (
                    :nom,
                    :categorie,
                    :adresse,
                    :telephone,
                    :email,
                    :site_web,
                    :description,
                    :horaires,
                    :latitude,
                    :longitude,
                    :logo
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':categorie' => $categorie !== '' ? $categorie : null,
            ':adresse' => $adresse !== '' ? $adresse : null,
            ':telephone' => $telephone !== '' ? $telephone : null,
            ':email' => $email !== '' ? $email : null,
            ':site_web' => $site_web !== '' ? $site_web : null,
            ':description' => $description !== '' ? $description : null,
            ':horaires' => $horaires !== '' ? $horaires : null,
            ':latitude' => $latitude !== '' ? (float) $latitude : null,
            ':longitude' => $longitude !== '' ? (float) $longitude : null,
            ':logo' => $logoName !== '' ? $logoName : null
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
                        <input
                            type="text"
                            class="form-control"
                            id="nom"
                            name="nom"
                            value="<?= htmlspecialchars($nom) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <input
                            type="text"
                            class="form-control"
                            id="categorie"
                            name="categorie"
                            value="<?= htmlspecialchars($categorie) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea
                            class="form-control"
                            id="adresse"
                            name="adresse"
                            rows="2"
                        ><?= htmlspecialchars($adresse) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input
                            type="text"
                            class="form-control"
                            id="telephone"
                            name="telephone"
                            value="<?= htmlspecialchars($telephone) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($email) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="site_web" class="form-label">Site web</label>
                        <input
                            type="url"
                            class="form-control"
                            id="site_web"
                            name="site_web"
                            value="<?= htmlspecialchars($site_web) ?>"
                            placeholder="https://example.com"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="4"
                        ><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="horaires" class="form-label">Horaires d'ouverture</label>
                        <textarea
                            class="form-control"
                            id="horaires"
                            name="horaires"
                            rows="4"
                            placeholder="Ex: Lundi - Vendredi : 08:00 - 18:00&#10;Samedi : 09:00 - 13:00&#10;Dimanche : Fermé"
                        ><?= htmlspecialchars($horaires) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input
                                type="text"
                                class="form-control"
                                id="latitude"
                                name="latitude"
                                value="<?= htmlspecialchars($latitude) ?>"
                                placeholder="Ex: 34.0331"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input
                                type="text"
                                class="form-control"
                                id="longitude"
                                name="longitude"
                                value="<?= htmlspecialchars($longitude) ?>"
                                placeholder="Ex: -5.0003"
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input
                            type="file"
                            class="form-control"
                            id="logo"
                            name="logo"
                            accept=".jpg,.jpeg,.png,.gif,.webp"
                        >
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