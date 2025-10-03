<?php
// Script per registrare un nuovo utente con password crittografata
// Da utilizzare per aggiungere nuovi utenti al sistema

require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die("Errore di connessione al database");
}

// Funzione per registrare un nuovo utente
function registerUser($conn, $email, $plainPassword) {
    // Verifico se l'utente esiste già
    $checkSql = "SELECT id FROM UtentiSito WHERE login = ? AND isDeleted = 0";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        return ["success" => false, "message" => "Utente già esistente con questa email"];
    }
    
    // Hash della password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    // Inserisco il nuovo utente
    $insertSql = "INSERT INTO UtentiSito (login, password, isDeleted) VALUES (?, ?, 0)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ss", $email, $hashedPassword);
    
    if ($insertStmt->execute()) {
        $newUserId = $conn->insert_id;
        return [
            "success" => true, 
            "message" => "Utente registrato con successo", 
            "user_id" => $newUserId
        ];
    } else {
        return [
            "success" => false, 
            "message" => "Errore durante la registrazione: " . $insertStmt->error
        ];
    }
}



$conn->close();

?>
