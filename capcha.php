<?php
	/* Generar imagen CAPCHA */

	/* Star Sesion */
	session_start();

	/* Characters to use */
	$permitted_chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/*
		Generate random string
		$input = Characters to use
		$strength = number of characters
	*/
	function generate_string($input, $strength = 5) {
		$input_length = strlen($input);
		$random_string = '';
		for($i = 0; $i < $strength; $i++) {
			$random_character = $input[mt_rand(0, $input_length - 1)];
			$random_string .= $random_character;
		}	
		return $random_string;
	}
	
	/* opens a buffer in which all output is stored */

	ob_start();
		/* Creates an image */

		$image = imagecreatetruecolor(200, 50);
		imageantialias($image, true);

		$colors = [];

		/* Generate a random color */

		$v_c = 25;//variable color
		$red = rand($v_c , 255 - $v_c );
		$green =  rand($v_c , 255 - $v_c );
		$blue =  rand($v_c , 255 - $v_c );


		/* Generate variations to the color */
		for($i = 0; $i < $v_c; $i++) {
			$r = rand(-$v_c ,$v_c );
			$g = rand(-$v_c ,$v_c );
			$b =rand(-$v_c ,$v_c );
			$colors[] = imagecolorallocate($image, $red+$r , $green+$g, $blue+$b);
		}

		/* Add the background to the image */
		imagefill($image, 0, 0, $colors[0]);

		/* Add random bars of collor to the image */
		for($i = 0; $i < 10; $i++) {
			imagesetthickness($image, rand(2, 10));
			$rect_color = $colors[rand(1, 4)];
			imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
		}

		/* Colors for the text */
		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$textcolors = [$black, $white];
		
		/* Get the folder of the images */
		$font_url = getcwd().trim('/./fonts/ ');
		
		/* add the name og the fonts */
		$font_name='noah-regular.ttf';
		$font= $font_url.$font_name;

		$font_name_2='roboto-regular.ttf';
		$font_2= $font_url.$font_name_2;

		$font_name_3='noah-bold.ttf';
		$font_3= $font_url.$font_name_3;

		$font_name_4='lulocleanone-bold.ttf';
		$font_4= $font_url.$font_name_4;		

		/* $string_length = number of characters six for this example */
		$string_length = 6;
		$captcha_string = generate_string($permitted_chars, $string_length);

		if(is_file($font)){
			$fonts = [$font, $font_4, $font_3, $font_4];
			for($i = 0; $i < $string_length; $i++) {
				$letter_space = 170/$string_length;
				$initial = 15;
			
				imagettftext($image, 20, rand(-15, 15), $initial + $i*$letter_space, rand(20, 40), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
			}
		}
		/* Create the image and delete it */
		imagepng($image);
		imagedestroy($image);
		
	/* Close the buffer */
	$buffer = ob_get_clean();
	
	/* set the file as imf */
	header('Content-type: image/png');

	/* Add the string to the session */

	$_SESSION['captcha_text'] = $captcha_string;

	/* show the code */
	echo( $buffer );
?>