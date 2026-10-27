<?php
/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

/**
ERROR UPLOAD
0 - File Upload Finished
1 - File Error size ini
2 - UPLOAD_ERR_FORM_SIZE
3 - UPLOAD_ERR_PARTIAL
4 - UPLOAD_ERR_NO_FILE (file not upload)
11 - File not allow
12 - File forbid
13 - File copy Error
14 - File Error size class
15 - Error array $_FILES or filesize > post_max_size
16 - File is not a valid image
*/

class UploadFile{

    /* File parametr from $_FILES */
    public $name = null;
    public $tmp_name = null;
    public $type = null;
    public $size = null;
    public $error = null;
    
    public $uploaded_real_name_file = "";

    /*Upload Dir*/
    public $dir = ".";
    public $new_dir_access = 0777;

    /*Config*/
    public $auto_rename_file = 1;
    public $auto_create_dir = 1;
    public $file_upload_ok = 0;
    public $file_name_md5 = 1;
    public $file_name_filter = 0;
    public $check_image = 0;

    /*install allow or forbid files ext*/
    public $allow_file = array();
    public $forbid_file = array('php','php2','php3','php4','php5','php6','php7','php8','phtml','pht','phar','phps','shtml','shtm','cgi','pl','py','sh','asp','aspx','jsp','js','html','htm','xhtml','htaccess','htpasswd');
    /*executable ext forbidden in any part of the name (file.php.jpg)*/
    public $forbid_exec_file = array('php','php2','php3','php4','php5','php6','php7','php8','phtml','pht','phar','phps','shtml','cgi');

    /*set upload max file size (kb)*/
    public $maxSizeFile = 0;

    /**
    * constructor
    * @param $file - $_FILES
    */
    function __construct($file){
        if (!is_array($file)){
            $this->error = 15;
            return 0;    
        }
        $this->name = $file['name'];
        $this->tmp_name = $file['tmp_name'];
        $this->type = $file['type'];
        $this->size = $file['size'];
        $this->error = $file['error'];
    }

    function setName($name){
        $this->name = $name;
    }

    function getName(){
        return $this->name;
    }
    
    function setDir($val){
        $this->dir = $val;
    }

    function getDir(){
        return $this->dir;
    }
    
    function setAutoRenameFile($val){
        $this->auto_rename_file = $val;
    }
    
    function setNameWithoutExt($name){
        $tmp = $this->parseNameFile($this->name);
        if ($tmp['ext']!='') $ext = ".".$tmp['ext']; else $ext = "";
        $this->name = $name.$ext;
    }

    /**
    * $size int - max size upload file in (Kb)
    */
    function setMaxSizeFile($size){
        $this->maxSizeFile = $size;
    }
    
    /**
    * set to md5 name file
    */
    function setFileNameMd5($val){
        $this->file_name_md5=$val;
    }
    
    /**
    * set filter name (enable, disable)
    */    
    function setFilterName($val){
        $this->file_name_filter = $val;
    }

    /**
    * check that the file is a real image of the type given by its extension
    */
    function setCheckImage($val){
        $this->check_image = $val;
    }

    /**
    * set array allow file upload
    */
    function setAllowFile($file){
        $this->allow_file = array_map('strtolower', $file);
        $this->forbid_file = array();
    }
    
    /**
    * set array forbid file upload
    */
    function setForbidFile($file){
        $this->forbid_file = array_map('strtolower',$file);
        $this->allow_file = array();
    }
    
    /**
    * after upload
    */
    function getError(){
        return $this->error;
    }

    /**
    * @param string name file
    * @return array("name","ext","dir")
    */
    function parseNameFile($name){
        $pathinfo=pathinfo($name);
        $ext=$pathinfo['extension'] ?? '';
        $name=$pathinfo['basename'];
        $dir=$pathinfo['dirname'] ?? '';
        if ($ext!="") $b_name=substr($name,0,strlen($name)-strlen($ext)-1); else $b_name=$name;
    return array('name'=>$b_name, "ext"=>$ext, "dir"=>$dir);
    }
        
    /**
    * rename file md5 name
    */
    function renameFileMd5($name){
        $m=$this->parseNameFile($name);
		$m['name']=md5(time().$m['name']);
        if ($m['ext']!="") $m['ext']='.'.$m['ext'];
        $name=$m['name'].$m['ext'];
    return $name;
    }

    /**
    * rename existented file
    */
    function renameExistingFile($dir, $name){
        if (is_file($dir."/".$name)) {
            $m=$this->parseNameFile($name);
            if ($m['ext']!="") $m['ext']='.'.$m['ext'];
            $i=1;
            $name=$m['name'].$i.$m['ext'];
            while (is_file($dir."/".$name)){
                $name=$m['name'].$i.$m['ext'];
                $i++;
            }
        }
    return $name;
    }
    
    /**
    * rename file from filter
    */
    function renameFileFilter($name){
        $name = strtr($name, array('ü'=>'u','ä'=>'a','ö'=>'o','Ü'=>'U','Ä'=>'A','Ö'=>'O','ß'=>'ss','а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>'','і'=>'i','є'=>'e','ї'=>'y'));
        $name = preg_replace("/[^a-zA-Z0-9\.\-]/", "_", $name);
    return $name;
    }

    /**
    * get test file allow
    */
    function getTestFileAllow(){
        $this->name = rtrim((string)$this->name, ". ");
        if ($this->name === '' || substr($this->name, 0, 1) == '.'){
            $this->error=12;
            return 0;
        }
        $mas=pathinfo($this->name);
        $ext=strtolower($mas['extension'] ?? '');

        if (count($this->allow_file)>0){
             if (!in_array($ext,$this->allow_file)) {
                 $this->error=11;
                 return 0;
             }
        }

        if (count($this->forbid_file)>0){
             if (in_array($ext,$this->forbid_file)) {
                 $this->error=12;
                 return 0;
             }
        }

        $parts = array_map('strtolower', array_slice(explode('.', $this->name), 1));
        if (count(array_intersect($parts, $this->forbid_exec_file))>0){
            $this->error=12;
            return 0;
        }

        if ($this->check_image && !$this->isValidImage($ext)){
            $this->error=16;
            return 0;
        }

        if ($this->maxSizeFile!=0 && $this->size > $this->maxSizeFile*1024){
             $this->error=14;
            return 0;
        }

    return 1;
    }

    /**
    * image content must match the extension and must not contain php code
    */
    function isValidImage($ext){
        if (!is_file((string)$this->tmp_name)){
            return 0;
        }
        $types = array('jpg'=>IMAGETYPE_JPEG, 'jpeg'=>IMAGETYPE_JPEG, 'jpe'=>IMAGETYPE_JPEG, 'gif'=>IMAGETYPE_GIF, 'png'=>IMAGETYPE_PNG, 'webp'=>IMAGETYPE_WEBP, 'bmp'=>IMAGETYPE_BMP);
        if ($ext == 'avif'){
            $info = @getimagesize($this->tmp_name);
            if (!(defined('IMAGETYPE_AVIF') && $info && $info[2] == IMAGETYPE_AVIF)){
                $head = (string)file_get_contents($this->tmp_name, false, null, 4, 8);
                if (substr($head, 0, 4) != 'ftyp' || !in_array(substr($head, 4, 4), array('avif', 'avis'))){
                    return 0;
                }
            }
        }else{
            if (!isset($types[$ext])){
                return 0;
            }
            $info = @getimagesize($this->tmp_name);
            if (!$info || $info[2] != $types[$ext]){
                return 0;
            }
        }
        $fp = fopen($this->tmp_name, 'rb');
        if (!$fp){
            return 0;
        }
        $prev = '';
        while(!feof($fp)){
            $data = $prev.fread($fp, 131072);
            if (stripos($data, '<?php') !== false || stripos($data, '__HALT_COMPILER') !== false){
                fclose($fp);
                return 0;
            }
            $prev = substr($data, -16);
        }
        fclose($fp);
    return 1;
    }

    /**
    * start upload
    */
    function upload(){
        if ($this->error!==0) return 0;
        if (!$this->getTestFileAllow()) return 0;
        if ($this->auto_create_dir && !is_dir($this->dir)) mkdir($this->dir, $this->new_dir_access);
        if ($this->file_name_md5) $this->name = $this->renameFileMd5($this->name);
        if ($this->file_name_filter) $this->name = $this->renameFileFilter($this->name);
        if ($this->auto_rename_file) $this->name = $this->renameExistingFile($this->dir, $this->name);
        $this->uploaded_real_name_file = $this->name;
        if (move_uploaded_file($this->tmp_name, $this->dir."/".$this->name)) {
            $this->file_upload_ok=1;
            return 1;
        }else{
            $this->file_upload_ok=0;
            $this->error=13;
            return 0;
        }
    }
	
	static function generateArrayFilesFromMultipleUpload($files) {
		if (!isset($files['tmp_name']) || !is_array($files['tmp_name'])) {
			return [];
		}
		$res = [];
		foreach($files['tmp_name'] as $k => $v) {
			$res[$k] = [
				'name' => $files['name'][$k],
				'type' => $files['type'][$k],
				'tmp_name' => $files['tmp_name'][$k],
				'error' => $files['error'][$k],
				'size' => $files['size'][$k]
			];
		}
		return $res;
	}

}

class UploadImage extends UploadFile{
    public $name_image = "";
    public $dir_image = "";
    public $quality = 85;
    public $prefix = "thumb_";

    function copyImage($width=120, $height=0){
        if (!$this->file_upload_ok) return 0;

        $this->name_image=$this->prefix.$this->name;
        if (!$this->dir_image) $this->dir_image = $this->dir;
        if ($this->auto_create_dir && !is_dir($this->dir_image))  mkdir($this->dir_image,$this->new_dir_access);
        if ($this->file_name_md5) $this->name_image = $this->renameFileMd5($this->name_image);
        if ($this->file_name_filter) $this->name = $this->renameFileFilter($this->name);
        if ($this->auto_rename_file) $this->name_image=$this->renameExistingFile($this->dir_image, $this->name_image);            
        return $this->resizeImage($this->dir."/".$this->uploaded_real_name_file, $width ,$height, $this->dir_image."/".$this->name_image, $this->quality);        
    }

    function setQuality($quality){
        $this->quality=$quality;
    }

    function getNameImage(){
        return $this->name_image;
    }
    
    function setDirImage($val){
         $this->dir_image = $val;
    }
    
    function getDirImage(){
        return $this->dir_image;
    }
    
    function setPrefixImage($val){
        $this->prefix = $val;
    }
    
    function getPrefixImage(){
        return $this->prefix;
    }
    
    function resizeImage($image, $nw=0, $nh=0, $img_to="", $quality=85){
        $path=pathinfo($image);
        $ext=$path['extension'];
        $ext=strtolower($ext);

        if (($ext=="jpg")or($ext=="jpeg")) 
            $si=imagecreatefromjpeg($image);
        elseif ($ext=="gif") 
            $si=imagecreatefromgif($image);
        elseif ($ext=="png") 
            $si=imagecreatefrompng($image);
        elseif ($ext=="webp") 
            $si=imagecreatefromwebp($image);
        else
            return 0;
        
        if (!$si) return 0;

        $sw=imagesx($si);
        $sh=imagesy($si);
        if ($nw==0 && $nh==0) $nw=$sw;
        if ($nh==0) $nh=(int)(($nw/$sw)*$sh);
        if ($nw==0) $nw=(int)(($nh/$sh)*$sw);
        $dim=imagecreatetruecolor($nw,$nh);
        if ($ext=="png") imagefilledrectangle($dim,0,0,$nw,$nh,0xFFFFFF);
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

}