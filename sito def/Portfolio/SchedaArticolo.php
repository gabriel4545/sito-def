<!-- Inizio Menu -->
<?php
include '../layout.php';
?>
<!-- Fine Menu -->

<!-- Inizio header -->
<header style="padding-top: 2rem; margin-bottom: 2rem;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 id="articoloTitolo" style="margin-bottom: 0;">Caricamento articolo...</h1>
            </div>
        </div>
    </div>
</header>
<!-- Fine header -->

<!-- Inizio contenuto articolo -->
<div class="container">
    <div class="row">
        <!-- Contenuto principale -->
        <div class="col-lg-8">
            <div class="card">
                <div id="articoloImmagineContainer" style="display: none; text-align: center; background: #f8f9fa; padding: 20px;">
                    <img id="articoloImmagine" class="img-fluid rounded shadow-sm" style="max-width: 100%; height: auto; object-fit: contain;" alt="Immagine articolo">
                </div>
                <div class="card-body">
                    <div id="articoloContenuto" class="mb-4">
                        <p>Caricamento contenuto...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>📋 Dettagli Articolo</h5>
                </div>
                <div class="card-body">
                    <p><strong>🗂️ Categoria:</strong> <span id="articoloCategoria">-</span></p>
                    <p><strong>📅 Data pubblicazione:</strong> <span id="articoloData">-</span></p>
                    <p><strong>🗓️ Annualità:</strong> <span id="articoloAnnualita">-</span></p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5>🔗 Azioni</h5>
                </div>
                <div class="card-body">
                    <a href="portfolio.php" class="btn btn-primary btn-sm w-100 mb-2">← Torna al Portfolio</a>
                    <button id="condividiArticolo" class="btn btn-outline-secondary btn-sm w-100">📤 Condividi</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fine contenuto articolo -->

<script>
  const index = document.getElementById('portfolio');
  index.classList.add('active');
  index.setAttribute('aria-current', 'page');
  
  // Carico l'articolo
  const urlParams = new URLSearchParams(window.location.search);
  const articoloId = urlParams.get('id');
  
  if (articoloId) {
      fetch(`_getArticolo.php?id=${articoloId}`)
          .then(response => response.json())
          .then(data => {
              if (data && data.length > 0) {
                  const articolo = data[0];
                  
                  // Aggiorno il titolo
                  document.getElementById('articoloTitolo').textContent = articolo.titolo;
                  document.title = articolo.titolo + ' - Portfolio';
                  
                  // Mostro l'immagine se presente
                  const imgContainer = document.getElementById('articoloImmagineContainer');
                  const img = document.getElementById('articoloImmagine');
                  
                  if (articolo.allegato && articolo.allegato.percorsoFile) {
                      // Correggo il percorso per la cartella Portfolio
                      let imagePath = articolo.allegato.percorsoFile;
                      if (imagePath.startsWith('uploads/')) {
                          imagePath = '../' + imagePath; // Aggiunge ../ per uscire dalla cartella Portfolio
                      }
                      img.src = imagePath;
                      img.alt = articolo.titolo;
                      imgContainer.style.display = 'block';
                      console.log('Caricando immagine:', imagePath);
                  } else {
                      imgContainer.style.display = 'none';
                      console.log('Nessuna immagine per questo articolo');
                  }
                  
                  // Contenuto articolo
                  document.getElementById('articoloContenuto').innerHTML = articolo.testo;
                  
                  // Dettagli sidebar
                  document.getElementById('articoloCategoria').textContent = articolo.categoria_nome;
                  document.getElementById('articoloData').textContent = new Date(articolo.data).toLocaleDateString('it-IT');
                  document.getElementById('articoloAnnualita').textContent = articolo.annualita;
                  
                  // Funzione condividi
                  document.getElementById('condividiArticolo').addEventListener('click', function() {
                      if (navigator.share) {
                          navigator.share({
                              title: articolo.titolo,
                              text: 'Dai un\'occhiata a questo articolo!',
                              url: window.location.href
                          });
                      } else {
                          // Fallback: copia URL
                          navigator.clipboard.writeText(window.location.href).then(() => {
                              alert('URL copiato negli appunti!');
                          });
                      }
                  });
                  
              } else {
                  document.getElementById('articoloTitolo').textContent = 'Articolo non trovato';
                  document.getElementById('articoloContenuto').innerHTML = '<p>L\'articolo richiesto non è disponibile.</p>';
              }
          })
          .catch(error => {
              console.error('Errore nel caricamento dell\'articolo:', error);
              document.getElementById('articoloTitolo').textContent = 'Errore di caricamento';
              document.getElementById('articoloContenuto').innerHTML = '<p>Si è verificato un errore nel caricamento dell\'articolo.</p>';
          });
  } else {
      document.getElementById('articoloTitolo').textContent = 'Articolo non specificato';
      document.getElementById('articoloContenuto').innerHTML = '<p>Nessun ID articolo fornito.</p>';
  }
</script>

<?php echo $footer; ?>
</body>

</html>