<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ get_setting('site_name', 'Alborada IPTV') }} | {{ p_trans('home_meta_tagline', null, 'Premium Streaming Service') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="/web-assets/frontend/css/bootstrap.css" rel="stylesheet">
    <style>
        :root {
            --bg: #080b1c;
            --surface: #0d1125;
            --surface-2: #111930;
            --surface-3: #171e3a;
            --text: #f0f2ff;
            --muted: #8a8fa8;
            --muted-2: #555c7a;
            --red: #ff3b3b;
            --red-dim: rgba(255, 59, 59, 0.08);
            --red-glow: rgba(255, 59, 59, 0.25);
            --green: #00e676;
            --green-dim: rgba(0, 230, 118, 0.08);
            --green-glow: rgba(0, 230, 118, 0.25);
            --pink: #e91e8c;
            --pink-dim: rgba(233, 30, 140, 0.1);
            --pink-glow: rgba(233, 30, 140, 0.4);
            --border: rgba(255, 255, 255, 0.07);
            --border-strong: rgba(255, 255, 255, 0.12);
            --radius: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── NOISE TEXTURE OVERLAY ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1000;
            opacity: 0.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .wrap {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        /* ─────────────────── NAV ─────────────────── */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border);
            background: rgba(6, 6, 8, 0.88);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 68px;
            gap: 24px;
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .logo-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 12px var(--green);
            flex-shrink: 0;
            margin-right: 2px;
            animation: pulse-dot 2.4s ease-in-out infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                box-shadow: 0 0 10px var(--green), 0 0 20px var(--green-dim);
            }

            50% {
                box-shadow: 0 0 18px var(--green), 0 0 32px rgba(0, 212, 106, 0.4);
            }
        }

        .logo-force {
            color: var(--red);
        }

        .nav-links {
            display: flex;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--muted);
            transition: color 0.2s;
            letter-spacing: 0.01em;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .nav-ctas {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.01em;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            white-space: nowrap;
            line-height: 1.4;
        }

        .btn-ghost {
            padding: 9px 16px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border-strong);
        }

        .btn-ghost:hover {
            background: var(--surface-3);
            color: var(--text);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .btn-primary {
            padding: 10px 20px;
            background: var(--red);
            color: #fff;
            box-shadow: 0 2px 8px var(--red-glow), 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            background: #ff5252;
            box-shadow: 0 4px 16px var(--red-glow), 0 2px 4px rgba(0, 0, 0, 0.3);
            transform: translateY(-1px);
        }

        .btn-green {
            padding: 10px 20px;
            background: var(--green);
            color: #0a0a0f;
            box-shadow: 0 2px 8px var(--green-glow), 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .btn-green:hover {
            background: #33ff99;
            box-shadow: 0 4px 16px var(--green-glow), 0 2px 4px rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
        }

        .btn-outline {
            padding: 10px 20px;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border-strong);
        }

        .btn-outline:hover {
            background: var(--surface-3);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .btn-lg {
            padding: 12px 28px;
            font-size: 0.925rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* ─────────────────── HERO ─────────────────── */
        .hero {
            position: relative;
            background: linear-gradient(135deg, #06081c 0%, #0c1040 50%, #0f1448 100%);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(233, 30, 140, 0.08) 0%, transparent 65%);
            top: -200px;
            left: -80px;
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(80, 60, 255, 0.07) 0%, transparent 65%);
            bottom: 40px;
            right: -80px;
            pointer-events: none;
        }

        .hero-content-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 48px;
            max-width: 1240px;
            margin: 0 auto;
            padding: 88px 28px 52px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .hero-left {
            position: relative;
        }

        .hero-left h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2.6rem, 4.2vw, 3.8rem);
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #fff;
            margin-bottom: 22px;
        }

        .hero-desc {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.62);
            line-height: 1.75;
            max-width: 480px;
            margin-bottom: 36px;
            font-weight: 400;
        }

        .hero-ctas {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        /* Hero right – movie image */
        .hero-right {
            position: relative;
            z-index: 2;
        }

        .hero-img {
            width: 100%;
            max-height: 450px;
            object-fit: cover;
            border-radius: 16px;
            display: block;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.55);
        }

        .hero-price-tag {
            position: absolute;
            bottom: 28px;
            left: -16px;
            text-align: left;
            pointer-events: none;
        }

        .hero-price-tag .from-label {
            display: block;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.65);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .hero-price-tag .price-num {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 5rem;
            color: rgba(255, 255, 255, 0.11);
            letter-spacing: -0.05em;
            line-height: 1;
        }

        /* Hero stats bar */
        .hero-stats-bar {
            background: rgba(0, 0, 0, 0.32);
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            position: relative;
            z-index: 2;
        }

        .hero-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        .hero-stat-item {
            padding: 28px 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }

        .hero-stat-item:last-child {
            border-right: none;
        }

        .hero-stat-item .stat-num {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2.2rem;
            color: #fff;
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 6px;
        }

        .hero-stat-item .stat-label {
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 600;
        }

        /* ─────────────────── PINK BUTTON ─────────────────── */
        .btn-pink {
            padding: 13px 36px;
            background: linear-gradient(135deg, #e91e8c, #c2157a);
            color: #fff !important;
            border-radius: 999px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 24px rgba(233, 30, 140, 0.45);
            border: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-pink:hover {
            background: linear-gradient(135deg, #f02a97, #d4207e);
            box-shadow: 0 6px 32px rgba(233, 30, 140, 0.6);
            transform: translateY(-2px);
            color: #fff !important;
        }

        /* Badge (used in other sections) */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px 6px 8px;
            background: var(--surface-2);
            border: 1px solid var(--border-strong);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 28px;
            letter-spacing: 0.02em;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
        }

        .badge span {
            color: var(--green);
            font-weight: 600;
        }

        /* stat-num / stat-label used in about section */
        .stat-num {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -0.03em;
            line-height: 1;
            color: var(--text);
            margin-bottom: 6px;
        }

        .stat-num .unit {
            color: var(--green);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--muted-2);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        /* ─────────────────── SECTIONS ─────────────────── */
        section {
            padding: 100px 0;
        }

        .sec-label {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--green);
            margin: 0 auto 16px;
        }

        .sec-label::before {
            content: '';
            display: block;
            width: 24px;
            height: 1.5px;
            background: var(--green);
            border-radius: 1px;
        }

        .sec-head {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 60px;
            gap: 16px;
        }

        .sec-head h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            letter-spacing: -0.03em;
            line-height: 1.1;
            max-width: 680px;
            margin: 0 auto;
            color: var(--text);
        }

        .sec-head p {
            color: var(--muted);
            line-height: 1.7;
            font-size: 1rem;
            font-weight: 400;
            max-width: 580px;
        }

        .sec-head.centered {
            align-items: center;
            text-align: center;
        }

        /* ─────────────────── SLIDER COMPONENT ─────────────────── */
        .slider-section {
            position: relative;
        }

        .slider-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 28px;
        }

        .slider-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border-strong);
            background: var(--surface-2);
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s;
            flex-shrink: 0;
            line-height: 1;
        }

        .slider-btn:hover {
            background: var(--surface-3);
            border-color: rgba(255, 255, 255, 0.22);
            transform: scale(1.08);
        }

        .slider-dots {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .slider-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: var(--border-strong);
            transition: all 0.3s;
            cursor: pointer;
        }

        .slider-dot.active {
            width: 22px;
            background: var(--green);
            box-shadow: 0 0 8px var(--green-glow);
        }

        /* ─────────────────── MOVIE SLIDER ─────────────────── */
        .slider-outer {
            position: relative;
            overflow: hidden;
        }

        .slider-outer::before,
        .slider-outer::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 12px;
            width: 60px;
            z-index: 2;
            pointer-events: none;
        }

        .slider-outer::before {
            left: 0;
            background: linear-gradient(to right, var(--bg), transparent);
        }

        .slider-outer::after {
            right: 0;
            background: linear-gradient(to left, var(--bg), transparent);
        }

        .slider-track {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            cursor: grab;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .slider-track::-webkit-scrollbar {
            display: none;
        }

        .slider-track:active {
            cursor: grabbing;
        }

        .movie-card {
            flex: 0 0 220px;
            scroll-snap-align: start;
            border-radius: 12px;
            overflow: hidden;
            background: var(--surface-2);
            border: 1px solid var(--border);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .movie-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-strong);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }

        .movie-poster {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }

        .movie-meta {
            padding: 16px 18px 20px;
        }

        .movie-meta .genre-tag {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--green);
            margin-bottom: 8px;
        }

        .movie-meta h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .movie-meta p {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ─────────────────── CHANNELS SLIDER ─────────────────── */
        .ch-slider-track {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
            cursor: grab;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .ch-slider-track::-webkit-scrollbar {
            display: none;
        }

        .ch-slider-track:active {
            cursor: grabbing;
        }

        .ch-card {
            flex: 0 0 180px;
            scroll-snap-align: start;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px 16px 20px;
            text-align: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .ch-card:hover {
            border-color: rgba(0, 230, 118, 0.2);
            background: var(--surface-2);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .ch-logo {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            margin-bottom: 4px;
            position: relative;
            overflow: hidden;
            background: var(--surface-2);
        }

        .ch-logo svg {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }

        .ch-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--text);
            display: block;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .ch-sub {
            font-size: 0.78rem;
            color: var(--muted-2);
            line-height: 1.5;
        }

        .ch-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background: rgba(232, 0, 10, 0.15);
            border: 1px solid rgba(232, 0, 10, 0.22);
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #ff4a50;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }

        .ch-live-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--red);
            animation: blink 1.4s ease-in-out infinite;
        }

        /* ─────────────────── FEATURES ─────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .feat-card {
            background: var(--surface);
            padding: 40px 32px;
            transition: background 0.25s;
            position: relative;
            overflow: hidden;
        }

        .feat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--red), var(--green));
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left;
        }

        .feat-card:hover::after {
            transform: scaleX(1);
        }

        .feat-card:hover {
            background: var(--surface-2);
        }

        .feat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 24px;
        }

        .feat-icon.r {
            background: var(--red-dim);
            color: var(--red);
        }

        .feat-icon.g {
            background: var(--green-dim);
            color: var(--green);
        }

        .feat-icon.w {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
        }

        .feat-card h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 12px;
        }

        .feat-card p {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 400;
        }

        /* ─────────────────── PRICING ─────────────────── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            align-items: start;
        }

        .price-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            position: relative;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .price-card:hover {
            border-color: var(--border-strong);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        }

        .price-card.featured {
            background: linear-gradient(160deg, rgba(255, 59, 59, 0.06), rgba(0, 230, 118, 0.04));
            border-color: rgba(0, 230, 118, 0.3);
            box-shadow: 0 4px 20px rgba(0, 230, 118, 0.1);
        }

        .price-card.featured:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
        }

        .plan-badge {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 22px;
        }

        .plan-badge.basic {
            background: rgba(255, 255, 255, 0.05);
            color: var(--muted);
        }

        .plan-badge.popular {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid rgba(0, 230, 118, 0.2);
        }

        .plan-badge.pro {
            background: var(--red-dim);
            color: var(--red);
            border: 1px solid rgba(255, 59, 59, 0.2);
        }

        .plan-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .plan-price-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin: 20px 0 8px;
        }

        .plan-price {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 3.2rem;
            letter-spacing: -0.05em;
            color: var(--text);
            line-height: 1;
        }

        .plan-period {
            font-size: 0.95rem;
            color: var(--muted);
            font-weight: 400;
        }

        .plan-desc {
            font-size: 0.88rem;
            color: var(--muted);
            margin-bottom: 28px;
            font-weight: 400;
        }

        .plan-divider {
            height: 1px;
            background: var(--border);
            margin: 26px 0;
        }

        .plan-features {
            list-style: none;
            display: grid;
            gap: 12px;
            margin-bottom: 32px;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 400;
        }

        .plan-features li .check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--green-dim);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* ─────────────────── REVIEWS ─────────────────── */
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .review-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .review-card:hover {
            border-color: var(--border-strong);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .stars {
            display: flex;
            gap: 4px;
            color: #f59e0b;
            font-size: 0.85rem;
        }

        .review-text {
            font-size: 0.95rem;
            color: var(--text);
            line-height: 1.75;
            font-weight: 400;
            flex: 1;
        }

        .review-author-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--red), var(--green));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: #fff;
            flex-shrink: 0;
        }

        .author-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
        }

        .author-loc {
            font-size: 0.8rem;
            color: var(--muted-2);
        }

        /* ─────────────────── DEVICES ─────────────────── */
        .devices-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .device-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px 24px;
            text-align: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .device-card:hover {
            border-color: rgba(0, 230, 118, 0.18);
            background: var(--surface-2);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .device-emoji {
            font-size: 2.2rem;
            margin-bottom: 18px;
            display: block;
        }

        .device-card h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .device-card p {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.65;
            font-weight: 400;
        }

        /* ─────────────────── CHANNELS ─────────────────── */
        .channels-grid {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: none;
        }

        .channels-grid::-webkit-scrollbar {
            display: none;
        }

        .ch-card {
            flex: 0 0 200px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px 18px;
            text-align: center;
            transition: all 0.3s;
        }

        .ch-card:hover {
            border-color: rgba(0, 212, 106, 0.2);
            transform: translateY(-3px);
        }

        .ch-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .ch-icon.sports {
            background: linear-gradient(135deg, #1a0505, #3d0000);
        }

        .ch-icon.movies {
            background: linear-gradient(135deg, #030d1a, #001935);
        }

        .ch-icon.kids {
            background: linear-gradient(135deg, #0a1a03, #0d2e00);
        }

        .ch-icon.music {
            background: linear-gradient(135deg, #1a0a03, #2e1500);
        }

        .ch-icon.news {
            background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
        }

        .ch-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
            margin-bottom: 4px;
            display: block;
            letter-spacing: -0.02em;
        }

        .ch-sub {
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* ─────────────────── FAQ ─────────────────── */
        .faq-list {
            display: grid;
            gap: 12px;
            max-width: 760px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        .faq-item:has(details[open]) {
            border-color: rgba(0, 230, 118, 0.18);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .faq-item summary {
            padding: 22px 26px;
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            color: var(--text);
            letter-spacing: -0.01em;
            user-select: none;
            transition: color 0.2s;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-item:hover summary {
            color: var(--green);
        }

        .faq-chevron {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid var(--border-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.65rem;
            color: var(--muted);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        details[open] .faq-chevron {
            background: var(--green-dim);
            border-color: var(--green);
            color: var(--green);
            transform: rotate(180deg);
        }

        .faq-body {
            padding: 0 26px 24px;
            font-size: 0.92rem;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 400;
        }

        /* ─────────────────── CTA BANNER ─────────────────── */
        .cta-banner {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 80px 64px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 59, 59, 0.06), transparent 65%);
            top: -250px;
            left: -150px;
            pointer-events: none;
        }

        .cta-banner::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 230, 118, 0.05), transparent 65%);
            bottom: -150px;
            right: -100px;
            pointer-events: none;
        }

        .cta-inner {
            position: relative;
            z-index: 1;
        }

        .cta-banner h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2.2rem, 3.5vw, 3.2rem);
            letter-spacing: -0.03em;
            margin-bottom: 16px;
            color: var(--text);
        }

        .cta-banner p {
            color: var(--muted);
            max-width: 540px;
            margin: 0 auto 40px;
            line-height: 1.75;
            font-weight: 400;
            font-size: 1.05rem;
        }

        .cta-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ─────────────────── FOOTER ─────────────────── */
        footer {
            border-top: 1px solid var(--border);
            padding: 64px 0 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr repeat(2, 1fr);
            gap: 56px;
            margin-bottom: 52px;
        }

        .footer-brand-desc {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.75;
            margin-top: 18px;
            font-weight: 400;
            max-width: 320px;
        }

        .footer-col h5 {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted-2);
            margin-bottom: 20px;
        }

        .footer-col a {
            display: block;
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 12px;
            transition: color 0.2s;
            font-weight: 400;
        }

        .footer-col a:hover {
            color: var(--text);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 36px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-copy {
            font-size: 0.85rem;
            color: var(--muted-2);
            font-weight: 400;
        }

        .footer-copy strong {
            color: var(--text);
            font-family: 'Syne', sans-serif;
        }

        .footer-social {
            display: flex;
            gap: 10px;
        }

        .social-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-strong);
            background: transparent;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .social-btn:hover {
            background: var(--surface-3);
            color: var(--text);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* ─────────────────── DIVIDER ─────────────────── */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 0;
        }

        /* ─────────────────── HAMBURGER ─────────────────── */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 40px;
            height: 40px;
            background: var(--surface-2);
            border: 1px solid var(--border-strong);
            border-radius: 10px;
            cursor: pointer;
            padding: 0 10px;
            flex-shrink: 0;
        }

        .hamburger span {
            display: block;
            height: 2px;
            background: var(--text);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .hamburger.open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .hamburger.open span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        .hamburger.open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        .mobile-menu {
            display: none;
            position: fixed;
            inset: 0;
            top: 68px;
            background: rgba(6, 6, 8, 0.98);
            backdrop-filter: blur(24px);
            z-index: 99;
            padding: 32px 28px;
            flex-direction: column;
            gap: 8px;
            border-top: 1px solid var(--border);
            overflow-y: auto;
        }

        .mobile-menu.open {
            display: flex;
        }

        .mobile-menu a {
            display: block;
            padding: 16px 0;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            letter-spacing: -0.02em;
            transition: color 0.2s;
        }

        .mobile-menu a:hover {
            color: var(--text);
        }

        .mobile-menu a:last-child {
            border-bottom: none;
        }

        .mobile-menu-ctas {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        /* ─────────────────── RESPONSIVE ─────────────────── */

        /* Tablet landscape */
        @media (max-width: 1100px) {
            .pricing-grid {
                gap: 14px;
            }

            .reviews-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Tablet portrait */
        @media (max-width: 1024px) {
            .wrap {
                padding: 0 24px;
            }

            /* Nav */
            .nav-links {
                display: none;
            }

            .nav-ctas {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            /* Hero */
            .hero-content-wrap {
                grid-template-columns: 1fr;
                padding: 64px 24px 44px;
                gap: 40px;
            }

            .hero-right {
                max-width: 520px;
            }

            .hero-price-tag {
                left: 0;
            }

            .hero-stats-row {
                padding: 0 24px;
            }

            /* Sections */
            section {
                padding: 64px 0;
            }

            .sec-head {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .sec-head p {
                grid-column: 1;
                max-width: 600px;
            }

            /* Features */
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Pricing */
            .pricing-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Devices */
            .devices-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            /* Reviews */
            .reviews-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Footer */
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 36px;
            }

            .footer-grid>div:first-child {
                grid-column: 1 / -1;
            }
        }

        /* Mobile large */
        @media (max-width: 768px) {
            .wrap {
                padding: 0 18px;
            }

            /* Hero */
            .hero-content-wrap {
                padding: 52px 18px 40px;
                gap: 32px;
            }

            .hero-left h1 {
                font-size: clamp(2rem, 8vw, 2.8rem);
            }

            .hero-ctas {
                flex-direction: column;
            }

            .hero-ctas .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .hero-stat-item {
                padding: 20px 12px;
            }

            .hero-stat-item .stat-num {
                font-size: 1.6rem;
            }

            /* Sections */
            section {
                padding: 52px 0;
            }

            /* Features */
            .features-grid {
                grid-template-columns: 1fr;
                gap: 0;
                border-radius: var(--radius);
            }

            .feat-card {
                padding: 28px 22px;
            }

            /* Pricing */
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 100%;
            }

            .price-card {
                padding: 26px 22px;
            }

            /* Reviews */
            .reviews-grid {
                grid-template-columns: 1fr;
            }

            /* Devices */
            .devices-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .device-card {
                padding: 22px 16px;
            }

            /* Movie cards */
            .movie-card {
                flex: 0 0 180px;
            }

            .movie-poster {
                height: 250px;
            }

            /* Channel cards */
            .ch-card {
                flex: 0 0 160px;
                padding: 20px 14px;
            }

            /* CTA */
            .cta-banner {
                padding: 48px 24px;
                border-radius: var(--radius-lg);
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-buttons .btn {
                width: 100%;
                max-width: 320px;
                justify-content: center;
            }

            /* FAQ */
            .faq-list {
                max-width: 100%;
            }

            .faq-item summary {
                font-size: 0.88rem;
                padding: 16px 18px;
            }

            .faq-body {
                padding: 0 18px 18px;
            }

            /* Footer */
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .footer-grid>div:first-child {
                grid-column: 1;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

        /* Mobile small */
        @media (max-width: 480px) {
            .wrap {
                padding: 0 16px;
            }

            .hero-left h1 {
                font-size: clamp(1.8rem, 9vw, 2.4rem);
            }

            .hero-stats-row {
                padding: 0 16px;
            }

            .hero-stat-item {
                padding: 16px 8px;
            }

            .hero-stat-item .stat-num {
                font-size: 1.3rem;
            }

            .devices-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .device-card {
                padding: 18px 12px;
            }

            .device-emoji {
                font-size: 1.6rem;
            }

            .sec-head h2 {
                font-size: 1.8rem;
            }

            .features-grid {
                border-radius: var(--radius-lg);
            }

            .btn-lg {
                padding: 13px 22px;
                font-size: 0.9rem;
            }

            .plan-price {
                font-size: 2.4rem;
            }

            .footer-col {}
        }

        /* ─────────────────── NAV AUTH & RESELLER ─────────────────── */
        .nav-auth {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-link-reseller {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--green);
            letter-spacing: 0.01em;
            transition: color 0.2s;
            padding: 0 4px;
        }

        .nav-link-reseller:hover {
            color: #00f07a;
        }

        /* ─────────────────── PRICING TOGGLE ─────────────────── */
        .pricing-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--surface-2);
            border: 1px solid var(--border-strong);
            border-radius: 12px;
            padding: 4px;
            margin: 0 auto 40px;
            width: fit-content;
        }

        .ptoggle-btn {
            padding: 9px 22px;
            border-radius: 9px;
            border: none;
            background: transparent;
            color: var(--muted);
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: all 0.22s;
            position: relative;
            white-space: nowrap;
        }

        .ptoggle-btn.active {
            background: var(--surface-3);
            color: var(--text);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        }

        .ptoggle-badge {
            position: absolute;
            top: -9px;
            right: -4px;
            background: var(--green);
            color: #020a05;
            font-size: 0.56rem;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 999px;
            letter-spacing: 0.06em;
            line-height: 1.4;
        }

        .plan-savings {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: var(--green-dim);
            border: 1px solid rgba(0, 212, 106, 0.25);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--green);
            letter-spacing: 0.06em;
            margin-top: 6px;
        }

        .trial-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: rgba(232, 0, 10, 0.1);
            border: 1px solid rgba(232, 0, 10, 0.25);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #ff5a60;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
        }

        .plan-expiry-note {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 14px;
        }

        .plan-expiry-note .expiry-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
        }

        /* ─────────────────── PORTAL PREVIEW ─────────────────── */
        .portal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 56px;
            align-items: center;
        }

        .portal-mock {
            background: var(--surface);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .portal-mock-bar {
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mock-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .mock-dot.red {
            background: #e8000a;
            opacity: 0.7;
        }

        .mock-dot.amber {
            background: #f59e0b;
            opacity: 0.7;
        }

        .mock-dot.green {
            background: var(--green);
            opacity: 0.7;
        }

        .mock-bar-label {
            margin-left: 8px;
            font-size: 0.75rem;
            color: var(--muted-2);
            font-weight: 500;
        }

        .portal-mock-body {
            padding: 20px;
            display: grid;
            gap: 12px;
        }

        .mock-stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .mock-stat-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 12px;
            text-align: center;
        }

        .mock-stat-val {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.04em;
            color: var(--text);
            margin-bottom: 3px;
        }

        .mock-stat-label {
            font-size: 0.67rem;
            color: var(--muted-2);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .mock-plan-row {
            background: var(--surface-2);
            border: 1px solid rgba(0, 212, 106, 0.22);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .mock-plan-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--text);
            margin-bottom: 2px;
        }

        .mock-plan-exp {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .mock-plan-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            background: var(--green-dim);
            border: 1px solid rgba(0, 212, 106, 0.25);
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--green);
            text-transform: uppercase;
            white-space: nowrap;
        }

        .mock-plan-status::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--green);
            animation: blink 1.4s ease-in-out infinite;
        }

        .mock-m3u-row {
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .mock-m3u-label {
            font-size: 0.7rem;
            color: var(--muted-2);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .mock-m3u-url {
            font-family: monospace;
            font-size: 0.75rem;
            color: var(--green);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .mock-copy-btn {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 7px;
            border: 1px solid var(--border-strong);
            background: var(--surface-2);
            color: var(--muted);
            cursor: default;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            flex-shrink: 0;
        }

        .portal-feature-list {
            list-style: none;
            display: grid;
            gap: 14px;
            margin-bottom: 36px;
        }

        .portal-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .portal-feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--green-dim);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .portal-feature-text strong {
            display: block;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 2px;
        }

        .portal-feature-text span {
            font-size: 0.83rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ─────────────────── SETUP GUIDE ─────────────────── */
        .setup-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 36px;
        }

        .setup-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 24px;
            transition: border-color 0.3s, transform 0.3s;
        }

        .setup-card:hover {
            border-color: var(--border-strong);
            transform: translateY(-4px);
        }

        .setup-step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--red-dim);
            color: var(--red);
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }

        .setup-platform-icon {
            font-size: 1.8rem;
            display: block;
            margin-bottom: 10px;
        }

        .setup-card h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 10px;
        }

        .setup-steps-list {
            list-style: none;
            display: grid;
            gap: 7px;
        }

        .setup-steps-list li {
            font-size: 0.82rem;
            color: var(--muted);
            padding-left: 14px;
            position: relative;
            line-height: 1.6;
        }

        .setup-steps-list li::before {
            content: '›';
            position: absolute;
            left: 0;
            color: var(--green);
            font-weight: 700;
        }

        .setup-cta-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
        }

        /* ─────────────────── RESELLER ─────────────────── */
        .reseller-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 56px;
            align-items: start;
        }

        .reseller-feat-list {
            display: grid;
            gap: 14px;
            margin-bottom: 36px;
        }

        .reseller-feat {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .reseller-feat-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--red-dim);
            color: var(--red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .reseller-feat-icon.g {
            background: var(--green-dim);
            color: var(--green);
        }

        .reseller-feat-text strong {
            display: block;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--text);
            margin-bottom: 3px;
        }

        .reseller-feat-text p {
            font-size: 0.83rem;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 300;
            margin: 0;
        }

        .reseller-mock {
            background: var(--surface);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .reseller-mock-header {
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .reseller-mock-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text);
        }

        .reseller-credit-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: var(--green-dim);
            border: 1px solid rgba(0, 212, 106, 0.25);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--green);
            font-family: 'Syne', sans-serif;
        }

        .reseller-mock-body {
            padding: 20px;
            display: grid;
            gap: 10px;
        }

        .reseller-sub-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            transition: border-color 0.3s;
        }

        .reseller-sub-row:hover {
            border-color: var(--border-strong);
        }

        .reseller-sub-name {
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 1px;
        }

        .reseller-sub-expiry {
            font-size: 0.72rem;
            color: var(--muted-2);
        }

        .reseller-status-active,
        .reseller-status-exp {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        .reseller-status-active {
            background: var(--green-dim);
            color: var(--green);
        }

        .reseller-status-exp {
            background: var(--red-dim);
            color: #ff5a60;
        }

        .reseller-mock-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .reseller-api-note {
            font-size: 0.75rem;
            color: var(--muted-2);
            font-family: monospace;
        }

        /* ─────────────────── RESPONSIVE: NEW SECTIONS ─────────────────── */
        @media (max-width: 1100px) {
            .portal-grid {
                gap: 36px;
            }

            .reseller-grid {
                gap: 36px;
            }
        }

        @media (max-width: 1024px) {
            .portal-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .setup-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .reseller-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .nav-auth {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .mock-stat-row {
                grid-template-columns: repeat(3, 1fr);
            }

            .mock-m3u-url {
                max-width: 120px;
            }

            .setup-grid {
                grid-template-columns: 1fr;
            }

            .setup-cta-row {
                flex-direction: column;
            }

            .setup-cta-row .btn {
                width: 100%;
                max-width: 320px;
                justify-content: center;
            }

            .portal-feature-list li {
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .mock-stat-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .pricing-toggle-wrap {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        /* ─────────────────── FOOTER 4 COLUMNS ─────────────────── */
        .footer-grid {
            grid-template-columns: 1.4fr repeat(3, 1fr) !important;
        }

        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .footer-grid>div:first-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 480px) {
            .footer-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* ─────────────────── ABOUT ─────────────────── */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .about-stat-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 20px;
            text-align: center;
            transition: border-color 0.25s, transform 0.25s;
        }

        .about-stat-card:hover {
            border-color: rgba(0, 230, 118, 0.2);
            transform: translateY(-3px);
        }

        .about-stat-val {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2.2rem;
            letter-spacing: -0.04em;
            color: var(--text);
            line-height: 1;
            margin-bottom: 6px;
        }

        .about-stat-val .unit {
            color: var(--green);
        }

        .about-stat-label {
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ─────────────────── CONTENT CATEGORIES ─────────────────── */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .category-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 36px 24px;
            text-align: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .category-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--red), var(--green));
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-card:hover::after {
            transform: scaleX(1);
        }

        .category-card:hover {
            border-color: var(--border-strong);
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
        }

        .category-icon {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 18px;
        }

        .category-card h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .category-card p {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ─────────────────── WHY CHOOSE ─────────────────── */
        .why-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .why-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .why-card:hover {
            border-color: var(--border-strong);
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
        }

        .why-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 22px;
        }

        .why-icon.r {
            background: var(--red-dim);
            color: var(--red);
        }

        .why-icon.g {
            background: var(--green-dim);
            color: var(--green);
        }

        .why-icon.w {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
        }

        .why-card h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text);
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }

        .why-card p {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.75;
        }

        /* ─────────────────── HOW TO ORDER ─────────────────── */
        .order-steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            margin-bottom: 40px;
        }

        .order-step {
            text-align: center;
            padding: 48px 32px;
            position: relative;
        }

        .order-step::after {
            content: '→';
            position: absolute;
            right: -8px;
            top: 48px;
            font-size: 1.8rem;
            color: var(--muted-2);
            pointer-events: none;
        }

        .order-step:last-child::after {
            display: none;
        }

        .order-step-num {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--red-dim);
            border: 2px solid rgba(255, 59, 59, 0.25);
            color: var(--red);
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .order-step h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .order-step p {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 260px;
            margin: 0 auto;
        }

        /* ─────────────────── NEWSLETTER ─────────────────── */
        .newsletter-section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 72px 64px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .newsletter-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 230, 118, 0.05), transparent 65%);
            top: -200px;
            right: -100px;
            pointer-events: none;
        }

        .newsletter-section h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 12px;
            position: relative;
        }

        .newsletter-section>p {
            color: var(--muted);
            font-size: 1rem;
            max-width: 480px;
            margin: 0 auto 32px;
            line-height: 1.75;
            position: relative;
        }

        .newsletter-form {
            display: flex;
            gap: 12px;
            justify-content: center;
            max-width: 500px;
            margin: 0 auto;
            flex-wrap: wrap;
            position: relative;
        }

        .newsletter-input {
            flex: 1;
            min-width: 220px;
            padding: 13px 18px;
            background: var(--surface-3);
            border: 1px solid var(--border-strong);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .newsletter-input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(0, 230, 118, 0.1);
        }

        .newsletter-input::placeholder {
            color: var(--muted-2);
        }

        /* ─────────────────── RESPONSIVE: NEW SECTIONS ─────────────────── */
        @media (max-width: 1024px) {
            .about-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-steps-grid {
                grid-template-columns: 1fr;
            }

            .order-step::after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .why-grid {
                grid-template-columns: 1fr;
            }

            .newsletter-section {
                padding: 48px 24px;
                border-radius: var(--radius-lg);
            }

            .newsletter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .order-step {
                padding: 32px 20px;
            }
        }

        @media (max-width: 480px) {
            .about-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="wrap">
            <nav class="navbar">
                <div class="logo">
                    <div class="logo-dot"></div>
                    {{ get_setting('site_name', 'Alborada') }}
                </div>
                <ul class="nav-links">
                    <li><a href="#about">About</a></li>
                    <li><a href="#channels">Channels</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#setup">How to Order</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#reseller" class="nav-link-reseller">Reseller</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
                <div class="nav-ctas">
                    <div class="nav-auth">
                        <a href="{{ route('customer.login') }}" class="btn btn-ghost">{{ p_trans('home_nav_login', null, 'Login') }}</a>
                        <a href="{{ route('customer.register') }}" class="btn btn-ghost">{{ p_trans('home_nav_register', null, 'Register') }}</a>
                    </div>
                    <a href="#pricing" class="btn btn-ghost">{{ p_trans('home_nav_plans', null, 'View Plans') }}</a>
                    <a href="{{ route('customer.register') }}" class="btn btn-primary">{{ p_trans('home_nav_trial', null, 'Start Free Trial') }}</a>
                </div>
                <button class="hamburger" id="hamburger" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
    </header>

    <div class="mobile-menu" id="mobileMenu">
        <a href="#about" class="mobile-nav-link">About</a>
        <a href="#channels" class="mobile-nav-link">Channels</a>
        <a href="#features" class="mobile-nav-link">Features</a>
        <a href="#pricing" class="mobile-nav-link">Pricing</a>
        <a href="#setup" class="mobile-nav-link">How to Order</a>
        <a href="#faq" class="mobile-nav-link">FAQ</a>
        <a href="#reseller" class="mobile-nav-link" style="color:var(--green);">Reseller</a>
        <a href="{{ route('contact') }}" class="mobile-nav-link">Contact</a>
        <div class="mobile-menu-ctas">
            <a href="{{ route('customer.login') }}" class="btn btn-ghost btn-lg"
                style="width:100%;justify-content:center;">Login</a>
            <a href="{{ route('customer.register') }}" class="btn btn-outline btn-lg"
                style="width:100%;justify-content:center;">Register</a>
            <a href="#pricing" class="btn btn-ghost btn-lg" style="width:100%;justify-content:center;">View Plans</a>
            <a href="{{ route('customer.register') }}" class="btn btn-primary btn-lg"
                style="width:100%;justify-content:center;">Start Free Trial</a>
        </div>
    </div>

    <main>

        <!-- ── HERO ── -->
        @if(!isset($sections['hero']) || $sections['hero']->is_active)
        <section class="hero" id="hero">
            <div class="hero-content-wrap">
                <div class="hero-left">
                    <h1>{!! p_trans('home_hero_heading', null, 'Best IPTV subscription #1<br>for the USA and Canada.') !!}</h1>
                    <p class="hero-desc">{{ p_trans('home_hero_desc', null, 'Experience unbeatable entertainment with the best IPTV service, offering the fastest and most reliable server in 4K, FHD, HD, and SD quality.') }}</p>
                    <div class="hero-ctas">
                        <a href="#pricing" class="btn btn-pink btn-lg">{{ p_trans('home_hero_btn1', null, 'Get Started') }}</a>
                        <a href="{{ route('customer.register') }}" class="btn btn-pink btn-lg">{{ p_trans('home_hero_btn2', null, 'Free Trial') }}</a>
                    </div>
                </div>
                <div class="hero-right">
                    @if(get_setting('home_hero_image'))
                    <img src="{{ asset(getFilePath(get_setting('home_hero_image'))) }}" alt="{{ p_trans('home_hero_heading', null, 'Premium IPTV Streaming') }}" class="hero-img">
                    @else
                    <img src="https://picsum.photos/seed/iptv-hero/700/460" alt="Premium IPTV Streaming" class="hero-img">
                    @endif
                    <div class="hero-price-tag">
                        <span class="from-label">{{ p_trans('home_hero_from_label', null, 'FROM') }}</span>
                        <span class="price-num">{{ p_trans('home_hero_from_price', null, '11.99$') }}</span>
                    </div>
                </div>
            </div>
            <div class="hero-stats-bar">
                <div class="hero-stats-row">
                    <div class="hero-stat-item">
                        <div class="stat-num">{{ p_trans('home_stat1_num', null, '40K+') }}</div>
                        <div class="stat-label">{{ p_trans('home_stat1_label', null, 'Live Channels') }}</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="stat-num">{{ p_trans('home_stat2_num', null, '150K+') }}</div>
                        <div class="stat-label">{{ p_trans('home_stat2_label', null, 'Movies / Series') }}</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="stat-num">{{ p_trans('home_stat3_num', null, '10K+') }}</div>
                        <div class="stat-label">{{ p_trans('home_stat3_label', null, 'Customers Happy') }}</div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── ABOUT ── -->
        @if(!isset($sections['about']) || $sections['about']->is_active)
        <section id="about">
            <div class="wrap">
                <div class="about-grid">
                    <div class="about-left">
                        <div class="sec-label">{{ p_trans('home_about_label', null, 'About ' . get_setting('site_name', 'Alborada')) }}</div>
                        <h2
                            style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(2rem,3.2vw,2.8rem);letter-spacing:-0.03em;line-height:1.1;color:var(--text);margin-bottom:20px;">
                            {{ p_trans('home_about_heading', null, 'The streaming service built for serious viewers') }}</h2>
                        <p
                            style="color:var(--muted);line-height:1.75;font-size:1rem;margin-bottom:28px;max-width:460px;">
                            {{ p_trans('home_about_desc', null, 'We built this service with one goal: deliver the most reliable, feature-rich IPTV experience available. We combine the fastest servers, the largest content library, and dedicated 24/7 human support so you never miss a moment.') }}</p>
                        <a href="#features" class="btn btn-outline btn-lg">{{ p_trans('home_about_btn', null, 'Discover More') }}</a>
                    </div>
                    <div class="about-stats">
                        <div class="about-stat-card">
                            <div class="about-stat-val">{{ p_trans('home_about_stat1_val', null, '40K+') }}</div>
                            <div class="about-stat-label">{{ p_trans('home_about_stat1_label', null, 'Live Channels') }}</div>
                        </div>
                        <div class="about-stat-card">
                            <div class="about-stat-val">{{ p_trans('home_about_stat2_val', null, '150K+') }}</div>
                            <div class="about-stat-label">{{ p_trans('home_about_stat2_label', null, 'Movies & Series') }}</div>
                        </div>
                        <div class="about-stat-card">
                            <div class="about-stat-val">{{ p_trans('home_about_stat3_val', null, '99.9%') }}</div>
                            <div class="about-stat-label">{{ p_trans('home_about_stat3_label', null, 'Server Uptime') }}</div>
                        </div>
                        <div class="about-stat-card">
                            <div class="about-stat-val">{{ p_trans('home_about_stat4_val', null, '24/7') }}</div>
                            <div class="about-stat-label">{{ p_trans('home_about_stat4_label', null, 'Expert Support') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── MOVIES ── -->
        <section id="movies">
            <div class="wrap">
                <div class="sec-label">Content Library</div>
                <div class="sec-head">
                    <h2>Featured titles & live events</h2>
                    <p>Browse top films, series and sports in a cinematic preview — all instantly available on demand
                        through your Alborada subscription.</p>
                </div>
            </div>
            <div class="wrap">
                <div class="slider-outer">
                    <div class="slider-track" id="movieSlider">
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf01/320/460" alt="Action" class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Action</span>
                                <h4>Blockbuster Hits</h4>
                                <p>Live-action thrillers & exclusive premieres.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf02/320/460" alt="Drama" class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Drama</span>
                                <h4>Award Dramas</h4>
                                <p>High-quality series instantly on demand.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf03/320/460" alt="Sports" class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Sports</span>
                                <h4>Live Sports</h4>
                                <p>Top leagues & tournaments worldwide.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf04/320/460" alt="Kids" class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Family</span>
                                <h4>Kids & Family</h4>
                                <p>Cartoons and family-friendly content.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf05/320/460" alt="Documentary"
                                class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Documentary</span>
                                <h4>Top Documentaries</h4>
                                <p>Premium true stories curated for you.</p>
                            </div>
                        </div>
                        <div class="movie-card">
                            <img src="https://picsum.photos/seed/sf06/320/460" alt="Sci-Fi" class="movie-poster">
                            <div class="movie-meta">
                                <span class="genre-tag">Sci-Fi</span>
                                <h4>Sci-Fi & Fantasy</h4>
                                <p>Mind-bending worlds and epic adventures.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── CONTENT CATEGORIES ── -->
        @if(!isset($sections['categories']) || $sections['categories']->is_active)
        <section id="categories">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_cat_label', null, 'Content Library') }}</div>
                <div class="sec-head centered">
                    <h2>{{ p_trans('home_cat_heading', null, 'Something for every viewer') }}</h2>
                    <p>{{ p_trans('home_cat_desc', null, 'From live sports to the latest blockbusters — we cover every content category you love.') }}</p>
                </div>
                <div class="categories-grid">
                    <div class="category-card">
                        <span class="category-icon">{{ p_trans('home_cat1_icon', null, '🎬') }}</span>
                        <h4>{{ p_trans('home_cat1_title', null, 'Movies & Series') }}</h4>
                        <p>{{ p_trans('home_cat1_desc', null, '150,000+ on-demand titles including the latest releases and full series box-sets.') }}</p>
                    </div>
                    <div class="category-card">
                        <span class="category-icon">{{ p_trans('home_cat2_icon', null, '⚽') }}</span>
                        <h4>{{ p_trans('home_cat2_title', null, 'Sporting Events') }}</h4>
                        <p>{{ p_trans('home_cat2_desc', null, 'Live coverage of every major league — Premier League, NFL, NBA, UFC and more.') }}</p>
                    </div>
                    <div class="category-card">
                        <span class="category-icon">{{ p_trans('home_cat3_icon', null, '📺') }}</span>
                        <h4>{{ p_trans('home_cat3_title', null, 'TV Shows') }}</h4>
                        <p>{{ p_trans('home_cat3_desc', null, 'Catch-up and live TV from hundreds of channels across the USA, UK, Canada and beyond.') }}</p>
                    </div>
                    <div class="category-card">
                        <span class="category-icon">{{ p_trans('home_cat4_icon', null, '🎥') }}</span>
                        <h4>{{ p_trans('home_cat4_title', null, 'Documentaries') }}</h4>
                        <p>{{ p_trans('home_cat4_desc', null, 'Award-winning documentaries on nature, history, science, true crime and culture.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── FEATURES ── -->
        @if(!isset($sections['features']) || $sections['features']->is_active)
        <section id="features">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_feat_label', null, 'Why ' . get_setting('site_name', 'Alborada')) }}</div>
                <div class="sec-head">
                    <h2>{{ p_trans('home_feat_heading', null, 'Everything your streaming setup needs') }}</h2>
                    <p>{{ p_trans('home_feat_desc', null, 'Professional delivery, robust infrastructure, and dedicated support — built for viewers who don\'t compromise.') }}</p>
                </div>
                <div class="features-grid">
                    <div class="feat-card">
                        <div class="feat-icon g">{{ p_trans('home_feat1_icon', null, '📱') }}</div>
                        <h3>{{ p_trans('home_feat1_title', null, 'Fully Compatible') }}</h3>
                        <p>{{ p_trans('home_feat1_desc', null, 'Works on every device — Smart TVs, Firestick, Android, iOS, Kodi, TiviMate and more.') }}</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon r">{{ p_trans('home_feat2_icon', null, '📡') }}</div>
                        <h3>{{ p_trans('home_feat2_title', null, 'High Availability Servers') }}</h3>
                        <p>{{ p_trans('home_feat2_desc', null, 'Resilient redundant infrastructure with advanced anti-freeze technology for near-zero interruption.') }}</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon w">{{ p_trans('home_feat3_icon', null, '🎬') }}</div>
                        <h3>{{ p_trans('home_feat3_title', null, 'Invaluable Content') }}</h3>
                        <p>{{ p_trans('home_feat3_desc', null, '40,000+ live channels and 150,000+ VOD titles spanning every genre, language and region.') }}</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon g">{{ p_trans('home_feat4_icon', null, '🔄') }}</div>
                        <h3>{{ p_trans('home_feat4_title', null, 'Free Updates') }}</h3>
                        <p>{{ p_trans('home_feat4_desc', null, 'Channel lists and VOD libraries are updated automatically — no manual action required.') }}</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon r">{{ p_trans('home_feat5_icon', null, '💳') }}</div>
                        <h3>{{ p_trans('home_feat5_title', null, 'Money Back Guarantee') }}</h3>
                        <p>{{ p_trans('home_feat5_desc', null, 'Not satisfied? We offer a full refund within the guarantee window — zero questions asked.') }}</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon w">{{ p_trans('home_feat6_icon', null, '🔒') }}</div>
                        <h3>{{ p_trans('home_feat6_title', null, '100% Secure Payment') }}</h3>
                        <p>{{ p_trans('home_feat6_desc', null, 'All transactions are encrypted end-to-end. Your payment details are never stored on our servers.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── PRICING ── -->
        @if(!isset($sections['pricing']) || $sections['pricing']->is_active)
        <section id="pricing">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_pricing_label', null, 'Pricing') }}</div>
                <div class="sec-head centered">
                    <h2>{{ p_trans('home_pricing_heading', null, 'Plans for every household') }}</h2>
                    <p>{{ p_trans('home_pricing_desc', null, 'Choose the package that matches your needs. Instant activation. Cancel any time.') }}</p>
                </div>

                @if($plans->isNotEmpty())
                <div class="pricing-grid" id="pricingGrid">
                    @foreach($plans as $plan)
                    @php
                        $isFeatured = $loop->iteration === 2 || ($plans->count() === 1);
                        $priceWhole = floor($plan->price);
                        $priceDec   = '.' . str_pad((int)(($plan->price - $priceWhole) * 100), 2, '0');
                        $btnClass   = $isFeatured ? 'btn-green' : ($loop->last ? 'btn-primary' : 'btn-outline');
                        $btnText    = $isFeatured ? p_trans('home_pricing_btn_featured', null, 'Choose Plan') : p_trans('home_pricing_btn', null, 'Order Now');
                    @endphp
                    <div class="price-card {{ $isFeatured ? 'featured' : '' }}">
                        @if($plan->is_trial)
                        <div class="trial-badge">★ {{ $plan->trial_days }}-Day Free Trial Available</div>
                        @endif
                        @if($isFeatured)
                        <div class="plan-badge popular">★ {{ p_trans('home_pricing_popular_badge', null, 'Most Popular') }}</div>
                        @else
                        <div class="plan-badge basic">{{ $plan->title }}</div>
                        @endif
                        <div class="plan-name">{{ $plan->max_connections ?? 1 }} {{ ($plan->max_connections ?? 1) > 1 ? 'Devices' : 'Device' }}</div>
                        <div class="plan-price-row">
                            <div class="plan-price">${{ $priceWhole }}<span style="font-size:1.5rem">{{ $priceDec }}</span></div>
                            <div class="plan-period">/ {{ $plan->duration_days }} days</div>
                        </div>
                        <div class="plan-desc">{{ $plan->description ?? '' }}</div>
                        <div class="plan-divider"></div>
                        <ul class="plan-features">
                            <li><span class="check">✓</span> {{ $plan->max_connections ?? 1 }} concurrent screen(s)</li>
                            <li><span class="check">✓</span> {{ $plan->streaming_quality ?? 'HD' }} quality</li>
                            @if($plan->catchup_days)
                            <li><span class="check">✓</span> {{ $plan->catchup_days }}-day catch-up TV</li>
                            @endif
                            @if($plan->dvr_enabled)
                            <li><span class="check">✓</span> DVR recording</li>
                            @endif
                            <li><span class="check">✓</span> Instant activation</li>
                        </ul>
                        <div class="plan-expiry-note">
                            <span class="expiry-dot"></span> {{ p_trans('home_pricing_expiry_note', null, 'Expiry tracked in your dashboard') }}
                        </div>
                        <div class="plan-divider"></div>
                        <a href="{{ route('subscription.confirm', $plan->id) }}" class="btn {{ $btnClass }}"
                            style="width:100%; border-radius:12px; padding:13px;">{{ $btnText }}</a>
                    </div>
                    @endforeach
                </div>
                @else
                {{-- Fallback when no plans in DB --}}
                <div class="pricing-grid" id="pricingGrid">
                    <div class="price-card">
                        <div class="plan-badge basic">Starter</div>
                        <div class="plan-name">1 Device</div>
                        <div class="plan-price-row">
                            <div class="plan-price">$11<span style="font-size:1.5rem">.99</span></div>
                            <div class="plan-period">/ month</div>
                        </div>
                        <div class="plan-desc">Perfect for solo viewers who want the full library.</div>
                        <div class="plan-divider"></div>
                        <a href="{{ route('pricing.plans') }}" class="btn btn-outline" style="width:100%;border-radius:12px;padding:13px;">View Plans</a>
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── REVIEWS ── -->
        @if(!isset($sections['reviews']) || $sections['reviews']->is_active)
        <section id="reviews">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_reviews_label', null, 'Customer Reviews') }}</div>
                <div class="sec-head">
                    <h2>{{ p_trans('home_reviews_heading', null, 'Trusted by thousands worldwide') }}</h2>
                    <p>{{ p_trans('home_reviews_desc', null, 'See why customers across Europe, North America, and the Middle East rely on us daily.') }}</p>
                </div>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"{{ p_trans('home_review1_text', null, 'Completely transformed my streaming setup. The channel selection is outstanding and the service never drops — even during peak hours.') }}"</p>
                        <div class="review-author-row">
                            <div class="avatar">{{ strtoupper(substr(p_trans('home_review1_name', null, 'Alex M.'), 0, 2)) }}</div>
                            <div>
                                <div class="author-name">{{ p_trans('home_review1_name', null, 'Alex M.') }}</div>
                                <div class="author-loc">{{ p_trans('home_review1_loc', null, 'London, UK') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"{{ p_trans('home_review2_text', null, 'The movie library is incredible and setup was instant. Support responded in minutes. Nothing else compares at this price point.') }}"</p>
                        <div class="review-author-row">
                            <div class="avatar">{{ strtoupper(substr(p_trans('home_review2_name', null, 'Jessica K.'), 0, 2)) }}</div>
                            <div>
                                <div class="author-name">{{ p_trans('home_review2_name', null, 'Jessica K.') }}</div>
                                <div class="author-loc">{{ p_trans('home_review2_loc', null, 'Toronto, CA') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"{{ p_trans('home_review3_text', null, 'Best IPTV I\'ve used. Works perfectly on Firestick, my smart TV, and mobile with zero buffering. Highly recommended.') }}"</p>
                        <div class="review-author-row">
                            <div class="avatar">{{ strtoupper(substr(p_trans('home_review3_name', null, 'Omar S.'), 0, 2)) }}</div>
                            <div>
                                <div class="author-name">{{ p_trans('home_review3_name', null, 'Omar S.') }}</div>
                                <div class="author-loc">{{ p_trans('home_review3_loc', null, 'Dubai, AE') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── PORTAL PREVIEW ── -->
        @if(!isset($sections['portal']) || $sections['portal']->is_active)
        <section id="portal">
            <div class="wrap">
                <div class="sec-label">Client Portal</div>
                <div class="portal-grid">
                    <!-- Mock dashboard -->
                    <div class="portal-mock">
                        <div class="portal-mock-bar">
                            <div class="mock-dot red"></div>
                            <div class="mock-dot amber"></div>
                            <div class="mock-dot green"></div>
                            <span class="mock-bar-label">dashboard.alborada.tv</span>
                        </div>
                        <div class="portal-mock-body">
                            <div class="mock-stat-row">
                                <div class="mock-stat-card">
                                    <div class="mock-stat-val" style="color:var(--green)">Active</div>
                                    <div class="mock-stat-label">Plan Status</div>
                                </div>
                                <div class="mock-stat-card">
                                    <div class="mock-stat-val">2</div>
                                    <div class="mock-stat-label">Screens</div>
                                </div>
                                <div class="mock-stat-card">
                                    <div class="mock-stat-val">47d</div>
                                    <div class="mock-stat-label">Remaining</div>
                                </div>
                            </div>
                            <div class="mock-plan-row">
                                <div>
                                    <div class="mock-plan-name">2 Devices — Quarterly</div>
                                    <div class="mock-plan-exp">Expires: Aug 22, 2026</div>
                                </div>
                                <div class="mock-plan-status">Live</div>
                            </div>
                            <div class="mock-m3u-row">
                                <div>
                                    <div class="mock-m3u-label">M3U Playlist URL</div>
                                    <div class="mock-m3u-url">http://stream.alborada.tv/get.php?username=…</div>
                                </div>
                                <button class="mock-copy-btn">Copy</button>
                            </div>
                        </div>
                    </div>
                    <!-- Feature list -->
                    <div>
                        <div class="sec-head" style="align-items:flex-start;text-align:left;margin-bottom:28px;">
                            <h2>Your streaming portal, all in one place</h2>
                            <p>Log in to manage active subscriptions, copy your M3U codes, track expiry dates, and open
                                support tickets — from one clean dashboard.</p>
                        </div>
                        <ul class="portal-feature-list">
                            <li>
                                <div class="portal-feature-icon">👤</div>
                                <div class="portal-feature-text">
                                    <strong>Profile &amp; Account</strong>
                                    <span>Update your email, password and contact details any time.</span>
                                </div>
                            </li>
                            <li>
                                <div class="portal-feature-icon">📋</div>
                                <div class="portal-feature-text">
                                    <strong>Subscription History</strong>
                                    <span>See active, expired and past plans with full expiry tracking.</span>
                                </div>
                            </li>
                            <li>
                                <div class="portal-feature-icon">🔗</div>
                                <div class="portal-feature-text">
                                    <strong>M3U &amp; Credentials</strong>
                                    <span>Instantly copy your M3U URL and login credentials to any player.</span>
                                </div>
                            </li>
                            <li>
                                <div class="portal-feature-icon">🎧</div>
                                <div class="portal-feature-text">
                                    <strong>Support Tickets</strong>
                                    <span>Submit and track support requests directly from your account.</span>
                                </div>
                            </li>
                        </ul>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;">
                            <a href="{{ route('customer.login') }}" class="btn btn-primary btn-lg">Login to
                                Portal</a>
                            <a href="{{ route('customer.register') }}" class="btn btn-ghost btn-lg">Create
                                Account</a>
                            <a href="{{ route('reseller.login') }}" class="btn btn-outline btn-lg"
                                style="border-color:rgba(0,212,106,.4);color:#00d46a;">Reseller Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── DEVICES ── -->
        @if(!isset($sections['devices']) || $sections['devices']->is_active)
        <section id="devices">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_devices_label', null, 'Compatibility') }}</div>
                <div class="sec-head">
                    <h2>{{ p_trans('home_devices_heading', null, 'Works on any device') }}</h2>
                    <p>{{ p_trans('home_devices_desc', null, get_setting('site_name', 'Alborada') . ' runs on every major platform — from 65-inch smart TVs to pocket-sized phones.') }}</p>
                </div>
                <div class="devices-grid">
                    <div class="device-card">
                        <span class="device-emoji">📺</span>
                        <h4>{{ p_trans('home_dev1_title', null, 'Smart TVs') }}</h4>
                        <p>{{ p_trans('home_dev1_desc', null, 'Samsung, LG, Sony, Android TV and all major brands.') }}</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">🔥</span>
                        <h4>{{ p_trans('home_dev2_title', null, 'Firestick') }}</h4>
                        <p>{{ p_trans('home_dev2_desc', null, 'Fast setup and smooth playback on Amazon Fire devices.') }}</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">📱</span>
                        <h4>{{ p_trans('home_dev3_title', null, 'Mobile') }}</h4>
                        <p>{{ p_trans('home_dev3_desc', null, 'Android and iOS — watch anywhere, any time.') }}</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">💻</span>
                        <h4>{{ p_trans('home_dev4_title', null, 'Desktop') }}</h4>
                        <p>{{ p_trans('home_dev4_desc', null, 'VLC, Kodi, TiviMate and all major IPTV players.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── HOW TO ORDER ── -->
        @if(!isset($sections['setup']) || $sections['setup']->is_active)
        <section id="setup">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_setup_label', null, 'How to Order') }}</div>
                <div class="sec-head centered">
                    <h2>{{ p_trans('home_setup_heading', null, 'Start streaming in three simple steps') }}</h2>
                    <p>{{ p_trans('home_setup_desc', null, 'Getting started takes less than 5 minutes. Your subscription activates instantly after payment.') }}</p>
                </div>
                <div class="order-steps-grid">
                    <div class="order-step">
                        <div class="order-step-num">01</div>
                        <span style="font-size:2.8rem;display:block;margin-bottom:18px;">{{ p_trans('home_step1_icon', null, '🛒') }}</span>
                        <h4>{{ p_trans('home_step1_title', null, 'Select Your Plan') }}</h4>
                        <p>{{ p_trans('home_step1_desc', null, 'Choose the subscription period and number of devices that best fits your household. All plans include the full channel and VOD library.') }}</p>
                    </div>
                    <div class="order-step">
                        <div class="order-step-num">02</div>
                        <span style="font-size:2.8rem;display:block;margin-bottom:18px;">{{ p_trans('home_step2_icon', null, '📧') }}</span>
                        <h4>{{ p_trans('home_step2_title', null, 'Receive Your Credentials') }}</h4>
                        <p>{{ p_trans('home_step2_desc', null, 'After payment, your M3U URL and login credentials are sent to your email instantly. No waiting, no manual processing.') }}</p>
                    </div>
                    <div class="order-step">
                        <div class="order-step-num">03</div>
                        <span style="font-size:2.8rem;display:block;margin-bottom:18px;">{{ p_trans('home_step3_icon', null, '🎉') }}</span>
                        <h4>{{ p_trans('home_step3_title', null, 'Enjoy Unlimited Access') }}</h4>
                        <p>{{ p_trans('home_step3_desc', null, 'Open your favourite IPTV app, enter your credentials, and start streaming 40,000+ channels and 150,000+ titles right away.') }}</p>
                    </div>
                </div>
                <div class="setup-cta-row" style="justify-content:center;">
                    <a href="#pricing" class="btn btn-primary btn-lg">{{ p_trans('home_setup_btn1', null, 'Choose a Plan') }}</a>
                    <a href="{{ route('contact') }}" class="btn btn-ghost btn-lg">{{ p_trans('home_setup_btn2', null, 'Contact Support') }}</a>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── WHY CHOOSE ── -->
        @if(!isset($sections['why']) || $sections['why']->is_active)
        <section id="why">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_why_label', null, 'Why ' . get_setting('site_name', 'Alborada')) }}</div>
                <div class="sec-head centered">
                    <h2>{{ p_trans('home_why_heading', null, 'Why thousands choose us') }}</h2>
                    <p>{{ p_trans('home_why_desc', null, 'We built the service we always wanted — reliable, flexible, and packed with content you actually watch.') }}</p>
                </div>
                <div class="why-grid">
                    <div class="why-card">
                        <div class="why-icon r">🎛️</div>
                        <h3>Customizable Packages</h3>
                        <p>Choose from 1 to 4 devices, pick your billing period from 1 to 12 months, and only pay for
                            what you need. Upgrade or change plan any time from your dashboard.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon g">📲</div>
                        <h3>Multi-Device Compatibility</h3>
                        <p>One subscription works across Smart TVs, Firestick, Android, iOS, Kodi, TiviMate, VLC and
                            every major M3U-compatible player — no extra setup needed.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon w">✨</div>
                        <h3>{{ p_trans('home_why3_title', null, 'High-Quality Streaming') }}</h3>
                        <p>{{ p_trans('home_why3_desc', null, 'Stream in true 4K, FHD, HD and SD depending on your connection. Our anti-freeze technology ensures smooth playback even during peak hours.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── CHANNELS ── -->
        @if(!isset($sections['channels']) || $sections['channels']->is_active)
        <section id="channels">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_channels_label', null, 'Channel Lineup') }}</div>
                <div class="sec-head">
                    <h2>{{ p_trans('home_channels_heading', null, 'Premium channels, curated') }}</h2>
                    <p>{{ p_trans('home_channels_desc', null, 'Scroll through our top channel categories — 40,000+ live feeds available instantly.') }}</p>
                </div>
                <div class="channels-grid">
                    <div class="ch-card">
                        <div class="ch-icon sports">⚽</div>
                        <span class="ch-name">Sports Hub</span>
                        <div class="ch-sub">Live sports channels</div>
                    </div>
                    <div class="ch-card">
                        <div class="ch-icon movies">🎬</div>
                        <span class="ch-name">Movie Max</span>
                        <div class="ch-sub">Blockbusters & premieres</div>
                    </div>
                    <div class="ch-card">
                        <div class="ch-icon kids">🧒</div>
                        <span class="ch-name">Kids TV</span>
                        <div class="ch-sub">Family entertainment</div>
                    </div>
                    <div class="ch-card">
                        <div class="ch-icon music">🎵</div>
                        <span class="ch-name">Music Live</span>
                        <div class="ch-sub">Top music channels</div>
                    </div>
                    <div class="ch-card">
                        <div class="ch-icon news">📰</div>
                        <span class="ch-name">News 24/7</span>
                        <div class="ch-sub">Global news coverage</div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── FAQ ── -->
        @if(!isset($sections['faq']) || $sections['faq']->is_active)
        <section id="faq">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_faq_label', null, 'FAQ') }}</div>
                <div class="sec-head centered">
                    <h2>{{ p_trans('home_faq_heading', null, 'Common questions answered') }}</h2>
                    <p>{{ p_trans('home_faq_desc', null, 'Everything you need to know before getting started.') }}</p>
                </div>

                <div class="faq-list">
                    <div class="faq-item">
                        <details open>
                            <summary>{{ p_trans('home_faq1_q', null, 'What internet speed do I need?') }} <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">{{ p_trans('home_faq1_a', null, 'For HD streaming we recommend at least 10 Mbps. For 4K quality or multiple simultaneous devices, 30+ Mbps is ideal for a smooth experience.') }}</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>{{ p_trans('home_faq2_q', null, 'How fast is account activation?') }} <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">{{ p_trans('home_faq2_a', null, 'Most accounts are activated instantly after payment. Your credentials are delivered by email within minutes — sometimes seconds.') }}</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>{{ p_trans('home_faq3_q', null, 'Can I stream on multiple devices simultaneously?') }} <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">{{ p_trans('home_faq3_a', null, 'Yes. Choose a multi-device plan to stream on 2 or 4 screens at the same time. Each device can watch something different from the full library.') }}</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>{{ p_trans('home_faq4_q', null, 'Which IPTV apps are supported?') }} <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">{{ p_trans('home_faq4_a', null, 'Works with all major IPTV players including TiviMate, IPTV Smarters, GSE Smart IPTV, VLC, Kodi, and most other M3U-compatible players.') }}</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>{{ p_trans('home_faq5_q', null, 'Is there a free trial available?') }} <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">{{ p_trans('home_faq5_a', null, 'Contact our support team to ask about trial access. We want you to be confident before committing to a plan.') }}</div>
                        </details>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <div class="section-divider"></div>

        <!-- ── RESELLER ── -->
        @if(!isset($sections['reseller']) || $sections['reseller']->is_active)
        <section id="reseller">
            <div class="wrap">
                <div class="sec-label">{{ p_trans('home_reseller_label', null, 'Reseller Program') }}</div>
                <div class="reseller-grid">
                    <!-- Features -->
                    <div>
                        <div class="sec-head" style="align-items:flex-start;text-align:left;margin-bottom:28px;">
                            <h2>{{ p_trans('home_reseller_heading', null, 'Grow a business on our infrastructure') }}</h2>
                            <p>{{ p_trans('home_reseller_desc', null, 'Buy credits, provision sub-accounts for your clients, and manage everything from one reseller dashboard. REST API access included.') }}</p>
                        </div>
                        <div class="reseller-feat-list">
                            <div class="reseller-feat">
                                <div class="reseller-feat-icon">💳</div>
                                <div class="reseller-feat-text">
                                    <strong>Credit-Based System</strong>
                                    <p>Top up your reseller wallet. Each subscription you create deducts credits — no
                                        monthly fees, no surprise invoices.</p>
                                </div>
                            </div>
                            <div class="reseller-feat">
                                <div class="reseller-feat-icon g">👥</div>
                                <div class="reseller-feat-text">
                                    <strong>Sub-User Management</strong>
                                    <p>Create, renew and suspend client accounts from your panel. Full visibility on
                                        active vs. expired lines.</p>
                                </div>
                            </div>
                            <div class="reseller-feat">
                                <div class="reseller-feat-icon">⚙️</div>
                                <div class="reseller-feat-text">
                                    <strong>API Automation</strong>
                                    <p>Use our REST API to automate provisioning, renewals and status checks. Build your
                                        own front-end on top.</p>
                                </div>
                            </div>
                            <div class="reseller-feat">
                                <div class="reseller-feat-icon g">📈</div>
                                <div class="reseller-feat-text">
                                    <strong>Bulk Pricing Tiers</strong>
                                    <p>Higher credit top-ups unlock better wholesale rates — the more you sell, the
                                        lower your cost per line.</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('reseller.register') }}" class="btn btn-primary btn-lg">Become a
                            Reseller</a>
                        <a href="{{ route('reseller.login') }}" class="btn btn-ghost btn-lg">Reseller Login</a>
                    </div>
                    <!-- Mock reseller panel -->
                    <div class="reseller-mock">
                        <div class="reseller-mock-header">
                            <span class="reseller-mock-title">Reseller Dashboard</span>
                            <span class="reseller-credit-pill">💳 Credits: 142</span>
                        </div>
                        <div class="reseller-mock-body">
                            <div class="reseller-sub-row">
                                <div>
                                    <div class="reseller-sub-name">client_john_doe</div>
                                    <div class="reseller-sub-expiry">Exp: Jul 15, 2026 · 2 screens</div>
                                </div>
                                <span class="reseller-status-active">Active</span>
                            </div>
                            <div class="reseller-sub-row">
                                <div>
                                    <div class="reseller-sub-name">client_sara_k</div>
                                    <div class="reseller-sub-expiry">Exp: Jun 30, 2026 · 1 screen</div>
                                </div>
                                <span class="reseller-status-active">Active</span>
                            </div>
                            <div class="reseller-sub-row">
                                <div>
                                    <div class="reseller-sub-name">client_omar_77</div>
                                    <div class="reseller-sub-expiry">Exp: May 01, 2026 · 4 screens</div>
                                </div>
                                <span class="reseller-status-exp">Expired</span>
                            </div>
                            <div class="reseller-sub-row">
                                <div>
                                    <div class="reseller-sub-name">client_lena_m</div>
                                    <div class="reseller-sub-expiry">Exp: Aug 12, 2026 · 2 screens</div>
                                </div>
                                <span class="reseller-status-active">Active</span>
                            </div>
                        </div>
                        <div class="reseller-mock-footer">
                            <span class="reseller-api-note">GET /api/reseller/lines</span>
                            <a href="{{ route('contact') }}" class="btn btn-ghost"
                                style="font-size:0.78rem;padding:7px 14px;">Request API Access</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ── CTA ── -->
        @if(!isset($sections['cta']) || $sections['cta']->is_active)
        <section>
            <div class="wrap">
                <div class="cta-banner" id="cta">
                    <div class="cta-inner">
                        <div class="sec-label" style="justify-content:center; margin-bottom:18px;">{{ p_trans('home_cta_label', null, 'Get Started Today') }}</div>
                        <h2>{{ p_trans('home_cta_heading', null, 'Ready to experience ' . get_setting('site_name', 'Alborada') . '?') }}</h2>
                        <p>{{ p_trans('home_cta_desc', null, 'Join thousands of viewers who switched to the most reliable IPTV service available. Instant activation. No contracts.') }}</p>
                        <div class="cta-buttons">
                            <a href="{{ route('pricing.plans') }}" class="btn btn-primary btn-lg">{{ p_trans('home_cta_btn1', null, 'View Plans') }}</a>
                            <a href="{{ route('contact') }}" class="btn btn-outline btn-lg">{{ p_trans('home_cta_btn2', null, 'Contact Support') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ── NEWSLETTER ── -->
        @if(!isset($sections['newsletter']) || $sections['newsletter']->is_active)
        <section>
            <div class="wrap">
                <div class="newsletter-section">
                    <div class="sec-label" style="justify-content:center;margin-bottom:16px;">{{ p_trans('home_newsletter_label', null, 'Stay Updated') }}</div>
                    <h2>{{ p_trans('home_newsletter_heading', null, 'Get exclusive deals & updates') }}</h2>
                    <p>{{ p_trans('home_newsletter_desc', null, 'Subscribe to our newsletter for special offers, new channel announcements, and IPTV tips delivered straight to your inbox.') }}</p>
                    <form class="newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}">
                        @csrf
                        <input type="email" name="email" class="newsletter-input" placeholder="{{ p_trans('home_newsletter_placeholder', null, 'Enter your email address') }}" required>
                        <button type="submit" class="btn btn-primary btn-lg">{{ p_trans('home_newsletter_btn', null, 'Subscribe') }}</button>
                    </form>
                    <p style="font-size:0.78rem;color:var(--muted-2);margin-top:14px;">{{ p_trans('home_newsletter_note', null, 'No spam. Unsubscribe any time.') }}</p>
                </div>
            </div>
        </section>
        @endif

    </main>

    <footer>
        <div class="wrap">
            <div class="footer-grid">
                <div>
                    <div class="logo">
                        <div class="logo-dot"></div>
                        {{ get_setting('site_name', 'Alborada') }}
                    </div>
                    <p class="footer-brand-desc">{{ p_trans('home_footer_brand_desc', null, 'Premium IPTV with secure delivery, 99.9% uptime and expert 24/7 support. Perfect for streaming enthusiasts and business customers.') }}</p>
                </div>
                <div class="footer-col">
                    <h5>Explore</h5>
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#devices">Devices</a>
                    <a href="#channels">Channels</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="footer-col">
                    <h5>Support</h5>
                    <a href="{{ route('contact') }}">Contact Us</a>
                    <a href="#setup">Setup Guide</a>
                    <a href="#reseller">Reseller Program</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="footer-col">
                    <h5>Account</h5>
                    <a href="{{ route('customer.login') }}">Login</a>
                    <a href="{{ route('customer.register') }}">Register</a>
                    <a href="{{ route('member.dashboard') }}">Dashboard</a>
                    <a href="{{ route('member.subscriptions') }}">My Subscriptions</a>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-copy">© {{ date('Y') }} <strong>{{ get_setting('site_name', 'Alborada IPTV') }}</strong>. All rights reserved.</div>
                <div class="footer-social">
                    <a href="#" class="social-btn">T</a>
                    <a href="#" class="social-btn">M</a>
                    <a href="#" class="social-btn">E</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="/web-assets/frontend/js/jquery-3.7.1.min.js"></script>
    <script src="/web-assets/frontend/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger menu
            const hamburger = document.getElementById('hamburger');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileLinks = document.querySelectorAll('.mobile-nav-link, .mobile-menu-ctas .btn');

            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('open');
                mobileMenu.classList.toggle('open');
                document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
            });

            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('open');
                    mobileMenu.classList.remove('open');
                    document.body.style.overflow = '';
                });
            });

            // Auto-scroll movie slider
            const slider = document.getElementById('movieSlider');
            if (slider) {
                let paused = false;
                let interval = setInterval(() => {
                    if (paused) return;
                    const card = slider.querySelector('.movie-card');
                    const cardW = card ? card.offsetWidth + 18 : 238;
                    if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - cardW) {
                        slider.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        slider.scrollBy({
                            left: cardW,
                            behavior: 'smooth'
                        });
                    }
                }, 3000);
                slider.addEventListener('mouseenter', () => paused = true);
                slider.addEventListener('mouseleave', () => paused = false);
                slider.addEventListener('touchstart', () => paused = true, {
                    passive: true
                });
                slider.addEventListener('touchend', () => setTimeout(() => paused = false, 2000), {
                    passive: true
                });
            }

            // Staggered fade-in on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = (i * 0.06) + 's';
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll(
                '.feat-card, .price-card, .review-card, .device-card, .why-card, .category-card').forEach(
                el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    observer.observe(el);
                });

            // Observe + animate
            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll(
                '.feat-card, .price-card, .review-card, .device-card, .why-card, .category-card').forEach(
                el => {
                    cardObserver.observe(el);
                });

            // ── PRICING TOGGLE ──
            (function() {
                var pricing = {
                    monthly: {
                        starter: {
                            price: '$11<span style="font-size:1.5rem">.99</span>',
                            period: '/ month',
                            savings: null
                        },
                        standard: {
                            price: '$19<span style="font-size:1.5rem">.99</span>',
                            period: '/ month',
                            savings: null
                        },
                        family: {
                            price: '$26<span style="font-size:1.5rem">.99</span>',
                            period: '/ month',
                            savings: null
                        },
                        premium: {
                            price: '$34<span style="font-size:1.5rem">.99</span>',
                            period: '/ month',
                            savings: null
                        }
                    },
                    quarterly: {
                        starter: {
                            price: '$30<span style="font-size:1.5rem">.57</span>',
                            period: '/ 3 months',
                            savings: 'Save 15%'
                        },
                        standard: {
                            price: '$50<span style="font-size:1.5rem">.97</span>',
                            period: '/ 3 months',
                            savings: 'Save 15%'
                        },
                        family: {
                            price: '$68<span style="font-size:1.5rem">.82</span>',
                            period: '/ 3 months',
                            savings: 'Save 15%'
                        },
                        premium: {
                            price: '$89<span style="font-size:1.5rem">.22</span>',
                            period: '/ 3 months',
                            savings: 'Save 15%'
                        }
                    },
                    biannual: {
                        starter: {
                            price: '$53<span style="font-size:1.5rem">.94</span>',
                            period: '/ 6 months',
                            savings: 'Save 25%'
                        },
                        standard: {
                            price: '$89<span style="font-size:1.5rem">.94</span>',
                            period: '/ 6 months',
                            savings: 'Save 25%'
                        },
                        family: {
                            price: '$121<span style="font-size:1.5rem">.44</span>',
                            period: '/ 6 months',
                            savings: 'Save 25%'
                        },
                        premium: {
                            price: '$157<span style="font-size:1.5rem">.44</span>',
                            period: '/ 6 months',
                            savings: 'Save 25%'
                        }
                    },
                    yearly: {
                        starter: {
                            price: '$96<span style="font-size:1.5rem">.00</span>',
                            period: '/ year',
                            savings: 'Save 33%'
                        },
                        standard: {
                            price: '$159<span style="font-size:1.5rem">.84</span>',
                            period: '/ year',
                            savings: 'Save 33%'
                        },
                        family: {
                            price: '$215<span style="font-size:1.5rem">.88</span>',
                            period: '/ year',
                            savings: 'Save 33%'
                        },
                        premium: {
                            price: '$279<span style="font-size:1.5rem">.88</span>',
                            period: '/ year',
                            savings: 'Save 33%'
                        }
                    }
                };

                function applyPeriod(period) {
                    ['starter', 'standard', 'family', 'premium'].forEach(function(plan) {
                        var card = document.querySelector('[data-plan="' + plan + '"]');
                        if (!card) return;
                        var data = pricing[period][plan];
                        var priceEl = card.querySelector('.plan-price');
                        var periodEl = card.querySelector('.plan-period');
                        var savingsEl = card.querySelector('.plan-savings');
                        if (priceEl) priceEl.innerHTML = data.price;
                        if (periodEl) periodEl.textContent = data.period;
                        if (savingsEl) {
                            if (data.savings) {
                                savingsEl.textContent = data.savings;
                                savingsEl.style.display = 'inline-flex';
                            } else {
                                savingsEl.style.display = 'none';
                            }
                        }
                    });
                }

                var toggleWrap = document.getElementById('pricingToggle');
                if (toggleWrap) {
                    var btns = toggleWrap.querySelectorAll('.ptoggle-btn');
                    btns.forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            btns.forEach(function(b) {
                                b.classList.remove('active');
                            });
                            btn.classList.add('active');
                            applyPeriod(btn.getAttribute('data-period'));
                        });
                    });
                }
            })();

        });
    </script>
</body>

</html>
