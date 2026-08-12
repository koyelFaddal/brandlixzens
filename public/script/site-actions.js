(() => {
    if (window.brandixzenSiteActionsLoaded) {
        return;
    }

    window.brandixzenSiteActionsLoaded = true;

    const whatsappUrl = 'https://web.whatsapp.com/send?phone=+918348101800';
    const calendlyUrl = 'https://calendly.com/koyelp210/30min';

    const styles = document.createElement('style');
    styles.textContent = `
        .brandixzen-calendar-modal{position:fixed;inset:0;z-index:100000;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(15,23,42,.72)}
        .brandixzen-calendar-modal.is-open{display:flex}
        .brandixzen-calendar-dialog{width:min(1080px,100%);height:min(850px,calc(100vh - 36px));display:flex;flex-direction:column;overflow:hidden;border-radius:28px;background:#fff;box-shadow:0 24px 80px rgba(0,0,0,.28)}
        .brandixzen-calendar-header{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #e5e7eb}
        .brandixzen-calendar-eyebrow{margin:0 0 6px;color:#4f46e5;font:700 12px/1.2 Arial,sans-serif;letter-spacing:4px}
        .brandixzen-calendar-title{margin:0;color:#0f172a;font:700 clamp(20px,3vw,27px)/1.2 Arial,sans-serif}
        .brandixzen-calendar-close{width:44px;height:44px;border:0;border-radius:50%;background:#f1f5f9;color:#64748b;font-size:31px;line-height:1;cursor:pointer}
        .brandixzen-calendar-frame-wrap{min-height:0;flex:1;margin:14px 22px 22px;overflow:hidden;border:1px solid #dbe3ef;border-radius:24px}
        .brandixzen-calendar-toolbar{display:flex;justify-content:flex-end;padding:11px 16px;border-bottom:1px solid #e5e7eb;background:#fff}
        .brandixzen-calendar-open{color:#4f46e5;font:700 16px/1.2 Arial,sans-serif;text-decoration:none}
        .brandixzen-calendar-frame{display:block;width:100%;height:calc(100% - 43px);border:0}
        body.brandixzen-modal-open{overflow:hidden}
        @media(max-width:600px){.brandixzen-calendar-modal{padding:0}.brandixzen-calendar-dialog{height:100%;border-radius:0}.brandixzen-calendar-header{padding:14px 16px}.brandixzen-calendar-frame-wrap{margin:8px 10px 10px;border-radius:16px}.brandixzen-calendar-open{font-size:14px}}
    `;
    document.head.appendChild(styles);

    const modal = document.createElement('div');
    modal.className = 'brandixzen-calendar-modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-labelledby', 'brandixzen-calendar-title');
    modal.innerHTML = `
        <div class="brandixzen-calendar-dialog">
            <div class="brandixzen-calendar-header">
                <div>
                    <p class="brandixzen-calendar-eyebrow">SCHEDULING</p>
                    <h2 class="brandixzen-calendar-title" id="brandixzen-calendar-title">Schedule Meeting With Us</h2>
                </div>
                <button class="brandixzen-calendar-close" type="button" aria-label="Close calendar">&times;</button>
            </div>
            <div class="brandixzen-calendar-frame-wrap">
                <div class="brandixzen-calendar-toolbar">
                    <a class="brandixzen-calendar-open" href="${calendlyUrl}" target="_blank" rel="noopener noreferrer">Open Calendly ↗</a>
                </div>
                <iframe class="brandixzen-calendar-frame" title="Schedule a meeting with Calendly" loading="lazy" allow="payment" src="${calendlyUrl}"></iframe>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const openCalendar = () => {
        modal.classList.add('is-open');
        document.body.classList.add('brandixzen-modal-open');
        modal.querySelector('.brandixzen-calendar-close').focus();
    };

    const closeCalendar = () => {
        modal.classList.remove('is-open');
        document.body.classList.remove('brandixzen-modal-open');
    };

    const scrollToFooterForm = (attempt = 0) => {
        const form = document.getElementById('name-adding-form');

        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => form.querySelector('input:not([type="hidden"])')?.focus({ preventScroll: true }), 650);
            return;
        }

        if (attempt < 30) {
            window.setTimeout(() => scrollToFooterForm(attempt + 1), 100);
        }
    };

    const scrollToNewsletter = (attempt = 0) => {
        const section = document.querySelector('[data-newsletter-section]');

        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => section.querySelector('input[name="email"]')?.focus({ preventScroll: true }), 650);
            return;
        }

        if (attempt < 30) window.setTimeout(() => scrollToNewsletter(attempt + 1), 100);
    };

    document.addEventListener('click', (event) => {
        const whatsappTrigger = event.target.closest('[data-action="whatsapp"]');
        const meetingTrigger = event.target.closest('[data-action="book-meeting"]');
        const quoteTrigger = event.target.closest('[data-action="quote-request"]');
        const newsletterTrigger = event.target.closest('[data-action="newsletter"]');
        const possibleConnectTrigger = event.target.closest('button, a');
        const connectTrigger = possibleConnectTrigger?.textContent.trim().toLowerCase() === 'connect now'
            ? possibleConnectTrigger
            : null;

        if (whatsappTrigger) {
            event.preventDefault();
            window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
        } else if (meetingTrigger) {
            event.preventDefault();
            openCalendar();
        } else if (quoteTrigger) {
            event.preventDefault();
            scrollToFooterForm();
        } else if (newsletterTrigger || connectTrigger) {
            event.preventDefault();
            scrollToNewsletter();
        }
    });

    modal.querySelector('.brandixzen-calendar-close').addEventListener('click', closeCalendar);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeCalendar();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeCalendar();
    });
})();
