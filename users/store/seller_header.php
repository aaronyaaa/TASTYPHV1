
<!-- Store Header -->
<div class="store-header bg-white text-dark overflow-hidden d-flex justify-content-center" style="min-height: 300px;">
  <div class="w-100" style="max-width: 1140px;">

    <!-- Cover -->
    <div id="coverContainer" class="position-relative overflow-hidden rounded mx-auto"
         style="height: 440px; width: 100%; background-color: #222;">
      <img id="coverPreview" src="<?= htmlspecialchars($coverPhoto) ?>" alt="Store Cover"
           class="position-absolute w-100" style="object-fit: cover; top: 0; left: 0;">
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
          </div>

          <!-- Store Info -->
          <div class="text-dark w-100">
            <div class="store-info-row">
              <div class="store-info-block">
                <h2 class="mb-1 fw-bold d-inline-block"><?= htmlspecialchars($storeName) ?></h2>
                <button class="btn btn-sm btn-outline-primary shop-info-btn" data-bs-toggle="modal" data-bs-target="#shopInfoModal">
                  <i class="fa-solid fa-location-dot me-1"></i> Shop Info
                </button>
              </div>

            <?php if (!empty($storeDescription)): ?>
              <p class="mb-2 text-muted" style="max-width: 600px;"><?= nl2br(htmlspecialchars($storeDescription)) ?></p>
            <?php endif; ?>

            <div class="d-flex gap-3 align-items-center">
              <span class="badge <?= $storeStatus === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                <?= ucfirst($storeStatus) ?>
              </span>
              <span class="text-warning">
                <i class="fa-solid fa-star"></i> <?= number_format($storeRating, 1) ?>
              </span>
            </div>
            <div class="action-buttons">
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#placeOrderModal">
                  <i class="fa-solid fa-cart-plus me-1"></i> Place an Pre-order
                </button>
<?php if (!$isOwnStore && $userId): ?>
  <a href="#" class="btn btn-sm btn-outline-secondary open-chat-btn"
     data-user-id="<?= $userId ?>" 
     data-user-name="<?= htmlspecialchars($userFullName) ?>">
    <i class="fa-solid fa-message me-1"></i> Message
  </a>
<?php endif; ?>




              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Divider -->
<hr class="my-0 border-top border-secondary">
<div class="container py-4">
  <p>This is the store’s public page. You can customize this to show products, categories, map, etc.</p>
</div>

<!-- Shop Info Modal -->
<?php include 'modal.php'; ?>

