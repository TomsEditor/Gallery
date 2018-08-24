<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: MIT

//Blend 2 frames of interlaced image into 1

function blend($inName1, $inName2, $outName, &$error)
{
	if (filesize($inName1) < 1)
	{
		$error = 'Input file #1 is empty';
		return 0;
	}
	if (filesize($inName2) < 1)
	{
		$error = 'Input file #2 is empty';
		return 0;
	}

	$temp = getimagesize($inName1);
	
	$wid = 1*$temp[0];
	$hei = 1*$temp[1];
	
	if ($wid > 800 or $hei > 800)
	{
		$error = 'Image #1 is too big. Max supported 800x800';
		return 0;
	}

	$temp = getimagesize($inName2);
	
	$wid2 = 1*$temp[0];
	$hei2 = 1*$temp[1];
	
	if ($wid != $wid2 or $hei != $hei2)
	{
		$error = 'Image #2 has different resolution than image #1';
		return 0;
	}

	$im = imagecreatefromstring(file_get_contents($inName1));
	if (!$im)
	{
		$error = 'Image #1 cannot be read. Is this PNG or GIF?';
		return 0;
	}

	$im2 = imagecreatefromstring(file_get_contents($inName2));
	if (!$im2)
	{
		$error = 'Image #2 cannot be read. Is this PNG or GIF?';
		return 0;
	}

	imagecopymerge($im2, $im, 0,0, 0,0, $wid, $hei, 50);
	imagedestroy($im);
	
	imagepng($im2, $outName);
	return 1;
}