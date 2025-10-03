<?php
// add_articolo.php - Crea un nuovo articolo

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

// Otteno dati JSON
$input = json_decode(file_get_contents('php://input'), true);
$titolo = isset($input['titolo']) ? trim($input['titolo']) : '';
$testo = isset($input['testo']) ? trim($input['testo']) : '';
$categoriaArticolo = isset($input['categoriaArticolo']) ? intval($input['categoriaArticolo']) : 0;
$allegatoId = isset($input['allegatoId']) ? intval($input['allegatoId']) : null;

// Validazione
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

// Verifico se esiste già un articolo con lo stesso titolo
$checkSql = "SELECT id FROM Articoli WHERE titolo = ? AND isDeleted = 0";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $titolo);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Esiste già un articolo con questo titolo']);
    exit();
}

//addslashes per escapare i caratteri speciali
$testoSicuro = addslashes($testo);

// Inserisco il nuovo articolo
if ($allegatoId !== null) {
    $insertSql = "INSERT INTO Articoli (categoriaArticolo, titolo, testo, allegato, isDeleted) VALUES (?, ?, ?, ?, 0)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("issi", $categoriaArticolo, $titolo, $testoSicuro, $allegatoId);
} else {
    $insertSql = "INSERT INTO Articoli (categoriaArticolo, titolo, testo, isDeleted) VALUES (?, ?, ?, 0)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("iss", $categoriaArticolo, $titolo, $testoSicuro);
}

if ($insertStmt->execute()) {
    $newId = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Articolo creato con successo',
        'id' => $newId,
        'titolo' => $titolo,
        'categoriaArticolo' => $categoriaArticolo
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante la creazione: ' . $insertStmt->error]);
}

$insertStmt->close();
$checkStmt->close();
$conn->close();
?>
