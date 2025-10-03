<?php
/**
 * Configurazione Database 
 * File centralizzato per la connessione al database
 */

// Impedisce l'accesso diretto al file
if (!defined('DB_CONFIG_INCLUDED')) {
    define('DB_CONFIG_INCLUDED', true);
}

// Configurazione database
class DatabaseConfig {
    // Credenziali database
    private static $host = "89.46.111.192";
    private static $username = "Sql1778332"; 
    private static $password = "Miaplacidus.45";
    private static $database = "Sql1778332_2";
    
    // Opzioni di connessione 
    private static $charset = "utf8";
    private static $options = [
        MYSQLI_OPT_CONNECT_TIMEOUT => 10
    ];
    
    /**
     * Crea una nuova connessione al database
     * @return mysqli Oggetto connessione MySQLi
     * @throws Exception Se la connessione fallisce
     */
    public static function getConnection() {
        try {
            // Creo la connessione
            $conn = new mysqli(
                self::$host, 
                self::$username, 
                self::$password, 
                self::$database
            );
            
            // Verifico errori di connessione
            if ($conn->connect_error) {
                throw new Exception("Connessione fallita: " . $conn->connect_error);
            }
            
            // Non imposto charset per replicare quello che succedeva prima, se imposto charset mi spariscono le emoticon. Su edge non le vedo comunque ma se una persona usa edge non merita di entrare sul mio sito
            
            return $conn;
            
        } catch (Exception $e) {
            error_log("Errore connessione database: " . $e->getMessage());
            throw new Exception("Errore di connessione al database");
        }
    }
    
    /**
     * @return mysqli
     */
    public static function connect() {
        return self::getConnection();
    }
    
    /**
     * Creo una connessione PDO
     * @return PDO Oggetto connessione PDO
     * @throws Exception Se la connessione fallisce
     */
    public static function getPDOConnection() {
        try {
            // DSN senza charset per mantenere compatibilità con versione precedente
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$database;
            $pdo = new PDO($dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            return $pdo;
        } catch (PDOException $e) {
            error_log("Errore connessione PDO: " . $e->getMessage());
            throw new Exception("Errore di connessione al database");
        }
    }
    
    /**
     * Chiudo la connessione
     * @param mysqli $conn Connessione da chiudere
     */
    public static function closeConnection($conn) {
        if ($conn && !$conn->connect_error) {
            $conn->close();
        }
    }
    
    /**
     * Ottengo le informazioni sulla connessione (per debug)
     * @return array
     */
    public static function getConnectionInfo() {
        return [
            'host' => self::$host,
            'database' => self::$database,
            'charset' => self::$charset
        ];
    }
}

// Funzione helper per retrocompatibilità
function getDatabaseConnection() {
    return DatabaseConfig::getConnection();
}


?>