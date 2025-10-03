<?php
$menu = sprintf('<header>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="background: linear-gradient(135deg, #667eea 0%%, #764ba2 100%%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.1);" >
  <div class="container" >
    <a class="navbar-brand fw-bold" href="/index.php" style="font-size: 1.5rem; color: white;">
      <img src="/img/logo.png" alt="GPIOS" style="width: 45px; height: 45px; border-radius: 12px; margin-right: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
      GPIOS
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="box-shadow: none;">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item mx-2">
          <a class="nav-link fw-semibold text-white position-relative" href="/index.php" id="index" style="transition: all 0.3s ease;">
            <i class="bi bi-house-fill me-2"></i>Home
          </a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link fw-semibold text-white position-relative" href="/contatti.php" id="contatti" style="transition: all 0.3s ease;">
            <i class="bi bi-envelope-fill me-2"></i>Contatti
          </a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link fw-semibold text-white position-relative" href="/Portfolio/portfolio.php" id="portfolio" style="transition: all 0.3s ease;">
            <i class="bi bi-file-text-fill me-2"></i>Articoli
          </a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link fw-semibold text-white position-relative" href="/privacy.php" id="privacy" style="transition: all 0.3s ease;">
            <i class="bi bi-shield-fill me-2"></i>Privacy
          </a>
        </li>
      </ul>
      <div class="d-flex">
        <a class="btn btn-light fw-semibold px-4 py-2" href="/login.php" style="border-radius: 25px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
          <i class="bi bi-person-fill me-2"></i>Area Riservata
        </a>
      </div>
    </div>
  </div>
</nav>
</header>');
$footer = sprintf('<footer class="footer py-5 mt-5" style="background: linear-gradient(135deg, #2c3e50 0%%, #3498db 100%%); color: white;">
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <h5 class="fw-bold mb-3" style="color: #ecf0f1;">GPIOS</h5>
      <p class="text-light opacity-75">IT Specialist specializzato in sicurezza e sviluppo software. Esperienza nel settore security B2B: TVCC, antintrusione, antincendio, automazione e controllo accessi.</p>
    </div>
    <div class="col-md-3">
      <h6 class="fw-semibold mb-3" style="color: #ecf0f1;">Link Utili</h6>
      <ul class="list-unstyled">
        <li><a href="/index.php" class="text-light text-decoration-none opacity-75 hover-opacity-100" style="transition: opacity 0.3s;">Home</a></li>
        <li><a href="/Portfolio/portfolio.php" class="text-light text-decoration-none opacity-75 hover-opacity-100" style="transition: opacity 0.3s;">Articoli</a></li>
        <li><a href="/contatti.php" class="text-light text-decoration-none opacity-75 hover-opacity-100" style="transition: opacity 0.3s;">Contatti</a></li>
      </ul>
    </div>
    <div class="col-md-3">
      <h6 class="fw-semibold mb-3" style="color: #ecf0f1;">Informazioni</h6>
      <ul class="list-unstyled">
        <li><a href="/privacy.php" class="text-light text-decoration-none opacity-75 hover-opacity-100" style="transition: opacity 0.3s;">Privacy Policy</a></li>
        <li><a href="/login.php" class="text-light text-decoration-none opacity-75 hover-opacity-100" style="transition: opacity 0.3s;">Area Riservata</a></li>
      </ul>
    </div>
  </div>
  <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
  <div class="row align-items-center">
    <div class="col-md-8">
      <p class="mb-0 text-light opacity-75">© 2025 www.perinogabriel.it by Iosif Perino Gabriel - All rights reserved</p>
    </div>
    <div class="col-md-4 text-md-end">
      <small class="text-light opacity-50">Powered by GPIOS</small>
    </div>
  </div>
  <a href="https://www.iubenda.com/privacy-policy/63407792" class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe " title="Privacy Policy ">Privacy Policy</a>
<a href="https://www.iubenda.com/privacy-policy/63407792/cookie-policy" class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe " title="Cookie Policy ">Cookie Policy</a>
<script type="text/javascript">
(function (w,d) {
  var loader = function () {
    if (d.getElementsByTagName("script").length > 0) {
      var s = d.createElement("script"), 
          tag = d.getElementsByTagName("script")[0]; 
      s.src="https://cdn.iubenda.com/iubenda.js"; 
      if (tag && tag.parentNode) {
        tag.parentNode.insertBefore(s,tag);
      }
    }
  }; 
  if(w.addEventListener){
    w.addEventListener("load", loader, false);
  }else if(w.attachEvent){
    w.attachEvent("onload", loader);
  }else{
    w.onload = loader;
  }
})(window, document);
</script>

</div>
</footer>');
?>
<!DOCTYPE html>
<html lang="it">

<head>
  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GPIOS - IT Security & Software Solutions</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/img/logo.png">
  <link rel="shortcut icon" type="image/png" href="/img/logo.png">
  <link rel="apple-touch-icon" href="/img/logo.png">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/utilities.css">
  <link rel="stylesheet" type="text/css" href="/css/index.css">
  
  <!-- Iubenda Cookie Solution -->
  <script type="text/javascript">
  window._iub = window._iub || [];
  window._iub.csConfiguration = {
    "floatingPreferencesButtonDisplay": "bottom-right",
    "lang": "it",
    "siteId": 3990527,
    "whitelabel": false,
    "cookiePolicyId": 63407792,
    "banner": {
      "acceptButtonDisplay": true,
      "closeButtonDisplay": false,
      "customizeButtonDisplay": true,
      "explicitWithdrawal": true,
      "listPurposes": true,
      "position": "float-top-center",
      "rejectButtonDisplay": true,
      "showTitle": false
    }
  };
  </script>
  <script type="text/javascript" src="https://cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8" async></script>
  
  <!-- Iubenda Widgets -->
  <script type="text/javascript">
  (function() {
    try {
      var script = document.createElement('script');
      script.src = 'https://embeds.iubenda.com/widgets/a695ca8f-417f-49a5-86a4-d26bbec010b4.js';
      script.async = true;
      script.onerror = function() {
        console.warn('Iubenda widget failed to load');
      };
      document.head.appendChild(script);
    } catch(e) {
      console.warn('Error loading Iubenda widget:', e);
    }
  })();
  </script>
  <style>
    * {
      font-family: 'Inter', sans-serif;
    }
    
    /* Navigation specific styles */
    .nav-link:hover {
      transform: translateY(-2px);
      color: #ffffff !important;
    }
    
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      width: 0;
      height: 2px;
      background: rgba(255,255,255,0.8);
      transition: var(--transition-base);
      transform: translateX(-50%);
    }
    
    .nav-link:hover::after {
      width: 80%;
    }
  </style>
  
  <script src="/js/bootstrap.bundle.js" ></script>
  <script src="/js/generalFunctions.js" ></script>

  <?php echo $menu; ?>
</head>
<body style="padding-top: 5rem;">
