<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alborada IPTV | Premium Streaming Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #060608;
            --surface: #0d0d11;
            --surface-2: #121217;
            --surface-3: #18181f;
            --text: #f0f0f5;
            --muted: #7a7a8c;
            --muted-2: #55555f;
            --red: #e8000a;
            --red-dim: rgba(232, 0, 10, 0.12);
            --red-glow: rgba(232, 0, 10, 0.35);
            --green: #00d46a;
            --green-dim: rgba(0, 212, 106, 0.1);
            --green-glow: rgba(0, 212, 106, 0.3);
            --border: rgba(255, 255, 255, 0.07);
            --border-strong: rgba(255, 255, 255, 0.12);
            --radius: 20px;
            --radius-lg: 28px;
            --radius-xl: 36px;
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
            font-family: 'DM Sans', sans-serif;
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
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            letter-spacing: 0.02em;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            white-space: nowrap;
        }

        .btn-ghost {
            padding: 10px 18px;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border-strong);
        }

        .btn-ghost:hover {
            background: var(--surface-3);
            color: var(--text);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary {
            padding: 11px 22px;
            background: var(--red);
            color: #fff;
            box-shadow: 0 4px 20px var(--red-glow);
        }

        .btn-primary:hover {
            background: #ff0a12;
            box-shadow: 0 6px 28px var(--red-glow);
            transform: translateY(-1px);
        }

        .btn-green {
            padding: 11px 22px;
            background: var(--green);
            color: #020a05;
            box-shadow: 0 4px 20px var(--green-glow);
        }

        .btn-green:hover {
            background: #00f07a;
            box-shadow: 0 6px 28px var(--green-glow);
            transform: translateY(-1px);
        }

        .btn-outline {
            padding: 11px 22px;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border-strong);
        }

        .btn-outline:hover {
            background: var(--surface-3);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .btn-lg {
            padding: 14px 30px;
            font-size: 0.95rem;
            border-radius: 12px;
        }

        /* ─────────────────── HERO ─────────────────── */
        .hero {
            position: relative;
            min-height: 92vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 64px;
            padding: 100px 0 80px;
            overflow: hidden;
        }

        /* Mesh gradient bg */
        .hero::before {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232, 0, 10, 0.14) 0%, transparent 70%);
            top: -200px;
            right: -180px;
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 212, 106, 0.12) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            pointer-events: none;
        }

        .hero-left {
            position: relative;
            z-index: 1;
        }

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

        .hero h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(3rem, 5vw, 5rem);
            line-height: 0.96;
            letter-spacing: -0.05em;
            margin-bottom: 24px;
        }

        .hero h1 em {
            font-style: normal;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.35);
        }

        .hero h1 .accent-red {
            color: var(--red);
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--muted);
            line-height: 1.85;
            max-width: 480px;
            margin-bottom: 38px;
            font-weight: 300;
        }

        .hero-ctas {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 52px;
        }

        .hero-stats {
            display: flex;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            overflow: hidden;
            width: fit-content;
        }

        .stat-item {
            padding: 18px 28px;
            border-right: 1px solid var(--border);
            position: relative;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-num {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
            letter-spacing: -0.04em;
            line-height: 1;
            color: var(--text);
            margin-bottom: 4px;
        }

        .stat-num .unit {
            color: var(--green);
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--muted-2);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 500;
        }

        /* Hero visual panel */
        .hero-right {
            position: relative;
            z-index: 1;
        }

        .hero-visual {
            display: grid;
            gap: 14px;
        }

        .vis-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px 24px;
            transition: border-color 0.3s, transform 0.3s;
        }

        .vis-card:hover {
            border-color: var(--border-strong);
            transform: translateX(4px);
        }

        .vis-card-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .vis-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .vis-icon.red {
            background: var(--red-dim);
            color: var(--red);
        }

        .vis-icon.green {
            background: var(--green-dim);
            color: var(--green);
        }

        .vis-icon.white {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text);
        }

        .vis-card h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .vis-card p {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.7;
        }

        .vis-live-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 0 0;
            border-top: 1px solid var(--border);
            margin-top: 10px;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: rgba(232, 0, 10, 0.15);
            border: 1px solid rgba(232, 0, 10, 0.25);
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #ff3a3f;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .live-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--red);
            box-shadow: 0 0 8px var(--red);
            animation: blink 1.4s ease-in-out infinite;
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

        .uptime-bar {
            flex: 1;
            height: 4px;
            background: var(--surface-3);
            border-radius: 999px;
            overflow: hidden;
        }

        .uptime-fill {
            height: 100%;
            width: 99.9%;
            background: linear-gradient(90deg, var(--green), #00ff88);
            border-radius: 999px;
        }

        .uptime-label {
            font-size: 0.78rem;
            color: var(--green);
            font-weight: 600;
            white-space: nowrap;
        }

        /* ─────────────────── SECTIONS ─────────────────── */
        section {
            padding: 88px 0;
        }

        .sec-label {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--green);
            margin: 0 auto 18px;
        }

        .sec-label::before {
            content: '';
            display: block;
            width: 20px;
            height: 2px;
            background: var(--green);
            border-radius: 1px;
        }

        .sec-head {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 52px;
            gap: 14px;
        }

        .sec-head h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2rem, 3vw, 2.8rem);
            letter-spacing: -0.04em;
            line-height: 1.05;
            max-width: 640px;
            margin: 0 auto;
        }

        .sec-head p {
            max-width: 680px;
        }

        .cta-banner .sec-label {
            justify-content: center;
            margin-bottom: 18px;
        }

        .sec-head p {
            color: var(--muted);
            line-height: 1.8;
            font-size: 0.95rem;
            font-weight: 300;
            max-width: 560px;
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
            border-radius: 18px;
            overflow: hidden;
            background: var(--surface-2);
            border: 1px solid var(--border);
            transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .movie-card:hover {
            transform: translateY(-6px);
            border-color: var(--border-strong);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .movie-poster {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }

        .movie-meta {
            padding: 14px 16px 18px;
        }

        .movie-meta .genre-tag {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--green);
            margin-bottom: 6px;
        }

        .movie-meta h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 4px;
        }

        .movie-meta p {
            font-size: 0.8rem;
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
            border-radius: var(--radius);
            padding: 24px 16px 20px;
            text-align: center;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .ch-card:hover {
            border-color: rgba(0, 212, 106, 0.28);
            background: var(--surface-2);
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
        }

        .ch-logo {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            margin-bottom: 4px;
            position: relative;
            overflow: hidden;
        }

        .ch-logo svg {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
        }

        .ch-name {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text);
            display: block;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .ch-sub {
            font-size: 0.75rem;
            color: var(--muted-2);
            line-height: 1.4;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 2px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .feat-card {
            background: var(--surface);
            padding: 36px 28px;
            transition: background 0.3s;
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
            transition: transform 0.3s;
            transform-origin: left;
        }

        .feat-card:hover::after {
            transform: scaleX(1);
        }

        .feat-card:hover {
            background: var(--surface-2);
        }

        .feat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 22px;
        }

        .feat-icon.r {
            background: var(--red-dim);
        }

        .feat-icon.g {
            background: var(--green-dim);
        }

        .feat-icon.w {
            background: rgba(255, 255, 255, 0.05);
        }

        .feat-card h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 10px;
        }

        .feat-card p {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 300;
        }

        /* ─────────────────── PRICING ─────────────────── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            align-items: start;
        }

        .price-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 32px;
            position: relative;
            transition: border-color 0.3s, transform 0.3s;
        }

        .price-card:hover {
            border-color: var(--border-strong);
            transform: translateY(-4px);
        }

        .price-card.featured {
            background: linear-gradient(160deg, rgba(232, 0, 10, 0.1), rgba(0, 212, 106, 0.06));
            border-color: rgba(0, 212, 106, 0.3);
            transform: scale(1.04);
        }

        .price-card.featured:hover {
            transform: scale(1.04) translateY(-4px);
        }

        .plan-badge {
            display: inline-flex;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 20px;
        }

        .plan-badge.basic {
            background: rgba(255, 255, 255, 0.07);
            color: var(--muted);
        }

        .plan-badge.popular {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid rgba(0, 212, 106, 0.25);
        }

        .plan-badge.pro {
            background: var(--red-dim);
            color: var(--red);
            border: 1px solid rgba(232, 0, 10, 0.25);
        }

        .plan-name {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 4px;
        }

        .plan-price-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin: 18px 0 6px;
        }

        .plan-price {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 3rem;
            letter-spacing: -0.06em;
            color: var(--text);
            line-height: 1;
        }

        .plan-period {
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 300;
        }

        .plan-desc {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 26px;
            font-weight: 300;
        }

        .plan-divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0;
        }

        .plan-features {
            list-style: none;
            display: grid;
            gap: 11px;
            margin-bottom: 28px;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.88rem;
            color: var(--muted);
            font-weight: 300;
        }

        .plan-features li .check {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--green-dim);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ─────────────────── REVIEWS ─────────────────── */
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .review-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: border-color 0.3s;
        }

        .review-card:hover {
            border-color: var(--border-strong);
        }

        .stars {
            display: flex;
            gap: 3px;
            color: #f59e0b;
            font-size: 0.8rem;
        }

        .review-text {
            font-size: 0.92rem;
            color: var(--text);
            line-height: 1.85;
            font-weight: 300;
            flex: 1;
        }

        .review-author-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--red), var(--green));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            flex-shrink: 0;
        }

        .author-name {
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--text);
        }

        .author-loc {
            font-size: 0.78rem;
            color: var(--muted-2);
        }

        /* ─────────────────── DEVICES ─────────────────── */
        .devices-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .device-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 22px;
            text-align: center;
            transition: all 0.3s;
        }

        .device-card:hover {
            border-color: rgba(0, 212, 106, 0.25);
            background: var(--surface-2);
            transform: translateY(-4px);
        }

        .device-emoji {
            font-size: 2rem;
            margin-bottom: 16px;
            display: block;
        }

        .device-card h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .device-card p {
            font-size: 0.83rem;
            color: var(--muted);
            line-height: 1.65;
            font-weight: 300;
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
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .faq-item:has(details[open]) {
            border-color: rgba(0, 212, 106, 0.25);
        }

        .faq-item details {}

        .faq-item summary {
            padding: 20px 24px;
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text);
            letter-spacing: -0.01em;
            user-select: none;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-chevron {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1px solid var(--border-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.7rem;
            color: var(--muted);
            transition: all 0.25s;
        }

        details[open] .faq-chevron {
            background: var(--green-dim);
            border-color: var(--green);
            color: var(--green);
            transform: rotate(180deg);
        }

        .faq-body {
            padding: 0 24px 22px;
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.85;
            font-weight: 300;
        }

        /* ─────────────────── CTA BANNER ─────────────────── */
        .cta-banner {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 72px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232, 0, 10, 0.12), transparent 65%);
            top: -200px;
            left: -100px;
            pointer-events: none;
        }

        .cta-banner::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 212, 106, 0.1), transparent 65%);
            bottom: -100px;
            right: -50px;
            pointer-events: none;
        }

        .cta-inner {
            position: relative;
            z-index: 1;
        }

        .cta-banner h2 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: clamp(2rem, 3.5vw, 3rem);
            letter-spacing: -0.04em;
            margin-bottom: 14px;
        }

        .cta-banner p {
            color: var(--muted);
            max-width: 500px;
            margin: 0 auto 36px;
            line-height: 1.8;
            font-weight: 300;
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
            padding: 60px 0 40px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr repeat(2, 1fr);
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand-desc {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.8;
            margin-top: 16px;
            font-weight: 300;
            max-width: 300px;
        }

        .footer-col h5 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted-2);
            margin-bottom: 18px;
        }

        .footer-col a {
            display: block;
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 11px;
            transition: color 0.2s;
            font-weight: 300;
        }

        .footer-col a:hover {
            color: var(--text);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 32px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 14px;
        }

        .footer-copy {
            font-size: 0.82rem;
            color: var(--muted-2);
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
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid var(--border-strong);
            background: transparent;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.2s;
            cursor: pointer;
            font-family: 'Syne', sans-serif;
        }

        .social-btn:hover {
            background: var(--surface-3);
            color: var(--text);
            border-color: rgba(255, 255, 255, 0.2);
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
            .hero {
                gap: 40px;
            }

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
            .hero {
                grid-template-columns: 1fr;
                min-height: auto;
                padding: 60px 0 56px;
                gap: 48px;
            }

            .hero::before,
            .hero::after {
                display: none;
            }

            .hero h1 {
                font-size: clamp(2.8rem, 6vw, 4rem);
            }

            .hero-desc {
                max-width: 100%;
            }

            .hero-stats {
                width: 100%;
            }

            .hero-right {
                max-width: 560px;
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
                grid-template-columns: repeat(3, 1fr);
            }

            .price-card.featured {
                transform: scale(1);
            }

            .price-card.featured:hover {
                transform: translateY(-4px);
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
            .hero {
                padding: 48px 0 48px;
                gap: 40px;
            }

            .hero h1 {
                font-size: clamp(2.4rem, 9vw, 3.2rem);
            }

            .hero-ctas {
                flex-direction: column;
            }

            .hero-ctas .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .hero-stats {
                width: 100%;
                border-radius: 14px;
            }

            .stat-item {
                padding: 14px 16px;
            }

            .stat-num {
                font-size: 1.4rem;
            }

            .badge {
                font-size: 0.75rem;
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

            .hero h1 {
                font-size: clamp(2rem, 10vw, 2.8rem);
            }

            .hero-stats {
                flex-direction: column;
            }

            .stat-item {
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .stat-item:last-child {
                border-bottom: none;
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

            .vis-card {
                padding: 18px 18px;
            }

            .footer-col {}
        }
    </style>
</head>

<body>

    <header>
        <div class="wrap">
            <nav class="navbar">
                <div class="logo">
                    <div class="logo-dot"></div>
                    Albora<span class="logo-force">da</span>
                </div>
                <ul class="nav-links">
                    <li><a href="#movies">Movies</a></li>
                    <li><a href="#channels">Channels</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#devices">Devices</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
                <div class="nav-ctas">
                    <a href="#pricing" class="btn btn-ghost">View Plans</a>
                    <a href="#pricing" class="btn btn-primary">Start Free Trial</a>
                </div>
                <button class="hamburger" id="hamburger" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
    </header>

    <div class="mobile-menu" id="mobileMenu">
        <a href="#movies" class="mobile-nav-link">Movies</a>
        <a href="#channels" class="mobile-nav-link">Channels</a>
        <a href="#features" class="mobile-nav-link">Features</a>
        <a href="#pricing" class="mobile-nav-link">Pricing</a>
        <a href="#devices" class="mobile-nav-link">Devices</a>
        <a href="#faq" class="mobile-nav-link">FAQ</a>
        <div class="mobile-menu-ctas">
            <a href="#pricing" class="btn btn-outline btn-lg" style="width:100%;justify-content:center;">View Plans</a>
            <a href="#pricing" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">Start Free
                Trial</a>
        </div>
    </div>

    <main>
        <div class="wrap">

            <!-- ── HERO ── -->
            <section class="hero">
                <div class="hero-left">
                    <div class="badge">
                        <div class="badge-dot"></div>
                        <span>Live Now</span> — 40,000+ channels streaming
                    </div>

                    <h1>
                        Premium<br>
                        <span class="accent-red">IPTV</span> for<br>
                        <em>every screen.</em>
                    </h1>

                    <p class="hero-desc">
                        Alborada delivers a professional streaming experience — high uptime, 150K+ on-demand titles,
                        and instant activation across all your devices.
                    </p>

                    <div class="hero-ctas">
                        <a href="#pricing" class="btn btn-primary btn-lg">Choose a Plan</a>
                        <a href="#features" class="btn btn-outline btn-lg">Explore Features</a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-num">40<span class="unit">K+</span></div>
                            <div class="stat-label">Live Channels</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num">150<span class="unit">K+</span></div>
                            <div class="stat-label">VOD Titles</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num">99.9<span class="unit">%</span></div>
                            <div class="stat-label">Uptime</div>
                        </div>
                    </div>
                </div>

                <div class="hero-right">
                    <div class="hero-visual">
                        <div class="vis-card">
                            <div class="vis-card-top">
                                <div class="vis-icon red">⚡</div>
                                <h4>Instant Activation</h4>
                            </div>
                            <p>Get your credentials within minutes. No waiting, no complicated setup.</p>
                            <div class="vis-live-row">
                                <div class="live-pill">Live</div>
                                <div class="uptime-bar">
                                    <div class="uptime-fill"></div>
                                </div>
                                <span class="uptime-label">99.9% uptime</span>
                            </div>
                        </div>

                        <div class="vis-card">
                            <div class="vis-card-top">
                                <div class="vis-icon green">🌐</div>
                                <h4>Global Compatibility</h4>
                            </div>
                            <p>Android, iOS, Smart TVs, Firestick, Kodi, TiviMate and all major IPTV apps.</p>
                        </div>

                        <div class="vis-card">
                            <div class="vis-card-top">
                                <div class="vis-icon white">🛡️</div>
                                <h4>Secure & Private</h4>
                            </div>
                            <p>No external logging. Privacy-first stream delivery with 24/7 monitoring.</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>

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

        <!-- ── FEATURES ── -->
        <section id="features">
            <div class="wrap">
                <div class="sec-label">Why Alborada</div>
                <div class="sec-head">
                    <h2>Everything your streaming setup needs</h2>
                    <p>Professional delivery, robust infrastructure, and dedicated support — built for viewers who don't
                        compromise.</p>
                </div>
                <div class="features-grid">
                    <div class="feat-card">
                        <div class="feat-icon r">📡</div>
                        <h3>Reliable Delivery</h3>
                        <p>Resilient redundant servers with advanced buffering for near-zero interruption on every
                            stream.</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon g">💰</div>
                        <h3>Flexible Pricing</h3>
                        <p>Transparent packages for single users, families and resellers — no hidden fees, ever.</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon w">🔒</div>
                        <h3>Security First</h3>
                        <p>Privacy-focused delivery with no external logging, data sharing, or third-party tracking.</p>
                    </div>
                    <div class="feat-card">
                        <div class="feat-icon r">🎧</div>
                        <h3>Expert Support</h3>
                        <p>24/7 help from IPTV specialists — fast response, guided setup, and ongoing assistance.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── PRICING ── -->
        <section id="pricing">
            <div class="wrap">
                <div class="sec-label">Pricing</div>
                <div class="sec-head centered">
                    <h2>Plans for every household</h2>
                    <p>Choose the package that matches your needs. Instant activation. Cancel any time.</p>
                </div>

                <div class="pricing-grid">
                    <!-- Basic -->
                    <div class="price-card">
                        <div class="plan-badge basic">Basic</div>
                        <div class="plan-name">1 Device</div>
                        <div class="plan-price-row">
                            <div class="plan-price">$11<span style="font-size:1.6rem">.99</span></div>
                            <div class="plan-period">/ month</div>
                        </div>
                        <div class="plan-desc">Perfect for solo viewers who want the full library.</div>
                        <div class="plan-divider"></div>
                        <ul class="plan-features">
                            <li><span class="check">✓</span> 40K+ live channels</li>
                            <li><span class="check">✓</span> 150K+ movies & series</li>
                            <li><span class="check">✓</span> HD & 4K quality</li>
                            <li><span class="check">✓</span> Standard support</li>
                        </ul>
                        <a href="#" class="btn btn-outline"
                            style="width:100%; border-radius:12px; padding:13px;">Order Now</a>
                    </div>

                    <!-- Popular -->
                    <div class="price-card featured">
                        <div class="plan-badge popular">★ Most Popular</div>
                        <div class="plan-name">2 Devices</div>
                        <div class="plan-price-row">
                            <div class="plan-price">$19<span style="font-size:1.6rem">.99</span></div>
                            <div class="plan-period">/ month</div>
                        </div>
                        <div class="plan-desc">Ideal for couples or small households with multiple screens.</div>
                        <div class="plan-divider"></div>
                        <ul class="plan-features">
                            <li><span class="check">✓</span> All Basic features</li>
                            <li><span class="check">✓</span> Dual-screen streaming</li>
                            <li><span class="check">✓</span> HD & 4K quality</li>
                            <li><span class="check">✓</span> Priority support</li>
                        </ul>
                        <a href="#" class="btn btn-green"
                            style="width:100%; border-radius:12px; padding:13px;">Choose Plan</a>
                    </div>

                    <!-- Pro -->
                    <div class="price-card">
                        <div class="plan-badge pro">Pro</div>
                        <div class="plan-name">4 Devices</div>
                        <div class="plan-price-row">
                            <div class="plan-price">$29<span style="font-size:1.6rem">.99</span></div>
                            <div class="plan-period">/ month</div>
                        </div>
                        <div class="plan-desc">Full household and business-ready multi-screen streaming.</div>
                        <div class="plan-divider"></div>
                        <ul class="plan-features">
                            <li><span class="check">✓</span> 4 concurrent streams</li>
                            <li><span class="check">✓</span> Advanced packages</li>
                            <li><span class="check">✓</span> HD & 4K quality</li>
                            <li><span class="check">✓</span> Business-grade service</li>
                        </ul>
                        <a href="#" class="btn btn-primary"
                            style="width:100%; border-radius:12px; padding:13px;">Get Started</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── REVIEWS ── -->
        <section id="reviews">
            <div class="wrap">
                <div class="sec-label">Customer Reviews</div>
                <div class="sec-head">
                    <h2>Trusted by thousands worldwide</h2>
                    <p>See why customers across Europe, North America, and the Middle East rely on Alborada daily.
                    </p>
                </div>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"Alborada completely transformed my streaming setup. The channel
                            selection is outstanding and the service never drops — even during peak hours."</p>
                        <div class="review-author-row">
                            <div class="avatar">AM</div>
                            <div>
                                <div class="author-name">Alex M.</div>
                                <div class="author-loc">London, UK</div>
                            </div>
                        </div>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"The movie library is incredible and setup was instant. Support
                            responded in minutes. Nothing else compares at this price point."</p>
                        <div class="review-author-row">
                            <div class="avatar">JK</div>
                            <div>
                                <div class="author-name">Jessica K.</div>
                                <div class="author-loc">Toronto, CA</div>
                            </div>
                        </div>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"Best IPTV I've used. Works perfectly on Firestick, my smart TV, and
                            mobile with zero buffering. Highly recommended to everyone."</p>
                        <div class="review-author-row">
                            <div class="avatar">OS</div>
                            <div>
                                <div class="author-name">Omar S.</div>
                                <div class="author-loc">Dubai, AE</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── DEVICES ── -->
        <section id="devices">
            <div class="wrap">
                <div class="sec-label">Compatibility</div>
                <div class="sec-head">
                    <h2>Works on any device</h2>
                    <p>Alborada runs on every major platform — from 65-inch smart TVs to pocket-sized phones.</p>
                </div>
                <div class="devices-grid">
                    <div class="device-card">
                        <span class="device-emoji">📺</span>
                        <h4>Smart TVs</h4>
                        <p>Samsung, LG, Sony, Android TV and all major brands.</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">🔥</span>
                        <h4>Firestick</h4>
                        <p>Fast setup and smooth playback on Amazon Fire devices.</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">📱</span>
                        <h4>Mobile</h4>
                        <p>Android and iOS — watch anywhere, any time.</p>
                    </div>
                    <div class="device-card">
                        <span class="device-emoji">💻</span>
                        <h4>Desktop</h4>
                        <p>VLC, Kodi, TiviMate and all major IPTV players.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider"></div>

        <!-- ── CHANNELS ── -->
        <section id="channels">
            <div class="wrap">
                <div class="sec-label">Channel Lineup</div>
                <div class="sec-head">
                    <h2>Premium channels, curated</h2>
                    <p>Scroll through our top channel categories — 40,000+ live feeds available instantly.</p>
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

        <div class="section-divider"></div>

        <!-- ── FAQ ── -->
        <section id="faq">
            <div class="wrap">
                <div class="sec-label">FAQ</div>
                <div class="sec-head centered">
                    <h2>Common questions answered</h2>
                    <p>Everything you need to know before getting started with Alborada.</p>
                </div>

                <div class="faq-list">
                    <div class="faq-item">
                        <details open>
                            <summary>What internet speed do I need? <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">For HD streaming we recommend at least 10 Mbps. For 4K quality or
                                multiple simultaneous devices, 30+ Mbps is ideal for a smooth experience.</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>How fast is account activation? <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">Most accounts are activated instantly after payment. Your credentials
                                are delivered by email within minutes — sometimes seconds.</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>Can I stream on multiple devices simultaneously? <span
                                    class="faq-chevron">▾</span></summary>
                            <div class="faq-body">Yes. Choose a multi-device plan to stream on 2 or 4 screens at the
                                same time. Each device can watch something different from the full library.</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>Which IPTV apps are supported? <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">Alborada works with all major IPTV players including TiviMate,
                                IPTV Smarters, GSE Smart IPTV, VLC, Kodi, and most other M3U-compatible players.</div>
                        </details>
                    </div>
                    <div class="faq-item">
                        <details>
                            <summary>Is there a free trial available? <span class="faq-chevron">▾</span></summary>
                            <div class="faq-body">Contact our support team to ask about trial access. We want you to be
                                confident before committing to a plan.</div>
                        </details>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA ── -->
        <section>
            <div class="wrap">
                <div class="cta-banner" id="cta">
                    <div class="cta-inner">
                        <div class="sec-label" style="justify-content:center; margin-bottom:18px;">Get Started Today
                        </div>
                        <h2>Ready to experience Alborada?</h2>
                        <p>Join thousands of viewers who switched to the most reliable IPTV service available. Instant
                            activation. No contracts.</p>
                        <div class="cta-buttons">
                            <a href="#pricing" class="btn btn-primary btn-lg">View Plans</a>
                            <a href="#faq" class="btn btn-outline btn-lg">Read FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer>
        <div class="wrap">
            <div class="footer-grid">
                <div>
                    <div class="logo">
                        <div class="logo-dot"></div>
                        Albora<span class="logo-force">da</span>
                    </div>
                    <p class="footer-brand-desc">Premium IPTV with secure delivery, 99.9% uptime and expert 24/7
                        support. Perfect for streaming enthusiasts and business customers.</p>
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
                    <a href="#">Contact Us</a>
                    <a href="#">Setup Guide</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-copy">© 2026 <strong>Alborada IPTV</strong>. All rights reserved.</div>
                <div class="footer-social">
                    <a href="#" class="social-btn">T</a>
                    <a href="#" class="social-btn">M</a>
                    <a href="#" class="social-btn">E</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

            document.querySelectorAll('.feat-card, .price-card, .review-card, .device-card, .vis-card').forEach(
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
                        entry.target.style.transform = entry.target.classList.contains(
                                'price-card') && entry.target.classList.contains('featured') ?
                            'scale(1.04)' : 'translateY(0)';
                        cardObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.feat-card, .price-card, .review-card, .device-card, .vis-card').forEach(
                el => {
                    cardObserver.observe(el);
                });
        });
    </script>
</body>

</html>
