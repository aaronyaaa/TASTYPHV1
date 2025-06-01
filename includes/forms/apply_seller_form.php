<!-- SELLER APPLICATION -->
<div class="card mb-5">
    <div class="card-header bg-primary text-white">
        <i class="fa-solid fa-store me-2"></i> Apply as a Seller
    </div>
    <div class="card-body">
        <form action="../backend/process_seller_application.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="<?= $_SESSION['userId'] ?>">

            <!-- Row 1: Business Name + Description -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Business Description</label>
                    <textarea name="description" class="form-control" rows="1" required></textarea>
                </div>
            </div>

            <!-- Row 2: Store Location -->
            <div class="mb-3">
                <label class="form-label">Store Location (via Leaflet)</label>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-2" onclick="locateUser('seller')">
                    <i class="fa-solid fa-crosshairs"></i> Locate Me
                </button>
                <div id="sellerMap" class="rounded border" style="height: 300px;"></div>

                <input type="hidden" name="latitude" id="sellerLat">
                <input type="hidden" name="longitude" id="sellerLng">
                <input type="text" name="store_address" id="storeAddress" class="form-control mt-2" placeholder="Detected address" readonly>

                <div id="sellerSelectedLocation" class="mt-2 small text-muted" style="display:none;"></div>
            </div>

            <!-- Row 3: Permits -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Business Permit</label>
                    <input type="file" name="business_permit" class="form-control" accept="image/*" onchange="previewImage(this, 'permitPreview')" required>
                    <img id="permitPreview" class="img-thumbnail mt-2 d-none" style="max-height: 150px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Health Permit</label>
                    <input type="file" name="health_permit" class="form-control" accept="image/*" onchange="previewImage(this, 'healthPreview')" required>
                    <img id="healthPreview" class="img-thumbnail mt-2 d-none" style="max-height: 150px;">
                </div>
            </div>

            <!-- Submit -->
            <div class="d-grid">
                <button type="submit" name="apply_seller" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Seller Application
                </button>
            </div>
        </form>
    </div>
</div>