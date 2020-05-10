<?php
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2);

    // write to logfile
	$date = str_replace(".", "_", date('Y.m.d'));
	createDirIfNotExists("./logs/logfiles_".str_replace(".", "_", date('Y.m')));
	$logfile = fopen("./logs/logfiles_".str_replace(".", "_", date('Y.m'))."/logfile_$date.txt", "a");
	$date = date('H:i:s');
	fwrite($logfile, "$date - $lang\n");
	fclose($logfile);

    if($lang == "de") {
        header("Location: ./de/");
    } else if($lang == "fr") {
        header("Location: ./fr/");
    } else if($lang == "es") {
        header("Location: ./es/");
    } else {
        // Default language
        header("Location: ./en/");
    }
    exit;

    //--------------------------------------------------------------------Funktionen--------------------------------------------------------------------

	function createDirIfNotExists($path) {
		if(!file_exists($path)) return mkdir($path, 0777, true);
		return false;
	}
?>