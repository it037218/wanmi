<?php
if (isset($_POST['path'])) {
	$path = $_POST['path'];
	header("Access-Control-Allow-Origin:*");
	$file = file_get_contents($path,FILE_USE_INCLUDE_PATH);
	echo $file;
} else {
	echo 'no';
}