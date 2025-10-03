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
$categoriaArticolo = '';

// Provo prima con JSON
$input = json_decode(file_get_contents('php://input'), true);
if ($input && isset($input['categoriaArticolo'])) {
    $categoriaArticolo = trim($input['categoriaArticolo']);
} 
// Se non funziona JSON, prova con POST tradizionale perhè non so piu che fare
else if (isset($_POST['categoriaArticolo'])) {
    $categoriaArticolo = trim($_POST['categoriaArticolo']);
}

if (empty($categoriaArticolo)) {
    die(json_encode(['success' => false, 'message' => 'Il nome della categoria non può essere vuoto']));
}

// Controllo se esiste già una categoria con lo stesso nome
$check_sql = "SELECT id FROM CategorieArticoli WHERE categoriaArticolo = ? AND isDeleted = 0";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $categoriaArticolo);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Esiste già una categoria con questo nome']));
}

// Insericso la nuova categoria nel database
$sql = "INSERT INTO CategorieArticoli (categoriaArticolo, isDeleted) VALUES (?, 0)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("s", $categoriaArticolo);

if ($stmt->execute()) {
    $newId = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Categoria aggiunta con successo',
        'id' => $newId,
        'categoria' => $categoriaArticolo
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'inserimento della categoria']);
}

$stmt->close();
$conn->close();
?>