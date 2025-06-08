function initIngredientMap(supplierLat, supplierLng, userLat, userLng, supplierAddress, userAddress) {
    // Haversine distance (in KM)
    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c).toFixed(2);
    }

    // Bearing
    function getBearing(lat1, lon1, lat2, lon2) {
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;
        const y = Math.sin(Δλ) * Math.cos(φ2);
        const x = Math.cos(φ1) * Math.sin(φ2) -
                  Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
        const θ = Math.atan2(y, x);
        return (θ * 180 / Math.PI + 360) % 360;
    }

    if (!isNaN(supplierLat) && !isNaN(supplierLng) && !isNaN(userLat) && !isNaN(userLng)) {
        const distance = parseFloat(haversineDistance(supplierLat, supplierLng, userLat, userLng));
        const routeColor = distance > 20 ? 'red' : distance > 5 ? 'orange' : 'green';
        const estimatedTime = (distance / 40 * 60).toFixed(0); // 40 km/h average speed

        const map = L.map('map').setView([(supplierLat + userLat) / 2, (supplierLng + userLng) / 2], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const supplierIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3177/3177361.png',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        const userIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/1946/1946429.png',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        L.marker([supplierLat, supplierLng], { icon: supplierIcon })
            .addTo(map)
            .bindPopup("📦 <strong>Supplier</strong><br>" + supplierAddress);

        L.marker([userLat, userLng], { icon: userIcon })
            .addTo(map)
            .bindPopup("👤 <strong>You</strong><br>" + userAddress);

        const route = [[supplierLat, supplierLng], [userLat, userLng]];
        const polyline = L.polyline(route, { color: routeColor, weight: 4, opacity: 0.8 }).addTo(map);
        map.fitBounds(polyline.getBounds());

        polyline.bindPopup(`<strong>📏 Distance:</strong> ${distance} km<br>⏱ ETA: ${estimatedTime} mins`).openPopup();

        L.polylineDecorator(route, {
            patterns: [{
                offset: '50%',
                repeat: 0,
                symbol: L.Symbol.arrowHead({
                    pixelSize: 12,
                    pathOptions: {
                        fillOpacity: 1,
                        weight: 0,
                        color: routeColor
                    }
                })
            }]
        }).addTo(map);

        // Motor icon (local file or hosted)
        const motorIcon = L.icon({
            iconUrl: '../uploads/icon/icon1.png',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        const motorMarker = L.rotatedMarker([supplierLat, supplierLng], {
            icon: motorIcon,
            rotationAngle: getBearing(supplierLat, supplierLng, userLat, userLng),
            rotationOrigin: 'center center'
        }).addTo(map);

        // Animation
        let progress = 0;
        const animate = () => {
            if (progress >= 1) return;
            progress += 0.003;
            const lat = supplierLat + (userLat - supplierLat) * progress;
            const lng = supplierLng + (userLng - supplierLng) * progress;
            const bearing = getBearing(lat, lng, userLat, userLng);
            motorMarker.setLatLng([lat, lng]);
            motorMarker.setRotationAngle(bearing);
            requestAnimationFrame(animate);
        };
        animate();
    } else {
        document.getElementById('map').innerHTML =
            '<div class="text-muted p-3">📍 Location data not available</div>';
    }
}
