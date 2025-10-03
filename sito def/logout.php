<?php
// logout.php - Gestisce il logout dell'utente

session_start();

// Distrugge tutte le variabili di sessione
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Invia header per evitare cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Restituisce risposta JSON per la chiamata AJAX
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "message" => "Logout effettuato con successo"
]);
?>
