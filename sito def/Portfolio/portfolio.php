<!-- Inizio Menu -->
<?php
include '../layout.php';
?>
<!-- Fine Menu -->

<!-- Portfolio Hero Section -->
<div class="container-fluid py-5 mb-5" style="background: linear-gradient(135deg, rgba(108, 124, 231, 0.1) 0%, rgba(124, 107, 204, 0.1) 100%);">
    <div class="container">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-briefcase-fill text-primary" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-3 fw-bold text-dark mb-3">Articoli & Progetti</h1>
            <div class="mx-auto mb-4" style="height: 4px; width: 120px; background: linear-gradient(135deg, #6c7ce7, #7c6bcc); border-radius: 2px;"></div>
            <p class="lead text-muted col-lg-8 mx-auto">
                Esplora i miei progetti, articoli e innovazioni tecnologiche. Ogni progetto racconta una storia di creatività e competenza tecnica.
            </p>
        </div>
    </div>
</div>

<!-- Smart Filters Section -->
<div class="container mb-5">
    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(248,249,250,0.9)); backdrop-filter: blur(10px);">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
            <div class="text-center">
                <h3 class="h5 fw-bold text-dark mb-2">
                    <i class="bi bi-funnel me-2 text-primary"></i>
                    Filtra i Contenuti
                </h3>
                <p class="text-muted small mb-0">Trova rapidamente ciò che ti interessa</p>
            </div>
        </div>
        <div class="card-body p-4">
            <form id="filterForm">
                <div class="row g-4 align-items-end">
                    <div class="col-lg-4">
                        <label for="yearFilter" class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>Anno
                        </label>
                        <select id="yearFilter" class="form-select form-select-lg" 
                                style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                onfocus="this.style.borderColor='#6c7ce7'; this.style.boxShadow='0 0 0 0.2rem rgba(108, 124, 231, 0.25)'"
                                onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                            <option value="">📅 Tutti gli anni</option>
                            <?php
                            $currentYear = date("Y");
                            for ($year = 2024; $year <= $currentYear; $year++) {
                                $selected = ($year == $currentYear) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>📅 $year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="categoryFilter" class="form-label fw-semibold text-dark">
                            <i class="bi bi-tags me-2 text-primary"></i>Categoria
                        </label>
                        <select id="categoryFilter" class="form-select form-select-lg"
                                style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                onfocus="this.style.borderColor='#6c7ce7'; this.style.boxShadow='0 0 0 0.2rem rgba(108, 124, 231, 0.25)'"
                                onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                            <option value="" selected>🏷️ Tutte le categorie</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-lg fw-semibold flex-grow-1" 
                                    style="background: linear-gradient(135deg, #6c7ce7, #7c6bcc); border: none; border-radius: 12px; color: white; transition: all 0.3s ease;"
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 8px 25px rgba(108, 124, 231, 0.3)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <i class="bi bi-search me-2"></i>Filtra
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-lg" 
                                    style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;"
                                    onmouseover="this.style.borderColor='#6c7ce7'; this.style.color='#6c7ce7'"
                                    onmouseout="this.style.borderColor='#e9ecef'; this.style.color='#6c757d'">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fine menu filtri -->

<!-- Projects Grid -->
<div class="container">
    <div class="row g-4" id="articoli-container" style="min-height: 400px;">
        <!-- I progetti verranno caricati qui dinamicamente -->
        <div class="col-12 text-center">
            <div class="loading-spinner mb-4">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Caricamento progetti...</span>
                </div>
            </div>
            <h4 class="text-muted">Caricamento progetti...</h4>
        </div>
    </div>
</div>


<!-- Fine elenco articoli -->


<script>
    const index = document.getElementById('portfolio');
    index.classList.add('active');
    index.setAttribute('aria-current', 'page');
</script>
<br />

<!-- Inizio footer -->
<?php echo $footer; ?>
<!-- Fine footer -->

<script src="/js/portfolio.js"></script>

</body>

</html>
