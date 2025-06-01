// Cover upload
const coverPreview = document.getElementById('coverPreview');
const coverInput = document.getElementById('coverInput');
const saveCoverBtn = document.getElementById('saveCoverBtn');
let selectedCoverFile = null;

coverInput.addEventListener('change', e => {
  selectedCoverFile = e.target.files[0];
  if (!selectedCoverFile) return;

  const reader = new FileReader();
  reader.onload = () => {
    coverPreview.src = reader.result;
    coverPreview.style.top = '0px';
  };
  reader.readAsDataURL(selectedCoverFile);
});

saveCoverBtn.addEventListener('click', async () => {
  if (!selectedCoverFile) return alert('Please select a cover image.');

  const formData = new FormData();
  formData.append('image', selectedCoverFile);
  formData.append('type', 'cover');

  try {
    const res = await fetch('../backend/seller/upload_store_image.php', {
      method: 'POST',
      body: formData,
    });
    const data = await res.json();
    if (res.ok) alert('Cover photo updated!');
    else alert(data.message || 'Failed to upload.');
  } catch (e) {
    alert('Error uploading cover image.');
  }
});

// Profile upload
const profilePreview = document.getElementById('profilePreview');
const profileInput = document.getElementById('profileInput');
const profileUploadTrigger = document.getElementById('profileUploadTrigger');
let selectedProfileFile = null;

profileUploadTrigger.addEventListener('click', () => profileInput.click());

profileInput.addEventListener('change', e => {
  selectedProfileFile = e.target.files[0];
  if (!selectedProfileFile) return;

  const reader = new FileReader();
  reader.onload = () => {
    profilePreview.src = reader.result;
  };
  reader.readAsDataURL(selectedProfileFile);

  uploadProfileImage();
});

async function uploadProfileImage() {
  const formData = new FormData();
  formData.append('image', selectedProfileFile);
  formData.append('type', 'profile');

  try {
    const res = await fetch('../backend/seller/upload_store_image.php', {
      method: 'POST',
      body: formData,
    });
    const data = await res.json();
    if (res.ok) alert('Profile photo updated!');
    else alert(data.message || 'Upload failed.');
  } catch (e) {
    alert('Error uploading profile image.');
  }
}

// Toggle Store Status
document.getElementById('toggleStatus')?.addEventListener('change', async (e) => {
  const badge = document.getElementById('storeStatusBadge');
  try {
    const res = await fetch('../backend/seller/toggle_store_settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'toggle_status' })
    });
    const data = await res.json();
    if (data.success) {
      // Update badge UI
      const newStatus = badge.textContent.trim() === 'Active' ? 'Inactive' : 'Active';
      badge.textContent = newStatus;
      badge.classList.toggle('bg-success');
      badge.classList.toggle('bg-secondary');
    } else {
      alert(data.error || 'Toggle failed');
      e.target.checked = !e.target.checked; // revert if error
    }
  } catch {
    alert('Error toggling status.');
    e.target.checked = !e.target.checked;
  }
});

// Toggle Public/Private
document.getElementById('toggleVisibility')?.addEventListener('change', async (e) => {
  const label = document.getElementById('visibilityLabel');
  try {
    const res = await fetch('../backend/seller/toggle_store_settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'toggle_visibility' })
    });
    const data = await res.json();
    if (data.success) {
      // Update label text
      const isNowPublic = label.textContent.includes('Private');
      label.textContent = `Visibility (${isNowPublic ? 'Public' : 'Private'})`;
    } else {
      alert(data.error || 'Toggle failed');
      e.target.checked = !e.target.checked;
    }
  } catch {
    alert('Error toggling visibility.');
    e.target.checked = !e.target.checked;
  }
});

