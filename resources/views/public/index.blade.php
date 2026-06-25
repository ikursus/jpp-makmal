@extends('layouts.app')

@section('title', 'Selamat Datang - JPP Makmal')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ===== JPP Landing — "Aqueous Institutional" ============================ */
    .lp {
        --navy-950: #021321;
        --navy-900: #042038;
        --navy-800: #003366;   /* brand navy */
        --navy-700: #013a63;
        --brand-green: #00a651;  /* brand green */
        --teal: #018a73;
        --aqua: #34e0c4;
        --aqua-soft: #8df4e2;
        --ink: #0d1b27;
        --muted: #5a6b78;
        --paper: #f4f8fb;
        --paper-2: #e9f1f6;
        --line: rgba(3, 41, 70, .10);
        --display: 'Bricolage Grotesque', 'Segoe UI', Tahoma, sans-serif;
        --body: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, sans-serif;

        font-family: var(--body);
        color: var(--ink);
        min-height: 100vh;
        min-height: 100dvh;
        display: grid;
        grid-template-columns: 1fr 1.18fr;
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .lp *,
    .lp *::before,
    .lp *::after { box-sizing: border-box; }

    /* ---- LEFT : brand panel ---------------------------------------------- */
    .lp__brand {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(120% 120% at 15% 10%, var(--navy-700) 0%, var(--navy-900) 45%, var(--navy-950) 100%);
        color: #eaf7ff;
        display: flex;
        padding: clamp(1.8rem, 3.4vw, 3.6rem);
    }

    /* flowing aurora mesh */
    .lp__aurora {
        position: absolute;
        inset: -25%;
        z-index: 0;
        pointer-events: none;
        filter: blur(46px) saturate(135%);
        background:
            radial-gradient(38% 46% at 22% 28%, rgba(0, 166, 81, .55), transparent 62%),
            radial-gradient(42% 50% at 80% 18%, rgba(52, 224, 196, .42), transparent 64%),
            radial-gradient(46% 56% at 70% 84%, rgba(1, 58, 99, .9), transparent 66%),
            radial-gradient(50% 60% at 18% 88%, rgba(0, 166, 81, .34), transparent 68%);
        transform: translate3d(calc(var(--mx, 0) * 1px), calc(var(--my, 0) * 1px), 0);
    }
    .lp__aurora::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(30% 40% at 60% 35%, rgba(141, 244, 226, .35), transparent 60%),
            radial-gradient(34% 44% at 35% 65%, rgba(1, 138, 115, .5), transparent 64%);
        mix-blend-mode: screen;
    }

    /* bioluminescent orbs */
    .lp__orbs {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        transform: translate3d(calc(var(--mx, 0) * 2.4px), calc(var(--my, 0) * 2.4px), 0);
        transition: transform .35s cubic-bezier(.22, 1, .36, 1);
    }
    .lp__orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(26px);
        mix-blend-mode: screen;
        opacity: .55;
    }
    .lp__orb--1 { width: 230px; height: 230px; top: 8%;  left: 58%;
        background: radial-gradient(circle, rgba(52, 224, 196, .85), transparent 70%); }
    .lp__orb--2 { width: 300px; height: 300px; top: 52%; left: -6%;
        background: radial-gradient(circle, rgba(0, 166, 81, .8), transparent 70%); }
    .lp__orb--3 { width: 160px; height: 160px; top: 70%; left: 62%;
        background: radial-gradient(circle, rgba(141, 244, 226, .75), transparent 70%); }

    /* fine grain texture */
    .lp__grain {
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
        opacity: .05;
        mix-blend-mode: overlay;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.82' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }

    .lp__brand-inner {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: clamp(1.4rem, 3vw, 2.4rem);
    }

    /* top : logo + status pill */
    .lp__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .lp__logo { display: flex; align-items: center; gap: 12px; }
    .lp__logo-mark {
        width: 46px; height: 46px;
        display: grid; place-items: center;
        border-radius: 14px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .16);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 8px 22px rgba(0, 0, 0, .28);
        backdrop-filter: blur(6px);
        color: var(--aqua-soft);
    }
    .lp__logo-mark svg { width: 24px; height: 24px; }
    .lp__logo-text {
        font-family: var(--display);
        font-weight: 600;
        font-size: 17px;
        letter-spacing: .14em;
        line-height: 1;
        color: #eaf7ff;
    }
    .lp__logo-text strong { font-weight: 800; color: #fff; }

    .lp__pill {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 11.5px; font-weight: 600; letter-spacing: .04em;
        padding: 7px 13px; border-radius: 999px;
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .15);
        color: rgba(234, 247, 255, .9);
        backdrop-filter: blur(6px);
    }
    .lp__pill-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--aqua);
        box-shadow: 0 0 0 0 rgba(52, 224, 196, .7);
        animation: lp-ping 2.4s ease-out infinite;
    }

    /* hero */
    .lp__hero {
        margin-top: auto;
        margin-bottom: auto;
        max-width: 32rem;
    }
    .lp__eyebrow {
        display: inline-block;
        font-size: 12px; font-weight: 600;
        letter-spacing: .18em; text-transform: uppercase;
        color: var(--aqua-soft);
        margin-bottom: 18px;
    }
    .lp__title {
        font-family: var(--display);
        font-weight: 700;
        font-size: clamp(2.1rem, 4.4vw, 3.5rem);
        line-height: 1.04;
        letter-spacing: -.02em;
        color: #fff;
        margin: 0 0 18px;
    }
    .lp__title-accent {
        display: block;
        background: linear-gradient(100deg, var(--aqua-soft) 0%, var(--aqua) 35%, var(--brand-green) 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
    }
    .lp__lede {
        font-size: clamp(.95rem, 1.1vw, 1.08rem);
        line-height: 1.65;
        color: rgba(220, 238, 248, .82);
        margin: 0 0 30px;
        max-width: 30rem;
    }

    /* CTA */
    .lp__cta {
        position: relative;
        display: inline-flex; align-items: center; gap: 12px;
        padding: 15px 26px;
        border-radius: 14px;
        font-family: var(--display);
        font-weight: 600; font-size: 16px;
        text-decoration: none;
        color: #032417;
        background: linear-gradient(135deg, var(--aqua-soft) 0%, var(--aqua) 48%, var(--brand-green) 100%);
        box-shadow: 0 14px 34px rgba(0, 166, 81, .38), inset 0 1px 0 rgba(255, 255, 255, .5);
        overflow: hidden;
        transition: transform .28s cubic-bezier(.22, 1, .36, 1), box-shadow .28s;
    }
    .lp__cta::before {            /* shine sweep */
        content: "";
        position: absolute;
        top: 0; left: -120%;
        width: 70%; height: 100%;
        background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .55), transparent);
        transform: skewX(-18deg);
    }
    .lp__cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 22px 48px rgba(0, 166, 81, .5), inset 0 1px 0 rgba(255, 255, 255, .6);
    }
    .lp__cta:hover::before { animation: lp-shine 1s ease forwards; }
    .lp__cta-ico {
        display: inline-grid; place-items: center;
        width: 26px; height: 26px;
        border-radius: 8px;
        background: rgba(3, 36, 23, .14);
        transition: transform .28s cubic-bezier(.22, 1, .36, 1);
    }
    .lp__cta-ico svg { width: 16px; height: 16px; }
    .lp__cta:hover .lp__cta-ico { transform: translateX(4px); }

    .lp__note {
        display: flex; align-items: center; gap: 8px;
        margin: 20px 0 0;
        font-size: 12.5px;
        color: rgba(200, 224, 236, .68);
    }
    .lp__note svg { width: 15px; height: 15px; flex-shrink: 0; color: var(--aqua-soft); }

    .lp__foot {
        font-size: 11.5px;
        color: rgba(200, 224, 236, .5);
        letter-spacing: .02em;
    }

    /* ---- RIGHT : features panel ------------------------------------------ */
    .lp__panel {
        position: relative;
        display: flex;
        align-items: center;
        padding: clamp(2rem, 4vw, 4.5rem);
        background:
            radial-gradient(110% 80% at 90% 0%, var(--paper-2) 0%, transparent 55%),
            var(--paper);
    }
    .lp__panel::before {        /* subtle dot grid */
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: radial-gradient(rgba(3, 41, 70, .055) 1px, transparent 1px);
        background-size: 22px 22px;
        -webkit-mask-image: radial-gradient(120% 90% at 80% 20%, #000 30%, transparent 80%);
        mask-image: radial-gradient(120% 90% at 80% 20%, #000 30%, transparent 80%);
    }
    .lp__panel-inner { position: relative; width: 100%; max-width: 36rem; }

    .lp__panel-head { margin-bottom: clamp(1.4rem, 2.4vw, 2.2rem); }
    .lp__kicker {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 11.5px; font-weight: 700;
        letter-spacing: .16em; text-transform: uppercase;
        color: var(--teal);
        margin-bottom: 12px;
    }
    .lp__kicker::before {
        content: ""; width: 22px; height: 2px; border-radius: 2px;
        background: linear-gradient(90deg, var(--teal), var(--aqua));
    }
    .lp__panel-title {
        font-family: var(--display);
        font-weight: 700;
        font-size: clamp(1.5rem, 2.4vw, 2.1rem);
        line-height: 1.15;
        letter-spacing: -.015em;
        color: var(--ink);
        margin: 0;
    }

    .lp__cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: clamp(14px, 1.4vw, 20px);
    }

    .lp__card {
        position: relative;
        padding: clamp(20px, 1.8vw, 26px);
        border-radius: 20px;
        background: rgba(255, 255, 255, .72);
        border: 1px solid var(--line);
        box-shadow: 0 1px 2px rgba(3, 41, 70, .04), 0 18px 40px -28px rgba(3, 41, 70, .35);
        backdrop-filter: blur(10px);
        overflow: hidden;
        transform: perspective(900px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) translateY(0);
        transition: transform .25s cubic-bezier(.22, 1, .36, 1), box-shadow .25s, border-color .25s;
        will-change: transform;
    }
    .lp__card::before {           /* gradient hairline border */
        content: "";
        position: absolute; inset: 0;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(52, 224, 196, .8), rgba(0, 166, 81, .25) 45%, transparent 70%);
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
                mask-composite: exclude;
        opacity: 0;
        transition: opacity .3s;
    }
    .lp__card::after {            /* cursor-follow glow */
        content: "";
        position: absolute; inset: 0;
        border-radius: inherit;
        background: radial-gradient(circle at var(--gx, 50%) var(--gy, 0%), rgba(52, 224, 196, .22), transparent 55%);
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
    }
    .lp__card:hover {
        border-color: transparent;
        box-shadow: 0 1px 2px rgba(3, 41, 70, .05), 0 30px 60px -30px rgba(1, 138, 115, .55);
    }
    .lp__card:hover::before,
    .lp__card:hover::after { opacity: 1; }

    .lp__card-ico {
        position: relative;
        width: 52px; height: 52px;
        display: grid; place-items: center;
        border-radius: 15px;
        margin-bottom: 16px;
        color: var(--teal);
        background: linear-gradient(150deg, #e9fbf5 0%, #d3f3ea 100%);
        box-shadow: inset 0 1px 0 #fff, 0 8px 18px -10px rgba(1, 138, 115, .6);
    }
    .lp__card-ico svg { width: 26px; height: 26px; }
    .lp__card:hover .lp__card-ico { animation: lp-bob 1.6s ease-in-out infinite; }

    .lp__card h3 {
        font-family: var(--display);
        font-weight: 700;
        font-size: 17px;
        color: var(--ink);
        margin: 0 0 6px;
    }
    .lp__card p {
        font-size: 13.5px;
        line-height: 1.55;
        color: var(--muted);
        margin: 0;
    }
    .lp__card-num {
        position: absolute;
        top: 16px; right: 18px;
        font-family: var(--display);
        font-weight: 700;
        font-size: 13px;
        color: rgba(1, 138, 115, .3);
        letter-spacing: .02em;
    }

    /* ---- entrance choreography ------------------------------------------- */
    .lp-rise { opacity: 1; }

    @media (prefers-reduced-motion: no-preference) {
        .lp-rise {
            opacity: 0;
            animation: lp-rise .85s cubic-bezier(.22, 1, .36, 1) both;
            animation-delay: calc(var(--i, 0) * 90ms + 120ms);
        }
        .lp__brand .lp-rise { animation-name: lp-rise-left; }

        .lp__aurora { animation: lp-drift 19s ease-in-out infinite alternate; }
        .lp__orb--1 { animation: lp-float 9s ease-in-out infinite; }
        .lp__orb--2 { animation: lp-float 12s ease-in-out infinite reverse; }
        .lp__orb--3 { animation: lp-float 7.5s ease-in-out infinite .6s; }
    }

    @keyframes lp-rise {
        from { opacity: 0; transform: translateY(22px); filter: blur(6px); }
        to   { opacity: 1; transform: translateY(0);    filter: blur(0); }
    }
    @keyframes lp-rise-left {
        from { opacity: 0; transform: translateX(-26px); filter: blur(6px); }
        to   { opacity: 1; transform: translateX(0);     filter: blur(0); }
    }
    @keyframes lp-drift {
        0%   { transform: translate3d(calc(var(--mx, 0) * 1px), calc(var(--my, 0) * 1px), 0) scale(1)    rotate(0deg); }
        100% { transform: translate3d(calc(var(--mx, 0) * 1px), calc(var(--my, 0) * 1px), 0) scale(1.12) rotate(8deg); }
    }
    @keyframes lp-float {
        0%, 100% { transform: translateY(0) translateX(0); }
        50%      { transform: translateY(-26px) translateX(14px); }
    }
    @keyframes lp-ping {
        0%   { box-shadow: 0 0 0 0 rgba(52, 224, 196, .65); }
        70%  { box-shadow: 0 0 0 9px rgba(52, 224, 196, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 224, 196, 0); }
    }
    @keyframes lp-shine { to { left: 130%; } }
    @keyframes lp-bob {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-5px); }
    }

    /* ---- responsive ------------------------------------------------------ */
    @media (max-width: 900px) {
        .lp {
            grid-template-columns: 1fr;
            min-height: auto;
        }
        .lp__brand { min-height: 78vh; }
        .lp__hero { margin-top: 1.5rem; }
    }
    @media (max-width: 560px) {
        .lp__cards { grid-template-columns: 1fr; }
        .lp__brand { min-height: auto; padding-top: 2.2rem; padding-bottom: 2.6rem; }
        .lp__pill { display: none; }
    }
</style>
@endpush

@section('content')
<div class="lp" id="lpRoot">

    {{-- ===================== LEFT : brand ===================== --}}
    <aside class="lp__brand">
        <div class="lp__aurora" aria-hidden="true"></div>
        <div class="lp__orbs" aria-hidden="true">
            <span class="lp__orb lp__orb--1"></span>
            <span class="lp__orb lp__orb--2"></span>
            <span class="lp__orb lp__orb--3"></span>
        </div>
        <div class="lp__grain" aria-hidden="true"></div>

        <div class="lp__brand-inner">
            <header class="lp__top lp-rise" style="--i:0">
                <div class="lp__logo">
                    <span class="lp__logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18"/><path d="M5 21V10l7-5 7 5v11"/>
                            <path d="M9 21v-6h6v6"/><path d="M9 11h0M15 11h0"/>
                        </svg>
                    </span>
                    <span class="lp__logo-text"><strong>JPP</strong>&nbsp;MAKMAL</span>
                </div>
                <span class="lp__pill"><span class="lp__pill-dot"></span> Sistem Dalaman</span>
            </header>

            <div class="lp__hero">
                <span class="lp__eyebrow lp-rise" style="--i:1">Jabatan Perkhidmatan Pembetungan Sabah</span>
                <h1 class="lp__title lp-rise" style="--i:2">
                    Sistem Pengurusan
                    <span class="lp__title-accent">Barangan Makmal</span>
                </h1>
                <p class="lp__lede lp-rise" style="--i:3">
                    Mengurus inventori dan permohonan pinjaman barangan makmal antara Ibu Pejabat
                    dan Pejabat Daerah — pantas, telus dan dalam talian sepenuhnya.
                </p>
                <a href="{{ route('login') }}" class="lp__cta lp-rise" style="--i:4">
                    <span>Log Masuk</span>
                    <span class="lp__cta-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                        </svg>
                    </span>
                </a>
                <p class="lp__note lp-rise" style="--i:5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                    Akses untuk pegawai berdaftar JPP sahaja
                </p>
            </div>

            <footer class="lp__foot lp-rise" style="--i:6">
                © {{ config('jpp-config.general.site_year') }} {{ config('jpp-config.general.site_copyright') }}
            </footer>
        </div>
    </aside>

    {{-- ===================== RIGHT : features ===================== --}}
    <main class="lp__panel">
        <div class="lp__panel-inner">
            <div class="lp__panel-head lp-rise" style="--i:0">
                <span class="lp__kicker">Ciri Utama</span>
                <h2 class="lp__panel-title">Semua keperluan makmal, dalam satu sistem.</h2>
            </div>

            <div class="lp__cards">
                <article class="lp__card lp-rise" style="--i:1" data-tilt>
                    <span class="lp__card-num">01</span>
                    <div class="lp__card-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>
                        </svg>
                    </div>
                    <h3>Urus Inventori</h3>
                    <p>Pengurusan stok barangan makmal secara berpusat dan tersusun.</p>
                </article>

                <article class="lp__card lp-rise" style="--i:2" data-tilt>
                    <span class="lp__card-num">02</span>
                    <div class="lp__card-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 10 4 7l3-3"/><path d="M4 7h13a4 4 0 0 1 4 4"/>
                            <path d="m17 14 3 3-3 3"/><path d="M20 17H7a4 4 0 0 1-4-4"/>
                        </svg>
                    </div>
                    <h3>Pinjam Barang</h3>
                    <p>Permohonan pinjaman dalam talian dengan aliran kelulusan jelas.</p>
                </article>

                <article class="lp__card lp-rise" style="--i:3" data-tilt>
                    <span class="lp__card-num">03</span>
                    <div class="lp__card-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h4l2 6 4-15 2 9h6"/>
                        </svg>
                    </div>
                    <h3>Status Real-time</h3>
                    <p>Semak status permohonan bila-bila masa, 24 jam sehari.</p>
                </article>

                <article class="lp__card lp-rise" style="--i:4" data-tilt>
                    <span class="lp__card-num">04</span>
                    <div class="lp__card-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18"/>
                            <rect x="6" y="10" width="3.2" height="8" rx="1"/>
                            <rect x="11" y="5" width="3.2" height="13" rx="1"/>
                            <rect x="16" y="13" width="3.2" height="5" rx="1"/>
                        </svg>
                    </div>
                    <h3>Laporan</h3>
                    <p>Analitik dan laporan pengurusan untuk keputusan tepat.</p>
                </article>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var root = document.getElementById('lpRoot');
        if (!root) { return; }

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) { return; }

        /* --- pointer parallax on the brand panel --- */
        var brand = root.querySelector('.lp__brand');
        if (brand) {
            var raf = null;
            brand.addEventListener('mousemove', function (e) {
                if (raf) { return; }
                raf = requestAnimationFrame(function () {
                    var r = brand.getBoundingClientRect();
                    var mx = ((e.clientX - r.left) / r.width - 0.5) * 26;
                    var my = ((e.clientY - r.top) / r.height - 0.5) * 26;
                    root.style.setProperty('--mx', mx.toFixed(2));
                    root.style.setProperty('--my', my.toFixed(2));
                    raf = null;
                });
            });
            brand.addEventListener('mouseleave', function () {
                root.style.setProperty('--mx', 0);
                root.style.setProperty('--my', 0);
            });
        }

        /* --- 3D tilt + cursor glow on feature cards --- */
        root.querySelectorAll('[data-tilt]').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width;
                var py = (e.clientY - r.top) / r.height;
                card.style.setProperty('--ry', ((px - 0.5) * 9).toFixed(2) + 'deg');
                card.style.setProperty('--rx', ((0.5 - py) * 9).toFixed(2) + 'deg');
                card.style.setProperty('--gx', (px * 100).toFixed(1) + '%');
                card.style.setProperty('--gy', (py * 100).toFixed(1) + '%');
            });
            card.addEventListener('mouseleave', function () {
                card.style.setProperty('--rx', '0deg');
                card.style.setProperty('--ry', '0deg');
            });
        });
    })();
</script>
@endpush
