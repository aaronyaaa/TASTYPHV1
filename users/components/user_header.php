<!-- User Profile Header -->
<div class="bg-white text-dark overflow-hidden d-flex justify-content-center" style="min-height: 300px;">
    <div class="w-100" style="max-width: 1140px;">

        <!-- Cover -->
        <div id="coverContainer" class="position-relative overflow-hidden rounded mx-auto"
            style="height: 440px; width: 100%; background-color: #222;">
            <img id="coverPreview" src="<?= htmlspecialchars($coverPhoto) ?>" alt="User Cover"
                class="position-absolute w-100" style="object-fit: cover; top: 0; left: 0;">
            <input type="file" id="coverInput" accept="image/*"
                class="form-control form-control-sm position-absolute end-0 top-0 mt-2 me-2"
                style="width: auto; z-index: 10;">
            <button type="button" class="btn btn-sm btn-light shadow position-absolute bottom-0 end-0 mb-2 me-2"
                id="saveCoverBtn">
                <i class="fa-solid fa-floppy-disk"></i> Save Cover
            </button>
        </div>

        <!-- Profile + Info -->
        <div class="px-4 pb-4 mt-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <!-- Left: Profile Image -->
                <div class="position-relative" style="margin-top: -70px;">
                    <img id="profilePreview" src="<?= htmlspecialchars($profileImage) ?>" alt="User Profile"
                        class="rounded-circle border border-3 border-white shadow"
                        style="width: 130px; height: 130px; object-fit: cover;">
                    <input type="file" id="profileInput" accept="image/*" class="d-none">
                    <button type="button" id="profileUploadTrigger"
                        class="position-absolute bottom-0 end-0 btn btn-sm btn-light rounded-circle shadow"
                        style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-camera"></i>
                    </button>
                </div>

                <!-- Center: Name + Info -->
                <div class="flex-grow-1">
                    <h2 class="fw-bold d-flex align-items-center gap-2 mb-1">
                        <?= htmlspecialchars($fullName) ?>
                        <?php if ($userStatus === 'online'): ?>
                            <span class="status-dot p-2 bg-success border border-white rounded-circle"
                                style="width: 12px; height: 12px;"></span>
                        <?php endif; ?>
                    </h2>

                    <p class="text-muted mb-1"><?= htmlspecialchars($bio ?? '') ?></p>

                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge <?= $userStatus === 'online' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($userStatus) ?>
                        </span>
                    </div>
                </div>

                <!-- Right: Toggles + Button -->
                <div class="d-flex flex-column gap-2 text-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggleUserStatus"
                            <?= $userStatus === 'online' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="toggleUserStatus">User Status (<?= ucfirst($userStatus) ?>)</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggleUserVisibility" <?= $isPublic ? 'checked' : '' ?>>
                        <label class="form-check-label" for="toggleUserVisibility" id="userVisibilityLabel">
                            Visibility (<?= $isPublic ? 'Public' : 'Private' ?>)
                        </label>
                    </div>

                    <button class="btn btn-outline-dark mt-2" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Divider -->
<hr class="my-0 border-top border-secondary">