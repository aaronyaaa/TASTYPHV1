// settings.js

$(document).ready(function() {
    const regionSelect = document.getElementById('regionSelect');
    const provinceSelect = document.getElementById('provinceSelect');
    const citySelect = document.getElementById('citySelect');
    const barangaySelect = document.getElementById('barangaySelect');
    const addressLineInput = document.querySelector('input[name="address_line"]');
    const postalCodeInput = document.querySelector('input[name="postal_code"]');

    // Initialize map when modal is fully shown
    $('#addAddressModal').on('shown.bs.modal', function () {
        if (typeof window.initMap === 'function') {
            window.initMap();
            setTimeout(() => {
                if (window.map) {
                    window.map.invalidateSize();
                    if (typeof window.updateMapFromAddress === 'function') {
                        window.updateMapFromAddress();
                    }
                }
            }, 200);
        }
    });

    // Update map live when any address field changes
    [regionSelect, provinceSelect, citySelect, barangaySelect, postalCodeInput].forEach(sel => {
        if(sel) sel.addEventListener('change', () => {
            if (typeof window.updateMapFromAddress === 'function') {
                window.updateMapFromAddress();
            }
        });
    });

    if(addressLineInput) {
        addressLineInput.addEventListener('input', () => {
            if (typeof window.updateMapFromAddress === 'function') {
                window.updateMapFromAddress();
            }
        });
    }
});

