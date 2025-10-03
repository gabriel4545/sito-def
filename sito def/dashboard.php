
<?php
// Avvia la sessione e verifica l'autenticazione
session_start();

// Controllo se l'utente è autenticato
if (!isset($_SESSION['utente_id']) || !isset($_SESSION['login'])) {
    // Se non è autenticato, reindirizza al login
    header('Location: login.php');
    exit();
}

// Connessione al database
require_once 'config/database.php';

try {
    $conn = DatabaseConfig::getConnection();
} catch (Exception $e) {
    die("Errore di connessione al database");
}

// Verifico che l'utente esista ancora nel database e non sia stato eliminato
$checkUserSql = "SELECT id, login FROM UtentiSito WHERE id = ? AND isDeleted = 0 LIMIT 1";
$checkStmt = $conn->prepare($checkUserSql);
$checkStmt->bind_param("i", $_SESSION['utente_id']);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    // L'utente non esiste più o è stato eliminato - distruggi la sessione
    session_destroy();
    header('Location: login.php');
    exit();
}

$checkStmt->close();

// Gestione azioni dirette
$message = '';
$messageType = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    switch($action) {
        case 'delete_articolo':
            if ($id > 0) {
                // Verifico che l'articolo esista
                $checkSql = "SELECT titolo FROM Articoli WHERE id = ? AND isDeleted = 0";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("i", $id);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                
                if ($checkResult->num_rows > 0) {
                    // Elimino l'articolo (soft delete)
                    $deleteSql = "UPDATE Articoli SET isDeleted = 1 WHERE id = ?";
                    $deleteStmt = $conn->prepare($deleteSql);
                    $deleteStmt->bind_param("i", $id);
                    
                    if ($deleteStmt->execute()) {
                        $message = 'Articolo eliminato con successo';
                        $messageType = 'success';
                    } else {
                        $message = 'Errore nell\'eliminazione dell\'articolo';
                        $messageType = 'danger';
                    }
                    $deleteStmt->close();
                } else {
                    $message = 'Articolo non trovato';
                    $messageType = 'danger';
                }
                $checkStmt->close();
            }
            break;
            
        case 'delete_utente':
            if ($id > 0 && $id != $_SESSION['utente_id']) {
                // Elimino l'utente
                $deleteSql = "DELETE FROM UtentiSito WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $id);
                
                if ($deleteStmt->execute()) {
                    $message = 'Utente eliminato con successo';
                    $messageType = 'success';
                } else {
                    $message = 'Errore nell\'eliminazione dell\'utente';
                    $messageType = 'danger';
                }
                $deleteStmt->close();
            }
            break;
            
        case 'toggle_utente':
            if ($id > 0 && $id != $_SESSION['utente_id'] && isset($_GET['status'])) {
                $status = intval($_GET['status']);
                $toggleSql = "UPDATE UtentiSito SET isDeleted = ? WHERE id = ?";
                $toggleStmt = $conn->prepare($toggleSql);
                $toggleStmt->bind_param("ii", $status, $id);
                
                if ($toggleStmt->execute()) {
                    $action_text = $status == 1 ? 'disattivato' : 'riattivato';
                    $message = "Utente $action_text con successo";
                    $messageType = 'success';
                } else {
                    $message = 'Errore nell\'aggiornamento dello stato utente';
                    $messageType = 'danger';
                }
                $toggleStmt->close();
            }
            break;
        
        case 'delete_categoria':
            if ($id > 0) {
                $deleteSql = "UPDATE CategorieArticoli SET isDeleted = 1 WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $id);
                
                if ($deleteStmt->execute()) {
                    $message = 'Categoria eliminata con successo';
                    $messageType = 'success';
                } else {
                    $message = 'Errore nell\'eliminazione della categoria';
                    $messageType = 'danger';
                }
                $deleteStmt->close();
            }
            break;
        
        case 'delete_richiesta':
            if ($id > 0) {
                $deleteSql = "UPDATE Richieste SET isDeleted = 1 WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $id);
                
                if ($deleteStmt->execute()) {
                    $message = 'Richiesta eliminata con successo';
                    $messageType = 'success';
                } else {
                    $message = 'Errore nell\'eliminazione della richiesta';
                    $messageType = 'danger';
                }
                $deleteStmt->close();
            }
            break;
        
        case 'delete_tipologia':
            if ($id > 0) {
                $deleteSql = "UPDATE MotiviRichieste SET isDeleted = 1 WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $id);
                
                if ($deleteStmt->execute()) {
                    $message = 'Tipologia eliminata con successo';
                    $messageType = 'success';
                } else {
                    $message = 'Errore nell\'eliminazione della tipologia';
                    $messageType = 'danger';
                }
                $deleteStmt->close();
            }
            break;
    }
    
    // Reindirizza per evitare il refresh della stessa azione
    if ($message) {
        $redirect_url = 'dashboard.php?msg=' . urlencode($message) . '&type=' . $messageType;
        header("Location: $redirect_url");
        exit();
    }
}

// Mostro messaggi se presenti
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Dashboard</title>
		
		<!-- Favicon -->
		<link rel="icon" type="image/png" href="img/logo.png">
		<link rel="shortcut icon" type="image/png" href="img/logo.png">
		<link rel="apple-touch-icon" href="img/logo.png">
		
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/dashboard.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<nav class="col-md-2 d-none d-md-block sidebar">
					<div class="position-sticky">
						<ul class="nav flex-column mt-4">
							<li class="nav-item">
								<a class="nav-link active" href="#richieste" onclick="showSection('richieste')">📬 Richieste</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#portfolio" onclick="showSection('portfolio')">💼 Portfolio</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#articoli" onclick="showSection('articoli')">📰 Articoli</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#utenti" onclick="showSection('utenti')">👥 Utenti</a>
							</li>
						</ul>
						
						<!-- Pulsante Logout in fondo alla sidebar -->
						<div class="mt-auto mb-4 px-3">
							<button class="btn btn-danger w-100" onclick="logout()">
								🚪 Logout
							</button>
						</div>
					</div>
		</nav>
		<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
			<?php if ($message): ?>
				<div class="alert alert-<?= $messageType ?> alert-dismissible fade show mt-3" role="alert">
					<?= htmlspecialchars($message) ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			<?php endif; ?>
			
			<!-- Sezione Richieste -->
			<!-- Sezione Articoli -->
			<div id="section-articoli" style="display:none;">
				<h2>📰 Articoli</h2>
				<button class="btn btn-success mb-2" onclick="addArticolo()">➕ Crea nuovo articolo</button>
				
				<?php
				// Query articoli con categorie e allegati
				$sql_articoli = "SELECT a.id, a.titolo, a.testo, a.categoriaArticolo, a.data, a.allegato, c.categoriaArticolo as categoria_nome,
								 al.percorsoFile as allegato_percorso
								 FROM Articoli a 
								 LEFT JOIN CategorieArticoli c ON a.categoriaArticolo = c.id 
								 LEFT JOIN Allegati al ON a.allegato = al.id
								 WHERE a.isDeleted = 0 
								 ORDER BY a.data DESC";
				$result_articoli = $conn->query($sql_articoli);
				?>
				
				<div class="table-card fade-in">
				<table class="table align-middle">
					<thead>
						<tr>
							<th>📝 ID</th>
							<th>📚 Titolo</th>
							<th>🏷️ Categoria</th>
							<th>📅 Data</th>
							<th>⚙️ Azioni</th>
						</tr>
					</thead>
					<tbody id="articoli-table">
						<?php
						if ($result_articoli->num_rows > 0) {
							while($row = $result_articoli->fetch_assoc()) {
								// Decodifica testo per anteprima
								$testoOriginale = $row['testo'];
								$testoDecodificato = $testoOriginale;
								
								// Per compatibilità, proviamo a decodificare se contiene slashes
								if (strpos($testoOriginale, '\\') !== false) {
									$testoDecodificato = stripslashes($testoOriginale);
								}
								
								$anteprimaTesto = strlen($testoDecodificato) > 50 ? substr(strip_tags($testoDecodificato), 0, 50) . '...' : strip_tags($testoDecodificato);
								
								// Preparo l'oggetto con i campi necessari per JavaScript
								$rowForJS = $row;
								$rowForJS['testo_decoded'] = $testoDecodificato;
								
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['titolo']) . "</td>";
								echo "<td>" . htmlspecialchars($row['categoria_nome'] ?? 'Senza categoria') . "</td>";
								echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
								echo "<td class='table-actions'>";
								echo "<button class='btn btn-info btn-sm' onclick='viewArticolo(" . json_encode($rowForJS) . ")'>👁️ Visualizza</button>";
								echo "<button class='btn btn-primary btn-sm' onclick='editArticolo(" . json_encode($rowForJS) . ")'>✏️ Modifica</button>";
								echo "<button class='btn btn-danger btn-sm' onclick='deleteArticolo(" . $row['id'] . ")'>🗑️ Elimina</button>";
								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='5' class='text-center'>Nessun articolo trovato</td></tr>";
						}
						?>
					</tbody>
				</table>
				</div>

				<h3 class="mt-5">🗂️ Categorie Articoli</h3>
				<button class="btn btn-success mb-3" onclick="addCategoriaArticolo()">➕ Aggiungi Categoria</button>
				
				<?php
				// Query categorie articoli
				$sql_categorie = "SELECT id, categoriaArticolo FROM CategorieArticoli WHERE isDeleted = 0 ORDER BY categoriaArticolo ASC";
				$result_categorie = $conn->query($sql_categorie);
				?>
				
				<div class="table-card fade-in">
				<table class="table align-middle">
					<thead>
						<tr>
							<th>🆔 ID</th>
							<th>🗂️ Nome Categoria</th>
							<th>📊 Articoli Collegati</th>
							<th>⚙️ Azioni</th>
						</tr>
					</thead>
					<tbody id="categorie-articoli-table">
						<?php
						if ($result_categorie->num_rows > 0) {
							while($row = $result_categorie->fetch_assoc()) {
								// Conto gli articoli collego a questa categoria
								$count_sql = "SELECT COUNT(*) as count FROM Articoli WHERE categoriaArticolo = " . $row['id'] . " AND isDeleted = 0";
								$count_result = $conn->query($count_sql);
								$article_count = $count_result->fetch_assoc()['count'];
								
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td><strong>" . htmlspecialchars($row['categoriaArticolo']) . "</strong></td>";
								echo "<td><span class='badge bg-info'>" . $article_count . " articoli</span></td>";
								echo "<td class='table-actions'>";
								echo "<button class='btn btn-primary btn-sm me-1' onclick='editCategoriaArticolo(" . json_encode($row) . ")'>✏️ Modifica</button>";
								echo "<button class='btn btn-danger btn-sm' onclick='deleteCategoriaArticolo(" . $row['id'] . ")'>🗑️ Elimina</button>";
								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='4' class='text-center text-muted'>Nessuna categoria trovata</td></tr>";
						}
						?>
					</tbody>
				</table>
				</div>
			</div>
			<div id="section-richieste">
				<h2>📬 Richieste in entrata</h2>
                <?php
				// Query richieste con tutte le informazioni e nome del motivo
$sql = "SELECT r.id, r.nome, r.cognome, r.telefono, r.cellulare, r.mail, r.indirizzo, r.nazione, 
               r.testoRichiesta, r.motivoRichiesta, r.data, m.nomeMotivoRichiesta 
        FROM Richieste r 
        LEFT JOIN MotiviRichieste m ON r.motivoRichiesta = m.id 
        WHERE r.isDeleted = 0 
        ORDER BY r.data DESC";
$result = $conn->query($sql);
?>

<div class="table-card fade-in">
<table class="table align-middle">
    <thead>
        <tr>
            <th>🆔 ID</th>
            <th>👤 Nome</th>
            <th>📧 Email</th>
            <th>💬 Messaggio</th>
            <th>⚙️ Azioni</th>
        </tr>
    </thead>
    <tbody id="richieste-table">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nome']) . " " . htmlspecialchars($row['cognome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($row['testoRichiesta'], 0, 50)) . "...</td>";
                echo "<td class='table-actions'>
                        <button class='btn btn-info btn-sm' onclick='viewRichiesta(" . json_encode($row) . ")'>Visualizza</button>
                        <button class='btn btn-danger btn-sm' onclick='deleteRow(" . $row['id'] . ")'>Elimina</button>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center'>Nessuna richiesta trovata</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>

				<h3 class="mt-5">🏷️ Tipologie richieste</h3>
				<button class="btn btn-success mb-2" onclick="addTipo()">Aggiungi tipologia</button>
				<?php
				// Query tipologie richieste
				$sql_tipologie = "SELECT id, nomeMotivoRichiesta FROM MotiviRichieste WHERE isDeleted = 0 ORDER BY nomeMotivoRichiesta ASC";
				$result_tipologie = $conn->query($sql_tipologie);
				?>
				<div class="table-card fade-in">
				<table class="table align-middle">
					<thead>
						<tr>
							<th>🆔 ID</th>
							<th>🏷️ Tipologia</th>
							<th>⚙️ Azioni</th>
						</tr>
					</thead>
					<tbody id="tipologie-table">
						<?php
						if ($result_tipologie->num_rows > 0) {
							while($row = $result_tipologie->fetch_assoc()) {
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['nomeMotivoRichiesta']) . "</td>";
								echo "<td class='table-actions'>
										<button class='btn btn-primary btn-sm' onclick='editTipo(this)'>Modifica</button>
										<button class='btn btn-danger btn-sm' onclick='deleteTipologia(" . $row['id'] . ")'>Elimina</button>
									  </td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='3' class='text-center'>Nessuna tipologia trovata</td></tr>";
						}
						?>
					</tbody>
				</table>
				</div>
			</div>
			<!-- Sezione Portfolio -->
			<div id="section-portfolio" style="display:none;">
				<h2>💼 Portfolio</h2>
				<div class="stats-card primary fade-in">
					<h4>🚧 Sezione in sviluppo</h4>
					<p class="mb-0">La gestione del portfolio sarà disponibile a breve.</p>
				</div>
			</div>
			
			<!-- Sezione Utenti -->
			<div id="section-utenti" style="display:none;">
				<h2>👥 Gestione Utenti</h2>
				<button class="btn btn-success mb-2" onclick="addUtente()">➕ Crea nuovo utente</button>
				
				<?php
				// Query utenti
				$sql_utenti = "SELECT id, login, isDeleted FROM UtentiSito ORDER BY login ASC";
				$result_utenti = $conn->query($sql_utenti);
				?>
				
				<div class="table-card fade-in">
				<table class="table align-middle">
					<thead>
						<tr>
							<th>🆔 ID</th>
							<th>📧 Login/Email</th>
							<th>📊 Stato</th>
							<th>⚙️ Azioni</th>
						</tr>
					</thead>
					<tbody id="utenti-table">
						<?php
						if ($result_utenti->num_rows > 0) {
							while($row = $result_utenti->fetch_assoc()) {
								$stato = $row['isDeleted'] == 0 ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-danger">Disattivato</span>';
								echo "<tr>";
								echo "<td>" . htmlspecialchars($row['id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['login']) . "</td>";
								echo "<td>$stato</td>";
								echo "<td class='table-actions'>";
								
								if ($row['isDeleted'] == 0) {
									echo "<button class='btn btn-primary btn-sm' onclick='editUtente(" . json_encode($row) . ")'>✏️ Modifica</button>";
									echo "<button class='btn btn-warning btn-sm' onclick='toggleUtenteStatus(" . $row['id'] . ", 1)'>🚫 Disattiva</button>";
								} else {
									echo "<button class='btn btn-success btn-sm' onclick='toggleUtenteStatus(" . $row['id'] . ", 0)'>✅ Riattiva</button>";
								}
								
								echo "<button class='btn btn-danger btn-sm' onclick='deleteUtente(" . $row['id'] . ")'>🗑️ Elimina</button>";
								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='4' class='text-center'>Nessun utente trovato</td></tr>";
						}
						?>
					</tbody>
				</table>
				</div>
			</div>
		</main>
	</div>
</div>
<script src="js/bootstrap.bundle.js"></script>
<script>
// Funzione per caricare immagini
async function uploadImage(file) {
	const formData = new FormData();
	formData.append('image', file);
	
	const response = await fetch('upload_image.php', {
		method: 'POST',
		body: formData
	});
	
	if (!response.ok) {
		throw new Error('Errore HTTP: ' + response.status);
	}
	
	const data = await response.json();
	
	if (!data.success) {
		throw new Error(data.message);
	}
	
	return data;
}

// Funzioni per gestire anteprime immagini
function handleImagePreview(inputId, previewContainerId, previewImgId) {
	const input = document.getElementById(inputId);
	const previewContainer = document.getElementById(previewContainerId);
	const previewImg = document.getElementById(previewImgId);
	
	input.addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			// Verifico che sia un'immagine
			if (file.type.startsWith('image/')) {
				const reader = new FileReader();
				reader.onload = function(e) {
					previewImg.src = e.target.result;
					previewContainer.style.display = 'block';
				};
				reader.readAsDataURL(file);
			} else {
				alert('Seleziona un file immagine valido');
				input.value = '';
				previewContainer.style.display = 'none';
			}
		} else {
			previewContainer.style.display = 'none';
		}
	});
}

function showSection(section) {
	// Nascondo tutte le sezioni
	const sections = ['richieste', 'portfolio', 'articoli', 'utenti'];
	sections.forEach(s => {
		const element = document.getElementById('section-' + s);
		if (s === section) {
			element.style.display = '';
			element.classList.add('fade-in');
			// Rimuovo la classe dopo l'animazione per poterla riapplicare
			setTimeout(() => element.classList.remove('fade-in'), 500);
		} else {
			element.style.display = 'none';
		}
	});
	
	// Aggiorno menu attivo
	document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
	document.querySelector('.sidebar a[href="#' + section + '"]').classList.add('active');
}

// Funzione per logout
function logout() {
	if (confirm('Sei sicuro di voler uscire dalla dashboard?')) {
		// Invio richiesta per distruggere la sessione
		fetch('logout.php', {
			method: 'POST'
		}).then(() => {
			// Reindirizzo alla pagina index
			window.location.href = 'index.php';
		}).catch(() => {
			// Anche in caso di errore, reindirizzo comunque perchè si
			window.location.href = 'index.php';
		});
	}
}

// Funzione per visualizzare i dettagli della richiesta
function viewRichiesta(richiesta) {
	// Popolo i campi della modal con i dati della richiesta
	document.getElementById('richiestaId').textContent = richiesta.id || 'N/D';
	document.getElementById('richiestaNome').textContent = richiesta.nome || 'N/D';
	document.getElementById('richiestaCognome').textContent = richiesta.cognome || 'N/D';
	document.getElementById('richiestaMail').textContent = richiesta.mail || 'N/D';
	document.getElementById('richiestaTelefono').textContent = richiesta.telefono || 'N/D';
	document.getElementById('richiestaCellulare').textContent = richiesta.cellulare || 'N/D';
	document.getElementById('richiestaIndirizzo').textContent = richiesta.indirizzo || 'N/D';
	document.getElementById('richiestaNazione').textContent = richiesta.nazione || 'N/D';
	document.getElementById('richiestaMotivoNome').textContent = richiesta.nomeMotivoRichiesta || 'N/D';
	document.getElementById('richiestaTestoCompleto').textContent = richiesta.testoRichiesta || 'N/D';
	
	// Formatto la data se presente
	if (richiesta.data) {
		const dataFormatted = new Date(richiesta.data).toLocaleDateString('it-IT', {
			year: 'numeric',
			month: 'long',
			day: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
		document.getElementById('richiestaData').textContent = dataFormatted;
	} else {
		document.getElementById('richiestaData').textContent = 'N/D';
	}
	
	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('viewRichiestaModal'));
	modal.show();
}
function deleteRow(id) {
	if (confirm('Sei sicuro di voler eliminare questa richiesta?')) {
		window.location.href = 'dashboard.php?action=delete_richiesta&id=' + id;
	}
}
function addTipo() {
	// Pulisco il campo input
	document.getElementById('nuovoNomeTipologia').value = '';
	
	// Reset validazione
	resetFormValidation('addTipologiaForm');
	
	// Mostra la modal
	const modal = new bootstrap.Modal(document.getElementById('addTipologiaModal'));
	modal.show();
}
function editTipo(btn) {
	const row = btn.closest('tr');
	const id = row.children[0].textContent;
	const nome = row.children[1].textContent;
	
	// Imposta i valori nella modal
	document.getElementById('tipologiaId').value = id;
	document.getElementById('nomeTipologia').value = nome;
	
	// Reset validazione
	resetFormValidation('editTipologiaForm');
	
	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('editTipologiaModal'));
	modal.show();
}

// Inizializzazione anteprime immagini
document.addEventListener('DOMContentLoaded', function() {
	// Inizializza anteprime per le modal articoli
	handleImagePreview('nuovaImmagineArticolo', 'nuovaImmaginePreview', 'nuovaImmaginePreviewImg');
	handleImagePreview('editImmagineArticolo', 'editImmaginePreview', 'editImmaginePreviewImg');
});

// Event listener per categorie articoli
document.addEventListener('DOMContentLoaded', function() {
	// Salvo nuova categoria articolo
	document.getElementById('saveCategoriaArticolo').addEventListener('click', function() {
		const categoriaArticolo = document.getElementById('nuovaCategoriaNome').value.trim();

		if (!categoriaArticolo) {
			alert('Inserisci un nome per la categoria');
			return;
		}

		const button = this;
		const originalText = button.textContent;
		button.disabled = true;
		button.textContent = 'Salvando...';

		// Uso FormData per una maggiore compatibilità e perchè ho già perso troppo tempo con JSON e non funziona ovunque
		const formData = new FormData();
		formData.append('categoriaArticolo', categoriaArticolo);

		fetch('add_categoria_articolo.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Ricarico la pagina per aggiornare la tabella
				location.reload();
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			button.disabled = false;
			button.textContent = originalText;
		});
	});

	// Aggiorno categoria articolo
	document.getElementById('updateCategoriaArticolo').addEventListener('click', function() {
		const id = document.getElementById('editCategoriaArticoloId').value;
		const categoriaArticolo = document.getElementById('editCategoriaArticoloNome').value.trim();

		if (!categoriaArticolo) {
			alert('Inserisci un nome per la categoria');
			return;
		}

		const button = this;
		const originalText = button.textContent;
		button.disabled = true;
		button.textContent = 'Aggiornando...';

		const formData = new FormData();
		formData.append('id', id);
		formData.append('categoriaArticolo', categoriaArticolo);

		fetch('update_categoria_articolo.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Ricarico la pagina per aggiornare la tabella
				location.reload();
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			button.disabled = false;
			button.textContent = originalText;
		});
	});
});

// Funzione per salvare le modifiche alla tipologia
document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('salvaTipologia').addEventListener('click', function() {
		// Valida il form prima di procedere
		if (!validateForm('editTipologiaForm')) {
			return;
		}
		
		const id = document.getElementById('tipologiaId').value;
		const nomeMotivoRichiesta = document.getElementById('nomeTipologia').value.trim();
		
		// Disabilito il pulsante durante l'operazione
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Salvando...';
		
		// Invio richiesta AJAX per aggiornare nel database
		fetch('update_tipologia.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				id: parseInt(id),
				nomeMotivoRichiesta: nomeMotivoRichiesta
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Aggiorno la riga nella tabella
				const rows = document.querySelectorAll('#tipologie-table tr');
				for (let row of rows) {
					if (row.children[0] && row.children[0].textContent == id) {
						row.children[1].textContent = nomeMotivoRichiesta;
						break;
					}
				}
				
				// Chiudo la modal
				const modal = bootstrap.Modal.getInstance(document.getElementById('editTipologiaModal'));
				modal.hide();
				
				alert('Tipologia aggiornata con successo!');
			} else {
				alert('Errore nell\'aggiornamento: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione durante l\'aggiornamento');
		})
		.finally(() => {
			// Riabilito il pulsante
			saveBtn.disabled = false;
			saveBtn.textContent = 'Modifica';
		});
	});
	
	// Event listener per aggiungere nuove tipologie
	document.getElementById('salvaNuovaTipologia').addEventListener('click', function() {
		// Valido il form prima di procedere
		if (!validateForm('addTipologiaForm')) {
			return;
		}
		
		const nomeMotivoRichiesta = document.getElementById('nuovoNomeTipologia').value.trim();
		
		// Disabilito il pulsante durante l'operazione
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Aggiungendo...';
		
		// Invio richiesta AJAX per inserire nel database
		fetch('add_tipologia.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				nomeMotivoRichiesta: nomeMotivoRichiesta
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Aggiungo la nuova riga alla tabella
				const table = document.getElementById('tipologie-table');
				const row = table.insertRow();
				row.innerHTML = `
					<td>${data.id}</td>
					<td>${data.nomeMotivoRichiesta}</td>
					<td class='table-actions'>
						<button class='btn btn-primary btn-sm' onclick='editTipo(this)'>Modifica</button>
						<button class='btn btn-danger btn-sm' onclick='deleteTipologia(${data.id})'>Elimina</button>
					</td>
				`;
				
				// Chiudo la modal e pulisco il form
				const modal = bootstrap.Modal.getInstance(document.getElementById('addTipologiaModal'));
				modal.hide();
				document.getElementById('nuovoNomeTipologia').value = '';
				
				alert('Tipologia aggiunta con successo!');
			} else {
				alert('Errore nell\'aggiunta: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione durante l\'aggiunta');
		})
		.finally(() => {
			// Riabilito il pulsante
			saveBtn.disabled = false;
			saveBtn.textContent = 'Aggiungi';
		});
	});
	
	// Event listener per aggiungere nuovi utenti
	document.getElementById('salvaNuovoUtente').addEventListener('click', function() {
		// Valido il form prima di procedere
		if (!validateForm('addUtenteForm')) {
			return;
		}
		
		const login = document.getElementById('nuovoLoginUtente').value.trim();
		const password = document.getElementById('nuovaPasswordUtente').value.trim();
		
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Creando...';
		
		fetch('add_utente.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				login: login,
				password: password
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload(); // Ricarico la pagina per mostrare il nuovo utente
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			saveBtn.disabled = false;
			saveBtn.textContent = 'Crea Utente';
		});
	});
	
	// Funzione per uploadare immagini
	async function uploadImage(file) {
		const formData = new FormData();
		formData.append('image', file);
		
		const response = await fetch('upload_image.php', {
			method: 'POST',
			body: formData
		});
		
		const result = await response.json();
		if (!result.success) {
			throw new Error(result.message);
		}
		
		return result;
	}
	
	// Event listener per modificare utenti
	document.getElementById('salvaModificaUtente').addEventListener('click', function() {
		// Valido il form prima di procedere (escludo password se vuota)
		const passwordField = document.getElementById('editPasswordUtente');
		const passwordValue = passwordField.value.trim();
		
		// Se la password è vuota, rimuovo temporaneamente l'attributo minlength per la validazione
		if (passwordValue === '') {
			passwordField.removeAttribute('minlength');
		} else {
			passwordField.setAttribute('minlength', '6');
		}
		
		if (!validateForm('editUtenteForm')) {
			return;
		}
		
		const id = document.getElementById('editUtenteId').value;
		const login = document.getElementById('editLoginUtente').value.trim();
		const password = passwordValue;
		
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Salvando...';
		
		fetch('update_utente.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				id: parseInt(id),
				login: login,
				password: password // Vuoto se non si vuole cambiare
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload(); // Ricarico la pagina per mostrare le modifiche
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			saveBtn.disabled = false;
			saveBtn.textContent = 'Salva Modifiche';
		});
	});
	
	// Event listener per aggiungere nuovi articoli
	document.getElementById('salvaNuovoArticolo').addEventListener('click', async function() {
		// Valido il form prima di procedere
		if (!validateForm('addArticoloForm')) {
			return;
		}
		
		const titolo = document.getElementById('nuovoTitoloArticolo').value.trim();
		const testo = document.getElementById('nuovoTestoArticolo').value.trim();
		const categoriaArticolo = document.getElementById('nuovaCategoriaArticolo').value;
		const imageFile = document.getElementById('nuovaImmagineArticolo').files[0];
		
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Creando...';
		
		let allegatoId = null;
		
		// Upload immagine se presente
		if (imageFile) {
			try {
				saveBtn.textContent = 'Caricando immagine...';
				const imageData = await uploadImage(imageFile);
				allegatoId = imageData.allegatoId;
			} catch (error) {
				alert('Errore nel caricamento immagine: ' + error.message);
				saveBtn.disabled = false;
				saveBtn.textContent = 'Crea Articolo';
				return;
			}
		}
		
		saveBtn.textContent = 'Salvando articolo...';
		
		fetch('add_articolo.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				titolo: titolo,
				testo: testo,
				categoriaArticolo: parseInt(categoriaArticolo),
				allegatoId: allegatoId
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload(); // Ricarico la pagina per mostrare il nuovo articolo
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			saveBtn.disabled = false;
			saveBtn.textContent = 'Crea Articolo';
		});
	});
	
	// Event listener per modificare articoli
	document.getElementById('salvaModificaArticolo').addEventListener('click', async function() {
		// Valido il form prima di procedere
		if (!validateForm('editArticoloForm')) {
			return;
		}
		
		const id = document.getElementById('editArticoloId').value;
		const titolo = document.getElementById('editTitoloArticolo').value.trim();
		const testo = document.getElementById('editTestoArticolo').value.trim();
		const categoriaArticolo = document.getElementById('editCategoriaArticolo').value;
		const imageFile = document.getElementById('editImmagineArticolo').files[0];
		
		const saveBtn = this;
		saveBtn.disabled = true;
		saveBtn.textContent = 'Salvando...';
		
		let allegatoId = null;
		
		// Upload nuova immagine se presente
		if (imageFile) {
			try {
				saveBtn.textContent = 'Caricando immagine...';
				const imageData = await uploadImage(imageFile);
				allegatoId = imageData.allegatoId;
			} catch (error) {
				alert('Errore nel caricamento immagine: ' + error.message);
				saveBtn.disabled = false;
				saveBtn.textContent = 'Salva Modifiche';
				return;
			}
		}
		
		saveBtn.textContent = 'Salvando modifiche...';
		
		const requestBody = {
			id: parseInt(id),
			titolo: titolo,
			testo: testo,
			categoriaArticolo: parseInt(categoriaArticolo)
		};
		
		// Aggiungi allegato solo se è stata caricata una nuova immagine
		if (allegatoId) {
			requestBody.allegatoId = allegatoId;
		}
		
		fetch('update_articolo.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(requestBody)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload(); // Ricarico la pagina per mostrare le modifiche
			} else {
				alert('Errore: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Errore:', error);
			alert('Errore di connessione');
		})
		.finally(() => {
			saveBtn.disabled = false;
			saveBtn.textContent = 'Salva Modifiche';
		});
	});
});
function deleteTipo(btn) {
	if (confirm('Eliminare questa tipologia?')) {
		btn.closest('tr').remove();
	}
}
function deleteTipologia(id) {
	if (confirm('Sei sicuro di voler eliminare questa tipologia?')) {
		window.location.href = 'dashboard.php?action=delete_tipologia&id=' + id;
	}
}
// Articoli
function addArticolo() {
	// Reset form e validazione
	document.getElementById('nuovoTitoloArticolo').value = '';
	document.getElementById('nuovoTestoArticolo').value = '';
	document.getElementById('nuovaCategoriaArticolo').value = '';
	document.getElementById('nuovaImmagineArticolo').value = '';
	
	// Nascondo anteprima
	document.getElementById('nuovaImmaginePreview').style.display = 'none';
	
	resetFormValidation('addArticoloForm');
	
	// Carico le categorie nel select
	loadCategorieArticoli('nuovaCategoriaArticolo');
	
	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('addArticoloModal'));
	modal.show();
}

function editArticolo(articolo) {
	// Imposto i valori nella modal
	document.getElementById('editArticoloId').value = articolo.id;
	document.getElementById('editTitoloArticolo').value = articolo.titolo;
	// Il testo è già decodificato dal server
	document.getElementById('editTestoArticolo').value = articolo.testo_decoded;
	document.getElementById('editCategoriaArticolo').value = articolo.categoriaArticolo;
	
	// Mostro informazioni e anteprima immagine corrente
	const currentImageInfo = document.getElementById('currentImageInfo');
	const currentImagePreview = document.getElementById('currentImagePreview');
	const currentImagePreviewImg = document.getElementById('currentImagePreviewImg');
	
	if (articolo.allegato_percorso) {
		currentImageInfo.innerHTML = `📎 Immagine corrente: <a href="${articolo.allegato_percorso}" target="_blank">Visualizza</a>`;
		currentImagePreviewImg.src = articolo.allegato_percorso;
		currentImagePreview.style.display = 'block';
	} else {
		currentImageInfo.innerHTML = '📎 Nessuna immagine presente';
		currentImagePreview.style.display = 'none';
	}
	
	// Reset campo file e anteprima nuova immagine
	document.getElementById('editImmagineArticolo').value = '';
	document.getElementById('editImmaginePreview').style.display = 'none';
	
	// Reset validazione
	resetFormValidation('editArticoloForm');
	
	// Carico le categorie nel select
	loadCategorieArticoli('editCategoriaArticolo', articolo.categoriaArticolo);

	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('editArticoloModal'));
	modal.show();
}

function viewArticolo(articolo) {
	// Popolo i campi della modal con i dati dell'articolo
	document.getElementById('viewArticoloId').textContent = articolo.id;
	document.getElementById('viewArticoloTitolo').textContent = articolo.titolo;
	document.getElementById('viewArticoloCategoria').textContent = articolo.categoria_nome || 'Senza categoria';
	document.getElementById('viewArticoloData').textContent = new Date(articolo.data).toLocaleDateString('it-IT');

	// Gestisco immagine
	const imgContainer = document.getElementById('viewArticoloImmagineContainer');
	const imgElement = document.getElementById('viewArticoloImmagine');
	
	if (articolo.allegato_percorso) {
		imgElement.src = articolo.allegato_percorso;
		imgContainer.style.display = 'block';
	} else {
		imgContainer.style.display = 'none';
	}
	
	// Il testo è già decodificato dal server
	document.getElementById('viewArticoloTesto').innerHTML = articolo.testo_decoded;

	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('viewArticoloModal'));
	modal.show();
}

function deleteArticolo(id) {
	if (confirm('Sei sicuro di voler eliminare questo articolo? Questa azione non può essere annullata.')) {
		// Approccio diretto - ricarico la pagina con i parametri
		window.location.href = 'dashboard.php?action=delete_articolo&id=' + id;
	}
}

// Funzione per caricare le categorie nei select
function loadCategorieArticoli(selectId, selectedValue = null) {
	fetch('get_categorie_articoli.php')
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				const select = document.getElementById(selectId);
				select.innerHTML = '<option value="">Seleziona categoria...</option>';
				
				data.data.forEach(categoria => {
					const option = document.createElement('option');
					option.value = categoria.id;
					option.textContent = categoria.nome;
					if (selectedValue && categoria.id == selectedValue) {
						option.selected = true;
					}
					select.appendChild(option);
				});
			}
		})
		.catch(error => {
			console.error('Errore nel caricamento categorie:', error);
		});
}
// Categorie articoli
function addCategoriaArticolo() {
	// Pulisce il campo input
	document.getElementById('nuovaCategoriaNome').value = '';
	
	// Mostra la modal
	const modal = new bootstrap.Modal(document.getElementById('addCategoriaArticoloModal'));
	modal.show();
}

function editCategoriaArticolo(categoria) {
	// Imposto i valori nella modal
	document.getElementById('editCategoriaArticoloId').value = categoria.id;
	document.getElementById('editCategoriaArticoloNome').value = categoria.categoriaArticolo;

	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('editCategoriaArticoloModal'));
	modal.show();
}

function deleteCategoriaArticolo(id) {
	if (confirm('Sei sicuro di voler eliminare questa categoria? Questa azione non può essere annullata.')) {
		window.location.href = 'dashboard.php?action=delete_categoria&id=' + id;
	}
}

// Funzioni per gestione utenti
function addUtente() {
	// Pulisce i campi input
	document.getElementById('nuovoLoginUtente').value = '';
	document.getElementById('nuovaPasswordUtente').value = '';
	
	// Reset validazione
	resetFormValidation('addUtenteForm');
	
	// Mostro la modal
	const modal = new bootstrap.Modal(document.getElementById('addUtenteModal'));
	modal.show();
}

function editUtente(utente) {
	// Imposto i valori nella modal
	document.getElementById('editUtenteId').value = utente.id;
	document.getElementById('editLoginUtente').value = utente.login;
	document.getElementById('editPasswordUtente').value = '';
	
	// Reset validazione
	resetFormValidation('editUtenteForm');
	
	// Mostra modal
	const modal = new bootstrap.Modal(document.getElementById('editUtenteModal'));
	modal.show();
}

function toggleUtenteStatus(id, status) {
	const action = status == 1 ? 'disattivare' : 'riattivare';
	if (confirm(`Sei sicuro di voler ${action} questo utente?`)) {
		window.location.href = 'dashboard.php?action=toggle_utente&id=' + id + '&status=' + status;
	}
}

function deleteUtente(id) {
	if (confirm('Sei sicuro di voler eliminare definitivamente questo utente? Questa azione non può essere annullata.')) {
		window.location.href = 'dashboard.php?action=delete_utente&id=' + id;
	}
}
</script>

<!-- Modal per aggiungere utente -->
<div class="modal fade" id="addUtenteModal" tabindex="-1" aria-labelledby="addUtenteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUtenteModalLabel">👥 Crea Nuovo Utente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addUtenteForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="nuovoLoginUtente" class="form-label">📧 Email/Login</label>
            <input type="email" class="form-control" id="nuovoLoginUtente" placeholder="Inserisci email utente" required>
            <div class="invalid-feedback">
              Inserisci un indirizzo email valido
            </div>
          </div>
          <div class="mb-3">
            <label for="nuovaPasswordUtente" class="form-label">🔒 Password</label>
            <input type="password" class="form-control" id="nuovaPasswordUtente" placeholder="Inserisci password" minlength="6" required>
            <div class="invalid-feedback">
              La password deve essere di almeno 6 caratteri
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-success" id="salvaNuovoUtente">Crea Utente</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per modificare utente -->
<div class="modal fade" id="editUtenteModal" tabindex="-1" aria-labelledby="editUtenteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUtenteModalLabel">✏️ Modifica Utente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUtenteForm" class="needs-validation" novalidate>
          <input type="hidden" id="editUtenteId">
          <div class="mb-3">
            <label for="editLoginUtente" class="form-label">📧 Email/Login</label>
            <input type="email" class="form-control" id="editLoginUtente" required>
            <div class="invalid-feedback">
              Inserisci un indirizzo email valido
            </div>
          </div>
          <div class="mb-3">
            <label for="editPasswordUtente" class="form-label">🔒 Nuova Password</label>
            <input type="password" class="form-control" id="editPasswordUtente" placeholder="Lascia vuoto per non modificare" minlength="6">
            <div class="form-text">💡 Lascia vuoto se non vuoi cambiare la password</div>
            <div class="invalid-feedback">
              La password deve essere di almeno 6 caratteri
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" id="salvaModificaUtente">Salva Modifiche</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per visualizzare dettagli richiesta -->
<div class="modal fade" id="viewRichiestaModal" tabindex="-1" aria-labelledby="viewRichiestaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewRichiestaModalLabel">📋 Dettagli Richiesta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>👤 Informazioni Personali</h6>
            <p><strong>🆔 ID:</strong> <span id="richiestaId"></span></p>
            <p><strong>👤 Nome:</strong> <span id="richiestaNome"></span></p>
            <p><strong>👤 Cognome:</strong> <span id="richiestaCognome"></span></p>
            <p><strong>📧 Email:</strong> <span id="richiestaMail"></span></p>
            <p><strong>📞 Telefono:</strong> <span id="richiestaTelefono"></span></p>
            <p><strong>📱 Cellulare:</strong> <span id="richiestaCellulare"></span></p>
          </div>
          <div class="col-md-6">
            <h6>ℹ️ Informazioni Aggiuntive</h6>
            <p><strong>🏠 Indirizzo:</strong> <span id="richiestaIndirizzo"></span></p>
            <p><strong>🌍 Nazione:</strong> <span id="richiestaNazione"></span></p>
            <p><strong>🏷️ Motivo:</strong> <span id="richiestaMotivoNome"></span></p>
            <p><strong>📅 Data:</strong> <span id="richiestaData"></span></p>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-12">
            <h6>💬 Testo Richiesta</h6>
            <div class="request-detail-box">
              <span id="richiestaTestoCompleto"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per aggiungere tipologia -->
<div class="modal fade" id="addTipologiaModal" tabindex="-1" aria-labelledby="addTipologiaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTipologiaModalLabel">➕ Aggiungi Nuova Tipologia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addTipologiaForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="nuovoNomeTipologia" class="form-label">Nome Tipologia</label>
            <input type="text" class="form-control" id="nuovoNomeTipologia" placeholder="Inserisci il nome della tipologia" minlength="2" required>
            <div class="invalid-feedback">
              Il nome della tipologia deve essere di almeno 2 caratteri
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-success" id="salvaNuovaTipologia">Aggiungi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per modifica tipologia -->
<div class="modal fade" id="editTipologiaModal" tabindex="-1" aria-labelledby="editTipologiaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTipologiaModalLabel">✏️ Modifica Tipologia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editTipologiaForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="nomeTipologia" class="form-label">Nome Tipologia</label>
            <input type="text" class="form-control" id="nomeTipologia" minlength="2" required>
            <input type="hidden" id="tipologiaId">
            <div class="invalid-feedback">
              Il nome della tipologia deve essere di almeno 2 caratteri
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Esci</button>
        <button type="button" class="btn btn-primary" id="salvaTipologia">Modifica</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per aggiungere articolo -->
<div class="modal fade" id="addArticoloModal" tabindex="-1" aria-labelledby="addArticoloModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addArticoloModalLabel">📰 Crea Nuovo Articolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addArticoloForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="nuovoTitoloArticolo" class="form-label">📚 Titolo</label>
            <input type="text" class="form-control" id="nuovoTitoloArticolo" placeholder="Inserisci il titolo dell'articolo" minlength="3" required>
            <div class="invalid-feedback">
              Il titolo deve essere di almeno 3 caratteri
            </div>
          </div>
          <div class="mb-3">
            <label for="nuovaCategoriaArticolo" class="form-label">🗂️ Categoria</label>
            <select class="form-select" id="nuovaCategoriaArticolo" required>
              <option value="">Seleziona categoria...</option>
            </select>
            <div class="invalid-feedback">
              Seleziona una categoria
            </div>
          </div>
          <div class="mb-3">
            <label for="nuovoTestoArticolo" class="form-label">📝 Testo</label>
            <textarea class="form-control" id="nuovoTestoArticolo" rows="8" placeholder="Inserisci il contenuto dell'articolo (supporta HTML)" minlength="10" required></textarea>
            <div class="form-text">💡 Puoi utilizzare tag HTML per formattare il testo</div>
            <div class="invalid-feedback">
              Il testo deve essere di almeno 10 caratteri
            </div>
          </div>
          <div class="mb-3">
            <label for="nuovaImmagineArticolo" class="form-label">🖼️ Immagine (opzionale)</label>
            <input type="file" class="form-control" id="nuovaImmagineArticolo" accept="image/*">
            <div class="form-text">💡 Formati supportati: JPG, PNG, GIF, WebP (max 5MB)</div>
            <div class="invalid-feedback">
              Seleziona un'immagine valida
            </div>
            <!-- Anteprima immagine -->
            <div id="nuovaImmaginePreview" class="mt-3" style="display: none;">
              <label class="form-label">👀 Anteprima:</label>
              <div class="border rounded p-2">
                <img id="nuovaImmaginePreviewImg" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;" alt="Anteprima immagine">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-success" id="salvaNuovoArticolo">Crea Articolo</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per modificare articolo -->
<div class="modal fade" id="editArticoloModal" tabindex="-1" aria-labelledby="editArticoloModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editArticoloModalLabel">✏️ Modifica Articolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editArticoloForm" class="needs-validation" novalidate>
          <input type="hidden" id="editArticoloId">
          <div class="mb-3">
            <label for="editTitoloArticolo" class="form-label">📚 Titolo</label>
            <input type="text" class="form-control" id="editTitoloArticolo" minlength="3" required>
            <div class="invalid-feedback">
              Il titolo deve essere di almeno 3 caratteri
            </div>
          </div>
          <div class="mb-3">
            <label for="editCategoriaArticolo" class="form-label">🗂️ Categoria</label>
            <select class="form-select" id="editCategoriaArticolo" required>
              <option value="">Seleziona categoria...</option>
            </select>
            <div class="invalid-feedback">
              Seleziona una categoria
            </div>
          </div>
          <div class="mb-3">
            <label for="editTestoArticolo" class="form-label">📝 Testo</label>
            <textarea class="form-control" id="editTestoArticolo" rows="8" minlength="10" required></textarea>
            <div class="form-text">💡 Puoi utilizzare tag HTML per formattare il testo</div>
            <div class="invalid-feedback">
              Il testo deve essere di almeno 10 caratteri
            </div>
          </div>
          <div class="mb-3">
            <label for="editImmagineArticolo" class="form-label">🖼️ Immagine</label>
            <input type="file" class="form-control" id="editImmagineArticolo" accept="image/*">
            <div class="form-text">💡 Lascia vuoto per mantenere l'immagine attuale. Formati: JPG, PNG, GIF, WebP (max 5MB)</div>
            <div id="currentImageInfo" class="form-text text-muted"></div>
            
            <!-- Immagine corrente -->
            <div id="currentImagePreview" class="mt-3" style="display: none;">
              <label class="form-label">📎 Immagine attuale:</label>
              <div class="border rounded p-2">
                <img id="currentImagePreviewImg" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;" alt="Immagine corrente">
              </div>
            </div>
            
            <!-- Anteprima nuova immagine -->
            <div id="editImmaginePreview" class="mt-3" style="display: none;">
              <label class="form-label">👀 Nuova immagine:</label>
              <div class="border rounded p-2">
                <img id="editImmaginePreviewImg" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;" alt="Anteprima nuova immagine">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary" id="salvaModificaArticolo">Salva Modifiche</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per visualizzare articolo -->
<div class="modal fade" id="viewArticoloModal" tabindex="-1" aria-labelledby="viewArticoloModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewArticoloModalLabel">👁️ Visualizza Articolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>🆔 ID:</strong> <span id="viewArticoloId"></span></p>
            <p><strong>📚 Titolo:</strong> <span id="viewArticoloTitolo"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>🗂️ Categoria:</strong> <span id="viewArticoloCategoria"></span></p>
            <p><strong>📅 Data:</strong> <span id="viewArticoloData"></span></p>
          </div>
        </div>
        <div class="row mb-3" id="viewArticoloImmagineContainer" style="display: none;">
          <div class="col-12">
            <h6>🖼️ Immagine:</h6>
            <img id="viewArticoloImmagine" class="img-fluid rounded" style="max-height: 300px; width: auto;" alt="Immagine articolo">
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <h6>📝 Contenuto:</h6>
            <div id="viewArticoloTesto" class="border p-3 rounded" style="max-height: 400px; overflow-y: auto;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>

<script>
// Bootstrap Form Validation
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
        
        // Aggiungo validazione in tempo reale sui campi
        const inputs = form.querySelectorAll('input[required], input[minlength], input[type="email"]')
        Array.from(inputs).forEach(input => {
            input.addEventListener('blur', () => {
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid')
                    input.classList.add('is-valid')
                } else {
                    input.classList.remove('is-valid')
                    input.classList.add('is-invalid')
                }
            })
            
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid') && input.checkValidity()) {
                    input.classList.remove('is-invalid')
                    input.classList.add('is-valid')
                }
            })
        })
    })
    
    // Funzione helper per validare un form specifico
    window.validateForm = function(formId) {
        const form = document.getElementById(formId)
        if (form) {
            if (form.checkValidity()) {
                form.classList.add('was-validated')
                return true
            } else {
                form.classList.add('was-validated')
                return false
            }
        }
        return false
    }
    
    // Funzione helper per resettare la validazione
    window.resetFormValidation = function(formId) {
        const form = document.getElementById(formId)
        if (form) {
            form.classList.remove('was-validated')
            const inputs = form.querySelectorAll('input')
            Array.from(inputs).forEach(input => {
                input.classList.remove('is-valid', 'is-invalid')
            })
        }
    }
})()
</script>

<!-- Modal per aggiungere categoria articolo -->
<div class="modal fade" id="addCategoriaArticoloModal" tabindex="-1" aria-labelledby="addCategoriaArticoloModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoriaArticoloModalLabel">🗂️ Aggiungi Categoria Articolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addCategoriaArticoloForm" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="nuovaCategoriaNome" class="form-label">📝 Nome Categoria</label>
            <input type="text" class="form-control" id="nuovaCategoriaNome" placeholder="Inserisci nome categoria" required>
            <div class="invalid-feedback">
              Inserisci un nome per la categoria
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Annulla</button>
        <button type="button" class="btn btn-primary" id="saveCategoriaArticolo">💾 Salva Categoria</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal per modificare categoria articolo -->
<div class="modal fade" id="editCategoriaArticoloModal" tabindex="-1" aria-labelledby="editCategoriaArticoloModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCategoriaArticoloModalLabel">✏️ Modifica Categoria Articolo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editCategoriaArticoloForm" class="needs-validation" novalidate>
          <input type="hidden" id="editCategoriaArticoloId">
          <div class="mb-3">
            <label for="editCategoriaArticoloNome" class="form-label">📝 Nome Categoria</label>
            <input type="text" class="form-control" id="editCategoriaArticoloNome" placeholder="Inserisci nome categoria" required>
            <div class="invalid-feedback">
              Inserisci un nome per la categoria
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Annulla</button>
        <button type="button" class="btn btn-primary" id="updateCategoriaArticolo">💾 Aggiorna Categoria</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
