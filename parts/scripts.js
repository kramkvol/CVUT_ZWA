    function check_username(usernameField, errorElementUsername) {
        usernameField.addEventListener("input", function () {
            const username = this.value.trim();

            if (!username) {
                errorElementUsername.textContent = "Username is required.";
                errorElementUsername.style.color = "red";
                return;
            }
        
            fetch("parts/check.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `username=${encodeURIComponent(username)}`,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    if (data.includes("Username is already taken.")) {
                        errorElementUsername.textContent = "Already taken.";
                        errorElementUsername.style.color = "red";
                    } else if (data.includes("Username is available.")) {
                        errorElementUsername.textContent = "Available.";
                        errorElementUsername.style.color = "green";
                    } else {
                        errorElementUsername.textContent = "Unexpected server response.";
                        errorElementUsername.style.color = "red";

                    }
                })
                .catch(error => {
                    errorElementUsername.textContent = "Error checking username.";
                    errorElementUsername.style.color = "red";
                });
        });
    }
    
    function check_email(emailField, errorElementEmail) {
        emailField.addEventListener("input", function () {
            const email = this.value.trim();

            if (!email) {
                errorElementEmail.textContent = "Email is required.";
                errorElementEmail.style.color = "red";
                return;
            }
        
            fetch("parts/check.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `email=${encodeURIComponent(email)}`,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    if (data.includes("Email is already taken.")) {
                        errorElementEmail.textContent = "Already taken.";
                        errorElementEmail.style.color = "red";
                    } else if (data.includes("Email is available.")) {
                        errorElementEmail.textContent = "Available.";
                        errorElementEmail.style.color = "green";
                    } else {
                        errorElementEmail.textContent = "Unexpected server response.";
                        errorElementEmail.style.color = "red";
                    }
                })
                .catch(error => {
                    errorElementEmail.textContent = "Error checking email.";
                    errorElementEmail.style.color = "red";
                });
            
        });
    }
    
    document.addEventListener("DOMContentLoaded", function () {
    if (window.location.pathname.endsWith("register.php")) {
        const usernameField = document.getElementById("username");
        const emailField = document.getElementById("email");
        const passwordField = document.getElementById("password");
        const confirmPasswordField = document.getElementById("cpassword");

        const errorElementUsername = document.getElementById("username-error");
        const errorElementEmail = document.getElementById("email-error");
        const errorElementPassword = document.getElementById("password-error");
        const errorElementConfirmPassword = document.getElementById("cpassword-error");

        check_username(usernameField, errorElementUsername);
        check_email(emailField, errorElementEmail);

        passwordField.addEventListener("input", validatePasswords);
        confirmPasswordField.addEventListener("input", validatePasswords);

        function validatePasswords() {
            const password = passwordField.value.trim();
            const confirmPassword = confirmPasswordField.value.trim();

            if (password !=="" && confirmPassword !=="") {
                if(password === confirmPassword){
                    errorElementPassword.textContent = "OK.";
                    errorElementPassword.style.color = "green";
                    errorElementConfirmPassword.textContent = "OK.";
                    errorElementConfirmPassword.style.color = "green";
                }
            }

            if(password !== confirmPassword){
                errorElementPassword.textContent = "Passwords do not match.";
                errorElementPassword.style.color = "red";
                errorElementConfirmPassword.textContent = "Passwords do not match.";
                errorElementConfirmPassword.style.color = "red";
            }
            
            if(password === "" && confirmPassword !== "" ){
                errorElementPassword.textContent = "Password is required.";
                errorElementPassword.style.color = "red";
                errorElementConfirmPassword.textContent = "Passwords do not match.";
                errorElementConfirmPassword.style.color = "red";
            }

            if(password !== "" && confirmPassword === "" ){
                errorElementPassword.textContent = "Passwords do not match.";
                errorElementPassword.style.color = "red";
                errorElementConfirmPassword.textContent = "Password is required.";
                errorElementConfirmPassword.style.color = "red";
            }
        
            if(password === "" && confirmPassword === "" ){
                errorElementPassword.textContent = "Password is required.";
                errorElementPassword.style.color = "red";
                errorElementConfirmPassword.textContent = "Password is required.";
                errorElementConfirmPassword.style.color = "red";
            }
        }
    }

    if (window.location.pathname.endsWith("login.php")) {
        const usernameField = document.getElementById("username");
        const passwordField = document.getElementById("password");
    
        const errorElementUsername = document.getElementById("username-error");
        const errorElementPassword = document.getElementById("password-error");
    
        usernameField.addEventListener("input", function () {
            errorElementUsername.textContent = this.value.trim() ? "OK." : "Username is required.";
            errorElementUsername.style.color = this.value.trim() ? "green" : "red";
        });
    
        passwordField.addEventListener("input", function () {
            errorElementPassword.textContent = this.value.trim() ? "OK." : "Password is required.";
            errorElementPassword.style.color = this.value.trim() ? "green" : "red";
        });
    }

    if (window.location.pathname.endsWith("profile.php")) {
        const usernameField = document.getElementById("new-username");
        const emailField = document.getElementById("new-email");

        const errorElementUsername = document.getElementById("new-username-error");
        const errorElementEmail = document.getElementById("new-email-error");

        check_username(usernameField, errorElementUsername);
        check_email(emailField, errorElementEmail);
    }
});
