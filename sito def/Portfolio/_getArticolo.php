<?php
// Connessione al database
require_once '../config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die("Errore di connessione al database");
}

//get the id of the article
$articolo_id = $_GET['id'];



// Query per ottenere i dati dagli articoli e tabelle correlate (solo se non cancellato)
$sql = "SELECT 
a.id AS articolo_id,
a.titolo,
a.testo,
a.data,
a.annualita,
a.categoriaArticolo,
c.categoriaArticolo as categoria_nome,
al.id AS allegato_id,
al.nomeFile,
al.percorsoFile
FROM 
Articoli a
LEFT JOIN 
CategorieArticoli c ON a.categoriaArticolo = c.id
LEFT JOIN 
Allegati al ON a.allegato = al.id
WHERE 
a.id = ? AND a.isDeleted = 0";


$query = $conn->prepare($sql);
$query->bind_param('i', $articolo_id);
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
        'categoria_nome' => $articolo['categoria_nome'] ?? 'Senza categoria',
        'allegato' => [
            'id' => $articolo['allegato_id'],
            'nomeFile' => $articolo['nomeFile'],
            'percorsoFile' => $articolo['percorsoFile']
        ]
    ];
}

// Restituisco i dati in formato JSON
header('Content-Type: application/json');
echo json_encode($articoliJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



$conn->close();
?>
