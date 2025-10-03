<?php
// add_utente.php - Aggiunge un nuovo utente al sistema

session_start();

// Verifico autenticazione
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

// Ottenere dati JSON
$input = json_decode(file_get_contents('php://input'), true);
$login = isset($input['login']) ? trim($input['login']) : '';
$newPassword = isset($input['password']) ? trim($input['password']) : '';

// Validazione
if (empty($login) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Tutti i campi sono obbligatori']);
    exit();
}

if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email non valida']);
    exit();
}

if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'La password deve essere di almeno 6 caratteri']);
    exit();
}

// Verifico se l'utente esiste già
$checkSql = "SELECT id FROM UtentiSito WHERE login = ? AND isDeleted = 0";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $login);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Esiste già un utente con questa email']);
    exit();
}

// Hash della password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Inserisco il nuovo utente
$insertSql = "INSERT INTO UtentiSito (login, password, isDeleted) VALUES (?, ?, 0)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("ss", $login, $hashedPassword);

if ($insertStmt->execute()) {
    $newUserId = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Utente creato con successo',
        'user_id' => $newUserId
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante la creazione: ' . $insertStmt->error]);
}

$insertStmt->close();
$checkStmt->close();
$conn->close();
?>
