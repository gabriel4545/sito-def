//Get data portofolio
let articoli;
fetch('_getDatiPortofolio.php')
    .then(response => response.text()) // Prendo la risposta come testo
    .then(data => {
        console.log(data);  // Log per controllare la risposta grezza
        try {
            articoli = JSON.parse(data);  // Ora provo a fare il parsing del JSON
            console.log(articoli);  // Log per controllare i dati decodificati
            displayArticoli(articoli);
            // Applico automaticamente il filtro dell'anno corrente
            applyCurrentYearFilter();
        } catch (error) {
            console.error('Errore nel parsing del JSON:', error);
        }
    })
    .catch(error => console.error('Errore nel recupero dei dati:', error));


//Get data categorie
let categorie;
fetch('_getCategorie.php')
    .then(response => {
        console.log('Response status:', response.status); // Debug
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        categorie = data;
        if (categorie) {
            for (let i = 0; i < categorie.length; i++) {
                let option = document.createElement("option");
                option.text = categorie[i].nome;
                option.value = categorie[i].id;
                document.getElementById("categoryFilter").add(option);
            }
        }
    })
    .catch(error => console.error('Errore:', error));

function displayArticoli(articoli) {
    const container = document.getElementById('articoli-container');
    container.innerHTML = ''; 

    if (articoli.length === 0) {
        // Nessun articolo trovato
        const noResultsDiv = document.createElement('div');
        noResultsDiv.classList.add('col-12', 'text-center', 'py-5');
        noResultsDiv.innerHTML = `
            <div class="card border-0" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05)); border-radius: 20px;">
                <div class="card-body p-5">
                    <i class="bi bi-search text-muted mb-4" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h4 class="fw-bold text-dark mb-3">Nessun progetto trovato</h4>
                    <p class="text-muted mb-4">Non ci sono articoli che corrispondono ai filtri selezionati.</p>
                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="document.getElementById('resetFilters').click()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Filtri
                    </button>
                </div>
            </div>
        `;
        container.appendChild(noResultsDiv);
        return;
    }

    articoli.forEach(articolo => {
        // Crea l'elemento per ogni articolo
        const col = document.createElement('div');
        col.classList.add('col-lg-4', 'col-md-6', 'col-12', 'mb-4');
        
        const card = document.createElement('div');
        card.classList.add('card', 'h-100', 'border-0', 'shadow-sm');
        card.style.borderRadius = '20px';
        card.style.transition = 'all 0.3s ease';
        card.style.overflow = 'hidden';
        
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        });

        // Aggiungi immagine solo se presente
        if (articolo.allegato && articolo.allegato.percorsoFile) {
            const imgContainer = document.createElement('div');
            imgContainer.style.position = 'relative';
            imgContainer.style.overflow = 'hidden';
            imgContainer.style.height = '250px';
            imgContainer.style.background = 'linear-gradient(135deg, #f8f9fa, #e9ecef)';
            
            const img = document.createElement('img');
            console.log('Articolo:', articolo.id, 'Allegato:', articolo.allegato);
            
            let imagePath = articolo.allegato.percorsoFile;
            if (imagePath.startsWith('uploads/')) {
                imagePath = '../' + imagePath; // Aggiunge ../ per uscire dalla cartella Portfolio
            }
            img.src = imagePath;
            img.classList.add('card-img-top');
            img.style.height = '100%';
            img.style.width = '100%';
            img.style.objectFit = 'cover';
            img.style.transition = 'transform 0.3s ease';
            img.alt = articolo.titolo;
            
            // Overlay effect
            const overlay = document.createElement('div');
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.right = '0';
            overlay.style.bottom = '0';
            overlay.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2))';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.3s ease';
            
            imgContainer.addEventListener('mouseenter', function() {
                img.style.transform = 'scale(1.1)';
                overlay.style.opacity = '1';
            });
            
            imgContainer.addEventListener('mouseleave', function() {
                img.style.transform = 'scale(1)';
                overlay.style.opacity = '0';
            });
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(overlay);
            card.appendChild(imgContainer);
            console.log('Usando immagine:', imagePath);
        } else {
            // Placeholder quando non c'è immagine
            const placeholder = document.createElement('div');
            placeholder.style.height = '250px';
            placeholder.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
            placeholder.style.display = 'flex';
            placeholder.style.alignItems = 'center';
            placeholder.style.justifyContent = 'center';
            placeholder.innerHTML = '<i class="bi bi-file-earmark-code text-white" style="font-size: 3rem; opacity: 0.7;"></i>';
            card.appendChild(placeholder);
            console.log('Nessuna immagine per articolo', articolo.id);
        }

        // Aggiungi contenuto della card
        const cardBody = document.createElement('div');
        cardBody.classList.add('card-body', 'p-4');
        cardBody.style.background = 'white';

        // Data badge
        const dateBadge = document.createElement('div');
        dateBadge.className = 'mb-3';
        dateBadge.innerHTML = `<span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.2);">
            <i class="bi bi-calendar-event me-1"></i>
            ${new Date(articolo.data).toLocaleDateString('it-IT')}
        </span>`;
        cardBody.appendChild(dateBadge);

        const title = document.createElement('h5');
        title.classList.add('card-title', 'fw-bold', 'mb-3');
        title.style.color = '#2c3e50';
        title.style.lineHeight = '1.3';
        title.textContent = articolo.titolo;
        cardBody.appendChild(title);

        // Descrizione breve
        const description = document.createElement('p');
        description.classList.add('card-text', 'text-muted', 'mb-4');
        description.style.fontSize = '0.95rem';
        description.style.lineHeight = '1.6';
        
        // Il testo è già decodificato dal server
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = articolo.testo;
        const plainText = tempDiv.textContent || tempDiv.innerText || '';
        const shortText = plainText.substring(0, 120) + (plainText.length > 120 ? '...' : '');
        description.textContent = shortText;
        cardBody.appendChild(description);

        // Bottone "Leggi di più" con stile moderno
        const link = document.createElement('a');
        link.href = `/Portfolio/SchedaArticolo.php?id=${articolo.id}`; 
        link.classList.add('btn', 'fw-semibold', 'px-4', 'py-2');
        link.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
        link.style.border = 'none';
        link.style.borderRadius = '25px';
        link.style.color = 'white';
        link.style.textDecoration = 'none';
        link.style.transition = 'all 0.3s ease';
        link.style.display = 'inline-flex';
        link.style.alignItems = 'center';
        link.style.gap = '8px';
        link.innerHTML = '<i class="bi bi-arrow-right-circle"></i> Leggi di più';
        
        // Hover effects per il bottone
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.4)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
        
        cardBody.appendChild(link);
        
        card.appendChild(cardBody);
        col.appendChild(card);
        container.appendChild(col);
    });
}

// Funzione per filtrare gli articoli
function filterArticoli() {
    const yearFilter = document.getElementById('yearFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    if (!articoli) {
        console.error('Articoli non ancora caricati');
        return;
    }
    
    console.log('Filtering with:', { yearFilter, categoryFilter });
    console.log('Sample articolo:', articoli[0]); // Debug per vedere la struttura
    
    let articoliFiltrati = articoli.filter(articolo => {
        let passaAnno = true;
        let passaCategoria = true;
        
        // Filtro per anno
        if (yearFilter && yearFilter !== '') {
            const dataArticolo = new Date(articolo.data);
            const annoArticolo = dataArticolo.getFullYear();
            passaAnno = annoArticolo.toString() === yearFilter;
        }
        
        // Filtro per categoria
        if (categoryFilter && categoryFilter !== '') {
            passaCategoria = articolo.categoriaArticolo && articolo.categoriaArticolo.toString() === categoryFilter;
        }
        
        return passaAnno && passaCategoria;
    });
    
    displayArticoli(articoliFiltrati);
}

// Funzione per resettare i filtri
function resetFilters() {
    const currentYear = new Date().getFullYear();
    document.getElementById('yearFilter').value = currentYear;
    document.getElementById('categoryFilter').value = ''; // '' corrisponde a "Tutte"
    filterArticoli();
}

// Event listeners per i filtri
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        filterArticoli();
    });
    
    document.getElementById('resetFilters').addEventListener('click', resetFilters);
    
    document.getElementById('yearFilter').addEventListener('change', filterArticoli);
    document.getElementById('categoryFilter').addEventListener('change', filterArticoli);
});

// Funzione per applicare filtro dell'anno corrente quando gli articoli sono caricati
function applyCurrentYearFilter() {
    if (articoli && articoli.length > 0) {
        filterArticoli();
    }
}