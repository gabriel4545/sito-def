<?php
// Connessione al database
require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Errore di connessione al database']));
}

// Controllo se è stata passata una richiesta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Metodo non consentito']));
}

// Ottieni i dati della tipologia
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$nomeMotivoRichiesta = isset($input['nomeMotivoRichiesta']) ? trim($input['nomeMotivoRichiesta']) : '';

if ($id <= 0) {
    die(json_encode(['success' => false, 'message' => 'ID tipologia non valido']));
}

if (empty($nomeMotivoRichiesta)) {
    die(json_encode(['success' => false, 'message' => 'Il nome della tipologia non può essere vuoto']));
}

// Aggiorna la tipologia nel database
$sql = "UPDATE MotiviRichieste SET nomeMotivoRichiesta = ? WHERE id = ? AND isDeleted = 0";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("si", $nomeMotivoRichiesta, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Tipologia aggiornata con successo', 'nomeMotivoRichiesta' => $nomeMotivoRichiesta]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna tipologia trovata con questo ID o nessuna modifica effettuata']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
