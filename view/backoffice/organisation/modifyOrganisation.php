<?php
require_once __DIR__."/../../../Controller/OrganisationController.php";
require_once __DIR__."/../../../Model/Organisation.php";

$orgCtrl = new OrganisationController();
$message = '';
$messageType = '';

// Configurer le dossier d'upload (MÊME QUE addOrganisation.php)
$uploadDir = __DIR__ . '/../../../../uploads/organisations/';
$relativeUploadDir = '/uploads/organisations/';

$id = $_GET['id'] ?? 0;
$organisationData = $orgCtrl->getOrganisation($id);

if (!$organisationData) {
    header("Location: organisationList.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom = trim($_POST['nom']);
        $description = trim($_POST['description']);
        $website_url = trim($_POST['website_url'] ?? '');
        $imageFileName = '';
        
        // Récupérer le nom de l'ancienne image
        $oldImagePath = $organisationData['image_url'] ?? '';
        $oldFileName = basename($oldImagePath);
        
        // Vérifier si un nouveau fichier a été uploadé
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image_file'];
            
            // Vérifier les erreurs d'upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors = [
                    UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur.',
                    UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire.',
                    UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
                    UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
                    UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
                    UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement du fichier.'
                ];
                throw new Exception($errors[$file['error']] ?? 'Erreur inconnue lors de l\'upload.');
            }
            
            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP, SVG.");
            }
            
            // Vérifier la taille (max 5MB)
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxFileSize) {
                throw new Exception("L'image est trop volumineuse (max 5MB).");
            }
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeNom = preg_replace('/[^a-zA-Z0-9]/', '_', $nom);
            $imageFileName = $safeNom . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $imageFileName;
            
            // Déplacer le fichier uploadé
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Erreur lors du déplacement du fichier uploadé.");
            }
            
            // Créer une miniature si nécessaire
            createThumbnailIfNeeded($uploadPath);
            
            // SUPPRIMER L'ANCIENNE IMAGE si elle existe et n'est pas une URL externe
            if (!empty($oldFileName) && 
                !filter_var($oldImagePath, FILTER_VALIDATE_URL) &&
                strpos($oldImagePath, $relativeUploadDir) !== false) {
                $oldFullPath = $uploadDir . $oldFileName;
                if (file_exists($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }
            
        } elseif (isset($_POST['existing_image']) && !empty($_POST['existing_image'])) {
            // Utiliser une image existante de la bibliothèque
            $imageFileName = basename($_POST['existing_image']);
            
            // Si on choisit une image différente de l'ancienne, supprimer l'ancienne
            if (!empty($oldFileName) && $oldFileName !== $imageFileName) {
                $oldFullPath = $uploadDir . $oldFileName;
                if (file_exists($oldFullPath) && 
                    !filter_var($oldImagePath, FILTER_VALIDATE_URL) &&
                    strpos($oldImagePath, $relativeUploadDir) !== false) {
                    unlink($oldFullPath);
                }
            }
        } else {
            // Garder l'ancienne image
            $imageFileName = $oldFileName;
        }
        
        // Créer l'organisation avec le chemin de l'image
        $imagePath = '';
        if (!empty($imageFileName)) {
            if (filter_var($imageFileName, FILTER_VALIDATE_URL)) {
                $imagePath = $imageFileName; // URL complète
            } else {
                $imagePath = $relativeUploadDir . $imageFileName; // Chemin local
            }
        }
        
        $updatedOrg = new Organisation(
            $id,
            $nom,
            $description,
            $website_url,
            $imagePath
        );
        
        $validationErrors = $orgCtrl->validateOrganisation($updatedOrg);
        
        if (empty($validationErrors)) {
            if ($orgCtrl->updateOrganisation($id, $updatedOrg)) {
                $message = "✅ Organisation modifiée avec succès!";
                $messageType = 'success';
                header("refresh:2;url=organisationList.php");
            }
        } else {
            $message = "❌ Erreurs de validation:<br>• " . implode("<br>• ", $validationErrors);
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = "❌ Erreur: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Fonction pour créer une miniature si nécessaire (identique à addOrganisation.php)
function createThumbnailIfNeeded($imagePath) {
    $info = getimagesize($imagePath);
    $mime = $info['mime'];
    
    // Définir la taille maximale souhaitée
    $maxWidth = 800;
    $maxHeight = 800;
    
    list($width, $height) = $info;
    
    // Si l'image est plus grande que les dimensions max, créer une miniature
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = $width / $height;
        
        if ($maxWidth / $maxHeight > $ratio) {
            $newWidth = $maxHeight * $ratio;
            $newHeight = $maxHeight;
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $ratio;
        }
        
        // Créer une nouvelle image
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($imagePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $source = imagecreatefromwebp($imagePath);
                }
                break;
            default:
                return false;
        }
        
        if (!$source) return false;
        
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        // Conserver la transparence pour les PNG et GIF
        if ($mime == 'image/png' || $mime == 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensionner l'image
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Sauvegarder l'image redimensionnée
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $imagePath, 85);
                break;
            case 'image/png':
                imagepng($thumbnail, $imagePath, 8);
                break;
            case 'image/gif':
                imagegif($thumbnail, $imagePath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    imagewebp($thumbnail, $imagePath, 85);
                }
                break;
        }
        
        // Libérer la mémoire
        imagedestroy($source);
        imagedestroy($thumbnail);
    }
    
    return true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Organisation - Mind Arena</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Remix Icons pour le dark mode -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        /* ================= THEME VARIABLES ================= */
        :root {
            --bg: #ffffff;
            --bg-soft: #f8fafc;
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(15,23,42,0.06);
            --sidebar-text: #111827;
            --sidebar-muted: #6b7280;

            --header-bg: rgba(255,255,255,0.96);

            --card-bg: #ffffff;
            --card-border: rgba(15,23,42,0.07);

            --primary: #8b5cf6;
            --primary-soft: rgba(139,92,246,0.09);
            --primary-hover: #7c3aed;

            --text: #111827;
            --text-muted: #6b7280;
            --border-subtle: rgba(148,163,184,0.35);

            --shadow-soft: 0 18px 40px rgba(15,23,42,0.12);

            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --danger-soft: rgba(239, 68, 68, 0.1);
        }

        body.dark {
            --bg: #0f1018;
            --bg-soft: #151626;
            --sidebar-bg: #11121b;
            --sidebar-border: rgba(255,255,255,0.05);
            --sidebar-text: #e5e7eb;
            --sidebar-muted: #9ca3af;

            --header-bg: rgba(15,16,24,0.96);

            --card-bg: #181927;
            --card-border: rgba(148,163,184,0.25);

            --primary: #8b5cf6;
            --primary-soft: rgba(139,92,246,0.13);
            --primary-hover: #a855f7;

            --text: #f9fafb;
            --text-muted: #9ca3af;
            --border-subtle: rgba(148,163,184,0.25);

            --shadow-soft: 0 18px 45px rgba(15,23,42,0.55);
        }

        /* ================= GLOBAL ================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background .25s ease, color .25s ease;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ================= LAYOUT ================= */
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ---------- SIMPLE SIDEBAR ---------- */
        .admin-sidebar {
            width: 230px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding: 18px 16px 18px;
            transition: background .25s ease, border-color .25s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 4px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            margin-bottom: 16px;
        }

        .sidebar-logo {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 10px 25px rgba(139,92,246,0.6);
        }

        .sidebar-title {
            display: flex;
            flex-direction: column;
        }
        .sidebar-title span:first-child {
            font-size: 14px;
            font-weight: 700;
            color: var(--sidebar-text);
        }
        .sidebar-title span:last-child {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: var(--sidebar-muted);
        }

        .sidebar-nav {
            margin-top: 10px;
            list-style: none;
            padding: 0;
        }

        .sidebar-nav-label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--sidebar-muted);
            letter-spacing: .12em;
            margin: 10px 6px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 9px;
            font-size: 14px;
            color: var(--sidebar-text);
            transition: background .18s ease, color .18s ease, transform .12s ease;
        }

        .sidebar-link i {
            font-size: 17px;
        }

        .sidebar-link:hover {
            background: var(--primary-soft);
            color: var(--primary);
            transform: translateX(2px);
        }

        .sidebar-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.08), 0 12px 25px rgba(139,92,246,0.6);
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px dashed var(--sidebar-border);
            font-size: 12px;
            color: var(--sidebar-muted);
        }

        .sidebar-footer a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-top: 6px;
            padding: 6px 8px;
            border-radius: 999px;
            transition: background .18s ease, color .18s ease;
        }

        .sidebar-footer a:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        /* ---------- MAIN AREA ---------- */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* TOP BAR */
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 20;
            height: 60px;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            transition: background .25s ease, border-color .25s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .header-left-title {
            font-weight: 600;
        }

        .header-left-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .header-search {
            flex: 0 0 280px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: var(--card-bg);
            border-radius: 999px;
            border: 1px solid var(--border-subtle);
            transition: background .25s ease, border-color .25s ease;
        }

        .header-search input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            background: transparent;
            color: var(--text);
        }

        .header-search i {
            font-size: 16px;
            color: var(--text-muted);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Toggle mode */
        .theme-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
        }

        .theme-toggle {
            width: 46px;
            height: 22px;
            border-radius: 999px;
            background: rgba(15,23,42,0.75);
            position: relative;
            cursor: pointer;
            transition: background .25s ease;
        }

        body:not(.dark) .theme-toggle {
            background: rgba(148,163,184,0.6);
        }

        .theme-toggle-thumb {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #f9fafb;
            position: absolute;
            top: 2px;
            left: 3px;
            transition: transform .22s ease;
            box-shadow: 0 6px 16px rgba(15,23,42,0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #0f172a;
        }

        body:not(.dark) .theme-toggle-thumb {
            transform: translateX(22px);
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            box-shadow: 0 10px 20px rgba(139,92,246,0.7);
        }

        /* CONTENT WRAP */
        .admin-content {
            flex: 1;
            padding: 24px 24px 26px;
            min-height: calc(100vh - 60px);
            background: var(--bg-soft);
            transition: background .25s ease;
        }

        .content-inner {
            max-width: 800px;
            margin: 0 auto;
        }

        .card-shell {
            background: var(--card-bg);
            border-radius: 18px;
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-soft);
            padding: 30px;
            transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        /* ================= FORM STYLES ================= */
        .form-container {
            margin-top: 20px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-label i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-subtle);
            border-radius: 10px;
            font-size: 1rem;
            background: var(--card-bg);
            color: var(--text);
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Validation Styles */
        .error-field {
            border-color: var(--danger) !important;
            box-shadow: 0 0 0 0.2rem rgba(245, 54, 92, 0.25) !important;
        }

        .success-field {
            border-color: var(--success) !important;
            box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25) !important;
        }

        .validation-error {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
            font-weight: 500;
        }

        .char-count {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            text-align: right;
        }

        .char-count.warning {
            color: var(--warning);
        }

        .message {
            padding: 16px 20px;
            margin: 0 0 24px 0;
            border-radius: 12px;
            border: 1px solid transparent;
            font-weight: 500;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #7f1d1d;
            border-color: rgba(239, 68, 68, 0.2);
        }

        /* Styles pour l'upload d'image */
        .image-upload-section {
            margin-top: 20px;
            padding: 20px;
            border: 2px dashed var(--border-subtle);
            border-radius: 10px;
            background: var(--card-bg);
        }
        
        .image-upload-area {
            text-align: center;
            padding: 40px 20px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 2px dashed var(--primary);
            background: var(--primary-soft);
        }
        
        .image-upload-area:hover {
            background: rgba(139, 92, 246, 0.15);
            transform: translateY(-2px);
        }
        
        .image-upload-area.dragover {
            background: rgba(139, 92, 246, 0.25);
            border-color: var(--primary-hover);
        }
        
        .upload-icon {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .upload-text h4 {
            color: var(--text);
            margin-bottom: 8px;
        }
        
        .upload-text p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }
        
        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .btn-upload {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-upload:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        
        #selectedFileName {
            margin-top: 15px;
            font-size: 0.9rem;
            color: var(--success);
            display: none;
        }
        
        .image-preview-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .image-preview-upload {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            border: 2px solid var(--border-subtle);
            margin: 0 auto;
            display: none;
            object-fit: contain;
        }
        
        .image-preview-upload.visible {
            display: block;
        }
        
        .existing-images {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-subtle);
        }
        
        .existing-images-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .image-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .image-suggestion {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .image-suggestion:hover {
            border-color: var(--primary);
            transform: scale(1.05);
        }
        
        .image-suggestion.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3);
        }
        
        .image-suggestion img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-suggestion-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 0.7rem;
            padding: 4px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .no-existing-images {
            text-align: center;
            padding: 20px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Button styles */
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139,92,246,0.4);
            background: linear-gradient(135deg, var(--primary-hover), #9333ea);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-muted);
            border: 2px solid var(--border-subtle);
        }

        .btn-outline:hover {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-danger {
            background: transparent;
            color: var(--danger);
            border: 2px solid var(--danger);
        }

        .btn-danger:hover {
            background: var(--danger-soft);
            border-color: var(--danger);
            color: var(--danger);
            transform: translateY(-2px);
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-subtle);
            flex-wrap: wrap;
        }

        .current-image-info {
            background: var(--primary-soft);
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            justify-content: space-between;
        }
        
        .current-image-info i {
            color: var(--primary);
        }

        @media (max-width: 960px) {
            .admin-sidebar {
                display: none;
            }
            .admin-header {
                padding-inline: 14px;
            }
            .header-search {
                display: none;
            }
            .admin-content {
                padding-inline: 14px;
            }
            .form-actions {
                flex-direction: column;
            }
            .image-suggestions {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .image-suggestion {
                width: 80px;
                height: 80px;
            }
            
            .upload-icon {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-shell">
        <!-- ======= Sidebar ======= -->
        <div class="admin-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-logo">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <div class="sidebar-title">
                    <span>Mind Arena</span>
                    <span>Backoffice</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-nav-label">Navigation</div>
                <a href="../../backoffice.php" class="sidebar-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <div class="sidebar-nav-label">Gestion</div>
                <a href="../don/donList.php" class="sidebar-link">
                    <i class="bi bi-currency-euro"></i>
                    <span>Liste des Dons</span>
                </a>
                <a href="organisationList.php" class="sidebar-link">
                    <i class="bi bi-building"></i>
                    <span>Organisations</span>
                </a>
                <a href="addOrganisation.php" class="sidebar-link">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouvelle Organisation</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="text-muted-small">© 2024 Mind Arena</span>
                <a href="/projet-dons/View/frontoffice/index.php">
                    <i class="bi bi-eye"></i>
                    Voir le site
                </a>
            </div>
        </div>

        <!-- ======= Main Content ======= -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <div>
                        <div class="header-left-title">Modifier l'Organisation</div>
                        <div class="header-left-sub">Modifier les informations de l'association</div>
                    </div>
                </div>

                <div class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>

                <div class="header-right">
                    <div class="theme-toggle-wrap">
                        <span>Mode</span>
                        <div class="theme-toggle" id="themeToggle">
                            <div class="theme-toggle-thumb">
                                <i class="ri-sun-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="header-user">
                        <div class="user-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <span>Admin</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <div class="content-inner">
                    <div class="card-shell">
                        <div class="form-container">
                            <h1 class="form-title">Modifier l'Organisation #<?= $organisationData['id'] ?></h1>
                            <p class="form-subtitle">Mettez à jour les informations de l'association partenaire</p>

                            <?php if ($message): ?>
                                <div class="message <?= $messageType === 'success' ? 'success' : 'error' ?>">
                                    <?= $message ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="orgForm" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="nom" class="form-label">
                                        <i class="bi bi-building"></i>
                                        Nom de l'organisation
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nom" 
                                           name="nom" 
                                           value="<?= htmlspecialchars($organisationData['nom'] ?? '') ?>"
                                           required
                                           placeholder="Ex: Médecins Sans Frontières">
                                    <span class="validation-error" id="nomError"></span>
                                    <div class="char-count" id="nomCount"><?= strlen($organisationData['nom'] ?? '') ?>/100 caractères</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-text-paragraph"></i>
                                        Description
                                    </label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="5"
                                              required
                                              placeholder="Décrivez l'organisation, sa mission, ses objectifs..."><?= htmlspecialchars($organisationData['description'] ?? '') ?></textarea>
                                    <span class="validation-error" id="descriptionError"></span>
                                    <div class="char-count" id="descriptionCount"><?= strlen($organisationData['description'] ?? '') ?>/500 caractères</div>
                                </div>

                                <div class="form-group">
                                    <label for="website_url" class="form-label">
                                        <i class="bi bi-globe"></i>
                                        Site Web (URL)
                                    </label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="website_url" 
                                           name="website_url" 
                                           value="<?= htmlspecialchars($organisationData['website_url'] ?? '') ?>"
                                           placeholder="Ex: https://www.organisation.org">
                                    <span class="validation-error" id="websiteUrlError"></span>
                                    <div class="char-count" id="websiteUrlCount"><?= strlen($organisationData['website_url'] ?? '') ?>/255 caractères</div>
                                </div>
                                
                                <!-- Section Upload d'image (comme dans addOrganisation.php) -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-image"></i>
                                        Logo/Image de l'organisation
                                    </label>
                                    
                                    <div class="image-upload-section">
                                        <!-- Afficher l'image actuelle -->
                                        <?php if (!empty($organisationData['image_url'])): 
                                            $currentImage = $organisationData['image_url'];
                                            // Déterminer si c'est une URL externe ou locale
                                            if (filter_var($currentImage, FILTER_VALIDATE_URL)) {
                                                $imageSrc = $currentImage;
                                                $imageType = 'url';
                                            } else {
                                                $imageSrc = $currentImage;
                                                $imageType = 'local';
                                            }
                                        ?>
                                            <div class="current-image-info">
                                                <i class="bi bi-check-circle-fill" style="color: var(--success);"></i>
                                                <span>Image actuelle</span>
                                                <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: auto;">
                                                    <?= $imageType === 'url' ? 'URL externe' : 'Image uploadée' ?>
                                                </span>
                                            </div>
                                            <div class="image-preview-container">
                                                <img id="currentImagePreview" 
                                                     class="image-preview-upload visible" 
                                                     src="<?= htmlspecialchars($imageSrc) ?>" 
                                                     alt="Image actuelle"
                                                     onerror="this.style.display='none';">
                                            </div>
                                        <?php else: ?>
                                            <div class="current-image-info" style="background: var(--primary-soft);">
                                                <i class="bi bi-info-circle" style="color: var(--primary);"></i>
                                                <span>Aucune image définie</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Zone de drag & drop pour nouvelle image -->
                                        <div style="margin-top: 20px;">
                                            <div class="image-upload-area" id="dropZone">
                                                <div class="upload-icon">
                                                    <i class="bi bi-cloud-arrow-up"></i>
                                                </div>
                                                <div class="upload-text">
                                                    <h4>Changer l'image</h4>
                                                    <p>Glissez-déposez ou cliquez pour parcourir</p>
                                                    <div class="file-input-wrapper">
                                                        <button type="button" class="btn-upload">
                                                            <i class="bi bi-folder2-open"></i>
                                                            Choisir un fichier
                                                        </button>
                                                        <input type="file" 
                                                               id="image_file" 
                                                               name="image_file" 
                                                               accept="image/jpeg, image/png, image/gif, image/webp, image/svg+xml"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div id="selectedFileName"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Aperçu de la nouvelle image sélectionnée -->
                                        <div class="image-preview-container">
                                            <img id="imagePreviewUpload" 
                                                 class="image-preview-upload" 
                                                 src="" 
                                                 alt="Nouvelle image">
                                        </div>
                                        
                                        <!-- Images existantes dans la bibliothèque -->
                                        <div class="existing-images">
                                            <div class="existing-images-label">
                                                <i class="bi bi-folder-symlink"></i>
                                                Sélectionner une image existante
                                            </div>
                                            
                                            <div class="image-suggestions" id="existingImagesList">
                                                <?php
                                                // Lister les images existantes dans le dossier uploads
                                                $existingImages = [];
                                                if (file_exists($uploadDir)) {
                                                    $files = scandir($uploadDir);
                                                    foreach ($files as $file) {
                                                        if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $file)) {
                                                            $existingImages[] = $file;
                                                        }
                                                    }
                                                }
                                                
                                                if (empty($existingImages)): ?>
                                                    <div class="no-existing-images">
                                                        <i class="bi bi-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                                        Aucune image dans la bibliothèque
                                                    </div>
                                                <?php else: ?>
                                                    <?php 
                                                    // Ajouter "Supprimer l'image" comme première option
                                                    ?>
                                                    <div class="image-suggestion" 
                                                         onclick="clearImageSelection()"
                                                         style="background: var(--danger-soft); border: 2px dashed var(--danger);">
                                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--danger);">
                                                            <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                                        </div>
                                                        <div class="image-suggestion-label" style="background: rgba(239, 68, 68, 0.9);">
                                                            Supprimer
                                                        </div>
                                                    </div>
                                                    
                                                    <?php foreach ($existingImages as $image): 
                                                        $imagePath = $relativeUploadDir . $image;
                                                        $fullPath = $uploadDir . $image;
                                                        $isCurrent = (!empty($organisationData['image_url']) && 
                                                                     basename($organisationData['image_url']) === $image);
                                                    ?>
                                                        <div class="image-suggestion <?= $isCurrent ? 'active' : '' ?>" 
                                                             onclick="selectExistingImage('<?= htmlspecialchars($image) ?>', '<?= htmlspecialchars($imagePath) ?>')"
                                                             data-image="<?= htmlspecialchars($image) ?>">
                                                            <img src="<?= htmlspecialchars($imagePath) ?>" 
                                                                 alt="<?= htmlspecialchars($image) ?>"
                                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"%3E%3Crect width=\"100\" height=\"100\" fill=\"%23f0f0f0\"/%3E%3Ctext x=\"50\" y=\"50\" font-family=\"Arial\" font-size=\"12\" fill=\"%23999\" text-anchor=\"middle\" dy=\".3em\"%3EImage%3C/text%3E%3C/svg%3E'">
                                                            <div class="image-suggestion-label">
                                                                <?= htmlspecialchars(substr($image, 0, 15)) . (strlen($image) > 15 ? '...' : '') ?>
                                                            </div>
                                                            <?php if ($isCurrent): ?>
                                                                <div style="position: absolute; top: 5px; right: 5px; background: var(--success); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                                    <i class="bi bi-check"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Champ caché pour l'image existante sélectionnée -->
                                            <input type="hidden" id="existing_image" name="existing_image" value="">
                                        </div>
                                    </div>
                                    
                                    <span class="validation-error" id="imageError"></span>
                                    <div class="char-count" id="imageInfo">
                                        Formats acceptés: JPEG, PNG, GIF, WebP, SVG | Max: 5MB
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i>
                                        Mettre à jour
                                    </button>
                                    <a href="organisationList.php" class="btn btn-outline">
                                        <i class="bi bi-arrow-left"></i>
                                        Annuler
                                    </a>
                                    <?php if (!empty($organisationData['image_url']) && !filter_var($organisationData['image_url'], FILTER_VALIDATE_URL)): ?>
                                        <button type="button" class="btn btn-danger" onclick="deleteCurrentImage()">
                                            <i class="bi bi-trash"></i>
                                            Supprimer l'image actuelle
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Thème dark / light
        (function () {
            const body = document.body;
            const toggle = document.getElementById('themeToggle');
            const thumb = document.querySelector('.theme-toggle-thumb');

            function applyTheme(theme) {
                if (theme === 'light') {
                    body.classList.remove('dark');
                    if (thumb) thumb.innerHTML = '<i class="ri-sun-fill"></i>';
                } else {
                    body.classList.add('dark');
                    if (thumb) thumb.innerHTML = '<i class="ri-moon-fill"></i>';
                }
                localStorage.setItem('ma-admin-theme', theme);
            }

            const saved = localStorage.getItem('ma-admin-theme') || 'light';
            applyTheme(saved);

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const next = body.classList.contains('dark') ? 'light' : 'dark';
                    applyTheme(next);
                });
            }
        })();

        // Gestion de l'upload d'image
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orgForm');
            const imageFileInput = document.getElementById('image_file');
            const dropZone = document.getElementById('dropZone');
            const previewImage = document.getElementById('imagePreviewUpload');
            const selectedFileName = document.getElementById('selectedFileName');
            const existingImageInput = document.getElementById('existing_image');
            const existingImages = document.querySelectorAll('.image-suggestion');
            const currentImagePreview = document.getElementById('currentImagePreview');
            
            // Gestion du drag & drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                if (dropZone) {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                }
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            if (dropZone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                function highlight() {
                    dropZone.classList.add('dragover');
                }

                function unhighlight() {
                    dropZone.classList.remove('dragover');
                }

                // Gestion du drop
                dropZone.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    
                    if (files.length > 0) {
                        const file = files[0];
                        if (file.type.startsWith('image/')) {
                            imageFileInput.files = files;
                            updatePreview(file);
                            resetExistingImageSelection();
                        } else {
                            alert('Veuillez sélectionner uniquement des fichiers image.');
                        }
                    }
                }

                // Cliquer sur la zone de drop
                dropZone.addEventListener('click', function() {
                    imageFileInput.click();
                });
            }

            // Gestion du changement de fichier
            if (imageFileInput) {
                imageFileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        updatePreview(this.files[0]);
                        resetExistingImageSelection();
                    }
                });
            }

            function updatePreview(file) {
                if (selectedFileName) {
                    selectedFileName.textContent = 'Nouveau fichier: ' + file.name;
                    selectedFileName.style.display = 'block';
                }
                
                if (previewImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.add('visible');
                        
                        // Cacher l'aperçu de l'image actuelle
                        if (currentImagePreview) {
                            currentImagePreview.style.display = 'none';
                        }
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Fonction pour sélectionner une image existante
            window.selectExistingImage = function(imageName, imagePath) {
                if (existingImageInput) {
                    existingImageInput.value = imageName;
                }
                
                // Mettre en évidence l'image sélectionnée
                existingImages.forEach(img => {
                    img.classList.remove('active');
                });
                event.target.closest('.image-suggestion').classList.add('active');
                
                // Afficher l'aperçu
                if (previewImage) {
                    previewImage.src = imagePath;
                    previewImage.classList.add('visible');
                    
                    // Cacher l'aperçu de l'image actuelle
                    if (currentImagePreview) {
                        currentImagePreview.style.display = 'none';
                    }
                }
                
                // Effacer l'input file
                if (imageFileInput) {
                    imageFileInput.value = '';
                }
                if (selectedFileName) {
                    selectedFileName.style.display = 'none';
                }
            }

            window.clearImageSelection = function() {
                if (existingImageInput) {
                    existingImageInput.value = '';
                }
                
                // Retirer les styles actifs
                existingImages.forEach(img => {
                    img.classList.remove('active');
                });
                
                // Effacer l'input file
                if (imageFileInput) {
                    imageFileInput.value = '';
                }
                if (selectedFileName) {
                    selectedFileName.style.display = 'none';
                }
                
                // Cacher l'aperçu de la nouvelle image
                if (previewImage) {
                    previewImage.classList.remove('visible');
                }
                
                // Remontrer l'image actuelle
                if (currentImagePreview) {
                    currentImagePreview.style.display = 'block';
                }
                
                // Afficher message
                alert("L'image sera supprimée lors de l'enregistrement.");
            }

            function resetExistingImageSelection() {
                if (existingImageInput) {
                    existingImageInput.value = '';
                }
                existingImages.forEach(img => {
                    img.classList.remove('active');
                });
            }

            // Validation
            const fields = {
                nom: document.getElementById('nom'),
                description: document.getElementById('description'),
                website_url: document.getElementById('website_url')
            };

            // Validation des champs
            Object.keys(fields).forEach(fieldName => {
                const field = fields[fieldName];
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(fieldName);
                    });
                    
                    // Compteur de caractères
                    field.addEventListener('input', function() {
                        updateCharCount(fieldName);
                    });
                }
            });

            // Initialiser les compteurs
            Object.keys(fields).forEach(fieldName => {
                updateCharCount(fieldName);
            });

            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                Object.keys(fields).forEach(fieldName => {
                    if (!validateField(fieldName)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Veuillez corriger les erreurs dans le formulaire.');
                }
            });

            function updateCharCount(fieldName) {
                const field = fields[fieldName];
                const counter = document.getElementById(fieldName + 'Count');
                if (!field || !counter) return;
                
                const length = field.value.length;
                let maxLength = 100;
                if (fieldName === 'description') maxLength = 500;
                if (fieldName === 'website_url') maxLength = 255;
                
                counter.textContent = `${length}/${maxLength} caractères`;
                if (length > maxLength * 0.8) {
                    counter.classList.add('warning');
                } else {
                    counter.classList.remove('warning');
                }
            }

            function validateField(fieldName) {
                const field = fields[fieldName];
                if (!field) return true;
                
                const errorElement = document.getElementById(fieldName + 'Error');
                const value = field.value.trim();
                
                field.classList.remove('error-field', 'success-field');
                if (errorElement) errorElement.textContent = '';
                
                let isValid = true;
                let message = '';
                
                switch(fieldName) {
                    case 'nom':
                        if (!value) {
                            message = "Le nom de l'organisation est obligatoire";
                            isValid = false;
                        } else if (value.length < 2) {
                            message = "Le nom doit contenir au moins 2 caractères";
                            isValid = false;
                        } else if (value.length > 100) {
                            message = "Le nom ne peut pas dépasser 100 caractères";
                            isValid = false;
                        }
                        break;
                        
                    case 'description':
                        if (!value) {
                            message = "La description est obligatoire";
                            isValid = false;
                        } else if (value.length < 10) {
                            message = "La description doit contenir au moins 10 caractères";
                            isValid = false;
                        } else if (value.length > 500) {
                            message = "La description ne peut pas dépasser 500 caractères";
                            isValid = false;
                        }
                        break;

                    case 'website_url':
                        if (value && !isValidUrl(value)) {
                            message = "Veuillez entrer une URL valide (commençant par http:// ou https://)";
                            isValid = false;
                        }
                        break;
                }
                
                if (!isValid) {
                    field.classList.add('error-field');
                    if (errorElement) errorElement.textContent = message;
                } else if (value) {
                    field.classList.add('success-field');
                }
                
                return isValid;
            }

            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        });

        // Fonction pour supprimer l'image actuelle
        function deleteCurrentImage() {
            if (confirm('Êtes-vous sûr de vouloir supprimer l\'image actuelle de cette organisation ?')) {
                // Cocher l'option "Supprimer"
                document.querySelectorAll('.image-suggestion').forEach(img => {
                    if (img.innerHTML.includes('bi-trash')) {
                        img.click();
                    }
                });
            }
        }
    </script>
</body>
</html>