document.addEventListener('DOMContentLoaded', () => {
    // Select all form inputs that have the 'required' attribute
    const formInputs = document.querySelectorAll('form input[required]');

    formInputs.forEach(input => {
        // Run validation on every keystroke and when the user clicks away
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('blur', () => validateField(input));
    });

    function validateField(input) {
        let isValid = true;

        // Validation Rules
        if (input.value.trim() === '') {
            isValid = false;
        } else if (input.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(input.value);
        } else if (input.type === 'password' && input.value.length < 6) {
            isValid = false;
        }

        // Apply visual feedback classes
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        }
    }
});