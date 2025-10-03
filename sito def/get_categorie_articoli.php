<?php
// get_categorie_articoli.php - Ottieni lista categorie articoli

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

// Query per ottenere tutte le categorie non eliminate
$sql = "SELECT id, categoriaArticolo FROM CategorieArticoli WHERE isDeleted = 0 ORDER BY categoriaArticolo ASC";
$result = $conn->query($sql);

$categorie = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorie[] = [
            'id' => intval($row['id']),
            'nome' => $row['categoriaArticolo']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $categorie
]);

$conn->close();
?>
