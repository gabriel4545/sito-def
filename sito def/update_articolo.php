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

// Ottengo dati JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$titolo = isset($input['titolo']) ? trim($input['titolo']) : '';
$testo = isset($input['testo']) ? trim($input['testo']) : '';
$categoriaArticolo = isset($input['categoriaArticolo']) ? intval($input['categoriaArticolo']) : 0;
$allegatoId = isset($input['allegatoId']) ? intval($input['allegatoId']) : null;

// Validazione
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID articolo non valido']);
    exit();
}

if (empty($titolo)) {
    echo json_encode(['success' => false, 'message' => 'Il titolo non può essere vuoto']);
    exit();
}

if (empty($testo)) {
    echo json_encode(['success' => false, 'message' => 'Il testo non può essere vuoto']);
    exit();
}

if ($categoriaArticolo <= 0) {
    echo json_encode(['success' => false, 'message' => 'Seleziona una categoria valida']);
    exit();
}

// Verifico che l'articolo esista
$checkSql = "SELECT id FROM Articoli WHERE id = ? AND isDeleted = 0";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Articolo non trovato']);
    exit();
}

// Verifico se il titolo è già utilizzato da un altro articolo
$duplicateCheckSql = "SELECT id FROM Articoli WHERE titolo = ? AND id != ? AND isDeleted = 0";
$duplicateStmt = $conn->prepare($duplicateCheckSql);
$duplicateStmt->bind_param("si", $titolo, $id);
$duplicateStmt->execute();
$duplicateResult = $duplicateStmt->get_result();

if ($duplicateResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Esiste già un altro articolo con questo titolo']);
    exit();
}

// Uso addslashes per escapare i caratteri speciali
$testoSicuro = addslashes($testo);

// Aggiorno l'articolo (allegato solo se fornito)
if ($allegatoId !== null) {
    $updateSql = "UPDATE Articoli SET categoriaArticolo = ?, titolo = ?, testo = ?, allegato = ? WHERE id = ? AND isDeleted = 0";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("issii", $categoriaArticolo, $titolo, $testoSicuro, $allegatoId, $id);
} else {
    $updateSql = "UPDATE Articoli SET categoriaArticolo = ?, titolo = ?, testo = ? WHERE id = ? AND isDeleted = 0";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("issi", $categoriaArticolo, $titolo, $testoSicuro, $id);
}

if ($updateStmt->execute()) {
    if ($updateStmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Articolo aggiornato con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna modifica effettuata']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento: ' . $updateStmt->error]);
}

$updateStmt->close();
$duplicateStmt->close();
$checkStmt->close();
$conn->close();
?>
