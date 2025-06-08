<!-- User Profile Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="editUserForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="editUserTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profileTab" type="button" role="tab">
              Profile Details
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#locationTab" type="button" role="tab">
              Location & Map
            </button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="editUserTabsContent">

          <!-- Profile Tab -->
          <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" name="first_name" class="form-control" placeholder="First Name"
                  value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                  value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <input type="text" name="middle_name" class="form-control" placeholder="Middle Name"
                  value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <input type="date" name="date_of_birth" class="form-control"
                  value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <input type="text" name="contact_number" class="form-control" placeholder="Contact Number"
                  value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <select name="gender" class="form-select">
                  <option value="">Select Gender</option>
                  <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                  <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                </select>
              </div>
              <div class="col-md-12">
                <input type="email" name="email" class="form-control" placeholder="Email"
                  value="<?= htmlspecialchars($user['email'] ?? '') ?>">
              </div>
            </div>
          </div>

          <!-- Location Tab -->
          <div class="tab-pane fade" id="locationTab" role="tabpanel">
            <div class="form-section w-100 mt-3">
              <input type="text" class="form-control mb-2" name="address_line" id="address_line"
                value="<?= htmlspecialchars($user['streetname'] ?? '') ?>" placeholder="Street, Building...">
              <input type="text" class="form-control mb-2" name="postal_code" id="postal_code"
                value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" placeholder="Postal Code">
              <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($user['latitude'] ?? '') ?>">
              <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($user['longitude'] ?? '') ?>">
              <input type="hidden" name="full_address" id="full_address" value="<?= htmlspecialchars($user['full_address'] ?? '') ?>">

              <button type="button" id="locateMeBtn" class="btn btn-primary w-100 mb-2">
                <i class="bi bi-geo-alt-fill"></i> Use Current Location
              </button>

              <div id="selectedLocation" class="selected-location mb-2" style="display:none;"></div>
              <div id="addressMap" style="height: 300px; border: 1px solid #ccc; border-radius: 8px;"></div>
            </div>
          </div>

        </div> <!-- end tab content -->
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
      </div>
    </form>
  </div>
</div>
