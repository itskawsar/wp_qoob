<?php
include_once(str_replace('wp-content' . DIRECTORY_SEPARATOR, '', explode(DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR, dirname(__FILE__))[0] . DIRECTORY_SEPARATOR . 'wp-load.php'));
$qoob_styles = '';
$path = get_template_directory() . '/blocks';

foreach (new DirectoryIterator($path) as $file) {
    if ($file->isDot())
        continue;

    if ($file->isDir()) {
        // masks urls
        $theme_url = get_template_directory_uri();
        $blocks_url = get_template_directory_uri() . '/blocks';
        $block_url = get_template_directory_uri() . '/blocks/' . $file->getFilename() . '/';
        // get block's config file
        $config_json = file_get_contents($block_url . 'config.json');
        // parsing config masks            
        $config_json = preg_replace('/%theme_url%/', $theme_url, $config_json);
        $config_json = preg_replace('/%block_url%/', $block_url, $config_json);
        $config_json = preg_replace('/%blocks_url%/', $blocks_url, $config_json);
        // getting assets
        $config = SmartUtils::decode($config_json, true);
        if (isset($config['assets'])) {
            $assets = $config['assets'];
            for ($i = 0; $i < count($assets); $i++) {
                if ($assets[$i]['type'] === 'style') {
                    // parsing styles masks
                    $style = file_get_contents($assets[$i]['src']);
                    $style = preg_replace('/%theme_url%/', $theme_url, $style);
                    $style = preg_replace('/%block_url%/', $block_url, $style);
                    $style = preg_replace('/%blocks_url%/', $blocks_url, $style);
                    $qoob_styles .= $style;
                }
            }
        }
    }
}
// printing styles like css file
header('Content-Type: text/css');
echo $qoob_styles;

