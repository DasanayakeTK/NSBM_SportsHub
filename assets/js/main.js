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

    // Toast Notification Logic
    const messageBoxes = document.querySelectorAll('.message-box');
    
    messageBoxes.forEach(box => {
        // Add a class to trigger the CSS animation
        box.classList.add('show-toast');
        
        // Remove the toast after 3.5 seconds
        setTimeout(() => {
            box.classList.remove('show-toast');
            box.classList.add('hide-toast');
            
            // Remove from DOM after the fade-out animation completes
            setTimeout(() => { box.remove(); }, 500);
        }, 3500);
    });
});