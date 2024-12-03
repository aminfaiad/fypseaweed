document.getElementById("register-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const fullName = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirm-password").value.trim();

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    if (!fullName || !email || !password) {
        alert("All fields are required!");
        return;
    }

    // Simulate registration process (can be replaced with API call)
    alert("Registration successful! Welcome, " + fullName);
    document.getElementById("register-form").reset();
});
