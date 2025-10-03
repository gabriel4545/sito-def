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
    die(json_encode(['success' => false, 'message' => 'ID categoria non valido']));
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

// Controllo se ci sono articoli che utilizzano questa categoria
$check_articles_sql = "SELECT COUNT(*) as count FROM Articoli WHERE categoriaArticolo = ? AND isDeleted = 0";
$check_articles_stmt = $conn->prepare($check_articles_sql);
$check_articles_stmt->bind_param("i", $id);
$check_articles_stmt->execute();
$articles_result = $check_articles_stmt->get_result();
$articles_count = $articles_result->fetch_assoc()['count'];

if ($articles_count > 0) {
    die(json_encode(['success' => false, 'message' => "Impossibile eliminare la categoria: è utilizzata da $articles_count articolo/i"]));
}

// Elimino la categoria (soft delete)
$sql = "UPDATE CategorieArticoli SET isDeleted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']));
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Categoria eliminata con successo'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Categoria non trovata o già eliminata']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione della categoria']);
}

$stmt->close();
$conn->close();
?>