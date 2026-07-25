<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSN Softtech — Why Us</title>
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&display=swap"
        rel="stylesheet">
</head>

<body>

    <section class="whymsn_wrap" id="whymsn_wrap">
        <div class="whymsn_head">
            <span class="whymsn_eyebrow" style="color: #D2241D !important;">Why MSN Softtech</span>
            <h2 class="whymsn_title" style="color: #00082ffa !important;"><em style="color: #D2241D !important;">One
                    hub.</em> Four strengths.<br>Every
                project.</h2>
            <p class="whymsn_sub" style="color: #00082ffa !important;">Website &amp; programming, mobile apps, AI &amp;
                automation, and marketing — all
                orbiting one accountable team.</p>
        </div>

        <div class="whymsn_stage" id="whymsn_stage">

            <!-- connector lines, drawn in % coordinates so they track the corners -->
            <svg class="whymsn_lines" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="whymsn_lineGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#E8C687" />
                        <stop offset="100%" stop-color="#7A1E2E" />
                    </linearGradient>
                </defs>
                <line class="whymsn_line" data-line="1" x1="16" y1="16" x2="50" y2="50" />
                <line class="whymsn_line" data-line="2" x1="84" y1="16" x2="50" y2="50" />
                <line class="whymsn_line" data-line="3" x1="16" y1="84" x2="50" y2="50" />
                <line class="whymsn_line" data-line="4" x1="84" y1="84" x2="50" y2="50" />
                <rect class="whymsn_node" data-node="1" x="15" y="15" width="2" height="2"
                    transform="rotate(45 16 16)" />
                <rect class="whymsn_node" data-node="2" x="83" y="15" width="2" height="2"
                    transform="rotate(45 84 16)" />
                <rect class="whymsn_node" data-node="3" x="15" y="83" width="2" height="2"
                    transform="rotate(45 16 84)" />
                <rect class="whymsn_node" data-node="4" x="83" y="83" width="2" height="2"
                    transform="rotate(45 84 84)" />
            </svg>

            <!-- center hub -->
            <div class="whymsn_hub">
                <span class="whymsn_ring"></span>
                <div class="whymsn_photo">
                    <img class="whymsn_photo_img" src="{{ asset('uploads/why-msn/msnsofttech.jpeg') }}"
                        width="302" height="302" alt="MSN SoftTech" loading="lazy">
                </div>
            </div>
            <!-- 4 corner cards -->
            <article class="whymsn_card whymsn_card--tl" data-reveal="1">
                <span class="whymsn_num">01</span>
                <span class="whymsn_rule"></span>
                <h3>AI-Native Development</h3>
                <p>Every build — web, mobile, or automation — runs through AI-assisted workflows, shipping faster
                    without cutting corners on quality.</p>
                <span class="whymsn_arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </span>
            </article>

            <article class="whymsn_card whymsn_card--tr" data-reveal="2">
                <span class="whymsn_num">02</span>
                <span class="whymsn_rule"></span>
                <h3>Full-Stack, One Team</h3>
                <p>Website, mobile app, AI agent, ad campaign, or POD store — you brief one team, not five separate
                    vendors.</p>
                <span class="whymsn_arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </span>
            </article>

            <article class="whymsn_card whymsn_card--bl" data-reveal="3">
                <span class="whymsn_num">03</span>
                <span class="whymsn_rule"></span>
                <h3>Built for Long-Term Partnership</h3>
                <p>Most clients start with one project and stay for years — we scale with you as your product and
                    marketing needs grow.</p>
                <span class="whymsn_arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </span>
            </article>

            <article class="whymsn_card whymsn_card--br" data-reveal="4">
                <span class="whymsn_num">04</span>
                <span class="whymsn_rule"></span>
                <h3>Outcomes Over Output</h3>
                <p>We track launches against real numbers — conversions, installs, ROAS, revenue — not just tickets
                    closed.</p>
                <span class="whymsn_arrow" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </span>
            </article>

        </div>
    </section>

    <style>
        #whymsn_wrap,
        #whymsn_wrap * {
            box-sizing: border-box;
        }

        #whymsn_wrap {
            --whymsn-ink: #FFFFFF;
            --whymsn-sub: #A9BBD1;
            --whymsn-wine: #E8C687;
            --whymsn-gold: #E8C687;
            --whymsn-gold-hi: #E8C687;
            --whymsn-page-bg: #FFFFFF;
            --whymsn-navy: #0A2038;
            --whymsn-navy-sub: #A9BBD1;
            --whymsn-card-line: rgba(23, 19, 15, .08);
            --whymsn-card-line-hover: red;

            position: relative;
            background: var(--whymsn-page-bg);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 60px 20px 60px;
            overflow: hidden;
        }

        /* soft ambient glow, premium ‘lit room’ backdrop */
        #whymsn_wrap::before,
        #whymsn_wrap::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        #whymsn_wrap::before {
            width: 520px;
            height: 520px;
            top: -180px;
            left: -160px;
            /* background: radial-gradient(circle, rgba(232, 198, 135, .22), transparent 70%); */
        }

        #whymsn_wrap::after {
            width: 560px;
            height: 560px;
            bottom: -220px;
            right: -180px;
            background: radial-gradient(circle, rgba(122, 30, 46, .10), transparent 70%);
        }

        /* ---------- heading ---------- */
        .whymsn_head {
            position: relative;
            z-index: 1;
            max-width: 600px;
            margin: 0 auto 0px;
            text-align: center;
        }

        .whymsn_eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: var(--whymsn-wine);
            margin-bottom: 24px;
        }

        .whymsn_eyebrow::before,
        .whymsn_eyebrow::after {
            content: "";
            display: block;
            width: 28px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--whymsn-gold));
        }

        .whymsn_eyebrow::after {
            background: linear-gradient(90deg, var(--whymsn-gold), transparent);
        }

        .whymsn_title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: clamp(32px, 4.4vw, 49px);
            font-weight: 800;
            color: var(--whymsn-ink);
            margin: 0 0 20px;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .whymsn_title em {
            font-style: italic;
            font-weight: 400;
            color: var(--whymsn-wine);
            letter-spacing: -0.01em;
        }

        .whymsn_sub {
            font-size: 16.5px;
            color: var(--whymsn-sub);
            line-height: 1.7;
            margin: 0;
            font-weight: 400;
        }

        /* ---------- stage ---------- */
        .whymsn_stage {
            position: relative;
            z-index: 1;
            width: min(94vw, 980px);
            aspect-ratio: 1 / 1;
            margin: 0 auto;
        }

        .whymsn_lines {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .whymsn_line {
            stroke: url(#whymsn_lineGrad);
            stroke-width: .16;
            stroke-linecap: round;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            opacity: 0;
            transition: stroke-dashoffset 1.1s ease, opacity .4s ease;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_line {
            opacity: .6;
            stroke-dashoffset: 0;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_line[data-line="2"] {
            transition-delay: .12s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_line[data-line="3"] {
            transition-delay: .24s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_line[data-line="4"] {
            transition-delay: .36s;
        }

        .whymsn_node {
            fill: var(--whymsn-gold);
            opacity: 0;
            transition: opacity .5s ease .9s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_node {
            opacity: .9;
        }

        /* ---------- center hub ---------- */
        .whymsn_hub {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: clamp(196px, 25vw, 302px);
            aspect-ratio: 1/1;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .whymsn_ring {
            position: absolute;
            inset: -12px;
            border-radius: 50%;
            background: conic-gradient(from 0deg,
                    var(--whymsn-gold-hi),
                    var(--whymsn-wine),
                    var(--whymsn-gold-hi) 100%);
            animation: whymsn_spin 14s linear infinite;
            opacity: .9;
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 2px), #000 calc(100% - 1px));
            mask: radial-gradient(farthest-side, transparent calc(100% - 2px), #000 calc(100% - 1px));
        }

        @keyframes whymsn_spin {
            to {
                transform: rotate(360deg);
            }
        }

        .whymsn_photo {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background:
                radial-gradient(circle at 28% 22%, rgba(232, 198, 135, .5), transparent 55%),
                radial-gradient(circle at 76% 82%, rgba(122, 30, 46, .6), transparent 58%),
                linear-gradient(160deg, #110A0A, #2A131A 58%, #4A1A26);
            box-shadow:
                0 40px 70px -26px rgba(23, 19, 15, .35),
                0 14px 26px -14px rgba(122, 30, 46, .3),
                inset 0 0 0 6px #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* padding removed — was pushing/cropping the photo inward */
        }

        .whymsn_photo::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, .12) 1px, transparent 1px);
            background-size: 16px 16px;
            opacity: .4;
            z-index: 1;
            pointer-events: none;
        }

        /* NEW: dedicated style for the actual photo */
        .whymsn_photo_img {
            position: relative;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 50%;
            display: block;
            z-index: 0;
        }

        /* ---------- cards ---------- */
        /* .whymsn_card {
            position: absolute;
            z-index: 3;
            width: clamp(200px, 23vw, 258px);
            background: #FFFFFF;
            border: 1px solid var(--whymsn-card-line);
            border-radius: 14px;
            padding: 30px 26px 28px;
            box-shadow:
                0 1px 2px rgba(23, 19, 15, .04),
                0 24px 44px -26px rgba(23, 19, 15, .16);
            opacity: 0;
            overflow: hidden;
            cursor: default;
            transition: opacity .55s ease, transform .4s cubic-bezier(.22, 1, .36, 1),
                background-color .45s ease, box-shadow .45s ease, border-color .45s ease;
        } */
        /* style edit */
        .whymsn_card {
            position: absolute;
            z-index: 3;
            width: clamp(200px, 23vw, 258px);
            background: #14273B;
            border: 1px solid var(--whymsn-card-line);
            border-radius: 14px;
            padding: 30px 26px 28px;
            box-shadow:
                0 1px 2px rgba(23, 19, 15, .04),
                0 24px 44px -26px rgba(23, 19, 15, .16);
            opacity: 0;
            overflow: hidden;
            cursor: default;
            /* transition: opacity .55s ease, transform .4s cubic-bezier(.22, 1, .36, 1),
                background-color .45s ease, box-shadow .45s ease, border-color .45s ease; */
        }

        .whymsn_card>* {
            position: relative;
            z-index: 2;
        }

        /* subtle sheen that sweeps across on hover */
        .whymsn_card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(120% 90% at 100% 0%, rgba(232, 198, 135, .16), transparent 60%);
            opacity: 0;
            transition: opacity .5s ease;
            z-index: 1;
            pointer-events: none;
        }

        /* fine gold corner bracket — the "editorial index" signature */
        .whymsn_card::after {
            content: "";
            position: absolute;
            top: 14px;
            right: 14px;
            width: 18px;
            height: 18px;
            border-top: 1.4px solid var(--whymsn-gold);
            border-right: 1.4px solid var(--whymsn-gold);
            opacity: .45;
            z-index: 1;
            transition: width .35s ease, height .35s ease, opacity .35s ease, border-color .35s ease;
        }

        .whymsn_card:hover {
            background-color: #0A2038;
            border-color: rgba(232, 198, 135, .35);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, .1),
                0 34px 60px -22px rgba(10, 32, 56, .55);
            transform: translateY(-6px);
        }

        .whymsn_card:hover::before {
            opacity: 1;
        }

        .whymsn_card:hover::after {
            width: 26px;
            height: 26px;
            opacity: 1;
            border-color: var(--whymsn-gold-hi);
        }

        .whymsn_card--tl {
            top: 9%;
            left: 7%;
            transform: translate(-14px, -14px);
        }

        .whymsn_card--tr {
            top: 9%;
            right: 7%;
            transform: translate(14px, -14px);
        }

        .whymsn_card--bl {
            bottom: 9%;
            left: 7%;
            transform: translate(-14px, 14px);
        }

        .whymsn_card--br {
            bottom: 9%;
            right: 7%;
            transform: translate(14px, 14px);
        }

        .whymsn_stage.whymsn_is-visible .whymsn_card {
            opacity: 1;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_card--tl {
            transform: translate(0, 0);
            transition-delay: 0s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_card--tr {
            transform: translate(0, 0);
            transition-delay: .12s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_card--bl {
            transform: translate(0, 0);
            transition-delay: .24s;
        }

        .whymsn_stage.whymsn_is-visible .whymsn_card--br {
            transform: translate(0, 0);
            transition-delay: .36s;
        }

        /* ---------- editorial numeral + rule (replaces icon badge) ---------- */
        .whymsn_num {
            display: block;
            font-family: 'Fraunces', 'Plus Jakarta Sans', serif;
            font-style: italic;
            font-weight: 600;
            font-size: 38px;
            line-height: 1;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, var(--whymsn-wine), var(--whymsn-gold));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 14px;
            transition: -webkit-text-fill-color .4s ease, background .4s ease;
        }

        .whymsn_rule {
            display: block;
            width: 34px;
            height: 2px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--whymsn-wine), var(--whymsn-gold));
            margin-bottom: 18px;
            transition: width .4s cubic-bezier(.22, 1, .36, 1), background .4s ease;
        }

        .whymsn_card:hover .whymsn_num {
            background: none;
            -webkit-text-fill-color: var(--whymsn-gold-hi);
            color: var(--whymsn-gold-hi);
        }

        .whymsn_card:hover .whymsn_rule {
            width: 52px;
            background: var(--whymsn-gold-hi);
        }

        .whymsn_card h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18.5px;
            font-weight: 700;
            color: var(--whymsn-ink);
            margin: 0 0 10px;
            line-height: 1.3;
            letter-spacing: -.01em;
            transition: color .4s ease;
        }

        .whymsn_card p {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 400;
            color: var(--whymsn-sub);
            line-height: 1.65;
            margin: 0;
            transition: color .4s ease;
        }

        .whymsn_card:hover h3 {
            color: #FFFFFF;
        }

        .whymsn_card:hover p {
            color: var(--whymsn-navy-sub);
        }

        /* arrow that slides in on hover, bottom-right */
        .whymsn_arrow {
            position: absolute;
            right: 26px;
            bottom: 24px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--whymsn-gold-hi);
            border: 1px solid rgba(232, 198, 135, .4);
            opacity: 0;
            transform: translate(-6px, 6px) scale(.85);
            transition: opacity .4s ease, transform .4s cubic-bezier(.22, 1, .36, 1);
        }

        .whymsn_arrow svg {
            width: 14px;
            height: 14px;
        }

        .whymsn_card:hover .whymsn_arrow {
            opacity: 1;
            transform: translate(0, 0) scale(1);
        }

        /* respect reduced motion */
        @media (prefers-reduced-motion: reduce) {

            .whymsn_ring,
            .whymsn_card,
            .whymsn_num,
            .whymsn_rule,
            .whymsn_arrow,
            .whymsn_line,
            .whymsn_node {
                animation: none !important;
                transition: none !important;
            }

            .whymsn_stage .whymsn_card,
            .whymsn_stage .whymsn_line,
            .whymsn_stage .whymsn_node {
                opacity: 1;
            }
        }

        /* ---------- responsive ---------- */
        @media (max-width: 900px) {
            .whymsn_lines {
                display: none;
            }

            .whymsn_stage {
                width: 100%;
                aspect-ratio: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 26px;
            }

            .whymsn_hub {
                position: relative;
                top: auto;
                left: auto;
                transform: none;
                width: min(58vw, 240px);
                margin-bottom: 4px;
                display: none;
            }

            .whymsn_cards-row {
                display: contents;
            }

            .whymsn_card {
                position: static;
                width: 100%;
                max-width: 460px;
                transform: none !important;
            }
        }

        @media (min-width: 901px) {
            .whymsn_stage {
                display: block;
            }
        }

        @media (max-width: 560px) {
            #whymsn_wrap {
                padding: 76px 16px 88px;
            }

            .whymsn_card {
                padding: 26px 22px 24px;
            }

            .whymsn_num {
                font-size: 32px;
            }
        }
    </style>

    <script>
        function initWhymsnReveal() {
            var stage = document.getElementById('whymsn_stage');
            if (!stage) return;
            if (stage.dataset.whymsnBound === '1') return;
            stage.dataset.whymsnBound = '1';

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reduceMotion || !('IntersectionObserver' in window)) {
                stage.classList.add('whymsn_is-visible');
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        stage.classList.add('whymsn_is-visible');
                        observer.unobserve(stage);
                    }
                });
            }, {
                threshold: 0.25
            });

            observer.observe(stage);
        }

        document.addEventListener('DOMContentLoaded', initWhymsnReveal);
        document.addEventListener('livewire:navigated', initWhymsnReveal);
    </script>
</body>

</html>
