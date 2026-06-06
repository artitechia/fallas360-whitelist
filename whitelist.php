<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/bootstrap.php';
require_once __DIR__ . '/core/solicitudes_repository.php';

function whitelist_h(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$count = 0;
try {
    $count = solicitudes_count_active();
} catch (Throwable $exception) {
    error_log('Whitelist count failed: ' . $exception->getMessage());
}

$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lista de acceso anticipado | Falles360</title>
<meta name="description" content="Apuntate al acceso anticipado de Falles360 y entra antes que nadie al mapa, agenda, rutas y Pasaporte Fallero de Fallas 2027.">
<style>
  :root {
    --orange: #f05a28;
    --orange-dark: #c03e15;
    --dark: #1a110a;
    --cream: #f7f4f1;
    --yellow: #ffd32a;
    --white: #ffffff;
    --muted: rgba(26, 18, 8, 0.56);
    --muted-dark: rgba(255, 255, 255, 0.62);
    --line: rgba(232, 70, 10, 0.12);
    --shadow: 0 20px 60px rgba(232, 70, 10, 0.24);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }

  body {
    background: var(--white);
    color: var(--dark);
    font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    overflow-x: hidden;
  }

  a { color: inherit; }

  nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    padding: 16px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(232, 70, 10, 0.1);
  }

  .logo {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 22px;
    letter-spacing: 0.04em;
    text-decoration: none;
    color: var(--dark);
  }

  .logo span { color: var(--orange); }

  .nav-back {
    font-size: 13px;
    font-weight: 700;
    color: rgba(26, 18, 8, 0.4);
    text-decoration: none;
    transition: color .2s;
    white-space: nowrap;
  }

  .nav-back:hover { color: var(--orange); }

  .hero {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 120px 24px 80px;
    position: relative;
    overflow: hidden;
  }

  .hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
  }

  .hero-content {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 760px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(232, 70, 10, 0.1);
    border: 1px solid rgba(232, 70, 10, 0.3);
    border-radius: 100px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 24px;
    animation: fadeUp .6s ease both;
  }

  .pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--orange);
    animation: pulse 1.5s ease-in-out infinite;
  }

  h1 {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: clamp(48px, 8vw, 86px);
    line-height: .95;
    text-transform: uppercase;
    text-align: center;
    max-width: 760px;
    margin-bottom: 18px;
    animation: fadeUp .7s ease .1s both;
  }

  h1 em { color: var(--orange); font-style: normal; }

  .subtitle {
    font-size: 16px;
    color: var(--muted);
    text-align: center;
    max-width: 480px;
    line-height: 1.65;
    margin-bottom: 40px;
    animation: fadeUp .7s ease .2s both;
  }

  .subtitle strong { color: var(--dark); }

  .counter-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 36px;
    animation: fadeUp .7s ease .25s both;
  }

  .avatars { display: flex; }

  .avatars span {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid var(--white);
    margin-left: -9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.05em;
  }

  .avatars span:first-child { margin-left: 0; background: #fde8e0; }
  .avatars span:nth-child(2) { background: #fcd5c6; }
  .avatars span:nth-child(3) { background: #fef3cd; }

  .counter-text {
    font-size: 13px;
    color: rgba(26, 18, 8, 0.5);
  }

  .counter-text strong { color: var(--orange); }

  .form-card {
    background: var(--white);
    border: 2px solid rgba(240, 90, 40, 0.22);
    border-radius: 20px;
    padding: 36px 40px;
    width: 100%;
    max-width: 480px;
    box-shadow: var(--shadow);
    animation: fadeUp .7s ease .3s both;
  }

  .form-card label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(26, 17, 10, 0.72);
    margin-bottom: 8px;
  }

  .form-group { margin-bottom: 16px; }

  .form-group input {
    width: 100%;
    background: #fff8f4;
    border: 1.5px solid rgba(240, 90, 40, 0.24);
    border-radius: 10px;
    padding: 14px 18px;
    font-size: 15px;
    font-family: inherit;
    color: var(--dark);
    outline: none;
    transition: border-color .2s, background .2s;
  }

  .form-group input::placeholder { color: rgba(26, 17, 10, 0.38); }

  .form-group input:focus {
    border-color: var(--orange);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(240, 90, 40, 0.08);
  }

  .btn-submit {
    width: 100%;
    margin-top: 8px;
    padding: 16px;
    background: var(--white);
    color: var(--orange);
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 18px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .btn-submit:hover {
    background: var(--cream);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
  }

  .btn-submit:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
  }

  .form-note {
    text-align: center;
    font-size: 12px;
    color: rgba(26, 17, 10, 0.52);
    margin-top: 14px;
    line-height: 1.5;
  }

  .form-note a {
    color: var(--orange-dark);
    text-decoration: underline;
    text-underline-offset: 2px;
  }

  .form-status {
    display: none;
    margin-top: 14px;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.45;
  }

  .form-status--error {
    display: block;
    background: #fff1ea;
    color: var(--orange-dark);
  }

  .form-status--ok {
    display: block;
    background: #eef9f3;
    color: #1f7a4f;
  }

  .success-state {
    display: none;
    text-align: center;
    padding: 10px 0;
    animation: fadeUp .5s ease both;
  }

  .success-icon {
    width: 64px;
    height: 64px;
    background: #fff1ea;
    border: 2px solid rgba(240, 90, 40, 0.18);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 900;
    color: var(--orange);
    margin: 0 auto 18px;
  }

  .success-state h2 {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 36px;
    text-transform: uppercase;
    color: var(--dark);
    margin-bottom: 10px;
    line-height: 1;
  }

  .success-state h2 em { color: var(--orange); font-style: normal; }

  .success-state p {
    color: rgba(26, 17, 10, 0.62);
    font-size: 14px;
    line-height: 1.6;
  }

  .perks {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    max-width: 480px;
    width: 100%;
    margin-top: 24px;
    animation: fadeUp .7s ease .4s both;
  }

  .perk {
    background: var(--cream);
    border: 1.5px solid rgba(232, 70, 10, 0.12);
    border-radius: 12px;
    padding: 16px 12px;
    text-align: center;
  }

  .perk-icon {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-width: 46px;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--white);
    color: var(--orange);
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
  }

  .perk-title {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 13px;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 4px;
  }

  .perk-desc {
    font-size: 11px;
    color: rgba(26, 18, 8, 0.45);
    line-height: 1.4;
  }

  .countdown-wrap {
    margin-top: 40px;
    text-align: center;
    animation: fadeUp .7s ease .5s both;
  }

  .countdown-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: rgba(26, 18, 8, 0.3);
    margin-bottom: 14px;
  }

  .countdown {
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .countdown-item {
    background: var(--cream);
    border: 1.5px solid rgba(232, 70, 10, 0.15);
    border-radius: 10px;
    padding: 12px 18px;
    min-width: 64px;
    text-align: center;
  }

  .countdown-num {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 32px;
    color: var(--orange);
    line-height: 1;
  }

  .countdown-unit {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(26, 18, 8, 0.35);
    margin-top: 4px;
  }

  .section-stats {
    background: var(--orange);
    padding: 48px 24px;
  }

  .stats-inner {
    max-width: 960px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    text-align: center;
  }

  .stat-num {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 56px;
    color: var(--white);
    line-height: 1;
  }

  .stat-label {
    margin-top: 4px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.68);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  .section-preview {
    background: var(--dark);
    padding: 80px 24px;
    position: relative;
    overflow: hidden;
  }

  .section-preview-inner {
    max-width: 960px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
  }

  .section-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 14px;
  }

  .section-title {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: clamp(36px, 5vw, 54px);
    line-height: .95;
    text-transform: uppercase;
    color: var(--white);
    margin-bottom: 18px;
  }

  .section-title em { color: var(--orange); font-style: normal; }

  .section-body {
    font-size: 15px;
    color: var(--muted-dark);
    line-height: 1.7;
    margin-bottom: 28px;
  }

  .feature-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .feature-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
  }

  .feature-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(232, 70, 10, 0.15);
    border: 1px solid rgba(232, 70, 10, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--white);
    font-size: 11px;
    font-weight: 900;
  }

  .feature-text-title {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 16px;
    text-transform: uppercase;
    color: var(--white);
    margin-bottom: 3px;
  }

  .feature-text-desc {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.48);
    line-height: 1.5;
  }

  .phone-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
  }

  .phone-outer {
    width: 240px;
    border-radius: 36px;
    background: #111111;
    padding: 12px;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08);
    position: relative;
  }

  .phone-screen {
    border-radius: 26px;
    overflow: hidden;
    background: #1a1a1a;
    aspect-ratio: 9 / 19.5;
  }

  .phone-notch {
    position: absolute;
    top: 12px;
    left: 50%;
    transform: translateX(-50%);
    width: 70px;
    height: 22px;
    background: #111111;
    border-radius: 0 0 14px 14px;
    z-index: 10;
  }

  .app-ui {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: var(--white);
  }

  .app-header {
    background: var(--dark);
    padding: 36px 14px 14px;
  }

  .app-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .app-logo-sm {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 14px;
    color: var(--white);
  }

  .app-logo-sm span { color: var(--orange); }

  .app-map-placeholder {
    background: #2a2015;
    border-radius: 10px;
    height: 110px;
    position: relative;
    overflow: hidden;
  }

  .map-dot {
    position: absolute;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
  }

  .app-bottom {
    flex: 1;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .app-card {
    background: #f9f5f0;
    border-radius: 8px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .app-card-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: var(--orange);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 900;
    color: var(--white);
    flex-shrink: 0;
  }

  .app-card-title {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 11px;
    color: var(--dark);
  }

  .app-card-sub {
    font-size: 9px;
    color: rgba(26, 18, 8, 0.45);
  }

  .app-nav {
    display: flex;
    justify-content: space-around;
    padding: 8px 6px;
    border-top: 1px solid #eeeeee;
    background: var(--white);
  }

  .app-nav-item {
    font-size: 8px;
    color: rgba(26, 18, 8, 0.35);
    text-align: center;
    font-weight: 700;
  }

  .app-nav-item.active { color: var(--orange); }

  .app-nav-dot {
    width: 18px;
    height: 3px;
    border-radius: 2px;
    background: currentColor;
    margin: 0 auto 2px;
  }

  .phone-badge {
    position: absolute;
    background: var(--white);
    border-radius: 12px;
    padding: 8px 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 12px;
    color: var(--dark);
    white-space: nowrap;
  }

  .phone-badge span { color: var(--orange); }
  .badge-left { left: -90px; top: 30%; }
  .badge-right { right: -90px; top: 55%; }

  .section-how {
    background: var(--cream);
    padding: 80px 24px;
  }

  .section-how-inner {
    max-width: 960px;
    margin: 0 auto;
  }

  .section-how h2,
  .section-cta h2 {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    line-height: .95;
    text-transform: uppercase;
  }

  .section-how h2 {
    font-size: clamp(36px, 5vw, 52px);
    color: var(--dark);
    margin-bottom: 10px;
  }

  .section-how h2 em,
  .section-cta h2 em { color: var(--orange); font-style: normal; }

  .section-how-sub {
    font-size: 15px;
    color: rgba(26, 18, 8, 0.5);
    margin-bottom: 48px;
    max-width: 460px;
    line-height: 1.6;
  }

  .steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  .step {
    position: relative;
  }

  .step-num {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 64px;
    color: rgba(232, 70, 10, 0.12);
    line-height: 1;
    margin-bottom: -10px;
  }

  .step-icon {
    color: var(--orange);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 12px;
  }

  .step-title {
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 20px;
    text-transform: uppercase;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .step-desc {
    font-size: 13px;
    color: rgba(26, 18, 8, 0.5);
    line-height: 1.6;
  }

  .step-connector {
    position: absolute;
    top: 32px;
    right: -12px;
    font-size: 20px;
    color: rgba(232, 70, 10, 0.3);
  }

  .section-cta {
    background: var(--dark);
    padding: 100px 24px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .section-cta h2 {
    position: relative;
    z-index: 1;
    font-size: clamp(48px, 7vw, 80px);
    color: var(--white);
    margin-bottom: 16px;
  }

  .section-cta p {
    position: relative;
    z-index: 1;
    font-size: 16px;
    color: rgba(255, 255, 255, 0.54);
    margin-bottom: 40px;
  }

  .cta-btn {
    display: inline-block;
    position: relative;
    z-index: 1;
    background: var(--orange);
    color: var(--white);
    font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
    font-weight: 900;
    font-size: 20px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 18px 48px;
    border-radius: 12px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 8px 30px rgba(232, 70, 10, 0.35);
  }

  .cta-btn:hover {
    background: var(--orange-dark);
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(232, 70, 10, 0.45);
  }

  .cta-note {
    position: relative;
    z-index: 1;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.3);
    margin-top: 16px;
  }

  footer {
    background: var(--dark);
    text-align: center;
    padding: 24px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.22);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
  }

  footer a {
    color: rgba(255, 255, 255, 0.3);
    text-decoration: none;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .5; transform: scale(.85); }
  }

  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
  }

  @media (max-width: 960px) {
    .section-preview-inner {
      grid-template-columns: 1fr;
      gap: 40px;
    }

    .phone-wrap { order: -1; }
    .badge-left, .badge-right { display: none; }
    .stats-inner { grid-template-columns: 1fr; gap: 32px; }
  }

  @media (max-width: 768px) {
    nav { padding: 14px 20px; }
    .hero { padding: 108px 20px 64px; }
    .steps { grid-template-columns: 1fr; gap: 32px; }
    .step-connector { display: none; }
    .section-preview,
    .section-how,
    .section-cta { padding-left: 20px; padding-right: 20px; }
    .section-cta { padding-top: 84px; padding-bottom: 84px; }
  }

  @media (max-width: 600px) {
    .nav-back { font-size: 12px; }
    h1 { font-size: clamp(40px, 16vw, 62px); }
    .subtitle { font-size: 15px; }
    .counter-wrap {
      flex-direction: column;
      text-align: center;
      gap: 10px;
    }
    .form-card { padding: 28px 20px; }
    .perks {
      grid-template-columns: 1fr;
      gap: 10px;
    }
    .countdown {
      gap: 8px;
      width: 100%;
    }
    .countdown-item {
      min-width: 0;
      flex: 1;
      padding: 10px 8px;
    }
    .countdown-num { font-size: 26px; }
    .stat-num { font-size: 44px; }
    .cta-btn {
      width: 100%;
      max-width: 340px;
      padding-left: 24px;
      padding-right: 24px;
    }
    .phone-outer { width: min(240px, 74vw); }
  }
</style>
</head>
<body>

<nav>
  <a href="./dist/index.html" class="logo">FALLES<span>360</span></a>
  <a href="./dist/index.html" class="nav-back">&larr; Volver a la app</a>
</nav>

<section class="hero">
  <div class="hero-bg" aria-hidden="true">
    <svg width="100%" height="100%" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
      <g transform="translate(1200,60) scale(2.2)">
        <path d="M40,160 C18,130 0,100 12,62 C18,42 30,30 24,8 C42,28 36,52 52,38 C46,64 64,72 58,98 C72,76 76,50 64,22 C88,42 92,76 78,108 C92,88 98,62 88,36 C110,60 108,100 90,126 Z" fill="#E8460A" opacity="0.06"/>
      </g>
      <g transform="translate(-40,380) scale(1.5)">
        <path d="M30,120 C14,100 0,78 8,52 C12,36 20,26 16,10 C28,22 24,38 36,28 C32,46 46,52 42,70 C52,54 56,34 46,14 C64,30 66,56 56,76 C66,62 70,42 62,20 C78,40 76,68 62,88 Z" fill="#E8460A" opacity="0.05"/>
      </g>
      <radialGradient id="hero-glow" cx="50%" cy="60%">
        <stop offset="0%" stop-color="#E8460A" stop-opacity="0.05"/>
        <stop offset="100%" stop-color="#E8460A" stop-opacity="0"/>
      </radialGradient>
      <circle cx="720" cy="540" r="480" fill="url(#hero-glow)"/>
    </svg>
  </div>

  <div class="hero-content">
    <div class="pill"><span class="pill-dot"></span>Acceso anticipado | Fallas 2027</div>

    <h1>Se el primero<br>en entrar a<br><em>Falles360.</em></h1>

    <p class="subtitle">
      Apuntate ahora y consigue <strong>acceso prioritario</strong> antes del lanzamiento.
      Mapa, agenda, rutas y Pasaporte Fallero listos para marzo.
    </p>

    <div class="counter-wrap">
      <div class="avatars" aria-hidden="true">
        <span>MAP</span><span>VIP</span><span>GO</span>
      </div>
      <p class="counter-text"><strong id="counter-num"><?php echo whitelist_h($count); ?></strong> personas ya en la lista</p>
    </div>

    <div class="form-card" id="form-card">
      <form id="waitlist-form" novalidate>
        <div id="form-inner">
          <div class="form-group">
            <label for="input-name">Tu nombre</label>
            <input type="text" id="input-name" name="name" placeholder="Maria Garcia" autocomplete="given-name">
          </div>
          <div class="form-group">
            <label for="input-email">Tu email</label>
            <input type="email" id="input-email" name="email" placeholder="maria@email.com" autocomplete="email">
          </div>
          <button class="btn-submit" id="btn-submit" type="submit">Apuntarme a la lista</button>
          <p class="form-note">
            Sin spam. Sin tarjeta. Solo te avisamos cuando abra.
            <a href="./dist/privacy.html">Privacidad</a>
          </p>
          <div class="form-status" id="form-status" aria-live="polite"></div>
        </div>

        <div class="success-state" id="success-state">
          <div class="success-icon">OK</div>
          <h2>Ya estas<br><em>en la lista.</em></h2>
          <p>Te avisaremos antes que nadie. Mientras tanto, ya tienes tu acceso reservado.</p>
        </div>
      </form>
    </div>

    <div class="perks">
      <div class="perk">
        <div class="perk-icon">EARLY</div>
        <div class="perk-title">Acceso<br>previo</div>
        <div class="perk-desc">Antes del lanzamiento oficial</div>
      </div>
      <div class="perk">
        <div class="perk-icon">FOUND</div>
        <div class="perk-title">Badge<br>fundador</div>
        <div class="perk-desc">Insignia exclusiva en tu perfil</div>
      </div>
      <div class="perk">
        <div class="perk-icon">ALERT</div>
        <div class="perk-title">Aviso<br>directo</div>
        <div class="perk-desc">El primero en cada novedad</div>
      </div>
    </div>

    <div class="countdown-wrap">
      <p class="countdown-label">Quedan para Fallas 2027</p>
      <div class="countdown">
        <div class="countdown-item">
          <div class="countdown-num" id="cd-days">0</div>
          <div class="countdown-unit">Dias</div>
        </div>
        <div class="countdown-item">
          <div class="countdown-num" id="cd-hours">0</div>
          <div class="countdown-unit">Horas</div>
        </div>
        <div class="countdown-item">
          <div class="countdown-num" id="cd-mins">0</div>
          <div class="countdown-unit">Min</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-stats">
  <div class="stats-inner">
    <div>
      <div class="stat-num">380+</div>
      <div class="stat-label">Monumentos mapeados</div>
    </div>
    <div>
      <div class="stat-num">19 MAR</div>
      <div class="stat-label">Dia de la Crema 2027</div>
    </div>
    <div>
      <div class="stat-num">100%</div>
      <div class="stat-label">Gratis para empezar</div>
    </div>
  </div>
</section>

<section class="section-preview">
  <div class="section-preview-inner">
    <div>
      <p class="section-label">Lo que te espera</p>
      <h2 class="section-title">La app que<br>necesitas para<br><em>marzo.</em></h2>
      <p class="section-body">Todo lo que necesitas para vivir las Fallas con criterio. Sin improvisar. Sin perderte nada.</p>

      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon">MAP</div>
          <div>
            <div class="feature-text-title">Mapa de calor interactivo</div>
            <div class="feature-text-desc">Ve donde esta la gente, las fallas mas visitadas y los barrios mas activos en tiempo real.</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">PASS</div>
          <div>
            <div class="feature-text-title">Pasaporte Fallero</div>
            <div class="feature-text-desc">Escanea monumentos, acumula insignias y convierte el recorrido en un reto memorable.</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">LIVE</div>
          <div>
            <div class="feature-text-title">Agenda en vivo</div>
            <div class="feature-text-desc">Mascletas, castillos, ofrendas y actos sincronizados para que no llegues tarde.</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon">SHOP</div>
          <div>
            <div class="feature-text-title">Marketplace fallero</div>
            <div class="feature-text-desc">Restaurantes, cupones y experiencias cerca de tu ruta o de cada falla.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="phone-wrap">
      <div class="phone-badge badge-left">HOT <span>386</span> fallas cerca</div>
      <div class="phone-outer">
        <div class="phone-notch"></div>
        <div class="phone-screen">
          <div class="app-ui">
            <div class="app-header">
              <div class="app-header-top">
                <span class="app-logo-sm">FALLES<span>360</span></span>
                <span style="font-size:10px;color:rgba(255,255,255,0.5);font-weight:700;">Valencia</span>
              </div>
              <div class="app-map-placeholder">
                <div class="map-dot" style="width:28px;height:28px;background:#E8460A;top:40%;left:35%;opacity:.92;color:#fff;font-size:10px;">25</div>
                <div class="map-dot" style="width:22px;height:22px;background:#f5b800;top:25%;left:60%;opacity:.86;color:#1A1208;font-size:9px;">91</div>
                <div class="map-dot" style="width:32px;height:32px;background:#E8460A;top:15%;left:20%;opacity:.74;color:#fff;font-size:10px;">386</div>
                <div class="map-dot" style="width:14px;height:14px;background:#E8460A;top:65%;left:70%;opacity:.6;"></div>
                <div class="map-dot" style="width:10px;height:10px;background:#f5b800;top:70%;left:45%;opacity:.54;"></div>
                <div style="position:absolute;bottom:6px;left:8px;background:rgba(26,18,8,0.82);border-radius:6px;padding:3px 7px;font-size:8px;color:#fff;font-weight:700;">Mapa de calor activo</div>
              </div>
            </div>
            <div class="app-bottom">
              <div style="font-family:Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;font-weight:900;font-size:11px;color:#1A1208;margin-bottom:2px;text-transform:uppercase;">Tu base fallera</div>
              <div class="app-card">
                <div class="app-card-icon">LIVE</div>
                <div>
                  <div class="app-card-title">Agenda | Marzo</div>
                  <div class="app-card-sub">12 actos hoy | 3 cerca de ti</div>
                </div>
              </div>
              <div class="app-card">
                <div class="app-card-icon" style="background:#f5b800;color:#1A1208;">PASS</div>
                <div>
                  <div class="app-card-title">Pasaporte Fallero</div>
                  <div class="app-card-sub">4/10 insignias desbloqueadas</div>
                </div>
              </div>
              <div class="app-card">
                <div class="app-card-icon" style="background:#1A1208;">SHOP</div>
                <div>
                  <div class="app-card-title">Marketplace</div>
                  <div class="app-card-sub">Cupon: -15% Restaurante El Carmen</div>
                </div>
              </div>
            </div>
            <div class="app-nav">
              <div class="app-nav-item active"><div class="app-nav-dot"></div>Mapa</div>
              <div class="app-nav-item"><div class="app-nav-dot"></div>Agenda</div>
              <div class="app-nav-item"><div class="app-nav-dot"></div>Retos</div>
              <div class="app-nav-item"><div class="app-nav-dot"></div>Mas</div>
            </div>
          </div>
        </div>
      </div>
      <div class="phone-badge badge-right">PASS Badge desbloqueada</div>
    </div>
  </div>
</section>

<section class="section-how">
  <div class="section-how-inner">
    <p class="section-label">Como funciona</p>
    <h2>Tres pasos.<br><em>Sin complicaciones.</em></h2>
    <p class="section-how-sub">Entrar es gratis y no necesitas instalar nada. La app funciona desde el navegador.</p>

    <div class="steps">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-icon">FORM</div>
        <div class="step-title">Te apuntas</div>
        <div class="step-desc">Dejas tu nombre y email. Sin tarjeta, sin compromiso. En 10 segundos.</div>
        <div class="step-connector">&rarr;</div>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-icon">MAIL</div>
        <div class="step-title">Te avisamos</div>
        <div class="step-desc">Recibes un email antes que nadie cuando Falles360 abra el acceso anticipado.</div>
        <div class="step-connector">&rarr;</div>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-icon">GO</div>
        <div class="step-title">Entras primero</div>
        <div class="step-desc">Configuras tu perfil, guardas rutas y llegas a marzo con la app hecha tuya.</div>
      </div>
    </div>
  </div>
</section>

<section class="section-cta">
  <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;" viewBox="0 0 1440 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <g transform="translate(1300,50) scale(2)">
      <path d="M35,140 C16,114 0,88 10,55 C16,37 26,26 21,7 C37,24 32,46 46,33 C40,56 56,63 51,86 C62,67 67,44 56,19 C77,37 80,67 69,95 Z" fill="#E8460A" opacity="0.1"/>
    </g>
    <g transform="translate(-20,300) scale(1.3)">
      <path d="M25,100 C12,83 0,65 7,44 C10,30 17,22 14,8 C24,19 21,33 30,24 C27,39 39,44 35,59 C44,46 47,29 39,12 C54,26 55,48 47,65 Z" fill="#E8460A" opacity="0.08"/>
    </g>
  </svg>

  <h2>Empieza este verano<br>y llega a marzo<br><em>con la app hecha tuya.</em></h2>
  <p>Mapa, agenda, rutas y Pasaporte Fallero en una entrada rapida.</p>
  <button class="cta-btn" type="button" id="cta-scroll">Apuntarme ahora</button>
  <p class="cta-note">Gratis para empezar | Sin tarjeta | Sin instalar nada</p>
</section>

<footer>&copy; 2026 Falles360 | Valencia | <a href="./dist/privacy.html">Privacidad</a></footer>

<script<?php echo app_csp_nonce_attr(); ?>>
  const csrfToken = <?php echo json_encode($csrfToken, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const apiEndpoint = './api/solicitudes.php';
  const targetDate = new Date('2027-03-19T00:00:00+01:00');

  const form = document.getElementById('waitlist-form');
  const nameInput = document.getElementById('input-name');
  const emailInput = document.getElementById('input-email');
  const submitButton = document.getElementById('btn-submit');
  const formInner = document.getElementById('form-inner');
  const successState = document.getElementById('success-state');
  const statusBox = document.getElementById('form-status');
  const counterNumber = document.getElementById('counter-num');
  const ctaScroll = document.getElementById('cta-scroll');
  const formCard = document.getElementById('form-card');

  function updateCountdown() {
    const diff = targetDate.getTime() - Date.now();
    if (diff <= 0) {
      document.getElementById('cd-days').textContent = '0';
      document.getElementById('cd-hours').textContent = '0';
      document.getElementById('cd-mins').textContent = '0';
      return;
    }

    document.getElementById('cd-days').textContent = String(Math.floor(diff / 86400000));
    document.getElementById('cd-hours').textContent = String(Math.floor((diff % 86400000) / 3600000));
    document.getElementById('cd-mins').textContent = String(Math.floor((diff % 3600000) / 60000));
  }

  function setStatus(message, kind) {
    statusBox.textContent = message;
    statusBox.className = 'form-status';
    if (!message) {
      return;
    }
    statusBox.classList.add(kind === 'ok' ? 'form-status--ok' : 'form-status--error');
  }

  function shake(element) {
    element.style.borderColor = 'white';
    element.style.animation = 'shake .3s ease';
    window.setTimeout(() => {
      element.style.animation = '';
      element.style.borderColor = '';
    }, 400);
  }

  function showSuccess(nextCount) {
    formInner.style.display = 'none';
    successState.style.display = 'block';
    if (typeof nextCount === 'number' && Number.isFinite(nextCount)) {
      counterNumber.textContent = String(nextCount);
    }
  }

  async function handleSubmit(event) {
    event.preventDefault();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim().toLowerCase();

    if (name.length < 2) {
      setStatus('Escribe tu nombre para entrar en la lista.', 'error');
      shake(nameInput);
      return;
    }

    if (!/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(email)) {
      setStatus('Escribe un email valido.', 'error');
      shake(emailInput);
      return;
    }

    submitButton.disabled = true;
    submitButton.textContent = 'Guardando...';
    setStatus('', 'ok');

    try {
      const response = await fetch(apiEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({
          name,
          email,
          source: 'whitelist_page',
        }),
      });

      const payload = await response.json().catch(() => ({}));
      if (!response.ok || payload.ok === false) {
        throw new Error(typeof payload.message === 'string' ? payload.message : 'No se pudo guardar la solicitud.');
      }

      showSuccess(typeof payload.count === 'number' ? payload.count : undefined);
    } catch (error) {
      setStatus(error instanceof Error ? error.message : 'No se pudo guardar la solicitud.', 'error');
      submitButton.disabled = false;
      submitButton.textContent = 'Apuntarme a la lista';
    }
  }

  updateCountdown();
  window.setInterval(updateCountdown, 60000);

  window.setTimeout(() => {
    const finalCount = Number.parseInt(counterNumber.textContent || '0', 10);
    let current = Math.max(0, finalCount - Math.min(24, finalCount));
    counterNumber.textContent = String(current);

    const interval = window.setInterval(() => {
      current += 1;
      if (current >= finalCount) {
        current = finalCount;
        window.clearInterval(interval);
      }
      counterNumber.textContent = String(current);
    }, 30);
  }, 800);

  form.addEventListener('submit', (event) => {
    void handleSubmit(event);
  });

  ctaScroll.addEventListener('click', () => {
    formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
</script>
</body>
</html>
