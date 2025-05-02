// Login page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const passwordToggle = document.getElementById('password-toggle');
    const passwordField = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');
    
    if (passwordToggle && passwordField && passwordEye) {
        passwordToggle.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordEye.classList.add('hide');
            } else {
                passwordField.type = 'password';
                passwordEye.classList.remove('hide');
            }
        });
    }
});