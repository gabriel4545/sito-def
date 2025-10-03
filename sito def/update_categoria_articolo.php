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


$id = 0;
$categoriaArticolo = '';

// json
$input = json_decode(file_get_contents('php://input'), true);
if ($input && isset($input['id']) && isset($input['categoriaArticolo'])) {
    $id = intval($input['id']);
    $categoriaArticolo = trim($input['categoriaArticolo']);
} 
// POST tradizionale
else if (isset($_POST['id']) && isset($_POST['categoriaArticolo'])) {
    $id = intval($_POST['id']);
    $categoriaArticolo = trim($_POST['categoriaArticolo']);
}

if ($id <= 0) {
    die(json_encode(['success' => false, 'message' => 'ID categoria non valido']));
}

if (empty($categoriaArticolo)) {
    die(json_encode(['success' => false, 'message' => 'Il nome della categoria non può essere vuoto']));
}

// Controllo se la categoria esiste
$check_sql = "SELECT id FROM CategorieArticoli WHERE id = ? AND isDeleted = 0";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    die(json_encode(['success' => false, 'message' => 'Categoria non trovata']));
}

// Controllo se esiste già una categoria con lo stesso nome (escludendo quella corrente)
$check_duplicate_sql = "SELECT id FROM CategorieArticoli WHERE categoriaArticolo = ? AND id != ? AND isDeleted = 0";
$check_duplicate_stmt = $conn->prepare($check_duplicate_sql);
$check_duplicate_stmt->bind_param("si", $categoriaArticolo, $id);
$check_duplicate_stmt->execute();
$check_duplicate_result = $check_duplicate_stmt->get_result();

if ($check_duplicate_result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Esiste già una categoria con questo nome']));
}

// Aggiorno la categoria nel database
$sql = "UPDATE CategorieArticoli SET categoriaArticolo = ? WHERE id = ? AND isDeleted = 0";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("si", $categoriaArticolo, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Categoria aggiornata con successo',
            'id' => $id,
            'categoria' => $categoriaArticolo
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nessuna modifica effettuata']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento della categoria']);
}

$stmt->close();
$conn->close();
?>