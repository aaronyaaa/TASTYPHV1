<!-- Store Header -->
<div class="bg-white text-dark overflow-hidden d-flex justify-content-center" style="min-height: 300px;">
    <div class="w-100" style="max-width: 1140px;">

        <!-- Cover -->
        <div id="coverContainer" class="position-relative overflow-hidden rounded mx-auto"
            style="height: 440px; width: 100%; background-color: #222;">
            <img id="coverPreview" src="<?= htmlspecialchars($coverPhoto) ?>" alt="Store Cover"
                class="position-absolute w-100" style="object-fit: cover; top: 0; left: 0;">
            <input type="file" id="coverInput" accept="image/*"
                class="form-control form-control-sm position-absolute end-0 top-0 mt-2 me-2" style="width: auto; z-index: 10;">
            <button type="button" class="btn btn-sm btn-light shadow position-absolute bottom-0 end-0 mb-2 me-2" id="saveCoverBtn">
                <i class="fa-solid fa-floppy-disk"></i> Save Cover
            </button>
        </div>

        <!-- Profile + Info -->
        <div class="px-4 pb-4 mt-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3">
                <div class="d-flex align-items-center gap-3">
                    <!-- Profile -->
                    <div class="position-relative" style="margin-top: -70px;">
                        <img id="profilePreview" src="<?= htmlspecialchars($profileImage) ?>" alt="Store Logo"
                            class="rounded-circle border border-3 border-white shadow"
                            style="width: 130px; height: 130px; object-fit: cover;">
                        <input type="file" id="profileInput" accept="image/*" class="d-none">
                        <button type="button" id="profileUploadTrigger"
                            class="position-absolute bottom-0 end-0 btn btn-sm btn-light rounded-circle shadow"
                            style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>

                    <!-- Store Info -->
                    <div class="text-dark">
                        <h2 class="mb-1 fw-bold"><?= htmlspecialchars($storeName) ?></h2>
                        <?php if (!empty($storeDescription)): ?>
                            <p class="mb-2 text-muted" style="max-width: 600px;"><?= nl2br(htmlspecialchars($storeDescription)) ?></p>
                        <?php endif; ?>
                        <div class="d-flex gap-3 align-items-center">
                            <span id="storeStatusBadge" class="badge <?= $storeStatus === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= ucfirst($storeStatus) ?>
                            </span>

                            <span class="text-warning">
                                <i class="fa-solid fa-star"></i> <?= number_format($storeRating, 1) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Toggles -->
                <div class="d-flex flex-column gap-2 mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleStatus" <?= $storeStatus === 'active' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="toggleStatus">Store Status</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleVisibility" <?= $isPublic ? 'checked' : '' ?>>
                        <label class="form-check-label" for="toggleVisibility" id="visibilityLabel">
                            Visibility (<?= $isPublic ? 'Public' : 'Private' ?>)
                        </label>
                    </div>
                    <button class="btn btn-outline-dark mt-2" data-bs-toggle="modal" data-bs-target="#editStoreModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit profile
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Divider -->
<hr class="my-0 border-top border-secondary">

<!-- JS Scripts -->