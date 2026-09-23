<?php
/**
 * Startseite Terra & Garten Hübers.
 * Projekte, Kontaktdaten und Rechtstexte werden über den Verwaltungsbereich (/admin/) gepflegt.
 */
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/layout.php';

$d = content_load();
$c = $d['contact'];
$projects = visible_projects($d);
$tel = 'tel:+' . phone_intl($c['phone']);
$wa = wa_link($c, wa_intro($c));

render_head($d, 'Terra & Garten Hübers – Erdbau, Garten- und Landschaftsbau in Haren', 'Terra & Garten – Ihr Dienstleister Christoph Hübers aus Haren (Ems): Erdbau, Baggerarbeiten, Garten- und Landschaftsbau, Rasen, Zaunbau, Transport und Material.', '');
render_header($d, '', (bool)$projects);
?>

<main>
<section class="hero" id="start" aria-label="Terra & Garten Hübers">
  <canvas id="terrain" aria-hidden="true"></canvas>
  <p class="hero-note">Ihr Projekt.<br>Unsere Leidenschaft.<small>Bewegen Sie den Finger oder die Maus – wir bewegen Erde.</small></p>
  <span class="scrollhint" aria-hidden="true"></span>
  <div class="wrap hero-inner">
    <div class="coords">
      <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>Haren (Ems) &amp; Emsland</span>
      <span>52°47′ N  7°14′ O</span>
    </div>
    <h1 class="logo-h1"><img src="assets/img/logo.webp" alt="Terra &amp; Garten – Ihr Dienstleister Hübers" width="1000" height="868" fetchpriority="high"></h1>
    <p class="lede">Erdbau, Baggerarbeiten und Gartenbau aus einer Hand. Vom ersten Aushub bis zum fertigen Garten.</p>
    <div class="cta">
      <a class="btn btn-sand" href="<?= e($tel) ?>"><?= ICON_PHONE ?>Jetzt anrufen</a>
      <a class="btn btn-wa" href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= ICON_WA ?>WhatsApp</a>
      <a class="btn btn-ghost" href="#kontakt">Projekt anfragen</a>
    </div>
  </div>
</section>

<div class="marquee" aria-hidden="true"><div class="track" id="mq"><span>Erdbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Baggerarbeiten<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Rasen<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Landschaftsbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Bepflanzung<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Zaunbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Transport<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Erdbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Baggerarbeiten<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Rasen<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Landschaftsbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Bepflanzung<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Zaunbau<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span><span>Transport<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c0-8 4-14 10-18-8 0-14 4-15 11-2-3-4-4-7-4 0 5 3 9 8 9"/></svg></span></div></div>
<section id="leistungen">
  <div class="wrap">
    <div class="head reveal">
      <h2>Was wir für Sie bewegen</h2>
      <p class="intro">Sechs Bereiche, ein Ansprechpartner. Sie müssen nicht zwischen Baggerbetrieb, Gärtner und Spedition koordinieren – das übernehmen wir.</p>
    </div>
    <div class="services">
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"><path d="M4 52h56"/><path d="M8 52l8-10h14l8 10"/><path d="M40 52c4-8 10-12 18-12"/><path d="M14 36l6-14 8 6-4 8"/><path d="M20 22l14-10 10 12"/><path d="M44 24l-6 8h10z"/></svg>
        <h3>Erdbau</h3>
        <div><p>Wir bereiten den Boden für alles, was darauf entstehen soll.</p><ul><li>Aushubarbeiten</li><li>Baugruben</li><li>Planierarbeiten</li></ul></div>
      </article>
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"><rect x="6" y="42" width="30" height="10" rx="5"/><circle cx="12" cy="47" r="2"/><circle cx="21" cy="47" r="2"/><circle cx="30" cy="47" r="2"/><path d="M10 42V30h16v12"/><path d="M14 30v-6h8l4 6"/><path d="M26 34l14-18 12 8"/><path d="M52 24l6 10-10 2z"/></svg>
        <h3>Baggerarbeiten</h3>
        <div><p>Präzise, zuverlässig und effizient – auch auf engem Grundstück.</p><ul><li>Minibagger-Einsatz</li><li>Erdarbeiten</li><li>Leitungsgräben</li><li>Abbruch &amp; Rückbau im Garten</li></ul></div>
      </article>
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"><path d="M4 54c10-8 20-8 28-2s18 6 28-2"/><path d="M4 60h56"/><path d="M32 50V28"/><path d="M32 36c-10 0-14-6-14-14 10 0 14 6 14 14z"/><path d="M32 30c8 0 12-5 12-12-8 0-12 5-12 12z"/><path d="M14 52v-8M50 50v-6"/></svg>
        <h3>Garten- &amp; Landschaftsbau</h3>
        <div><p>Gärten, die jeden Tag genutzt werden und trotzdem gut aussehen.</p><ul><li>Gartenumgestaltung</li><li>Rasen anlegen</li><li>Beete &amp; Einfassungen</li></ul></div>
      </article>
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"><path d="M32 58V34"/><path d="M32 42l-8-6M32 38l7-6"/><path d="M32 34c-12 0-18-6-18-14S22 6 32 6s18 6 18 14-6 14-18 14z"/><path d="M20 58h24"/></svg>
        <h3>Bepflanzung</h3>
        <div><p>Die passende Pflanze für Boden, Licht und Pflegeaufwand.</p><ul><li>Sträucher</li><li>Bäume</li><li>Rasenflächen</li></ul></div>
      </article>
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"><path d="M8 58V16l4-6 4 6v42M24 58V16l4-6 4 6v42M40 58V16l4-6 4 6v42"/><path d="M4 26h52M4 46h52"/><path d="M52 58V22h8v36"/></svg>
        <h3>Zaunbau</h3>
        <div><p>Grenzen ziehen, Einblicke nehmen, Grundstücke sichern.</p><ul><li>Sichtschutz</li><li>Holzzäune</li><li>Gabionen</li></ul></div>
      </article>
      <article class="svc reveal">
        <svg class="ic" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"><path d="M4 44V20h26l6 10h10l8 8v6z"/><path d="M4 20l6-8h18"/><circle cx="14" cy="48" r="5"/><circle cx="44" cy="48" r="5"/><path d="M38 34h14"/></svg>
        <h3>Transport &amp; Material</h3>
        <div><p>Wir liefern, was Ihr Projekt braucht – direkt an die Baustelle.</p><ul><li>Schüttgut</li><li>Mutterboden</li><li>Kies &amp; Splitt</li><li>und mehr</li></ul></div>
      </article>
    </div>
  </div>
</section>

<section class="profile" id="profil" aria-label="Aufbau einer Rasenfläche">
  <div class="sticky">
    <div class="wrap pgrid">
      <svg class="soil" viewBox="0 0 640 440" role="img" aria-label="Querschnitt einer Rasenfläche mit Untergrund, Oberboden, Feinplanum und Rasen">
        <defs>
          <pattern id="pDirt" width="22" height="22" patternUnits="userSpaceOnUse"><rect width="22" height="22" fill="#6b5236"/><circle cx="5" cy="6" r="1.6" fill="#57422b"/><circle cx="15" cy="14" r="2.4" fill="#7d6445"/><circle cx="18" cy="4" r="1" fill="#4e3b25"/></pattern>
          <pattern id="pHumus" width="18" height="16" patternUnits="userSpaceOnUse"><rect width="18" height="16" fill="#3a2d21"/><circle cx="4" cy="4" r="1.3" fill="#2c2219"/><circle cx="12" cy="10" r="1.8" fill="#4a3a2b"/><path d="M8 13l4-1" stroke="#5a4633" stroke-width="1"/></pattern>
          <pattern id="pFine" width="6" height="6" patternUnits="userSpaceOnUse"><rect width="6" height="6" fill="#8a7358"/><circle cx="1.5" cy="1.5" r=".8" fill="#a18a6c"/><circle cx="4.5" cy="4" r=".7" fill="#76614a"/></pattern>
        </defs>
        <g class="ly"><rect x="0" y="330" width="640" height="110" fill="url(#pDirt)"/><text x="18" y="392" fill="#ece7dc">Untergrund</text></g>
        <g class="ly"><rect x="0" y="252" width="640" height="78" fill="url(#pHumus)"/><text x="18" y="296" fill="#ece7dc">Oberboden</text></g>
        <g class="ly"><rect x="0" y="230" width="560" height="22" fill="url(#pFine)"/><rect x="560" y="230" width="80" height="22" fill="url(#pHumus)"/><text x="18" y="246" fill="#ece7dc" style="font-size:13px">Feinplanum</text></g>
        <g class="ly">
          <rect x="0" y="212" width="560" height="18" fill="#4b3a26"/>
          <g id="blades"></g>
          <rect x="566" y="196" width="34" height="96" fill="#c8c4bb" stroke="#1d1a15" stroke-width="1.5"/>
          <path d="M556 300h60v-30l-14-8h-46z" fill="#9d9a93"/>
          <text x="540" y="182" fill="currentColor" style="font-size:13px">Einfassung</text>
          <text x="18" y="226" fill="#ece7dc" style="font-size:13px">Rasen</text>
        </g>
      </svg>
      <div class="ptext">
        <h2>Was unter einem guten Rasen steckt</h2>
        <p class="intro">Das meiste sieht man später nicht. Genau deshalb machen wir es gründlich. Scrollen Sie weiter und sehen Sie, wie eine Fläche entsteht.</p>
        <ol class="psteps">
          <li><b><small>Schritt 1 von 4</small>Untergrund</b><p>Alter Rasen und Wurzeln raus, Boden lockern, Gefälle anlegen – damit Wasser abläuft, statt Pfützen zu bilden.</p></li>
          <li><b><small>Schritt 2 von 4</small>Oberboden</b><p>Frischer Mutterboden in ausreichender Stärke. Er ist die Grundlage für kräftige, tiefe Wurzeln.</p></li>
          <li><b><small>Schritt 3 von 4</small>Feinplanum</b><p>Abziehen, walzen, nochmal abziehen. Nur eine wirklich ebene Fläche ergibt einen ebenen Rasen.</p></li>
          <li><b><small>Schritt 4 von 4</small>Rasen &amp; Kante</b><p>Rollrasen verlegen oder einsäen, anwalzen, wässern. Saubere Einfassungen halten die Kante dauerhaft in Form.</p></li>
        </ol>
        <div class="pbar"><i id="pbar"></i></div>
      </div>
    </div>
  </div>
</section>

<section class="band">
  <svg class="edge" viewBox="0 0 1200 34" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 34V20l40-6 30 8 55-12 48 9 36-7 60 10 44-14 52 12 38-5 70 9 40-11 56 8 30-6 64 10 42-13 58 11 34-4 66 7 44-12 50 10 38-6 60 9 46-10 40 7 30-5 V34z"/></svg>
  <svg class="band-lines" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true" fill="none" stroke="#c9ae7f" stroke-width="1.2">
    <path d="M-50 480C150 420 260 520 460 470S760 330 980 380 1250 300 1250 300"/><path d="M-50 430C160 370 280 470 470 420S760 280 990 330 1250 250 1250 250"/><path d="M-50 380C170 320 300 420 480 370S760 230 1000 280 1250 200 1250 200"/><path d="M-50 330C180 270 320 370 490 320S760 180 1010 230 1250 150 1250 150"/><path d="M-50 280C190 220 340 320 500 270S760 130 1020 180 1250 100 1250 100"/><path d="M-50 230C200 170 360 270 510 220S760 80 1030 130 1250 50 1250 50"/>
  </svg>
  <div class="wrap band-grid">
    <div class="reveal">
      <span class="script">Stark im</span>
      <h2>Erdbau &amp; Gartenbau</h2>
    </div>
    <ul class="promise reveal">
      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg><div><b>Kompetent</b><span>Erdbau und Gartenbau aus einer Hand, ohne Umwege über Subunternehmer.</span></div></li>
      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg><div><b>Zuverlässig</b><span>Was besprochen ist, wird so umgesetzt.</span></div></li>
      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg><div><b>Saubere Arbeit</b><span>Die Baustelle wird so hinterlassen, wie wir sie selbst vorfinden möchten.</span></div></li>
      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg><div><b>Termingerecht</b><span>Klare Absprachen zu Start und Dauer der Arbeiten.</span></div></li>
      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg><div><b>Individuelle Beratung</b><span>Persönlich vor Ort, bevor ein Angebot geschrieben wird.</span></div></li>
    </ul>
  </div>
</section>


<?php if ($projects): ?>
<section class="proj" id="projekte">
<?php foreach ($projects as $pi => $p): ?>
  <div class="proj-item">
    <div class="wrap">
      <div class="head reveal">
        <h2><?= e($p['title']) ?></h2>
<?php if ($p['intro'] !== ''): ?>
        <p class="intro"><?= e($p['intro']) ?></p>
<?php endif; ?>
      </div>
<?php if ($p['has_ba']): $b = $p['before']; $a = $p['after']; ?>
      <div class="ba reveal" style="--ar:<?= (int)$b['w'] ?>/<?= (int)$b['h'] ?>">
        <img src="<?= e($b['src']) ?>" alt="<?= e($b['alt'] ?? 'Vorher') ?>" width="<?= (int)$b['w'] ?>" height="<?= (int)$b['h'] ?>" loading="lazy">
        <img class="after" src="<?= e($a['src']) ?>" alt="<?= e($a['alt'] ?? 'Nachher') ?>" width="<?= (int)$a['w'] ?>" height="<?= (int)$a['h'] ?>" loading="lazy">
        <span class="tag l">Vorher</span><span class="tag r">Nachher</span>
        <input type="range" min="0" max="100" value="50" aria-label="Vorher-Nachher-Vergleich">
        <div class="handle"><div class="knob"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/></svg></div></div>
      </div>
<?php endif; ?>
<?php if (count($p['images']) > 1): ?>
      <div class="strip-nav"><button type="button" class="s-prev" aria-label="Vorherige Bilder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button><button type="button" class="s-next" aria-label="Nächste Bilder"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button></div>
<?php endif; ?>
    </div>
<?php if ($p['images']): ?>
    <div class="strip">
<?php foreach ($p['images'] as $i => $img): ?>
      <button type="button" class="shot" data-full="<?= e($img['src']) ?>"><div class="ph"><img src="<?= e($img['thumb'] ?? $img['src']) ?>" alt="<?= e($img['caption'] ?? '') ?>" loading="lazy" decoding="async"></div><div class="cap"><b><?= $i + 1 ?></b><span><?= e($img['caption'] ?? '') ?></span></div></button>
<?php endforeach; ?>
    </div>
<?php endif; ?>
  </div>
<?php endforeach; ?>
</section>
<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Bildansicht"><img alt=""><p></p>
  <button class="x" type="button" aria-label="Schließen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  <button class="pv" type="button" aria-label="Vorheriges Bild"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button><button class="nx" type="button" aria-label="Nächstes Bild"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
</div>
<?php endif; ?>

<section id="ablauf">
  <div class="wrap">
    <div class="head reveal">
      <h2>So läuft Ihr Projekt</h2>
      <p class="intro">Vom ersten Anruf bis zur fertigen Fläche – in drei Schritten, mit einem festen Ansprechpartner.</p>
    </div>
    <div class="steps" id="steps">
      <div class="rail" aria-hidden="true"><i></i>
        <svg class="digger" viewBox="0 0 64 40" fill="currentColor"><rect x="2" y="30" width="30" height="8" rx="4"/><path d="M6 30V18h14l6 6v6z"/><rect x="9" y="20" width="7" height="6" fill="var(--bg)"/><g class="arm"><path d="M24 20l16-14 16 10-3 3-13-7-14 11z"/><path d="M52 16l8 12-12 2z"/></g></svg>
      </div>
      <div class="step reveal"><span class="n">1</span><h3>Anrufen oder schreiben</h3><p>Erzählen Sie kurz, was Sie vorhaben. Fotos vom Grundstück helfen bei der ersten Einschätzung.</p></div>
      <div class="step reveal"><span class="n">2</span><h3>Besichtigung &amp; Angebot</h3><p>Wir schauen uns alles vor Ort an, beraten Sie zu Material und Umsetzung und erstellen ein klares Angebot.</p></div>
      <div class="step reveal"><span class="n">3</span><h3>Umsetzung</h3><p>Mit Maschine und eigenem Transport – vom Aushub bis zum letzten Handgriff.</p></div>
    </div>
  </div>
</section>


<section class="about" id="ueber">
  <div class="wrap about-grid">
    <div class="reveal">
      <figure class="portrait" style="margin:0 auto"><img src="assets/img/portrait.jpg" alt="Christoph Hübers am Minibagger" width="600" height="750" loading="lazy">
      <div class="badge has-logo">
        <svg class="ring" viewBox="0 0 200 200" aria-hidden="true"><defs><path id="circ" d="M100,100 m-86,0 a86,86 0 1,1 172,0 a86,86 0 1,1 -172,0"/></defs><text font-family="Barlow, sans-serif" font-size="9.5" letter-spacing="3.2" fill="#c9ae7f"><textPath href="#circ">ERDBAU · BAGGERARBEITEN · GARTENBAU · RASEN · ZAUNBAU · TRANSPORT ·</textPath></text></svg>
        <img src="assets/img/logo.webp" alt="" aria-hidden="true" width="1000" height="868" loading="lazy">
      </div>
    </figure>
    </div>
    <div class="reveal">
      <h2>Christoph Hübers</h2>
      <p>Hinter Terra &amp; Garten steht ein Mann mit Bagger, Spaten und dem Anspruch, Arbeit abzuliefern, auf die man noch in zehn Jahren gern schaut. Als junges Unternehmen aus Haren sind wir im gesamten Emsland für Privatkunden und Gewerbe unterwegs.</p>
      <p>Sie sprechen immer direkt mit dem, der auch auf der Baustelle steht. Das spart Zeit, Missverständnisse und oft auch Geld.</p>
      <p class="quote">Wir freuen uns auf Ihr Projekt!</p>
    </div>
  </div>
</section>

<section class="area" id="gebiet">
  <svg class="edge" viewBox="0 0 1200 34" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 34V20l40-6 30 8 55-12 48 9 36-7 60 10 44-14 52 12 38-5 70 9 40-11 56 8 30-6 64 10 42-13 58 11 34-4 66 7 44-12 50 10 38-6 60 9 46-10 40 7 30-5 V34z"/></svg>
  <div class="wrap area-grid">
    <div class="reveal">
      <h2>Zuhause in Haren, unterwegs in der Region</h2>
      <p class="intro">Von Haren aus sind wir mit Bagger und Transporter schnell bei Ihnen. Ihr Ort ist nicht dabei? Fragen Sie einfach nach.</p>
      <ul class="towns"><li>Haren (Ems)</li><li>Meppen</li><li>Lathen</li><li>Twist</li><li>Dörpen</li><li>Sögel</li><li>Papenburg</li><li>Lingen</li></ul>
    </div>
    <svg class="map" viewBox="-10 20 480 660" role="img" aria-label="Stilisierte Karte: Haren im Zentrum, umliegende Orte im Emsland">
      <path d="M150 40 C140 150 175 230 160 300 S120 430 150 520 S120 610 130 670" fill="none" stroke="rgba(236,231,220,.25)" stroke-width="1.5" stroke-dasharray="6 7"/>
      <text x="105" y="120" style="font-size:12px;fill:#8d877b">NL</text><text x="180" y="120" style="font-size:12px;fill:#8d877b">DE</text>
      <path class="ems" pathLength="1" d="M268 680 C272 640 280 610 268 560 S250 480 256 440 S222 380 226 350 S262 300 272 260 S282 200 280 160 S300 100 318 50" fill="none" stroke="#7d8a58" stroke-width="4" stroke-linecap="round"/>
      <text x="200" y="420" style="font-size:13px;fill:#a3b077;font-style:italic">Ems</text>
      <circle class="pulse" cx="236" cy="360" r="200" stroke-width="1.5"/><circle class="pulse" cx="236" cy="360" r="200" stroke-width="1.5"/><circle class="pulse" cx="236" cy="360" r="200" stroke-width="1.5"/>
      <circle cx="327" cy="70" r="4" fill="#c9ae7f"/><text x="337" y="75" text-anchor="start">Papenburg</text><circle cx="290" cy="190" r="4" fill="#c9ae7f"/><text x="302" y="195" text-anchor="start">Dörpen</text><circle cx="284" cy="280" r="4" fill="#c9ae7f"/><text x="296" y="285" text-anchor="start">Lathen</text><circle cx="405" cy="310" r="4" fill="#c9ae7f"/><text x="395" y="315" text-anchor="end">Sögel</text><circle cx="30" cy="370" r="4" fill="#c9ae7f"/><text x="40" y="375" text-anchor="start">Emmen (NL)</text><circle cx="266" cy="460" r="4" fill="#c9ae7f"/><text x="278" y="465" text-anchor="start">Meppen</text><circle cx="151" cy="520" r="4" fill="#c9ae7f"/><text x="161" y="525" text-anchor="start">Twist</text><circle cx="284" cy="630" r="4" fill="#c9ae7f"/><text x="296" y="635" text-anchor="start">Lingen</text>
      <g class="home"><circle cx="236" cy="360" r="11" fill="#c9ae7f"/><circle cx="236" cy="360" r="4.5" fill="#1d1a15"/><text x="222" y="366" text-anchor="end">Haren</text></g>
    </svg>
  </div>
</section>

<section id="kontakt">
  <div class="wrap contact-grid">
    <div class="reveal">
      <h2>Kontakt</h2>
      <p class="intro">Am schnellsten erreichen Sie uns telefonisch oder per WhatsApp – gern auch mit Fotos vom Grundstück.</p>
<?php if ($c['phone'] !== ''): ?>
      <a class="phone" href="<?= e($tel) ?>"><?= e($c['phone']) ?></a>
<?php endif; ?>
      <ul class="facts">
<?php if ($c['phone'] !== ''): ?>
        <li><?= ICON_WA ?><a href="<?= e($wa) ?>" target="_blank" rel="noopener">Per WhatsApp schreiben</a></li>
<?php endif; ?>
<?php if ($c['email'] !== ''): ?>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg><a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a></li>
<?php endif; ?>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg><span><?= e($c['owner']) ?><br><?= e($c['street']) ?><br><?= e(trim($c['zip'] . ' ' . $c['city'])) ?></span></li>
      </ul>
    </div>
    <div class="reveal">
      <div class="cfg" id="cfg" data-wa="<?= e(phone_intl($c['phone'])) ?>" data-mail="<?= e($c['email']) ?>" data-greeting="<?= e($c['greeting']) ?>">
        <h3 style="font-family:var(--display);font-size:2rem;margin:0;line-height:1">Anfrage in 30 Sekunden</h3>
        <fieldset><legend>Was steht an?</legend><div class="chips" data-group="leistung" data-multi="1">
          <button type="button" class="chip" aria-pressed="false">Erdbau</button><button type="button" class="chip" aria-pressed="false">Baggerarbeiten</button><button type="button" class="chip" aria-pressed="false">Garten &amp; Rasen</button><button type="button" class="chip" aria-pressed="false">Bepflanzung</button><button type="button" class="chip" aria-pressed="false">Zaunbau</button><button type="button" class="chip" aria-pressed="false">Transport &amp; Material</button>
        </div></fieldset>
        <fieldset><legend>Wie groß ist das Projekt?</legend><div class="chips" data-group="umfang">
          <button type="button" class="chip" aria-pressed="false">Klein<small>ein Tag</small></button><button type="button" class="chip" aria-pressed="false">Mittel<small>einige Tage</small></button><button type="button" class="chip" aria-pressed="false">Groß<small>mehrere Wochen</small></button><button type="button" class="chip" aria-pressed="false">Weiß ich noch nicht</button>
        </div></fieldset>
        <fieldset><legend>Wann soll es losgehen?</legend><div class="chips" data-group="zeit">
          <button type="button" class="chip" aria-pressed="false">So bald wie möglich</button><button type="button" class="chip" aria-pressed="false">In 1–3 Monaten</button><button type="button" class="chip" aria-pressed="false">Flexibel</button>
        </div></fieldset>
        <div class="f2">
          <label>Ihr Name<input id="cName" autocomplete="name"></label>
          <label>Ort<input id="cOrt" placeholder="z. B. Haren-Emmeln"></label>
        </div>
        <label>Noch etwas dazu? <span style="font-weight:400;color:var(--muted)">(optional)</span><textarea id="cMsg" placeholder="z. B. Rasenfläche ca. 150 m², alter Rasen muss raus" style="min-height:90px"></textarea></label>
        <div class="preview" id="preview" aria-live="polite"></div>
        <div class="send">
<?php if ($c['phone'] !== ''): ?>
          <a class="btn btn-wa" id="sendWa" href="<?= e($wa) ?>" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>Per WhatsApp senden</a>
<?php endif; ?>
<?php if ($c['email'] !== ''): ?>
          <a class="btn btn-ink" id="sendMail" href="mailto:<?= e($c['email']) ?>">Per E-Mail senden</a>
<?php endif; ?>
        </div>
        <p class="form-note">Ihre Angaben werden nicht auf unserem Server gespeichert – die Nachricht wird erst über WhatsApp bzw. Ihr E-Mail-Programm verschickt. <a href="datenschutz/">Datenschutz</a></p>
      </div>
    </div>
  </div>
</section>
</main>
<?php render_footer($d, ''); ?>
