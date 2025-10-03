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

// Gestisco sia JSON che POST tradizionale
$id = 0;

// Provo prima con JSON
$input = json_decode(file_get_contents('php://input'), true);
if ($input && isset($input['id'])) {
    $id = intval($input['id']);
} 
// Se non funziona JSON, provo con POST tradizionale
else if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
}

if ($id <= 0) {
    die(json_encode(['success' => false, 'message' => 'ID tipologia non valido']));
}

// Aggiorno il campo isDeleted invece di eliminare fisicamente il record
$sql = "UPDATE MotiviRichieste SET isDeleted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Tipologia eliminata con successo']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna tipologia trovata con questo ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>