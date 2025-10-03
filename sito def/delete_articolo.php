<?php
// delete_articolo.php - Elimina un articolo

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

// Ottieno l'ID dall'input POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validazione
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID articolo non valido']);
    exit();
}

// Verifico che l'articolo esista
$checkSql = "SELECT titolo FROM Articoli WHERE id = ? AND isDeleted = 0";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Articolo non trovato']);
    exit();
}

$articleData = $checkResult->fetch_assoc();

// Soft delete - imposta isDeleted = 1
$deleteSql = "UPDATE Articoli SET isDeleted = 1 WHERE id = ? AND isDeleted = 0";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    if ($deleteStmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Articolo "' . $articleData['titolo'] . '" eliminato con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessun articolo eliminato']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione: ' . $deleteStmt->error]);
}

$deleteStmt->close();
$checkStmt->close();
$conn->close();
?>