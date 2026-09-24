<?php
/**
 * Leistungsseiten (für Google): je Leistung eine eigene Seite mit Text, Fragen & Antworten.
 * Texte hier ändern – Aufbau und Design kommen aus render_service_page().
 */
declare(strict_types=1);

const TOWNS = ['Haren (Ems)', 'Meppen', 'Lathen', 'Twist', 'Dörpen', 'Sögel', 'Papenburg', 'Lingen'];

function services(): array
{
    return [
        'gartenbau-haren' => [
            'nav'   => 'Garten- & Landschaftsbau',
            'title' => 'Garten- und Landschaftsbau in Haren (Ems) & Emsland',
            'desc'  => 'Gartenumgestaltung, Beete, Einfassungen und Bepflanzung in Haren (Ems), Meppen und im Emsland. Terra & Garten Hübers – alles aus einer Hand.',
            'h1'    => 'Garten- und Landschaftsbau in Haren (Ems)',
            'lead'  => 'Ein Garten, der jeden Tag genutzt wird und trotzdem gut aussieht: Wir gestalten Ihren Garten um – vom ersten Spatenstich bis zur fertigen Bepflanzung. Mit eigener Maschine und direkt aus Haren.',
            'sections' => [
                ['Gartenumgestaltung aus einer Hand', 'Viele Gartenprojekte scheitern an der Koordination: Erst der Bagger, dann der Gärtner, dann noch jemand für den Transport. Bei uns kommt alles aus einer Hand. Wir räumen alte Flächen ab, bereiten den Boden vor, legen Beete und Rasen an und setzen Einfassungen, die dauerhaft in Form bleiben.'],
                ['Bepflanzung, die zu Ihrem Garten passt', 'Welche Pflanze wächst gut, wie viel Pflege ist realistisch, wie viel Sonne bekommt die Fläche? Wir wählen Sträucher, Bäume und Hecken passend zu Boden, Licht und Ihrem Pflegeaufwand aus – und pflanzen sie fachgerecht.'],
            ],
            'list'  => ['Gartenumgestaltung und Neuanlage', 'Beete anlegen und Einfassungen setzen', 'Bepflanzung mit Sträuchern, Bäumen und Hecken', 'Rasenflächen anlegen', 'Vorbereitende Erd- und Baggerarbeiten', 'Lieferung von Mutterboden und Material'],
            'faq'   => [
                ['Was kostet eine Gartenumgestaltung?', 'Das hängt stark von Größe, Zustand und Wünschen ab. Deshalb schauen wir uns Ihren Garten zuerst vor Ort an und erstellen danach ein klares Angebot – ohne versteckte Posten.'],
                ['Übernehmen Sie auch die Erdarbeiten vorab?', 'Ja. Aushub, Planieren und das Anlegen von Gefälle erledigen wir selbst mit unserem Minibagger. Sie brauchen keinen zusätzlichen Betrieb.'],
                ['Wie lange dauert eine Umgestaltung?', 'Kleinere Arbeiten sind oft an einem Tag erledigt, eine komplette Neuanlage dauert meist einige Tage. Den genauen Zeitraum besprechen wir vorab verbindlich mit Ihnen.'],
                ['In welchen Orten sind Sie unterwegs?', 'Von Haren aus im gesamten Emsland, z. B. in Meppen, Lathen, Twist, Dörpen, Sögel, Papenburg und Lingen. Ihr Ort ist nicht dabei? Fragen Sie einfach nach.'],
            ],
        ],
        'erdbau-baggerarbeiten-haren' => [
            'nav'   => 'Erdbau & Baggerarbeiten',
            'title' => 'Erdbau & Baggerarbeiten in Haren (Ems) – Minibagger',
            'desc'  => 'Aushub, Baugruben, Leitungsgräben, Planierarbeiten und Minibagger-Einsatz in Haren (Ems) und im Emsland – inklusive Transport von Mutterboden, Kies und Splitt.',
            'h1'    => 'Erdbau und Baggerarbeiten in Haren (Ems)',
            'lead'  => 'Wir bereiten den Boden für alles, was darauf entstehen soll: präzise, zuverlässig und auch auf engem Grundstück. Mit Minibagger und eigenem Transport – aus Haren für das ganze Emsland.',
            'sections' => [
                ['Minibagger-Einsatz – auch wo es eng wird', 'Ob Leitungsgraben quer durch den Garten, Aushub für ein Fundament oder das Abtragen alter Flächen: Mit dem Minibagger arbeiten wir präzise, auch direkt an Beeten, Terrassen und Hauswänden.'],
                ['Transport & Material direkt zur Baustelle', 'Schüttgut, Mutterboden, Kies und Splitt liefern wir direkt dorthin, wo sie gebraucht werden. So müssen Sie sich nicht um Lieferanten und Termine kümmern.'],
            ],
            'list'  => ['Aushubarbeiten und Baugruben', 'Leitungsgräben', 'Planierarbeiten und Gefälle anlegen', 'Minibagger-Einsatz', 'Abbruch & Rückbau im Garten', 'Transport von Schüttgut, Mutterboden, Kies & Splitt'],
            'faq'   => [
                ['Kommt der Minibagger auch in kleine Gärten?', 'In den meisten Fällen ja – genau dafür ist ein Minibagger da. Ob die Zufahrt passt, klären wir bei der Besichtigung vor Ort.'],
                ['Liefern Sie auch Material wie Mutterboden oder Kies?', 'Ja. Schüttgut, Mutterboden, Kies und Splitt bringen wir direkt zur Baustelle. Menge und Material stimmen wir vorher mit Ihnen ab.'],
                ['Kann ich nur einzelne Arbeiten beauftragen?', 'Natürlich. Sie können uns für einzelne Baggerarbeiten buchen oder das komplette Projekt vom Aushub bis zum fertigen Garten übergeben.'],
                ['Wie schnell können Sie anfangen?', 'Das hängt von der Auftragslage ab. Rufen Sie an oder schreiben Sie uns per WhatsApp – wir nennen Ihnen schnell einen realistischen Termin.'],
            ],
        ],
        'rasen-anlegen-emsland' => [
            'nav'   => 'Rasen anlegen',
            'title' => 'Rasen anlegen & Rollrasen verlegen im Emsland',
            'desc'  => 'Neuer Rasen in Haren, Meppen und im Emsland: alten Rasen entfernen, Untergrund vorbereiten, Oberboden, Feinplanum, Rollrasen oder Einsaat – alles aus einer Hand.',
            'h1'    => 'Rasen anlegen im Emsland',
            'lead'  => 'Das meiste an einem guten Rasen sieht man später nicht – genau deshalb machen wir es gründlich. Vom Entfernen des alten Rasens bis zur sauberen Kante: So entsteht eine Fläche, die lange schön bleibt.',
            'sections' => [
                ['So entsteht Ihr neuer Rasen', 'Zuerst kommen alter Rasen und Wurzeln raus, der Boden wird gelockert und ein Gefälle angelegt, damit Wasser abläuft statt Pfützen zu bilden. Dann folgt frischer Oberboden in ausreichender Stärke. Beim Feinplanum wird abgezogen, gewalzt und nochmal abgezogen – nur eine wirklich ebene Fläche ergibt einen ebenen Rasen. Zum Schluss verlegen wir Rollrasen oder säen ein, walzen an und wässern.'],
                ['Saubere Kanten, die halten', 'Eine gute Einfassung trennt Rasen und Beet dauerhaft und macht das Mähen leichter. Wir setzen sie gleich mit – dann bleibt die Kante in Form.'],
            ],
            'list'  => ['Alten Rasen und Wurzeln entfernen', 'Boden lockern und Gefälle anlegen', 'Mutterboden liefern und einbauen', 'Feinplanum (abziehen und walzen)', 'Rollrasen verlegen oder Rasen einsäen', 'Rasenkanten und Einfassungen setzen'],
            'faq'   => [
                ['Rollrasen oder Einsaat – was ist besser?', 'Rollrasen ist sofort grün und nach wenigen Wochen belastbar, kostet aber mehr. Eine Einsaat ist günstiger, braucht aber Zeit und Pflege, bis der Rasen dicht ist. Wir beraten Sie, was zu Ihrem Garten passt.'],
                ['Wann ist die beste Zeit für neuen Rasen?', 'Grundsätzlich in der frostfreien Zeit vom Frühjahr bis in den Herbst. Sehr heiße, trockene Wochen sind ungünstiger, weil der Rasen dann viel Wasser braucht.'],
                ['Muss der alte Rasen komplett raus?', 'Für ein dauerhaft gutes Ergebnis meistens ja. Moos, Unkraut und Unebenheiten wachsen sonst wieder durch. Bei der Besichtigung sehen wir uns die Fläche genau an.'],
                ['Wie pflege ich den neuen Rasen?', 'In den ersten Wochen ist regelmäßiges Wässern das Wichtigste. Sie bekommen von uns nach der Arbeit einfache Tipps für die erste Zeit.'],
            ],
        ],
        'zaunbau-haren' => [
            'nav'   => 'Zaunbau',
            'title' => 'Zaunbau & Sichtschutz in Haren (Ems) und im Emsland',
            'desc'  => 'Sichtschutzzäune, Holzzäune und Gabionen in Haren (Ems), Meppen und im Emsland – inklusive Pfosten setzen und Erdarbeiten. Terra & Garten Hübers.',
            'h1'    => 'Zaunbau und Sichtschutz in Haren (Ems)',
            'lead'  => 'Grenzen ziehen, Einblicke nehmen, Grundstücke sichern: Wir bauen Sichtschutz, Holzzäune und Gabionen – stabil gesetzt und sauber ausgerichtet.',
            'sections' => [
                ['Stabil von Grund auf', 'Ein Zaun ist nur so gut wie seine Pfosten. Weil wir Erdarbeiten selbst erledigen, setzen wir Pfosten und Fundamente sorgfältig und in einem Arbeitsgang – auch wenn vorher erst ein alter Zaun oder Bewuchs weichen muss.'],
                ['Beratung vor Ort', 'Welcher Zaun passt zu Haus und Garten, wie hoch soll der Sichtschutz sein, wo verläuft die Grenze? Das besprechen wir gemeinsam vor Ort, bevor ein Angebot geschrieben wird.'],
            ],
            'list'  => ['Sichtschutzzäune', 'Holzzäune', 'Gabionen', 'Pfosten und Fundamente setzen', 'Abbau alter Zäune', 'Vorbereitende Erdarbeiten'],
            'faq'   => [
                ['Brauche ich eine Genehmigung für meinen Zaun?', 'Das hängt von Höhe, Lage und dem Bebauungsplan ab. Im Zweifel lohnt eine kurze Nachfrage bei der Stadt bzw. Gemeinde – wir weisen Sie bei der Planung darauf hin.'],
                ['Entfernen Sie auch den alten Zaun?', 'Ja, Abbau und Rückbau im Garten gehören zu unseren Leistungen. Das stimmen wir im Angebot mit ab.'],
                ['Welches Material ist am langlebigsten?', 'Das hängt von Ihren Wünschen ab: Holz wirkt natürlich, braucht aber etwas Pflege; Gabionen sind sehr langlebig und pflegeleicht. Wir beraten Sie gern.'],
                ['Kann ich Zaunbau und Gartenarbeiten kombinieren?', 'Gerade das ist unsere Stärke – z. B. neuer Sichtschutz zusammen mit neuem Rasen und Beeten, alles in einem Projekt.'],
            ],
        ],
    ];
}

function render_service_page(string $key): void
{
    $all = services();
    $s = $all[$key];
    $d = content_load();
    $c = $d['contact'];
    $projects = visible_projects($d);
    $base = '../';
    $origin = canonical_origin();

    $schema = [
        [
            '@context' => 'https://schema.org',
            '@type'    => 'Service',
            'name'     => $s['h1'],
            'serviceType' => $s['nav'],
            'description' => $s['desc'],
            'areaServed'  => array_merge(TOWNS, ['Emsland']),
            'provider' => ['@type' => 'HomeAndConstructionBusiness', 'name' => $c['company'] . ' ' . $c['owner'], 'url' => $origin . '/', 'telephone' => '+' . phone_intl($c['phone'])],
        ],
        [
            '@context' => 'https://schema.org',
            '@type'    => 'FAQPage',
            'mainEntity' => array_map(function ($f) {
                return ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]];
            }, $s['faq']),
        ],
        [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => $origin . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $s['nav'], 'item' => $origin . '/' . $key . '/'],
            ],
        ],
    ];

    render_head($d, $s['title'] . ' | Terra & Garten Hübers', $s['desc'], $base, '/' . $key . '/', $schema);
    render_header($d, $base, (bool)$projects, true);
    ?>
<main class="subpage">
  <section class="page-hero">
    <div class="wrap">
      <nav class="crumbs" aria-label="Brotkrümelnavigation"><a href="<?= e($base) ?>">Startseite</a> › <span><?= e($s['nav']) ?></span></nav>
      <h1><?= e($s['h1']) ?></h1>
      <p class="lede"><?= e($s['lead']) ?></p>
      <?php render_cta($c); ?>
    </div>
  </section>

  <section class="page-body">
    <div class="wrap page-grid">
      <div class="page-text">
        <?php foreach ($s['sections'] as $sec): ?>
        <h2><?= e($sec[0]) ?></h2>
        <p><?= e($sec[1]) ?></p>
        <?php endforeach; ?>

        <h2>Häufige Fragen</h2>
        <div class="faq">
          <?php foreach ($s['faq'] as $f): ?>
          <details><summary><?= e($f[0]) ?></summary><p><?= e($f[1]) ?></p></details>
          <?php endforeach; ?>
        </div>
      </div>
      <aside class="page-side">
        <div class="side-box">
          <h2>Unsere Leistungen</h2>
          <ul class="checks"><?php foreach ($s['list'] as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul>
        </div>
        <div class="side-box">
          <h2>Einsatzgebiet</h2>
          <p>Von Haren aus im ganzen Emsland:</p>
          <ul class="towns"><?php foreach (TOWNS as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
        </div>
      </aside>
    </div>
  </section>

  <?php if ($projects): ?>
  <section class="page-projects">
    <div class="wrap">
      <h2>Aus unseren Projekten</h2>
      <?php render_project_cards($projects, $base); ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="page-more">
    <div class="wrap">
      <h2>Weitere Leistungen</h2>
      <ul class="more-links">
        <?php foreach ($all as $k => $o): if ($k === $key) continue; ?>
        <li><a href="<?= e($base . $k) ?>/"><?= e($o['nav']) ?> →</a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
</main>
<?php
    render_footer($d, $base);
}

/** Anruf / WhatsApp / Anfrage-Buttons */
function render_cta(array $c, string $base = '../'): void
{
    ?>
      <div class="cta">
        <?php if ($c['phone'] !== ''): ?>
        <a class="btn btn-sand" href="tel:+<?= e(phone_intl($c['phone'])) ?>"><?= ICON_PHONE ?>Jetzt anrufen</a>
        <a class="btn btn-wa" href="<?= e(wa_link($c, wa_intro($c))) ?>" target="_blank" rel="noopener"><?= ICON_WA ?>WhatsApp</a>
        <?php endif; ?>
        <a class="btn btn-ghost" href="<?= e($base) ?>#kontakt">Projekt anfragen</a>
      </div>
<?php
}

/** Kacheln mit Vorschaubild und Link zur Projektseite */
function render_project_cards(array $projects, string $base): void
{
    echo '<ul class="pcards">';
    foreach ($projects as $p) {
        $img = $p['has_ba'] ? $p['after'] : ($p['images'][0] ?? null);
        $src = $img ? ($img['thumb'] ?? $img['src']) : '';
        echo '<li><a href="' . e($base . 'projekte/' . $p['slug']) . '/">';
        if ($src) {
            echo '<span class="ph"><img src="' . e($base . $src) . '" alt="' . e($p['title']) . '" loading="lazy"></span>';
        }
        echo '<b>' . e($p['title']) . '</b><span>Projekt ansehen →</span></a></li>';
    }
    echo '</ul>';
}
