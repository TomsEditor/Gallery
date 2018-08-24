<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: MIT

//Add borders thus making 320x200 images into 384x272

function uncrop($inName, $outName, &$error)
{
	if (filesize($inName) < 1)
	{
		$error = 'Uploaded file is empty';
		return 0;
	}

	$temp = getimagesize($inName);
	
	$wid = 1*$temp[0];
	$hei = 1*$temp[1];
	
	if ($wid != 320 or $hei != 200)
	{
		$error = 'Image should be 320x200';
		return 0;
	}

	$im = imagecreatefromstring(file_get_contents($inName));
	if (!$im)
	{
		$error = 'Image cannot be read. Is this PNG or GIF?';
		return 0;
	}

	$im2 = imagecreatetruecolor(384, 272);

	imagecopy($im2, $im, 32,35, 0,0, 320, 200);
	imagedestroy($im);
	
	imagepng($im2, $outName);
	return 1;	
}