<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: MIT

//Crop borders thus making 320x200 images from 384x272

function uncrop($inName, $outName, &$error)
{
	if (filesize($inName) < 1)
	{
		$error = 'Input file is empty';
		return 0;
	}

	$temp = getimagesize($inName);
	
	$wid = 1*$temp[0];
	$hei = 1*$temp[1];
	
	if ($wid != 384 or $hei != 272)
	{
		$error = 'Image should be 384x272';
		return 0;
	}		
		
	$im = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name']));
	if (!$im)
	{
		$error = 'Image cannot be read. Is this PNG or GIF?';
		return 0;
	}
	
	$im2 = imagecreatetruecolor(320, 200);

	imagecopy($im2, $im, 0,0, 32,35, 320, 200);
	imagedestroy($im);
	
	imagepng($im2, $outName);
	return 1;
}