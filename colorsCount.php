<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: Public Domain

function colorsCount($fname)
{	
	if (!is_file($fname))
	{
		return 0;
	}
	
	$im = imagecreatefrompng($fname);
	if (!$im) 
	{
		return 0;
	}
	
	imagepalettetotruecolor($im);
	
	list($wid, $hei) = array(imagesx($im), imagesy($im));
	
	$uniq = array();
	
	for ($y=0; $y<$hei; $y++)
	for ($x=0; $x<$wid; $x++)
	{
		$rgb = imagecolorat($im, $x, $y);
		$rgb = $rgb & 0xFFFFFF;
		
		$uniq[$rgb] = 1;
	}
	
	imagedestroy($im);
	
	return count($uniq);
}