const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');
console.log(id);

let articolo = null;
fetch('_getArticolo.php', {id: id})
    .then(response => response.text()) // Prendo la risposta come testo
    .then(data => {
        console.log(data);  // Log per controllare la risposta grezza
        try {
            articolo = JSON.parse(data);  // Ora provo a fare il parsing del JSON
            articolo = articolo[0];
            console.log(articolo);  // Log per controllare i dati decodificati
        } catch (error) {
            console.error('Errore nel parsing del JSON:', error);
        }
    })
    .catch(error => console.error('Errore nel recupero dei dati:', error));
