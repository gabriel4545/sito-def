<?php

session_start();

// Verifica autenticazione
if (!isset($_SESSION['utente_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit();
}

// Connessione al database
require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit();
}

// Controllo metodo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit();
}

// Ottengo i dati dall'input POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? intval($_POST['status']) : null;

// Validazione
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
    exit();
}

// Verifico che non sia l'utente corrente
if ($id == $_SESSION['utente_id']) {
    echo json_encode(['success' => false, 'message' => 'Non puoi disattivare il tuo account mentre sei connesso']);
    exit();
}

// Verifico che l'utente esista e ottengo lo stato attuale
$checkSql = "SELECT login, isDeleted FROM UtentiSito WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
if (!$checkStmt) {
    echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query: ' . $conn->error]);
    exit();
}
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
    exit();
}

$userData = $checkResult->fetch_assoc();
$currentStatus = $userData['isDeleted'];

// Uso il status ricevuto se presente, altrimenti fai toggle
if ($status !== null) {
    $newStatus = intval($status);
} else {
    $newStatus = $currentStatus == 0 ? 1 : 0; // Toggle automatico se status non specificato
}

$actionText = $newStatus == 0 ? 'riattivato' : 'disattivato';

// Aggiorno lo stato (toggle isDeleted)
$updateSql = "UPDATE UtentiSito SET isDeleted = ? WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Errore nella preparazione dell\'update: ' . $conn->error]);
    exit();
}
$updateStmt->bind_param("ii", $newStatus, $id);

if ($updateStmt->execute()) {
    if ($updateStmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Utente "' . $userData['login'] . '" ' . $actionText . ' con successo',
            'newStatus' => $newStatus
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessun cambiamento effettuato']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento: ' . $updateStmt->error]);
}

$updateStmt->close();
$checkStmt->close();
$conn->close();
?>