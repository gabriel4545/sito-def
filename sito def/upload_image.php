<?php
session_start();

// Controllo autenticazione (usa le stesse chiavi del dashboard)
if (!isset($_SESSION['utente_id']) || !isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato']);
    exit();
}

// Controllo metodo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit();
}

// Controllo presenza file
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Nessun file caricato o errore nel caricamento']);
    exit();
}

$file = $_FILES['image'];
$uploadDir = 'uploads/articoli/';

// Controllo dimensione file (max 5MB)
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File troppo grande. Dimensione massima: 5MB']);
    exit();
}

// Controllo tipo file
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($fileInfo, $file['tmp_name']);
finfo_close($fileInfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipo di file non supportato. Usa JPG, PNG, GIF o WebP']);
    exit();
}

// Genera nome file unico
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'articolo_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
$targetPath = $uploadDir . $fileName;

// Crea directory se non esiste
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Sposta il file
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Connessione al database per salvare l'allegato
    require_once 'config/database.php';
    
    try {
        $conn = DatabaseConfig::getConnection();
    } catch (Exception $e) {
        // Se il database fallisce, rimuovi il file caricato
        unlink($targetPath);
        echo json_encode(['success' => false, 'message' => 'Errore di connessione database']);
        exit();
    }
    
    // Inserisci l'allegato nella tabella Allegati
    $insertSql = "INSERT INTO Allegati (nomeFile, percorsoFile) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ss", $file['name'], $targetPath);
    
    if ($insertStmt->execute()) {
        $allegatoId = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Immagine caricata con successo',
            'allegatoId' => $allegatoId,
            'path' => $targetPath,
            'fileName' => $fileName
        ]);
    } else {
        // Se l'inserimento nel database fallisce, rimuovi il file
        unlink($targetPath);
        echo json_encode(['success' => false, 'message' => 'Errore nel salvare l\'allegato nel database']);
    }
    
    $insertStmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nel salvare il file']);
}
?>
