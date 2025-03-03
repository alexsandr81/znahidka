document.addEventListener("DOMContentLoaded", function() {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_new_password");
    const passwordError = document.getElementById("passwordError");
    const saveButton = document.getElementById("saveButton");

    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            passwordError.style.display = "block";
            saveButton.disabled = true; // Отключаем кнопку сохранения
        } else {
            passwordError.style.display = "none";
            saveButton.disabled = false; // Включаем кнопку сохранения
        }
    }

    password.addEventListener("input", validatePasswords);
    confirmPassword.addEventListener("input", validatePasswords);
});
