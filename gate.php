<?php
// Afscherming van de klantensite zolang FlitsFood niet live is.
// Zonder geldige toegang krijgt iedereen /binnenkort/ te zien.
// Toegang krijgen: https://flitsfood.com/?toegang=CODE  of de code invullen onder "Team" op de binnenkort-pagina.
// De code zelf staat NIET in deze repo (alleen de SHA-256 ervan); zie TOEGANG-PERSONEEL.txt.
// Afscherming opheffen bij de lancering: .htaccess-regels "Afscherming" weghalen (of AAN = false hieronder).

const AAN = true;
const HASH = '6e551777a3e71b37e288fdb764fdb10f7efefb50c2fee835ac1fbf8c25fd8cb7';
const KOEKJE = 'ff_toegang';

header('Cache-Control: no-store, private');
header('X-LiteSpeed-Cache-Control: no-cache');
header('Vary: Cookie');

function geldig($code) {
    return is_string($code) && $code !== '' && hash_equals(HASH, hash('sha256', trim($code)));
}

function zet_koekje($code) {
    setcookie(KOEKJE, trim($code), [
        'expires' => time() + 365 * 24 * 3600, 'path' => '/',
        'secure' => true, 'httponly' => true, 'samesite' => 'Lax',
    ]);
}

$pad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Code via het formulier op de binnenkort-pagina.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (geldig($_POST['toegang'] ?? '')) { zet_koekje($_POST['toegang']); header('Location: /', true, 303); }
    else { header('Location: /?fout=1', true, 303); }
    exit;
}

// Code via de link ?toegang=...
if (isset($_GET['toegang'])) {
    if (geldig($_GET['toegang'])) { zet_koekje($_GET['toegang']); header('Location: ' . $pad, true, 302); }
    else { header('Location: /?fout=1', true, 302); }
    exit;
}

$open = !AAN || geldig($_COOKIE[KOEKJE] ?? '');

if (!$open) {
    header('X-Robots-Tag: noindex');
    header('Content-Type: text/html; charset=utf-8');
    readfile(__DIR__ . '/binnenkort/index.html');
    exit;
}

// Toegang ok: het gevraagde HTML-bestand tonen (alleen binnen de site, alleen .html).
if (substr($pad, -1) === '/') $pad .= 'index.html';
$basis = realpath(__DIR__);
$bestand = realpath(__DIR__ . '/' . ltrim(rawurldecode($pad), '/'));
if ($bestand === false || strpos($bestand, $basis . DIRECTORY_SEPARATOR) !== 0 || substr($bestand, -5) !== '.html') {
    http_response_code(404);
    echo 'Niet gevonden';
    exit;
}
header('Content-Type: text/html; charset=utf-8');
readfile($bestand);
