<?php
// backend_login.php: gestisce la richiesta AJAX di login
header('Content-Type: application/json');
require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Errore di connessione al database"]);
    exit();
}

// Ricevo dati JSON
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Compila tutti i campi."]);
    exit();
}

// Prima cerco l'utente per email/login
$sql = "SELECT id, login, password FROM UtentiSito WHERE login = ? AND isDeleted = 0 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $utente = $result->fetch_assoc();

    // Verifico la password usando password_verify per hash sicuri
    if (password_verify($password, $utente['password'])) {
        // Password corretta - avvia sessione
        session_start();
        $_SESSION['utente_id'] = $utente['id'];
        $_SESSION['login'] = $utente['login'];
        
        echo json_encode([
            "success" => true, 
            "message" => "Accesso effettuato con successo!", 
            "redirect" => "dashboard.php"
        ]);
    } else {
        // Password errata
        echo json_encode([
            "success" => false, 
            "message" => "Email o password errati."
        ]);
    }
} else {
    // Utente non trovato
    echo json_encode([
        "success" => false, 
        "message" => "Email o password errati."
    ]);
}
$stmt->close();
$conn->close();
