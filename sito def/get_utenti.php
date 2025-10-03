<?php
// get_utenti.php - Ottieni lista utenti con paginazione

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

// Costruisco query base 
$whereClause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $whereClause = ' WHERE login LIKE ?';
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Conto totale record
$countSql = "SELECT COUNT(*) as total FROM UtentiSito" . $whereClause;
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

// Query per dati paginati
$dataSql = "SELECT id, login, isDeleted, data_creazione 
            FROM UtentiSito" . $whereClause . " 
            ORDER BY data_creazione DESC 
            LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$dataStmt = $conn->prepare($dataSql);
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$dataResult = $dataStmt->get_result();

$utenti = [];
while ($row = $dataResult->fetch_assoc()) {
    $isDeleted = intval($row['isDeleted']);
    $utenti[] = [
        'id' => intval($row['id']),
        'login' => $row['login'],
        'attivo' => $isDeleted == 0 ? 1 : 0, 
        'attivo_text' => $isDeleted == 0 ? 'Attivo' : 'Disattivato',
        'data_creazione' => $row['data_creazione'],
        'is_current_user' => intval($row['id']) == $_SESSION['utente_id']
    ];
}

echo json_encode([
    'success' => true,
    'data' => $utenti,
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
