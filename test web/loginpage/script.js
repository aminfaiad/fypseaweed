document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
        alert("Please fill in all fields!");
        return;
    }

    // Simulate login process (can be replaced with API call)
    if (email === "test@seaweed.com" && password === "password123") {
        alert("Login successful! Welcome back.");
    } else {
        alert("Invalid email or password. Please try again.");
    }

    document.getElementById("login-form").reset();
});
