<?php
include 'layout.php';
?>

<!-- Hero Section -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 70vh; display: flex; align-items: center;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-white">
        <div class="hero-content">
          <h1 class="display-3 fw-bold mb-4" style="text-shadow: 0 4px 12px rgba(0,0,0,0.3);">
            Gabriel Perino Iosif
          </h1>
          <div class="mb-4" style="height: 4px; width: 80px; background: rgba(255,255,255,0.8); border-radius: 2px;"></div>
          <h2 class="h3 fw-light mb-4 opacity-90">
            <i class="bi bi-shield-check me-2"></i>
            IT Specialist & Security Expert
          </h2>
          <h2 class="h3 fw-light mb-4 opacity-90">
            <i class="bi bi-laptop me-2"></i>
            Sviluppo Web & Innovazione Tecnologica
          </h2>
          <p class="lead mb-4 opacity-75">
            Specializzato in sviluppo web e sicurezza informatica, impiegato presso HDI Distribuzione, distributore B2B leader nel settore sicurezza (sistemi antintrusione, controllo accessi, videosorveglianza, rivelazione incendi e networking).
          </p>
          <div class="d-flex gap-3 flex-wrap">
            <a href="/Portfolio/portfolio.php" class="btn btn-light btn-lg px-4 py-3 rounded-pill fw-semibold" style="box-shadow: 0 8px 25px rgba(0,0,0,0.2);">
              <i class="bi bi-briefcase me-2"></i>Scopri il Portfolio
            </a>
            <a href="/contatti.php" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-semibold">
              <i class="bi bi-envelope me-2"></i>Contattami
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6 text-center">
        <div class="hero-image position-relative">
          <div class="floating-card" style="animation: float 6s ease-in-out infinite;">
            <img src="./img/logo.png" alt="GPIOS Logo" 
                 style="width: 200px; height: 200px; border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); filter: drop-shadow(0 10px 20px rgba(255,255,255,0.1));">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(2deg); }
}
</style>
<!-- About Section -->
<div class="container py-5">
  <div class="row align-items-center">
    <div class="col-lg-6 mb-5 mb-lg-0">
      <div class="about-content pe-lg-4">
        <div class="section-header mb-4">
          <h2 class="display-4 fw-bold text-dark mb-3">Chi Sono?</h2>
          <div style="height: 4px; width: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 2px;"></div>
        </div>
        
        <div class="content-text">
          <p class="lead text-muted mb-4">
            Mi chiamo <strong>Perino Gabriel Iosif</strong> e lavoro come <strong>Tecnico - IT Specialist</strong> presso <em>HDI Distribuzione</em>, distributore B2B specializzato in sistemi di sicurezza: TVCC, antintrusione, antincendio, automazione e controllo accessi. Sono un appassionato di tecnologia e del mondo automobilistico.
          </p>
          
          <div class="skills-highlight mb-4">
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="skill-item p-3 rounded-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); border-left: 4px solid #667eea;">
                  <i class="bi bi-shield-lock h4 text-primary mb-2"></i>
                  <h6 class="fw-semibold mb-1">IT Security & Infrastructure</h6>
                  <small class="text-muted">Sicurezza B2B & Manutenzione</small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="skill-item p-3 rounded-3" style="background: linear-gradient(135deg, rgba(118, 75, 162, 0.1), rgba(102, 126, 234, 0.1)); border-left: 4px solid #764ba2;">
                  <i class="bi bi-camera-video h4 text-primary mb-2"></i>
                  <h6 class="fw-semibold mb-1">Security Systems</h6>
                  <small class="text-muted">TVCC, Antintrusione, Automazione</small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="skill-item p-3 rounded-3" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(176, 42, 55, 0.1)); border-left: 4px solid #dc3545;">
                  <i class="bi bi-fire h4 text-danger mb-2"></i>
                  <h6 class="fw-semibold mb-1">Safety & Fire Prevention</h6>
                  <small class="text-muted">Antincendio & Controllo Accessi</small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="skill-item p-3 rounded-3" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(25, 135, 84, 0.1)); border-left: 4px solid #28a745;">
                  <i class="bi bi-car-front h4 text-success mb-2"></i>
                  <h6 class="fw-semibold mb-1">Automotive Passion</h6>
                  <small class="text-muted">Innovazione & Tecnologia</small>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Security Technologies -->
          <div class="security-tech mb-4">
            <h6 class="fw-semibold text-dark mb-3">
              <i class="bi bi-gear me-2 text-primary"></i>Settori di Specializzazione HDI
            </h6>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.2);">
                <i class="bi bi-camera-video me-1"></i>TVCC & Videosorveglianza
              </span>
              <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(176, 42, 55, 0.1)); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.2);">
                <i class="bi bi-shield-exclamation me-1"></i>Sistemi Antintrusione
              </span>
              <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(255, 109, 7, 0.1), rgba(230, 98, 6, 0.1)); color: #ff6d07; border: 1px solid rgba(255, 109, 7, 0.2);">
                <i class="bi bi-fire me-1"></i>Prevenzione Antincendio
              </span>
              <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(25, 135, 84, 0.1)); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2);">
                <i class="bi bi-house-gear me-1"></i>Automazione
              </span>
              <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(253, 126, 20, 0.1)); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.2);">
                <i class="bi bi-key me-1"></i>Controllo Accessi
              </span>
            </div>
          </div>
          
          <p class="text-muted mb-4">
            Nel mio ruolo presso <strong>HDI Distribuzione</strong>, distributore B2B leader nel settore sicurezza, mi occupo della <em>manutenzione e gestione dell'infrastruttura IT aziendale</em> che supporta la distribuzione di sistemi di <strong>videosorveglianza (TVCC)</strong>, <strong>antintrusione</strong>, <strong>antincendio</strong>, <strong>automazione</strong> e <strong>controllo accessi</strong>. Il mio lavoro spazia dall'implementazione di soluzioni software all'ottimizzazione dei processi digitali per il settore security.

            <strong>Supporto Tecnico Clienti</strong>: Sono responsabile del supporto tecnico per i clienti sulla sede di Parma, garantendo che le loro esigenze siano soddisfatte e che i sistemi funzionino senza intoppi.
          </p>
          
          <p class="text-muted mb-4">
            Oltre alla mia competenza tecnica nel reparto software, nutro una <strong>grande passione per il mondo automotive</strong>, dove tecnologia e innovazione si fondono per creare il futuro della mobilità. Questa doppia passione mi permette di avere una visione completa dell'evoluzione tecnologica in diversi settori industriali.
          </p>
          
          <div class="cta-section">
            <p class="fw-semibold text-dark mb-3">Interessato a soluzioni IT innovative?</p>
            <div class="d-flex gap-3 flex-wrap">
              <a href="/contatti.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-check me-2"></i>Parliamo di Sicurezza IT
              </a>
              <a href="/Portfolio/portfolio.php" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-semibold">
                <i class="bi bi-code-square me-2"></i>Esplora i Progetti
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6">
      <div class="about-image text-center position-relative">
        <div class="image-container" style="position: relative; display: inline-block;">
          <div class="decorative-bg" style="position: absolute; top: 20px; left: 20px; right: -20px; bottom: -20px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 30px; opacity: 0.1; z-index: 1;"></div>
          <img src="./img/img_anteprima.jpg" alt="Gabriel Perino Iosif" 
               style="width: 100%; max-width: 400px; height: auto; object-fit: contain; border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); position: relative; z-index: 2;">
          <div class="floating-elements">
            <div class="floating-icon" style="position: absolute; top: -10px; right: -10px; background: white; width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 3; animation: float 4s ease-in-out infinite;">
              <i class="bi bi-laptop text-primary h4 mb-0"></i>
            </div>
            <div class="floating-icon" style="position: absolute; bottom: 20px; left: -20px; background: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0,0,0,0.1); z-index: 3; animation: float 3s ease-in-out infinite reverse;">
              <i class="bi bi-gear text-success h5 mb-0"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<script>
  const index = document.getElementById('index');
  index.classList.add('active');
  index.setAttribute('aria-current', 'page');
</script>
<?php echo $footer; ?>
</body>

</html>