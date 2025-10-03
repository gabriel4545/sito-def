<?php
// Connessione al database
require_once '../config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die("Errore di connessione al database");
}

// Query per ottenere i dati dagli articoli e tabelle correlate (solo quelli non cancellati)
$sql = "SELECT 
            a.id AS articolo_id,
            a.titolo,
            a.testo,
            a.data,
            a.annualita,
            a.categoriaArticolo,
            al.id AS allegato_id,
            al.nomeFile,
            al.percorsoFile
        FROM 
            Articoli a
        LEFT JOIN 
            Allegati al ON a.allegato = al.id
        WHERE 
            a.isDeleted = 0
        ORDER BY 
            a.data DESC";

$query = $conn->prepare($sql);
$query->execute();
$result = $query->get_result();
$articoli = $result->fetch_all(MYSQLI_ASSOC);

if (empty($articoli)) {
    echo json_encode([]);  // Restituisco un array vuoto se non ci sono articoli
    exit();
}

// Creazione dell'array per il JSON
$articoliJson = [];
foreach ($articoli as $articolo) {
    // Decodifico il testo dal nuovo formato
    $testoOriginale = $articolo['testo'];
    $testoDecodificato = $testoOriginale;
    
    // Per compatibilità, provo a decodificare se contiene slashes
    if (strpos($testoOriginale, '\\') !== false) {
        $testoDecodificato = stripslashes($testoOriginale);
    }
    
    $articoliJson[] = [
        'id' => $articolo['articolo_id'],
        'titolo' => $articolo['titolo'],
        'testo' => $testoDecodificato, // Testo già decodificato
        'data' => $articolo['data'],
        'annualita' => $articolo['annualita'],
        'categoriaArticolo' => $articolo['categoriaArticolo'],
        'allegato' => [
            'id' => $articolo['allegato_id'],
            'nomeFile' => $articolo['nomeFile'],
            'percorsoFile' => $articolo['percorsoFile']
        ]
    ];
}

// Restituisco i dati in formato JSON
header('Content-Type: application/json');
$json = json_encode($articoliJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Controllo errori JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Errore nella codifica JSON: ' . json_last_error_msg()]);
} else {
    echo $json;
}



$conn->close();
?>
