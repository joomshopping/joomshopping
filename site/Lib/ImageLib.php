<?php
/**
* @version      5.1.0 15.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

class ImageLib{
	
	/**
	* Create image from text (for captcha) 
	* @param int widht
	* @param int height	
	* @param string text
	* @param string Font (Arial)
    * @param int fontsize
    * @param string if not empty then save to file        
	* @param int randomline    
	*/
	static function createImageTextKod($width, $height, $text, $TTFFile, $fontsize=16, $file=null,$randomline=2){
		$img = imagecreate($width,$height);
		$col = imagecolorallocate($img,255,255,255);

		$line = imagecolorallocate($img,220,220,220);
		for($i=5;$i<$width;$i=$i+$fontsize)  imageline($img,$i,0,$i,$height,$line);
		for($i=5;$i<$height;$i=$i+$fontsize) imageline($img,0,$i,$width,$i,$line);
		
		$c[0] = imagecolorallocate($img,20,50,235);
		$c[1] = imagecolorallocate($img,20,220,23);
		$c[2] = imagecolorallocate($img,220,20,23);
		$c[3] = imagecolorallocate($img,255,120,10);
		$c[4] = imagecolorallocate($img,120,120,140);
        $c[5] = imagecolorallocate($img,20,130,140);
        $c[6] = imagecolorallocate($img,120,130,40);
        
        srand(time());
                
        for($i=0;$i<$randomline;$i++){
            $color = $c[rand(0,6)];
            $y1=rand(0,$height);
            $y2=rand(0,$height);
            imageline($img,0,$y1,$width,$y2,$color);
        }
        
		for($i=0;$i<strlen($text);$i++){
			$symb = substr($text,$i,1);			
            $color = $c[rand(0,6)];
			$angle = rand(-15,15);
			$y = $fontsize+intval($fontsize/2)+intval($fontsize/5)+rand(-intval($fontsize/2), intval($fontsize/2))+1;
            $x = $fontsize*$i+intval($fontsize/2)+1;
	        imagettftext ($img, $fontsize, $angle, $x, $y, $color, $TTFFile, $symb);
		}
        
		imagejpeg($img, $file, 90);
	}
    
    /**
    * generata kod and save in SESSION
    * 
    * @param mixed $symbol
    * @param mixed $securityText
    * @return string kod
    */
    static function genereteCapchaKod($symbol=6, $securityText='0000450000'){
        if ($symbol>32) die('Error genereteCapchaKod');
        $kod = substr(md5(time().$securityText),0,$symbol);
        $_SESSION['image_lib_captha_kod_1'] = $kod;
    return $kod;    
    }
    
    /**
    * test kod generata function genereteCapchaKod
    * 
    * @param mixed $kod
    */
    static function getCapchaKodTest($kod){
        return $_SESSION['image_lib_captha_kod_1']==$kod;
    }

	/**
	* Simple Resize Image 
	* @param string path file
	* @param int width
	* @param int height
	* @param string save to file (if empty - print image)
	* @param int quality (0,100)		
	*/	
	static function resizeImage($image, $nw=0, $nh=0, $img_to="", $quality=85){
        $path = pathinfo($image);
        $ext = $path['extension'];
        $ext = strtolower($ext);

        if ( ($ext=="jpg") or ($ext=="jpeg") ){
            $si=imagecreatefromjpeg($image);
        }elseif ($ext=="gif"){
            $si=imagecreatefromgif($image);
        }elseif ($ext=="png"){ 
            $si=imagecreatefrompng($image);
        }elseif ($ext=="webp"){
            $si=imagecreatefromwebp($image);
        }else{
            return 0;
        }
        
        if (!$si) return 0;    

        $sw = imagesx($si);
        $sh = imagesy($si);
        if ($nw==0 && $nh==0) $nw = $sw;
        if ($nh==0) $nh = (int)(($nw/$sw)*$sh);
        if ($nw==0) $nw = (int)(($nh/$sh)*$sw);
        $dim = imagecreatetruecolor($nw,$nh);
        
        if ($ext=="png"){
            imagealphablending($dim, false);
            imagesavealpha($dim, true);
        }
        if ($ext=="gif"){
            $trnprt_color = imagecolorallocatealpha($dim, 255, 255, 255, 127);
            imagefill($dim, 0, 0, $trnprt_color);
            imagecolortransparent($dim, $trnprt_color);
            imagetruecolortopalette($dim, true, 256);
        }

        imagecopyresampled($dim,$si,0,0,0,0,$nw,$nh,$sw,$sh);

        switch($ext){
            case 'jpg':
            case 'jpeg':
                imagejpeg($dim, $img_to, $quality);
            break;
            case 'webp':
                imagewebp($dim, $img_to, $quality);
            break;
            case 'gif':
                if ($img_to)
                    imagegif($dim, $img_to);
                else
                    imagegif($dim);
            break;
            case 'png':
                if (phpversion()>='5.1.2'){
                    imagepng($dim, $img_to, 10-max(intval($quality/10),1));
                }else{
                    imagepng($dim, $img_to);
                }
            break;
            default:
                return 0;
            break;
        }

        imagedestroy($si);
        imagedestroy($dim);

    return 1;
    }

	/**
	* Resize image Magic
	* @param string path image
	* @param int width
	* @param int height
	* @param int (0 - show full foto, 1 - cut foto )
	* @param int (2 - fill $color or fill transparent, 1 - fill $color, 0 - not fill)
	* @param string save to file (if empty - print image)
	* @param int quality (0,100)
	* @param int $color_fill (0xffffff - white)
    * @param int interlace - enable / disable	
	*/
	static function resizeImageMagic($img, $w, $h, $thumb_flag = 0, $fill_flag = 1, $name = "", $qty = 85, $color_fill = 0xffffff, $interlace = 1){
        if ((int)ini_get("memory_limit")<120){
            ini_set("memory_limit", "120M");
        }
		
		self::imageFixOrientation($img);
		
		$new_w = $w;
		$new_h = $h;
		$path = pathinfo($img); 
        $ext = $path['extension']; 
        $ext = strtolower($ext);
		
		$imagedata = @getimagesize($img);

		$img_w = $imagedata[0];
		$img_h = $imagedata[1];

		if (!$img_w && !$img_h) return 0;

		if (!$w){
			$w = $new2_w = $h * ($img_w/$img_h);
			$new2_h = $h;
		}elseif (!$h){
			$h = $new2_h = $w * ($img_h/$img_w);
			$new2_w = $w;
		}else{
            
			if ($img_h*($new_w/$img_w) > $new_h){
				$new2_w=$img_w*$new_h/$img_h;
				$new2_h=$new_h;
			}else{
				$new2_w=$new_w;
				$new2_h=$img_h*$new_w/$img_w;
			}

			if ($thumb_flag){
				if ($img_h*($new_w/$img_w) < $new_h){
					$new2_w = $img_w*$new_h/$img_h;
					$new2_h = $new_h;
				}else{
					$new2_w = $new_w;
					$new2_h = $img_h*$new_w/$img_w;
				}
			}
            
            if (!$thumb_flag && !$fill_flag){
                $new2_w = $w;
                $new2_h = $h;
            }
		}
        
        if ( ($ext=="jpg") or ($ext=="jpeg") ){
            $image = imagecreatefromjpeg($img);
        }elseif ($ext=="gif"){
            $image = imagecreatefromgif($img);
        }elseif ($ext=="webp"){
            $image = imagecreatefromwebp($img);
        }elseif ($ext=="png"){
            $image = imagecreatefrompng($img);
        }else{
            return 0;
        }

		$thumb = imagecreatetruecolor((int)$w, (int)$h);
        
        if ($fill_flag){
            if ($fill_flag==2){
                if ($ext=="png"){
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                    $trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                    imagefill($thumb, 0, 0, $trnprt_color);
                }elseif($ext=="gif"){
                    $trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                    imagefill($thumb, 0, 0, $trnprt_color);
                    imagecolortransparent($thumb, $trnprt_color);
                    imagetruecolortopalette($thumb, true, 256);
                }else{
                    imagefill($thumb, 0, 0, $color_fill);
                }
            }else{
		        imagefill($thumb, 0, 0, $color_fill);
            }
        }

		if ($thumb_flag){
			imagecopyresampled ($thumb, $image, intval(($w-$new2_w)/2), intval(($h-$new2_h)/2), 0, 0, intval($new2_w), intval($new2_h), $imagedata[0], $imagedata[1]);            
		}elseif ($fill_flag){
	        if ($new2_w<$w) imagecopyresampled ($thumb, $image, intval(($w-$new2_w)/2), 0, 0, 0, intval($new2_w), intval($new2_h), $imagedata[0], $imagedata[1]);
            if ($new2_h<$h) imagecopyresampled ($thumb, $image, 0, intval(($h-$new2_h)/2), 0, 0, intval($new2_w), intval($new2_h), $imagedata[0], $imagedata[1]);
            if ($new2_w==$w && $new2_h==$h) imagecopyresampled ($thumb, $image, 0, 0, 0, 0, intval($new2_w), intval($new2_h), $imagedata[0], $imagedata[1]);            
		}else{            
            $thumb = @imagecreatetruecolor(intval($new2_w), intval($new2_h));
            if ($ext=="png"){
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }
            if ($ext=="gif"){
                $trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                imagefill($thumb, 0, 0, $trnprt_color);
                imagecolortransparent($thumb, $trnprt_color);
                imagetruecolortopalette($thumb, true, 256);
            }
            imagecopyresampled ($thumb, $image, 0, 0, 0, 0, intval($new2_w), intval($new2_h), $imagedata[0], $imagedata[1]);
		}

        if ($interlace){
		    imageinterlace($thumb, 1);
        }
	
		if ($ext=="png") {
            imagepng($thumb, $name, 10-max(intval($qty/10),1));
        }
		if ($ext=="gif"){
            if ($name)    
                imagegif($thumb, $name);
            else
                imagegif($thumb);    
        }		
        if (($ext=="jpg")or($ext=="jpeg")) {
			imagejpeg($thumb, $name, $qty);
		}
		if ($ext=="webp") {
			imagewebp($thumb, $name, $qty);
		}
		
		return 1;	
	}
	
	static function imageFixOrientation($img) {
		if (!function_exists('exif_read_data')) {
			return 0;
		}
        $exif = @exif_read_data($img);
        if (isset($exif['Orientation']) && ($exif['Orientation']==3 OR $exif['Orientation']==6 OR $exif['Orientation']==8)) {
			$path = pathinfo($img); 
			$ext = $path['extension']; 
			$ext = strtolower($ext);
			if ($ext=="jpg" or $ext=="jpeg") {
				$imageResource = imagecreatefromjpeg($img); 
				switch ($exif['Orientation']) {
					case 3:
					$image = imagerotate($imageResource, 180, 0);
					break;
					case 6:
					$image = imagerotate($imageResource, -90, 0);
					break;
					case 8:
					$image = imagerotate($imageResource, 90, 0);
					break;
				}
				imagejpeg($image, $img);
				imagedestroy($imageResource);
				imagedestroy($image);
				return 1;
			}
        }
		return 0;
	}
	
	/**
	* Add watermark
	* @param string  path image .jpg
	* @param string  path image .png
	* @param string save to file (if empty - print image)
	* @param int quality
	*/
	static function addWatermark($image, $watermark, $name='', $qty=80){
		$watermark = imagecreatefrompng($watermark);
    	$watermark_width = imagesx($watermark);
    	$watermark_height = imagesy($watermark);
    	$size = getimagesize($image);
    	$image = imagecreatefromjpeg($image);    	
	    $dest_x = $size[0] - $watermark_width;
	    $dest_y = $size[1] - $watermark_height - 25;
	    imagecopyresampled($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);
	    imagejpeg($image,$name,$qty);
	    imagedestroy($image);
	    imagedestroy($watermark);
	return 1;    
	}

}