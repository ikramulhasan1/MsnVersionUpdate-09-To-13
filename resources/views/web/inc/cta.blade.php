<div class="cta-scope">
    <section class="cta-cta-section">
        <h2 class="cta-cta-heading">
            <span class="cta-line1">One Partner for All Your Digital Growth Needs</span>
            <span class="cta-line2">Web Development • Mobile Apps • AI Solutions • Digital Marketing • eCommerce</span>
        </h2>
        <div class="cta-cta-btns">
            <a href="//wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" class="cta-cta-btn cta-cta-btn-whatsapp">
                <svg class="cta-wa-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.13-2.9-7C17.19 3.03 14.7 2 12.04 2Zm0 18.13h-.01c-1.48 0-2.94-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.25-4.36c0-4.53 3.69-8.22 8.23-8.22 2.2 0 4.26.86 5.82 2.41a8.15 8.15 0 0 1 2.41 5.81c0 4.53-3.69 8.22-8.21 8.22Zm4.51-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.18-.54.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31s-.87.85-.87 2.08.89 2.41 1.02 2.58c.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z" />
                </svg>
                Chat on WhatsApp
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            {{-- <a href="{{ route('get-quote') }}" class="cta-cta-btn cta-cta-btn-outline">
                Email Our Experts
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a> --}}
            <button type="button" class="cta-cta-btn cta-cta-btn-outline"
                onclick="document.getElementById('quotePopupModal').classList.add('is-open'); document.body.style.overflow='hidden';">
                Email Our Experts
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </div>
    </section>
</div>


<div id="quotePopupModal" class="qp-modal">
    <div class="qp-modal-backdrop"
        onclick="document.getElementById('quotePopupModal').classList.remove('is-open'); document.body.style.overflow='';">
    </div>
    <div class="qp-modal-panel">
        <button type="button" class="qp-modal-close"
            onclick="document.getElementById('quotePopupModal').classList.remove('is-open'); document.body.style.overflow='';"
            aria-label="Close">&times;</button>
        <div class="qp-modal-body">
            @include('web.inc.contact-form')
        </div>
    </div>
</div>

<style>
    .cta-cta-btn-whatsapp {
        background: #1DA851;
        border-color: #25D366;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding-top: 9px;
        padding-bottom: 9px;
    }

    .cta-cta-btn-whatsapp:hover {
        background: #1DA851;
        border-color: #1DA851;
        color: #fff;
    }

    .cta-wa-icon {
        width: 32px !important;
        height: 32px !important;
        flex: none;
    }











    .qp-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .qp-modal.is-open {
        display: flex;
    }

    .qp-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(7, 12, 20, .6);
        backdrop-filter: blur(2px);
    }

    .qp-modal-panel {
        position: relative;
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 40px 32px 32px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .25);
    }

    .qp-modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 34px;
        height: 34px;
        border: none;
        background: #f2f2ef;
        border-radius: 50%;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        color: #333;
    }

    .qp-modal-close:hover {
        background: #e6e6e2;
    }

    @media (max-width: 560px) {
        .qp-modal-panel {
            padding: 32px 18px 24px;
        }
    }
</style>

<script>
    // Esc key দিয়ে বন্ধ করার জন্য
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const m = document.getElementById('quotePopupModal');
            m.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    });
</script>
