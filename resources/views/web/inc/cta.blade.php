<div class="cta-scope">
    {{-- <section class="cta-unico-section">
        <div class="container">
            <h1 class="cta-unico-heading">The Unico Difference</h1>

            <div class="cta-hero-img-wrap">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1600&auto=format&fit=crop"
                    alt="Team celebrating together">
            </div>

            <div class="row">
                <div class="col-6 col-md-3 cta-feature-col">
                    <div class="cta-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                    </div>
                    <h3 class="cta-feature-title">AI-Native Efficiency</h3>
                    <p class="cta-feature-text">Nearly 80% of our code is AI-generated with Claude Code, and every
                        line is reviewed by our engineers. Our team works with AI across the entire delivery
                        lifecycle — from code generation and review to testing and documentation.</p>
                </div>

                <div class="col-6 col-md-3 cta-feature-col">
                    <div class="cta-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3l1.6 4.7L18 9l-4.4 1.3L12 15l-1.6-4.7L6 9l4.4-1.3L12 3z"></path>
                            <path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15z"></path>
                        </svg>
                    </div>
                    <h3 class="cta-feature-title">Loved by Clients</h3>
                    <p class="cta-feature-text">Highly rated across review platforms including Clutch, DesignRush,
                        and GoodFirms. Clients return, refer, and stay — because we treat every engagement as a
                        long-term partnership, not a one-time transaction.</p>
                </div>

                <div class="col-6 col-md-3 cta-feature-col">
                    <div class="cta-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M2 21v-1a7 7 0 0 1 7-7h0"></path>
                            <path d="M16 11l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="cta-feature-title">Versatile Team</h3>
                    <p class="cta-feature-text">Our team has evolved from traditional engineering through no-code
                        and low-code to AI-native development. Existing members have upskilled through structured AI
                        training, and new hires bring hands-on AI fluency from day one.</p>
                </div>

                <div class="col-6 col-md-3 cta-feature-col">
                    <div class="cta-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l8 3v6c0 5-3.4 8.5-8 11-4.6-2.5-8-6-8-11V5l8-3z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="cta-feature-title">Outcomes Over Output</h3>
                    <p class="cta-feature-text">We do not measure success by hours logged or features shipped. Every
                        engagement is structured around achieving your business objectives — and every AI
                        consultation ends with a working prototype, not a strategy deck.</p>
                </div>
            </div>
        </div>
    </section> --}}

    <section class="cta-cta-section">
        <h2 class="cta-cta-heading">
            <span class="cta-line1">Ready to explore what AI can do</span><br>
            <span class="cta-line2">for your business?</span>
        </h2>
        <a href="//wa.me/{{ str_replace(' ', '', $social->whatsapp) }}" class="cta-cta-btn">
            Chat on WhatsApp
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
        <a href="{{ route('get-quote') }}" class="cta-cta-btn">
            Email Our Experts
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </section>
</div>