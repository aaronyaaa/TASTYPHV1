document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault(); 
    const formData = new FormData(this);
    fetch("api/auth/login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/tastyphv1/users/home.php';
        } else {
            alert(data.message || "Invalid credentials");
        }
    })
    .catch(error => {
        alert("Error: " + error.message);
    });
});

document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault(); 
    const formData = new FormData(this);
    fetch("api/auth/signup.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Sign up successful!");
            window.location.href = '/tastyphv1/index.php';  // Absolute path to the main index.php
        } else {
            alert(data.message || "Error during sign up");
        }
    })
    .catch(error => {
        alert("Error: " + error.message);
    });
});
