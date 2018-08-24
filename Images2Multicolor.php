<?php

echo 
' *******************************************
    _   _              _   _   ___   _   _
   /   / \  |\ | \  / |_  |_|   |   |_  |_|
   \_  \_/  | \|  \/  |_  | \   |   |_  | \
  
   Lossless Converter from PNG/GIF  to KLA 
            by TheTom/SAMAR            v 1.0
 *******************************************
';

// Website: http://tomseditor.com/gallery
// Github : https://github.com/TomsEditor
// License: MIT
// TODO: 
// Handle 3 colors + BG per cell instead of 3 colors + black

@mkdir('kla');

foreach (glob('png/*.png') AS $inName)
{
	$outName = 'kla/' . pathinfo($inName, PATHINFO_FILENAME) . '.kla';

	$res = png2kla($inName, $outName, $error);
	if (!$res)
	{
		echo basename($inName) . ': ' . $error . "\r\n";
	}
}

function png2kla($inName, $outName, &$error)
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

	for ($x=0; $x<320; $x+=2)
	{
		for ($y=0; $y<200; $y++)
		{
			$color1 = imagecolorat($im, $x  , $y);
			$color2 = imagecolorat($im, $x+1, $y);
			
			if ($color1 !== $color2)
			{
				$error = 'This is not a Multicolor image';
				return 0;
			}
		}
	}

	$cell = imagecreatetruecolor(8, 8);

	$pepto = array(0=>0,16777215=>1,6829867=>2,7382194=>3,7290246=>4,5803331=>5,3483769=>6,12109679=>7,
			7294757=>8,4405504=>9,10119001=>10,4473924=>11,7105644=>12,10146436=>13,7102133=>14,9803157=>15);

	$out = "\x0\x60";

	$palette = '';
	$palette2 = '';

	for ($y=0; $y<25; $y++)
	{
		for ($x=0; $x<40; $x++)	
		{
			imagecopy($cell, $im, 0,0, $x*8, $y*8, 8, 8);
			
			$uniq = array();
			
			for ($j=0; $j<8; $j++)
			{		
				for ($i=0; $i<8; $i+=2)
				{
					$rgb = imagecolorat($cell, $i, $j);
					if ($rgb !== 0) //don't count black
					{
						$uniq[$rgb] = 1;
					}
				}
	        	}

			$uniq = array_keys($uniq);
			$count = count($uniq);

			if ($count > 3)
			{
				$error = 'More than 4 colors per cell';
				return 0;
			}	
			if ($count == 0)
			{
				$uniq[] = 0;
				$uniq[] = 0;
				$uniq[] = 0;
			}
			else if ($count == 1)
			{
				$uniq[] = 0;
				$uniq[] = 0;
			}
			else if ($count == 2)
			{
				$uniq[] = 0;
			}

			for ($j=0; $j<8; $j++)
			{		
				$byte = 0;
				for ($i=0; $i<8; $i+=2)
				{
					$rgb = imagecolorat($cell, $i, $j);
					
					foreach ($uniq AS $num=>$color)
					{
						if ($rgb === 0)
						{
							$index = 0;
							break;
						}
						if ($color === $rgb)
						{
							$index = $num+1;
							break;
						}
					}

					$byte = $byte + ($index << (6 - $i));
				}
				$out .= chr($byte);
			}

			if (isset($pepto[$uniq[0]]) and 
			    isset($pepto[$uniq[1]]) and
				isset($pepto[$uniq[2]]) )
			{
				$color1 = $pepto[$uniq[0]];
				$color2 = $pepto[$uniq[1]];
				$color3 = $pepto[$uniq[2]];
			}
			else
			{
				$error = 'Image needs to use Pepto palette';
				return 0;
			}
			$palette  .= chr($color2 + ($color1 << 4));
			$palette2 .= chr($color3                 );
		}
	}

	$out .= $palette.$palette2.chr(0);

	imagedestroy($cell);	
	imagedestroy($im);

	file_put_contents($outName, $out);
	return 1;
}