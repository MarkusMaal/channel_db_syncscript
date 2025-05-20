<?php
$whitelist = array(
    '127.0.0.1',
    '::1',
    'localhost:8000',
    'staging.markustegelane.eu'
);
$remote = !in_array($_SERVER['REMOTE_ADDR'], $whitelist) && !str_starts_with($_SERVER["REMOTE_ADDR"], "192.");
if ($remote && $_SERVER["HTTP_HOST"] == "nossl.markustegelane.eu" && !empty($_POST)) {
	die("Turvaline ühendus vajalik // Secure connection required");
}
if (date("Y-m-d") == "2024-04-01") {
	header("location: https://www.youtube.com/watch?v=56JC3zGgBQI"); die();
}
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/setup.php")) {
	try {
		include($_SERVER["DOCUMENT_ROOT"]."/setup.php");
	} catch (Throwable $e) {
		include($_SERVER["DOCUMENT_ROOT"]."/builder/conn_err.php");
		exit();
	}
} else if (file_exists($_SERVER["DOCUMENT_ROOT"]."/builder/index.php")) {
	readfile($_SERVER["DOCUMENT_ROOT"]."/admin/theme.php");
	include($_SERVER["DOCUMENT_ROOT"]."/builder/index.php");
	exit();
} else {
	die("<!doctype html><body style='font-family: sans-serif;'><b>Fatal error: </b>Missing database configuration. Either copy over setup.php from markusmaal.ee application or add the /builder directory to server root.</body>");
}
