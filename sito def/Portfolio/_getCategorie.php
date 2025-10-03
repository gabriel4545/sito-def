<?php
// Connessione al database
require_once '../config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die("Errore di connessione al database");
}

// Query per ottenere le categorie
$sql = "
    SELECT 
        id AS categoriaId,
        categoriaArticolo,
        isDeleted
    FROM CategorieArticoli
    WHERE isDeleted = 0;
";

$result = $conn->query($sql);

// Controllo risultati
if ($result->num_rows > 0) {
    $categorie = [];

    // Creazione degli oggetti
    while ($row = $result->fetch_assoc()) {
        $categoria = [
            "id" => $row["categoriaId"],
            "nome" => $row["categoriaArticolo"]
        ];
        $categorie[] = $categoria;
    }

    // Restituzione del risultato come JSON
    header('Content-Type: application/json');
    echo json_encode($categorie);
} else {
    // Nessun dato trovato
    header('Content-Type: application/json');
    echo json_encode([]);
}

// Chiusura connessione
$conn->close();
?>
