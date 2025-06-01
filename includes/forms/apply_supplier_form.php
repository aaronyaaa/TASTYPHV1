<div class="card">
    <div class="card-header bg-warning text-dark">
        <i class="fa-solid fa-truck me-2"></i> Apply as a Supplier
    </div>
    <div class="card-body">
        <form action="../backend/process_supplier_application.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="userId" value="<?= $_SESSION['userId'] ?>">

            <!-- Row 1: Business Name + Description -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="1" required></textarea>
                </div>
            </div>

            <!-- Row 2: Store Location -->
            <div class="mb-3">
                <label class="form-label">Store Location (via Leaflet)</label>
                <button type="button" class="btn btn-sm btn-outline-secondary mb-2" onclick="locateUser('supplier')">
                    <i class="fa-solid fa-crosshairs"></i> Locate Me
                </button>
                <div id="supplierMap" class="rounded border" style="height: 300px;"></div>

                <input type="hidden" name="latitude" id="supplierLat">
                <input type="hidden" name="longitude" id="supplierLng">
                <input type="text" name="full_address" id="supplierAddress" class="form-control mt-2" placeholder="Detected address" readonly>

                <div id="supplierSelectedLocation" class="mt-2 small text-muted" style="display:none;"></div>
            </div>

            <!-- Row 3: License Upload -->
            <div class="mb-3">
                <label class="form-label">Business License</label>
                <input type="file" name="business_license" class="form-control" accept="image/*" onchange="previewImage(this, 'licensePreview')" required>
                <img id="licensePreview" class="img-thumbnail mt-2 d-none" style="max-height: 150px;">
            </div>

            <!-- Submit -->
            <div class="d-grid">
                <button type="submit" name="apply_supplier" class="btn btn-warning">
                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Supplier Application
                </button>
            </div>
        </form>
    </div>
</div>