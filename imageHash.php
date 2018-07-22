<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: Public Domain

function imageHash($fname)
{
	if (!is_file($fname))
	{
		return '';
	}
	
	$im = imagecreatefrompng($fname);
	if (!$im) 
	{
		return '';
	}
	
	imagepalettetotruecolor($im);
	
	list($wid, $hei) = array(imagesx($im), imagesy($im));
	
	$buff = '';
	
	for ($y=0; $y<$hei; $y++)
	for ($x=0; $x<$wid; $x++)
	{
		$rgb = imagecolorat($im, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		$buff .= chr($r).chr($g).chr($b);
	}

	imagedestroy($im);

	return sha1($buff);
}