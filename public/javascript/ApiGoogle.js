// ApiGoogle.js

// Fonction pour activer l'autocomplétion sur le champ d'adresse
function activatePlacesAutocomplete() {
    var input = document.getElementById('registration_form_address'); 
    var autocomplete = new google.maps.places.Autocomplete(input);
}

// Appel de la fonction d'activation de l'autocomplétion lors du chargement de la page
google.maps.event.addDomListener(window, 'load', activatePlacesAutocomplete);
