<div class="cta-scope">
    <section class="cta-cta-section">
        <h2 class="cta-cta-heading">
            <span class="cta-line1">One Partner for All Your Digital Growth Needs</span>
            <span class="cta-line2">Web Development • Mobile Apps • AI Solutions • Digital Marketing • eCommerce</span>
        </h2>
        <div class="cta-cta-btns">
            <a href="//wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" class="cta-cta-btn">
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
