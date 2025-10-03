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

// Ottieni dati JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$login = isset($input['login']) ? trim($input['login']) : '';
$newPassword = isset($input['password']) ? trim($input['password']) : '';

// Validazione
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utente non valido']);
    exit();
}

if (empty($login)) {
    echo json_encode(['success' => false, 'message' => 'Il login non può essere vuoto']);
    exit();
}

if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email non valida']);
    exit();
}

// Verifica che l'utente esista
$checkSql = "SELECT id FROM UtentiSito WHERE id = ? AND isDeleted = 0";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
    exit();
}

// Verifica se il login è già utilizzato da un altro utente
$duplicateCheckSql = "SELECT id FROM UtentiSito WHERE login = ? AND id != ? AND isDeleted = 0";
$duplicateStmt = $conn->prepare($duplicateCheckSql);
$duplicateStmt->bind_param("si", $login, $id);
$duplicateStmt->execute();
$duplicateResult = $duplicateStmt->get_result();

if ($duplicateResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Esiste già un altro utente con questa email']);
    exit();
}

// Prepara l'aggiornamento
if (!empty($newPassword)) {
    // Aggiorna login e password
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'La password deve essere di almeno 6 caratteri']);
        exit();
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateSql = "UPDATE UtentiSito SET login = ?, password = ? WHERE id = ? AND isDeleted = 0";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssi", $login, $hashedPassword, $id);
} else {
    // Aggiorna solo il login
    $updateSql = "UPDATE UtentiSito SET login = ? WHERE id = ? AND isDeleted = 0";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $login, $id);
}

if ($updateStmt->execute()) {
    if ($updateStmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Utente aggiornato con successo']);
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
