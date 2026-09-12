<?php
/*
 * IDEIGLENES diagnosztika — a config.php megtalálását vizsgálja.
 * Tokennel védett, és SOHA nem ír ki kulcsértéket, csak neveket és
 * igaz/hamis értékeket. Használat után azonnal törölni kell.
 */
if (($_GET['t'] ?? '') !== '99d1f6a22f25fbb9f325bb18543a1992') { http_response_code(404); exit('Not found'); }
header('Content-Type: text/plain; charset=utf-8');

function vizsgal(string $cimke, string $f): void
{
    echo "$cimke\n  útvonal: $f\n";
    echo '  létezik: ', var_export(is_file($f), true), "\n";
    if (!is_file($f)) { echo "\n"; return; }
    echo '  olvasható: ', var_export(is_readable($f), true), "\n";
    echo '  méret: ', filesize($f), " byte\n";
    echo '  jogok: ', substr(sprintf('%o', fileperms($f)), -4), "\n";
    echo '  tulajdonos uid: ', fileowner($f), ' | a PHP uid-ja: ', function_exists('posix_geteuid') ? posix_geteuid() : '?', "\n";
    $c = @include $f;
    echo '  tömböt ad vissza: ', var_export(is_array($c), true), "\n";
    if (is_array($c)) {
        echo '  kulcsok (CSAK a nevek): ', implode(', ', array_keys($c)), "\n";
        foreach ($c as $k => $v) {
            echo '    - ', $k, ': ', (is_string($v) ? ($v === '' ? 'ÜRES' : 'kitöltve, ' . strlen($v) . ' karakter') : gettype($v)), "\n";
        }
    }
    echo "\n";
}

echo "__DIR__ = ", __DIR__, "\n";
echo "realpath(..) = ", realpath(__DIR__ . '/..'), "\n";
echo "PHP = ", PHP_VERSION, " | open_basedir = ", ini_get('open_basedir') ?: '(nincs)', "\n\n";
vizsgal('[1] a public_html FÖLÖTT:', __DIR__ . '/../config.php');
vizsgal('[2] a public_html-BEN:', __DIR__ . '/config.php');

echo "config-nevű fájlok a két mappában (csak nevek):\n";
foreach ([__DIR__, __DIR__ . '/..'] as $d) {
    $t = @scandir($d);
    if ($t === false) { echo '  ', $d, ": (nem olvasható)\n"; continue; }
    $talalat = implode(', ', array_filter($t, function ($x) { return stripos($x, 'config') !== false; }));
    echo '  ', $d, ': ', ($talalat !== '' ? $talalat : '(nincs ilyen)'), "\n";
}

echo "\na telepített kód verziója:\n";
$lib = @file_get_contents(__DIR__ . '/stripe-lib.php');
echo '  az új, "első HASZNÁLHATÓ" betöltő van fent: ',
     var_export($lib !== false && strpos($lib, 'első HASZNÁLHATÓ') !== false, true), "\n";
