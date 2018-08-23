<?php

//Author: Tom/Samar
//Website: tomseditor.com/gallery
//License: MIT

@mkdir('art');

foreach (glob('png/*.png') AS $inName)
{
	$outName = 'art/' . pathinfo($inName, PATHINFO_FILENAME) . '.art';

	png2art($inName, $outName, $error);
}

function png2art($inName, $outName, &$error)
{
	if (filesize($inName) < 1)
	{
		$error = 'Input file is empty';
		return 0;
	}
	
	$temp = getimagesize($inName);
	
	if ($temp[0] != 320 or $temp[1] != 200)
	{
		$error = 'Resolution needs to be 320x200';
		return 0;
	}
	
	$im = imagecreatefromstring(file_get_contents($inName));
	if (!$im)
	{
		$error = 'Image cannot be read. Is this PNG or GIF?';
		return 0;
	}
	imagepalettetotruecolor($im);
	
	$cell = imagecreatetruecolor(8, 8);

	$pepto = array(0=>0,16777215=>1,6829867=>2,7382194=>3,7290246=>4,5803331=>5,3483769=>6,12109679=>7,
			7294757=>8,4405504=>9,10119001=>10,4473924=>11,7105644=>12,10146436=>13,7102133=>14,9803157=>15);
	
	$out = chr(0).chr(32);
	
	$palette = '';
	
	for ($y=0; $y<25; $y++)
	for ($x=0; $x<40; $x++)	
	{
		imagecopy($cell, $im, 0,0, $x*8, $y*8, 8, 8);
		
		$uniq = array();
		
		for ($j=0; $j<8; $j++)
		for ($i=0; $i<8; $i++)
		{
			$rgb = imagecolorat($cell, $i, $j);
			$uniq[$rgb] = 1; 
		}
		
		$uniq = array_keys($uniq);
		$count = count($uniq);
		
		if ($count > 2)
		{
			$error = 'More than 2 colors per cell';
			return 0;
		}
		if ($count == 1)
		{
			$uniq[] = 0;
		}
		
		for ($j=0; $j<8; $j++)
		{
			$byte = 0;
			for ($i=0; $i<8; $i++)
			{
				$rgb = imagecolorat($cell, $i, $j);
				$index = ($rgb === $uniq[0]) ? 0 : 1;

				$byte = $byte + ($index << (7-$i));
			}
			$out .= chr($byte);
		}
		
		if (isset($pepto[$uniq[0]]) and isset($pepto[$uniq[1]]) )
		{
			$color1 = $pepto[$uniq[0]];
			$color2 = $pepto[$uniq[1]];
		}
		else
		{
			$error = 'Image needs to use Pepto palette';
			return 0;
		}
		
		$palette .= chr($color1 + ($color2 << 4));
	}

	$out .= $palette.chr(0);
	
	imagedestroy($cell);
	imagedestroy($im);
	
	file_put_contents($outName, $out);
	return 1;
}