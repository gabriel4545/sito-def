<?php
include 'layout.php';

/*Connessione*/
require_once 'config/database.php';

/**
 * CONNESSIONE AL DATABASE
 */
try {
    $conn = DatabaseConfig::getPDOConnection();
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connessione fallita: " . $e->getMessage();
    die();
}

// Recupero i motivi delle richieste dal database
$motiviRichieste = [];
try {
    $sqlMotivi = "SELECT id, nomeMotivoRichiesta FROM MotiviRichieste WHERE isDeleted = 0 ORDER BY nomeMotivoRichiesta";
    $queryMotivi = $conn->prepare($sqlMotivi);
    $queryMotivi->execute();
    $motiviRichieste = $queryMotivi->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // In caso di errore, uso dei motivi di default hardcoded per evitare che il form sia vuoto
    $motiviRichieste = [
        ['id' => 1, 'nomeMotivoRichiesta' => 'Informazioni'],
        ['id' => 3, 'nomeMotivoRichiesta' => 'Preventivo']
    ];
}

// Gestione invio form
$errorMessage = '';
$successMessage = '';

if (isset($_POST['submit'])) {
    // Validazione e raccolta dati
    $nome = trim($_POST['_nome'] ?? '');
    $cognome = trim($_POST['_cognome'] ?? '');
    $email = trim($_POST['_email'] ?? '');
    $telefono = trim($_POST['_telefono'] ?? '');
    $cellulare = trim($_POST['_cellulare'] ?? '');
    $nazione = trim($_POST['_nazione'] ?? '');
    $indirizzo = trim($_POST['_indirizzo'] ?? '');
    $motivo = trim($_POST['_motivo'] ?? '');
    $messaggio = trim($_POST['_messaggio'] ?? '');
    $datas = date('Y-m-d H:i:s');

    // Validazione base
    if (empty($nome) || empty($cognome) || empty($email) || empty($cellulare) || empty($motivo) || empty($messaggio)) {
        $errorMessage = 'Tutti i campi obbligatori devono essere compilati.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Inserire un indirizzo email valido.';
    } else {
        try {
            // Verifico che il motivo esista nella tabella MotiviRichieste
            $checkMotivo = "SELECT id FROM MotiviRichieste WHERE id = :motivo AND isDeleted = 0";
            $queryCheckMotivo = $conn->prepare($checkMotivo);
            $queryCheckMotivo->bindParam(":motivo", $motivo, PDO::PARAM_INT);
            $queryCheckMotivo->execute();
            
            if ($queryCheckMotivo->rowCount() == 0) {
                throw new Exception("Motivo della richiesta non valido.");
            }

            // Inizio transazione
            $conn->beginTransaction();

            // Inserimento nella tabella Richieste
            $sqlRichiesta = "INSERT INTO `Richieste` (`nome`, `cognome`, `telefono`, `cellulare`, `mail`, `indirizzo`, `nazione`, `testoRichiesta`, `motivoRichiesta`, `data`) 
                             VALUES (:nome, :cognome, :telefono, :cellulare, :email, :indirizzo, :nazione, :messaggio, :motivo, :data)";
            $queryRichiesta = $conn->prepare($sqlRichiesta);
            $queryRichiesta->bindParam(":nome", $nome, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":cognome", $cognome, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":telefono", $telefono, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":cellulare", $cellulare, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":email", $email, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":indirizzo", $indirizzo, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":nazione", $nazione, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":messaggio", $messaggio, PDO::PARAM_STR);
            $queryRichiesta->bindParam(":motivo", $motivo, PDO::PARAM_INT);
            $queryRichiesta->bindParam(":data", $datas, PDO::PARAM_STR);
            $queryRichiesta->execute();

            // Verifico se la tabella Utenti esiste prima di inserire
            $checkTableUtenti = "SHOW TABLES LIKE 'Utenti'";
            $queryCheckTable = $conn->prepare($checkTableUtenti);
            $queryCheckTable->execute();
            
            if ($queryCheckTable->fetchColumn()) {
                // Inserimento nella tabella Utenti solo se esiste
                $sqlUtente = "INSERT INTO `Utenti` (`nome`, `cognome`, `mail`, `cellulare`, `indirizzo`, `nazione`, `isDeleted`) 
                              VALUES (:nome, :cognome, :email, :cellulare, :indirizzo, :nazione, 0)";
                $queryUtente = $conn->prepare($sqlUtente);
                $queryUtente->bindParam(":nome", $nome, PDO::PARAM_STR);
                $queryUtente->bindParam(":cognome", $cognome, PDO::PARAM_STR);
                $queryUtente->bindParam(":email", $email, PDO::PARAM_STR);
                $queryUtente->bindParam(":cellulare", $cellulare, PDO::PARAM_STR);
                $queryUtente->bindParam(":indirizzo", $indirizzo, PDO::PARAM_STR);
                $queryUtente->bindParam(":nazione", $nazione, PDO::PARAM_STR);
                $queryUtente->execute();
            }

            // Commit della transazione
            $conn->commit();
            
            $successMessage = 'Messaggio inviato con successo! Ti risponderò entro 24 ore.';
        } catch (Exception $e) {
            // Rollback in caso di errore
            $conn->rollBack();
            $errorMessage = 'Errore durante l\'invio del messaggio: ' . $e->getMessage();
        }
    }
}
?>

<!-- Contact Hero Section -->
<div class="container-fluid py-5 mb-5" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-3 fw-bold text-dark mb-3">Collaboriamo Insieme</h1>
            <div class="mx-auto mb-4" style="height: 4px; width: 100px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px;"></div>
            <p class="lead text-muted col-lg-8 mx-auto">
                Specializzato in IT e sicurezza informatica presso HDI Distribuzione. Interessato a progetti di sviluppo web, infrastrutture IT e innovazione nel settore security B2B.
            </p>
        </div>
        
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card h-100 border-0 text-center" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));">
                    <div class="card-body p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-envelope-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Email</h5>
                        <p class="text-muted mb-2">Scrivimi direttamente</p>
                        <a href="mailto:info@perinogabriel.it" class="btn btn-outline-primary btn-sm rounded-pill">
                            info@perinogabriel.it
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 text-center" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(25, 135, 84, 0.05));">
                    <div class="card-body p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-whatsapp text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">WhatsApp</h5>
                        <p class="text-muted mb-2">Contatto rapido</p>
                        <a href="https://wa.me/393351602418" target="_blank" class="btn btn-outline-success btn-sm rounded-pill">
                            +39 335 1602418
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 text-center" style="background: linear-gradient(135deg, rgba(118, 75, 162, 0.05), rgba(102, 126, 234, 0.05));">
                    <div class="card-body p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Specializzazione</h5>
                        <p class="text-muted mb-2">Esperto in</p>
                        <span class="badge bg-success rounded-pill px-3 py-2">IT Security B2B</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-0 text-center" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));">
                    <div class="card-body p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-building text-info" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">HDI Distribuzione</h5>
                        <p class="text-muted mb-2">Settore specializzazione</p>
                        <span class="badge bg-info rounded-pill px-3 py-2">Security B2B</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HDI  -->
<div class="container mb-5">
    <div class="text-center mb-4">
        <h2 class="h3 fw-bold text-dark mb-3">Settori di Competenza HDI</h2>
        <p class="text-muted">Distribuzioni B2B nel settore sicurezza e tecnologie avanzate</p>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));">
                <i class="bi bi-camera-video text-primary h2 mb-2"></i>
                <h6 class="fw-semibold small">TVCC</h6>
                <small class="text-muted">Videosorveglianza</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(176, 42, 55, 0.1));">
                <i class="bi bi-shield-exclamation text-danger h2 mb-2"></i>
                <h6 class="fw-semibold small">Antintrusione</h6>
                <small class="text-muted">Protezione perimetrale</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255, 109, 7, 0.1), rgba(230, 98, 6, 0.1));">
                <i class="bi bi-fire text-warning h2 mb-2"></i>
                <h6 class="fw-semibold small">Antincendio</h6>
                <small class="text-muted">Rivelazione incendi</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(25, 135, 84, 0.1));">
                <i class="bi bi-house-gear text-success h2 mb-2"></i>
                <h6 class="fw-semibold small">Automazione</h6>
                <small class="text-muted">Building automation</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(253, 126, 20, 0.1));">
                <i class="bi bi-key text-warning h2 mb-2"></i>
                <h6 class="fw-semibold small">Controllo Accessi</h6>
                <small class="text-muted">Gestione sicurezza</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(11, 172, 204, 0.1));">
                <i class="bi bi-ethernet text-info h2 mb-2"></i>
                <h6 class="fw-semibold small">Networking</h6>
                <small class="text-muted">Infrastrutture IT</small>
            </div>
        </div>
    </div>
</div>

<!-- Contatti -->
<div class="container">
    <div class="row g-5 align-items-start">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h2 class="h3 fw-bold text-dark mb-3">
                        <i class="bi bi-headset me-2 text-primary"></i>
                        Richiedi Consulenza Tecnica
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="bi bi-code-slash me-2"></i>
                        Sviluppo soluzioni web personalizzate e fornisco consulenze IT. Contattami per progetti di sviluppo web o consulenze sui sistemi di sicurezza distribuiti da HDI.
                    </p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Grazie per avermi contattato!</strong> <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Errore:</strong> <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($successMessage)): ?>
                    <form class="row g-4 needs-validation" novalidate id="formContatto" method="post">
                        <div class="col-md-6">
                            <label for="_nome" class="form-label fw-semibold">
                                <i class="bi bi-person me-1 text-primary"></i>Nome *
                            </label>
                            <input type="text" class="form-control form-control-lg" id="_nome" name="_nome" required 
                                   style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                   onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>Inserire il nome
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="_cognome" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1 text-primary"></i>Cognome *
                            </label>
                            <input type="text" class="form-control form-control-lg" id="_cognome" name="_cognome" required
                                   style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                   onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>Inserire il cognome
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="_email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1 text-primary"></i>Email *
                            </label>
                            <div class="input-group input-group-lg has-validation">
                                <span class="input-group-text" style="border-radius: 12px 0 0 12px; border: 2px solid #e9ecef; border-right: none; background: #f8f9fa;">
                                    <i class="bi bi-at text-muted"></i>
                                </span>
                                <input type="email" class="form-control" id="_email" name="_email" required
                                       style="border-radius: 0 12px 12px 0; border: 2px solid #e9ecef; border-left: none; transition: all 0.3s ease;"
                                       onfocus="this.style.borderColor='#667eea'; this.previousElementSibling.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                       onblur="this.style.borderColor='#e9ecef'; this.previousElementSibling.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>Inserire un indirizzo email valido
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="_telefono" class="form-label fw-semibold">
                                <i class="bi bi-telephone me-1 text-primary"></i>Telefono
                            </label>
                            <input type="tel" class="form-control form-control-lg" id="_telefono" name="_telefono"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                   onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                        </div>
                        <div class="col-md-6">
                            <label for="_cellulare" class="form-label fw-semibold">
                                <i class="bi bi-phone me-1 text-primary"></i>Cellulare *
                            </label>
                            <input type="tel" class="form-control form-control-lg" id="_cellulare" name="_cellulare" required
                                   style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                   onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>Inserire un numero di telefono valido
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="_nazione" class="form-label fw-semibold">
                                        <i class="bi bi-flag me-1 text-primary"></i>Nazione
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="_nazione" name="_nazione" placeholder="Italia"
                                           style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                           onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                           onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">                    
                                </div>
                                <div class="col-md-8">
                                    <label for="_indirizzo" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt me-1 text-primary"></i>Indirizzo
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="_indirizzo" name="_indirizzo" placeholder="Via, Numero civico"
                                           style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                           onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                           onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">                    
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="_motivo" class="form-label fw-semibold">
                                <i class="bi bi-chat-square-text me-1 text-primary"></i>Motivo del Contatto *
                            </label>
                            <select class="form-select form-select-lg" id="_motivo" name="_motivo" required
                                    style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                    onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                                <option selected disabled value="">Seleziona il motivo del contatto...</option>
                                <?php foreach ($motiviRichieste as $motivo): ?>
                                    <option value="<?= htmlspecialchars($motivo['id']) ?>">
                                        <?= htmlspecialchars($motivo['nomeMotivoRichiesta']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>Selezionare il motivo del contatto
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="_messaggio" class="form-label fw-semibold">
                                <i class="bi bi-chat-left-text me-1 text-primary"></i>Il tuo Messaggio *
                            </label>
                            <textarea class="form-control form-control-lg" id="_messaggio" name="_messaggio" rows="6" required
                                      placeholder="Descrivi il tuo progetto o esigenza. Offro sviluppo di siti web personalizzati e supporto tecnico nel settore sicurezza B2B tramite HDI Distribuzione (videosorveglianza, controllo accessi, antintrusione, networking)..."
                                      style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: vertical;"
                                      onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                      onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'"></textarea>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>Inserire un messaggio
                            </div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Più dettagli fornisci, meglio potrò aiutarti. Includi informazioni su tempistiche, budget e obiettivi.
                                </small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check p-3 rounded-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05)); border: 2px solid #e9ecef;">
                                <input class="form-check-input" type="checkbox" value="1" id="invalidCheck" name="invalidCheck" required
                                       style="transform: scale(1.2); accent-color: #667eea;">
                                <label class="form-check-label fw-semibold" for="invalidCheck" style="margin-left: 8px;">
                                    <i class="bi bi-shield-check text-success me-2"></i>
                                    Accetto i <a href="/privacy.php" class="text-decoration-none fw-bold">termini e le condizioni</a> *
                                </label>
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>Devi accettare i termini e le condizioni per proseguire
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 text-center">
                            <button class="btn btn-lg px-5 py-3 fw-semibold" type="submit" name="submit"
                                    style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 25px; color: white; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 35px rgba(102, 126, 234, 0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'">
                                <i class="bi bi-send-fill me-2"></i>Invia Messaggio
                            </button>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Ti risponderò entro 24 ore
                                </small>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Maps -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h3 class="h5 fw-bold text-dark mb-3">
                        <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                        Dove Trovarmi
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="map-container" style="height: 300px; border-radius: 0 0 20px 20px; overflow: hidden;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2782.9691107309286!2d10.3385705!3d44.8364684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4780402397484a93%3A0x4fc5fc67accec02f!2sHDi%20Distribuzione!5e0!3m2!1sit!2sit!4v1727802000000!5m2!1sit!2sit" 
                            width="100%" 
                            height="100%" 
                            style="border:0; filter: grayscale(20%) contrast(1.2);" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <!-- Contatti -->
                    <div class="p-4">
                        <div class="contact-info">
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-semibold mb-2 text-primary">
                                    <i class="bi bi-shield-check me-2"></i>HDI Distribuzione
                                </h6>
                                <p class="mb-0 text-muted small">IT Specialist - Settore sicurezza B2B</p>
                            </div>
                            
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-semibold mb-2 text-primary">
                                    <i class="bi bi-clock me-2"></i>Disponibilità Consulenze
                                </h6>
                                <p class="mb-1 small">Lun - Ven: 9:00 - 18:00</p>
                                <p class="mb-0 text-muted small">Supporto tecnico specializzato</p>
                            </div>
                            
                            <div class="text-center mt-4">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="mailto:info@perinogabriel.it" class="btn btn-outline-primary btn-sm rounded-pill" title="Invia Email">
                                        <i class="bi bi-envelope"></i>
                                    </a>
                                    <a href="tel:+393351602418" class="btn btn-outline-success btn-sm rounded-pill" title="Chiama">
                                        <i class="bi bi-telephone"></i>
                                    </a>
                                    <a href="https://wa.me/393351602418" target="_blank" class="btn btn-outline-success btn-sm rounded-pill" title="Contattami su WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>



<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<script>
    const index = document.getElementById('contatti');
    index.classList.add('active');
    index.setAttribute('aria-current', 'page');
</script>
<?php echo $footer; ?>
</body>

</html>