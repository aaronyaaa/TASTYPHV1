document.getElementById('profileUploadTrigger').addEventListener('click', () => {
  document.getElementById('profileInput').click();
});

document.getElementById('profileInput').addEventListener('change', function () {
  if (this.files[0]) {
    uploadPhoto('profile', this.files[0]);
  }
});

document.getElementById('saveCoverBtn').addEventListener('click', function () {
  const fileInput = document.getElementById('coverInput');
  if (fileInput.files[0]) {
    uploadPhoto('cover', fileInput.files[0]);
  } else {
    alert("Please choose a cover image first.");
  }
});

function uploadPhoto(type, file) {
  const formData = new FormData();
  formData.append('type', type);
  formData.append(type === 'profile' ? 'profile_photo' : 'cover_photo', file);

  fetch('../backend/user_photo_upload.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert('✅ ' + (type === 'profile' ? 'Profile' : 'Cover') + ' photo updated!');
        if (type === 'profile') {
          document.getElementById('profilePreview').src = '../' + data.path + '?v=' + new Date().getTime();
        } else {
          document.getElementById('coverPreview').src = '../' + data.path + '?v=' + new Date().getTime();
        }
      } else {
        alert('❌ ' + data.message);
        console.error(data);
      }
    })
    .catch(err => {
      alert('❌ Upload failed.');
      console.error(err);
    });
}
document.getElementById('coverInput').addEventListener('change', function () {
  const file = this.files[0];
  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById('coverPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});
