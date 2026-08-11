(() => {
    const initializeForm = (form) => {
        if (form.dataset.leadFormInitialized === 'true') {
            return;
        }

        form.dataset.leadFormInitialized = 'true';

        const captchaText = form.querySelector('[data-captcha-text]');
        const captchaKey = form.querySelector('input[name="captcha_key"]');
        const refreshButton = form.querySelector('[data-refresh-captcha]');
        const sourceUrl = form.querySelector('input[name="source_url"]');

        sourceUrl.value = window.location.href;

        const refreshCaptcha = async () => {
            refreshButton.disabled = true;

            try {
                const response = await fetch('/lead-form/captcha', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Captcha request failed');
                }

                const data = await response.json();
                captchaText.textContent = data.captcha;
                captchaKey.value = data.key;
            } catch (error) {
                captchaText.textContent = 'Unavailable';
                captchaKey.value = '';
            } finally {
                refreshButton.disabled = false;
            }
        };

        refreshButton.addEventListener('click', refreshCaptcha);
        refreshCaptcha();
    };

    window.initializeLeadForms = () => {
        document.querySelectorAll('[data-lead-form]').forEach(initializeForm);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initializeLeadForms);
    } else {
        window.initializeLeadForms();
    }

    const observer = new MutationObserver((mutations) => {
        if (mutations.some((mutation) => mutation.addedNodes.length > 0)) {
            window.initializeLeadForms();
        }
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });
})();
