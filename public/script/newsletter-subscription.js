(() => {
    if (window.brandixzenNewsletterLoaded) return;
    window.brandixzenNewsletterLoaded = true;

    const initialize = (form) => {
        if (form.dataset.newsletterInitialized === 'true') return;
        form.dataset.newsletterInitialized = 'true';

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const input = form.querySelector('[name="email"]');
            const button = form.querySelector('[type="submit"]');
            const message = form.querySelector('[data-newsletter-message]');
            const originalLabel = button.textContent;

            message.textContent = '';
            button.disabled = true;
            button.textContent = 'Please wait...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ email: input.value }),
                });
                const data = await response.json();

                if (!response.ok) {
                    const validationMessage = data.errors?.email?.[0];
                    throw new Error(validationMessage || data.message || 'Unable to subscribe. Please try again.');
                }

                input.value = '';
                message.style.color = '#15803d';
                message.textContent = data.message;
            } catch (error) {
                message.style.color = '#dc2626';
                message.textContent = error.message;
            } finally {
                button.disabled = false;
                button.textContent = originalLabel;
            }
        });
    };

    window.initializeNewsletterForms = () => {
        document.querySelectorAll('[data-newsletter-form]').forEach(initialize);
    };

    window.initializeNewsletterForms();
    new MutationObserver(window.initializeNewsletterForms)
        .observe(document.documentElement, { childList: true, subtree: true });
})();
