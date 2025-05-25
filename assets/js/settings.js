// settings.js

$(document).ready(function() {

    const regionSelect = document.getElementById('regionSelect');
    const provinceSelect = document.getElementById('provinceSelect');
    const citySelect = document.getElementById('citySelect');
    const barangaySelect = document.getElementById('barangaySelect');
    const addressLineInput = document.querySelector('input[name="address_line"]');
    const postalCodeInput = document.querySelector('input[name="postal_code"]');
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    let map, marker;

    function initMap() {
        if (map) {
            map.invalidateSize();
            return;
        }
        map = L.map('addressMap').setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([12.8797, 121.7740], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            console.log('Marker dragged to:', pos.lat, pos.lng);
            // Optional: save lat/lng somewhere if needed
        });
    }

    async function updateMapFromAddress() {
        if (!regionSelect || !provinceSelect || !citySelect || !barangaySelect || !addressLineInput || !postalCodeInput) return;

        const regionText = regionSelect.options[regionSelect.selectedIndex]?.text || '';
        const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const cityText = citySelect.options[citySelect.selectedIndex]?.text || '';
        const barangayText = barangaySelect.options[barangaySelect.selectedIndex]?.text || '';
        const street = addressLineInput.value || '';
        const postal = postalCodeInput.value || '';

        // Only geocode if all fields are filled
        if (!regionText || !provinceText || !cityText || !barangayText || !street || !postal) return;

        const fullAddress = `${street}, ${barangayText}, ${cityText}, ${provinceText}, ${regionText}, ${postal}, Philippines`;

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}`);
            const data = await response.json();
            if (data && data.length > 0) {
                const lat = data[0].lat;
                const lon = data[0].lon;
                map.setView([lat, lon], 16);
                marker.setLatLng([lat, lon]);
            }
        } catch (error) {
            console.error('Geocoding error:', error);
        }
    }

    // Initialize map when modal is fully shown
    $('#addAddressModal').on('shown.bs.modal', function () {
        initMap();
        setTimeout(() => {
            map.invalidateSize();
            updateMapFromAddress();
        }, 200);

        // Reset marker position on modal open if needed
        if (marker) {
            marker.setLatLng([12.8797, 121.7740]);
            map.setView([12.8797, 121.7740], 6);
        }
    });

    // Update map live when any address field changes
    [regionSelect, provinceSelect, citySelect, barangaySelect, postalCodeInput].forEach(sel => {
        if(sel) sel.addEventListener('change', updateMapFromAddress);
    });
    if(addressLineInput) addressLineInput.addEventListener('input', updateMapFromAddress);

});

