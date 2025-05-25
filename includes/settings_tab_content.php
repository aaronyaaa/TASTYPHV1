
<div class="tab-content" id="settingsTabContent">
    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <form id="profileForm" action="../backend/update_profile.php" method="POST" enctype="multipart/form-data">
                    <h2 class="mb-4">My Profile</h2>
                    <p class="text-muted mb-4">Manage and protect your account</p>

                    <div class="profile-circle" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background-color: #6c757d; display: flex; align-items: center; justify-content: center;">
                        <?php if ($profile_pics): ?>
                            <img src="<?php echo $profile_pics; ?>" alt="User Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" viewBox="0 0 16 16" width="48" height="48">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z" />
                                <path fill-rule="evenodd" d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-12 text-center mt-4 mt-lg-0">
                            <img src="<?php echo !empty($user['profile_pics']) ? htmlspecialchars($user['profile_pics']) : '../assets/img/default-profile.png'; ?>" alt="Profile Image" class="rounded-circle mb-3" style="width:140px; height:140px; object-fit: cover;">
                            <div>
                                <label class="btn btn-outline-primary btn-sm" for="profile-image-input">Select Image</label>
                                <input type="file" id="profile-image-input" name="profile_image" accept="image/jpeg, image/png" style="display:none;">
                            </div>
                            <div class="text-muted small mt-2">
                                File size: maximum 1 MB<br />
                                File extension: .JPEG, .PNG
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-12 form-floating">
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required placeholder="First Name" />
                            <label for="first_name">First Name</label>
                        </div>
                        <div class="col-md-4 col-12 form-floating">
                            <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name'] ?? ''); ?>" placeholder="Middle Name" />
                            <label for="middle_name">Middle Name</label>
                        </div>
                        <div class="col-md-4 col-12 form-floating">
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required placeholder="Last Name" />
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Email Address" />
                        <label for="email">Email Address</label>
                        <small class="form-text text-primary mt-1">Change your email address here.</small>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" required placeholder="Phone Number" />
                        <label for="contact_number">Phone Number</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required placeholder="Date of Birth" max="<?php echo date('Y-m-d'); ?>" />
                        <label for="date_of_birth">Date of Birth</label>
                        <small class="form-text text-muted mt-1">You have already done KYC. Changing your birthday is not permitted.</small>
                    </div>

                    <fieldset class="mb-4">
                        <legend class="col-form-label pt-0 mb-2">Gender</legend>
                        <div class="d-flex gap-3">
                            <?php
                            $genders = ['Male', 'Female', 'Other'];
                            $userGender = isset($user['gender']) ? strtolower($user['gender']) : '';
                            foreach ($genders as $gender):
                                $genderLower = strtolower($gender);
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" value="<?php echo $gender; ?>" id="gender-<?php echo $genderLower; ?>" <?php echo ($userGender === $genderLower) ? 'checked' : ''; ?> />
                                    <label class="form-check-label" for="gender-<?php echo $genderLower; ?>"><?php echo $gender; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary px-4">Update Changes</button>
                        <span id="formMessage" class="ms-3"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Addresses Tab -->
    <div class="tab-pane fade" id="addresses-pane" role="tabpanel" aria-labelledby="addresses-tab" tabindex="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">My Addresses</h2>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New Address</button>
        </div>
        <h5>Address</h5>
        <?php if (!empty($user['full_address'])): ?>
            <p><?php echo htmlspecialchars($user['full_address']); ?></p>
        <?php else: ?>
            <p>No saved address.</p>
        <?php endif; ?>
    </div>

    <!-- Banks & Cards Tab -->
    <div class="tab-pane fade" id="banks-pane" role="tabpanel" aria-labelledby="banks-tab" tabindex="0">
        <h2>Banks & Cards</h2>
        <p>Coming soon...</p>
    </div>

    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
        <h2>Change Password</h2>
        <p>Coming soon...</p>
    </div>
</div>

<script src="../assets/js/settingsTabs.js"></script>