<?php
// get_articoli.php - Ottieni lista articoli con paginazione

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

// Parametri per paginazione
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(5, intval($_GET['limit']))) : 10;
$offset = ($page - 1) * $limit;

// Parametri per ricerca
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Costruisco query base - mostra tutti gli articoli non eliminati
$whereClause = ' WHERE a.isDeleted = 0';
$params = [];
$types = '';

if (!empty($search)) {
    $whereClause = ' WHERE a.isDeleted = 0 AND a.titolo LIKE ?';
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Conto totale record
$countSql = "SELECT COUNT(*) as total FROM Articoli a" . $whereClause;
if (!empty($params)) {
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
} else {
    $countResult = $conn->query($countSql);
}

$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Query per dati paginati con join alle categorie
$dataSql = "SELECT a.id, a.titolo, a.testo, a.categoriaArticolo, a.data, a.annualita,
                   c.categoriaArticolo as categoria_nome
            FROM Articoli a 
            LEFT JOIN CategorieArticoli c ON a.categoriaArticolo = c.id" . $whereClause . " 
            ORDER BY a.data DESC 
            LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$dataStmt = $conn->prepare($dataSql);
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$dataResult = $dataStmt->get_result();

$articoli = [];
while ($row = $dataResult->fetch_assoc()) {
    // Il testo è salvato nel database, lo uso direttamente
    $testoOriginale = $row['testo'];
    
    // Per compatibilità, provo a decodificare se contiene slashes, altrimenti lo uso così com'è perchè mi sono rotto il cazzo di tutti sti problemi di encoding
    $testoDecodificato = $testoOriginale;
    if (strpos($testoOriginale, '\\') !== false) {
        $testoDecodificato = stripslashes($testoOriginale);
    }
    
    $anteprimaTesto = strlen(strip_tags($testoDecodificato)) > 100 ? 
        substr(strip_tags($testoDecodificato), 0, 100) . '...' : 
        strip_tags($testoDecodificato);
    
    $articoli[] = [
        'id' => intval($row['id']),
        'titolo' => $row['titolo'],
        'testo_raw' => $testoOriginale, // Testo salvato originale
        'testo_decoded' => $testoDecodificato, // Testo per editing e visualizzazione
        'testo_anteprima' => $anteprimaTesto,
        'categoria_id' => intval($row['categoriaArticolo']),
        'categoria_nome' => $row['categoria_nome'] ?? 'Senza categoria',
        'data' => $row['data'],
        'annualita' => $row['annualita']
    ];
}

echo json_encode([
    'success' => true,
    'data' => $articoli,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_records' => $totalRecords,
        'limit' => $limit,
        'offset' => $offset
    ]
]);

$dataStmt->close();
if (isset($countStmt)) {
    $countStmt->close();
}
$conn->close();
?>
