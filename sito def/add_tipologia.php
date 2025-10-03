<?php
// Connessione al database
require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Errore di connessione al database']));
}

// Controllo se è stata mutti(passata) una richiesta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Metodo non consentito']));
}

// Ottengo il nome della nuova tipologia
$input = json_decode(file_get_contents('php://input'), true);
$nomeMotivoRichiesta = isset($input['nomeMotivoRichiesta']) ? trim($input['nomeMotivoRichiesta']) : '';

if (empty($nomeMotivoRichiesta)) {
    die(json_encode(['success' => false, 'message' => 'Il nome della tipologia non può essere vuoto']));
}

// Controllo se esiste già una tipologia con lo stesso nome
$check_sql = "SELECT id FROM MotiviRichieste WHERE nomeMotivoRichiesta = ? AND isDeleted = 0";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $nomeMotivoRichiesta);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Esiste già una tipologia con questo nome']));
}

// Inserisco la nuova tipologia nel database
$sql = "INSERT INTO MotiviRichieste (nomeMotivoRichiesta, isDeleted) VALUES (?, 0)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("s", $nomeMotivoRichiesta);

if ($stmt->execute()) {
    $newId = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Tipologia aggiunta con successo',
        'id' => $newId,
        'nomeMotivoRichiesta' => $nomeMotivoRichiesta
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'inserimento: ' . $stmt->error]);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>
