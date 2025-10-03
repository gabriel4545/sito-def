<?php
// delete_utente.php - Elimina un utente

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

// Ottiengo l'ID dall'input POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validazione
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
    exit();
}

// Verifico che non sia l'utente corrente
if ($id == $_SESSION['utente_id']) {
    echo json_encode(['success' => false, 'message' => 'Non puoi eliminare il tuo account mentre sei connesso']);
    exit();
}

// Verifico che l'utente esista
$checkSql = "SELECT login FROM UtentiSito WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
    exit();
}

$userData = $checkResult->fetch_assoc();

// Elimino l'utente completamente dal database (hard delete) - giusto per dimostrazione hard delete e non soft delete come toggle_utente_status.php
$deleteSql = "DELETE FROM UtentiSito WHERE id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    if ($deleteStmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Utente "' . $userData['login'] . '" eliminato con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessun utente eliminato']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione: ' . $deleteStmt->error]);
}

$deleteStmt->close();
$checkStmt->close();
$conn->close();
?>