<?php

require __DIR__ . '/../../../../src/AppBundle/Util/AdChoiceConverter.php';

$positions = \AppBundle\Util\AdChoiceConverter::$positions;

$force = isset($argv[1]) && $argv[1] == '--force';

foreach ($positions as $k => $location) {
    $bits = explode('_', $location);
    list($area, $position, $number) = explode('_', $location);

    $file = $location . '.html.twig';
    $varname = str_replace('-', '_', $location);

    $twig = <<<TWIG
{% set {$varname} = ad('{$location}') %}
{% if {$varname} is not empty %}
    {% for ad in {$varname} %}
        <div class="announcement announcement__{$area} announcement__{$area}--{$position}">
            <a href="{{ url('ad_redirect', { 'uuid': ad.uuid }) }}">
                <img src="{{ ad.image }}" title="{{ ad.title }}">
            </a>
        </div>
    {% endfor %}
{% endif %}
TWIG;

    echo "{$file}...";
    if (!file_exists($file) || $force) {
        file_put_contents($file, $twig);
        echo 'DONE!' . PHP_EOL;
    } else {
        echo 'SKIP!' . PHP_EOL;
    }
}

echo "Generating scss...";
$scss = '.announcement {' . PHP_EOL;
foreach ($positions as $k => $location) {
    $bits = explode('_', $location);
    list($area, $position, $number) = explode('_', $location);

    $scss .= "  &__{$area} {\n";
        $scss .= "    &--{$position} {\n";
            $scss .= PHP_EOL;
        $scss .= "    }\n";
    $scss .= "  }\n";
    $scss .= PHP_EOL;
}
$scss .= '}';
echo "DONE!" . PHP_EOL;

echo "Generating php constants...";
$php = '<?php' . PHP_EOL . PHP_EOL;
$php .= 'class bla {' . PHP_EOL . PHP_EOL;
foreach ($positions as $k => $location) {
    $php .= sprintf('const COUNT_%s = %d;' . PHP_EOL, strtoupper(str_replace('-', '_', $location)), 0);
}
$php .= '}';

file_put_contents('ad_limits.gen.php', $php);
echo 'DONE!' . PHP_EOL;