let locations = [];
let languages = [];

function loadLocation() {
    fetch('/public/mw-1/country.json')
        .then(response => response.json())
        .then(data => {
            locations = data;
            initLocationList();
        })
        .catch(error => {
            console.error('Error fetching locations:', error);
        });
}

function initLocationList() {
    const locationSelect = $('#location');
    locationSelect.empty().append('<option value="">Select a location</option>'); // Reset options
    locations.forEach(tz => {
        locationSelect.append(new Option(tz.name, tz.name));
    });
    const defaultLocation = $('#default-location').text().trim();
    if (defaultLocation) {
        locationSelect.val(defaultLocation).trigger('change'); // Set default and refresh Select2
    }
}

function loadLanguage() {
    fetch('/public/mw-1/language.json')
        .then(response => response.json())
        .then(data => {
            languages = data;
            initLanguageList();
        })
        .catch(error => {
            console.error('Error fetching languages:', error);
        });
}

function initLanguageList() {
    const languageSelect = $('#language');
    languageSelect.empty().append('<option value="">Select a language</option>'); // Reset options
    languages.forEach(tz => {
        languageSelect.append(new Option(tz.name, tz.name));
    });
    const defaultLanguages = JSON.parse($('#default-languages').text().trim() || "[]");
    if (defaultLanguages.length > 0) {
        languageSelect.val(defaultLanguages).trigger('change'); // Set default multiple values
    }
}

// Call functions on page load
document.addEventListener("DOMContentLoaded", function () {
    loadLocation();
    loadLanguage();
});
