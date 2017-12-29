<?php
	$date = new DateTime("2018-01-02 07:00:00");
	var_dump($date);
	var_dump($date->modify('+1 day'));
?>