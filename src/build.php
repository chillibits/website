<?php
    // Build tool for the ChilliBits website

    // Constants
    $languages = ["en", "de", "fr", "es"];
    $file_exceptions = ["build.php", "strings.json"];
    $strings_file = "strings.json";
    
    // Loop through every language
    foreach($languages as $lang) {
        // If directory already exists, clear it
        if(file_exists("../$lang") && is_dir("../$lang")) clearContentsOfDir("../$lang");
        // Copy all necessary files to the directory
        recursiveCopy(".", "../$lang", $file_exceptions);
        // Replace strings with the translation
        foreach(rglob("../$lang/*.html") as $file) replaceStrings($file, $lang);
        foreach(rglob("../$lang/*.php") as $file) replaceStrings($file, $lang);
        // Crate different instances of the sitemap
        replaceSitemapValues($lang);
    }
    exit;

    // --------------------- Functions ----------------------

    function clearContentsOfDir($path) {
        foreach(glob("$path/*") as $file) {
            if(is_dir($file)) clearContentsOfDir($file);
            unlink($file);
        }
        return;
    }

    function recursiveCopy($src, $dst, $file_exceptions) {
        $dir = opendir($src);
        @mkdir($dst);
        while($file = readdir($dir)) {
            if ($file != '.' && $file != '..' && !in_array($file, $file_exceptions)) {
                if(is_dir($src.'/'.$file)) {
                    recursiveCopy($src.'/'.$file, $dst.'/'.$file, $file_exceptions);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }

    function replaceStrings($file, $lang) {
        global $strings_file;
    
        // Decode string.json file
        $content = file_get_contents($strings_file);
        $json = json_decode($content, true);
        $json = $json[$lang];

        // Load current file
        $html_code = file_get_contents("../$lang/$file");

        // Replace html lang tag
        $html_code = str_replace('lang="en"', 'lang="'.$lang.'"', $html_code);

        // Replace lang placeholders
        $html_code = str_replace('[lang]', $lang, $html_code);
        $html_code = str_replace('[LANG]', strtoupper($lang), $html_code);

        // Replace string occurences
        foreach($json as $key => $value) $html_code = str_replace("str_$key", $value, $html_code);

        // Save current file
        file_put_contents($file, $html_code);
    }

    function replaceSitemapValues($lang) {
        $content = file_get_contents("../$lang/sitemap.xml");

        // Replace language code
        $content = str_replace("<lang>", $lang, $content);
        // Set current datetime as last modified
        $content = str_replace("<mod>", date('Y-m-d\TH:i:s')."+01:00", $content);

        file_put_contents("../$lang/sitemap.xml", $content);
    }

    function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags); 
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
?>
build successful