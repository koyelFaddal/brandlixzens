document.addEventListener('DOMContentLoaded', () => {
    function generateCaptcha() {
        const charsArray = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const lengthOtp = 6;
        let captcha = [];
        for (let i = 0; i < lengthOtp; i++) {
            let index = Math.floor(Math.random() * charsArray.length);
            captcha.push(charsArray[index]);
        }
        return captcha.join('');
    }

    function refreshCaptcha(formId) {
        const captchaText = document.querySelector(`#${formId} #captcha-text`);

        if (!captchaText) {
            return;
        }

        captchaText.textContent = generateCaptcha();
    }

    function initializeCaptcha(formId) {
        refreshCaptcha(formId);

        const refreshButton = document.querySelector(`#${formId} #refresh-captcha`);

        if (!refreshButton) {
            return;
        }

        refreshButton.addEventListener('click', () => {
            refreshCaptcha(formId);
        });

        const form = document.getElementById(formId);
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const captchaInput = form.querySelector('#captcha').value;
            const captchaText = form.querySelector('#captcha-text').textContent;
            if (captchaInput === captchaText) {
                alert('Captcha matched');
                // Perform form submission or other necessary actions
            } else {
                alert('Captcha not matched. Please try again.');
            }
        });
    }

    // Initialize CAPTCHA for both forms
});
