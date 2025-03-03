document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_new_password");
    const passwordError = document.getElementById("passwordError");
    const saveButton = document.getElementById("saveButton");

    function validatePasswords() {
        const passwordValue = password.value.trim();
        const confirmPasswordValue = confirmPassword.value.trim();
        const minLength = 6; // Минимальная длина пароля

        if (passwordValue.length === 0 && confirmPasswordValue.length === 0) {
            passwordError.classList.add("d-none");
            saveButton.disabled = false;
            return;
        }

        if (passwordValue.length < minLength) {
            passwordError.textContent = `❌ Пароль должен быть не менее ${minLength} символов!`;
            passwordError.classList.remove("d-none");
            saveButton.disabled = true;
            return;
        }

        if (passwordValue !== confirmPasswordValue) {
            passwordError.textContent = "❌ Пароли не совпадают!";
            passwordError.classList.remove("d-none");
            saveButton.disabled = true;
        } else {
            passwordError.classList.add("d-none");
            saveButton.disabled = false;
        }
    }

    password.addEventListener("input", validatePasswords);
    confirmPassword.addEventListener("input", validatePasswords);
});
