<?php
$url = $_GET['url'];
if(in_array($url, [
	'https://ponyfiction.org/',
], true)) {
	header('Location: '.$url);
} else {
	header('Content-Type: text/plain');
	echo 'Illegal URL';
}
?>
