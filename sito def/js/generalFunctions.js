function correctJsonString(jsonString) {
    try {
        // Provo a parsare la stringa JSON direttamente
        JSON.parse(jsonString);
        return jsonString; // Restituisco la stringa originale se non ci sono problemi
    } catch (error) {
        console.warn("JSON non valido rilevato. Tentativo di correzione...");

        const corrected = jsonString
            .replace(/\\(?!["\\/bfnrtu])/g, "\\\\") // Escapa i backslash non validi
            .replace(/[\b]/g, "\\b") // Escapa i caratteri di backspace
            .replace(/[\f]/g, "\\f") // Escapa i caratteri di form feed
            .replace(/[\n]/g, "\\n") // Escapa i caratteri di nuova riga
            .replace(/[\r]/g, "\\r") // Escapa i ritorni a capo
            .replace(/[\t]/g, "\\t") // Escapa i caratteri di tabulazione
            .replace(/'/g, "\\u0027"); // Sostituisce i singoli apici con doppi apici (problema comune)

        // Provo a parsare di nuovo dopo le correzioni
        try {
            JSON.parse(corrected); // Verifico che la stringa corretta sia un JSON valido
            return corrected; // Restituisco la stringa JSON corretta
        } catch (finalError) {
            // Sollevo un errore se non è possibile correggere la stringa
            throw new Error("Impossibile correggere la stringa JSON: " + finalError.message);
        }
    }
}
let formatData = (data) => {
    if (data == '' || data == null)
        return '';

    data = data.toString();
    data = data.substr(8, 2) + '/' + data.substr(5, 2) + '/' + data.substr(0, 4);
    if (data == '01/01/1900')
        return '';
    return data;
}


function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
}

function b64DecodeUnicode(str) {

    if (!str) {
        return '';
    }
    
    if (typeof str !== 'string') {
        str = String(str);
    }
    
    
    return str;
}
