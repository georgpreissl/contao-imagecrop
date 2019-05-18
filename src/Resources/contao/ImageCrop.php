<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

namespace GeorgPreissl\IC;



class ImageCrop extends \Backend
{

	public function croppingImage(DataContainer $dc)
	{

		// crop-form has been submitted, so lets crop the image

		if (strlen($this->Input->get('token')) && $this->Input->get('token') == $this->Session->get('tl_imagecrop'))
		{			

			$strW = $this->Input->get('new_locw');
			$strH = $this->Input->get('new_loch');
			$strX = $this->Input->get('new_locx');
			$strY = $this->Input->get('new_locy');

			$strPath = TL_ROOT.'/'.$this->Input->get('id');

			// Function to check file type: JPEG, GIF and PNG support
			function minimime($fname) {
			    $fh=fopen($fname,'rb');
			    if ($fh) {
			        $bytes6=fread($fh,6);
			        fclose($fh);
			        if ($bytes6===false) return false;
			        if (substr($bytes6,0,3)=="\xff\xd8\xff") return 'image/jpeg';
			        if ($bytes6=="\x89PNG\x0d\x0a") return 'image/png';
			        if ($bytes6=="GIF87a" || $bytes6=="GIF89a") return 'image/gif';
			        return 'application/octet-stream';
			    }
			    return false;
			}
			$ext = minimime($strPath);

			// Create two variables of the type resource
			// resource -> a special type of variable, holding a reference to an external resource

			// Create a new image resource identifier (the source image)
			if($ext == 'image/png'){
				$resImgSrc = imagecreatefrompng($strPath);
			} else if($ext == 'image/gif'){
				$resImgSrc = imagecreatefromgif($strPath);
			} else if($ext == 'image/jpeg'){
				$resImgSrc = imagecreatefromjpeg($strPath);
			}
 			
 			// Create a true color image resource identifier (the destination image)
			$resImgDest = imagecreatetruecolor($strW, $strH);
 

 			// Copy part of the source image
			imagecopy($resImgDest, $resImgSrc, 0, 0, $strX, $strY, $strW, $strH);


			$objFile = new File($this->Input->get('id'));
			
			$error = false;
			if ($this->Input->get('UpdateImage') == $GLOBALS['TL_LANG']['MSC']['imagecropUpdateImage'])
			{
				// overwrite the image

				// Get the cached thumbnail to destroy it
				$_height = ($objFile->height < 70) ? $objFile->height : 70;
				$_width = (($objFile->width * $_height / $objFile->height) > 400) ? 90 : '';
				$thumbnail = $this->getImage($this->Input->get('id'),$_width, $_height);
				
				$strCacheName = 'system/html/' . $objFile->filename . '-' . substr(md5('-w' . $objFile->width . '-h' . $objFile->height . '-' .urldecode($this->Input->get('id'))), 0, 8) . '.' . $objFile->extension;
				header ('Content-Type: ' . $ext);
				if($ext == 'image/png'){
					imagepng($resImgDest, TL_ROOT.'/'.$this->Input->get('id'));
				} else if($ext == 'image/gif'){
					imagegif($resImgDest, TL_ROOT.'/'.$this->Input->get('id'));
				} else if($ext == 'image/jpeg'){
					imagejpeg($resImgDest, TL_ROOT.'/'.$this->Input->get('id'));
				}
				imagedestroy($resImgDest);
				$thumbnail = new File($thumbnail);
				$thumbnail->delete();

			} else {
			
				// create a copy of the image
				$strNewPath = $objFile->dirname.'/'.$objFile->filename.'_croppedCopy_'.time().'.'.$objFile->extension;
				
				// create a JPEG file from the image resource 
				header ('Content-Type: ' . $ext);
				if($ext == 'image/png'){
					imagepng($resImgDest, $strNewPath);
				} else if($ext == 'image/gif'){
					imagegif($resImgDest, $strNewPath);
				} else if($ext == 'image/jpeg'){
					imagejpeg($resImgDest, $strNewPath);
				}

				// frees any memory associated with the image resource
				imagedestroy($resImgDest);
				
			}

			if (!$error) 
			{
				$this->log('Image file "'.$this->Input->get('id').'" has been cropped', 'tl_image_cropper imageCrop()', TL_FILES);
			}

			if (version_compare(VERSION, '3.5', '>')) {
				// funktioniert unter 4.4
				$this->redirect('contao?do=files');
			} else {
				// funktioniert unter 3.5
				$this->redirect($this->Environment->script.'?do=files');
			}
			
		}

		// Setup the form

		// create the token (e.g. '8d7cfa67389c2df17e192965f7121793')
		$strToken = md5(uniqid('', true));

		$this->Session->set('tl_imagecrop', $strToken);

		// get the predefined ratios and sizes
		$arrSettingSizes = $GLOBALS['TL_CONFIG']['useImagecropSizes'] ? deserialize($GLOBALS['TL_CONFIG']['imagecropSizes'],true) : array();
		$arrSettingARs = $GLOBALS['TL_CONFIG']['useImagecropARs'] ? deserialize($GLOBALS['TL_CONFIG']['imagecropAspectRatios'],true) : array();

		if (TL_MODE == 'BE')
		{
			var_dump("jo");
			$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/imagecrop/html/scripts/cropper.min.js'; 
			$GLOBALS['TL_CSS'][] = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; 
			$GLOBALS['TL_CSS'][] = 'system/modules/imagecrop/html/css/bootstrap.css'; 
			$GLOBALS['TL_CSS'][] = 'system/modules/imagecrop/html/css/cropper.css'; 

			$this->Template = new BackendTemplate('be_imagecrop');
			$this->Template->back = $this->Environment->base . preg_replace('/&(amp;)?(id|key|submit|imagecrop|token)=[^&]*/', '', $this->Environment->request);
			$this->Template->imageSrc = $dc->id;
			$this->Template->inputDo = $this->Input->get('do');
			$this->Template->inputKey = $this->Input->get('key');
			$this->Template->inputId = $this->Input->get('id');
			$this->Template->token = $strToken;
			$this->Template->messages = $this->getMessages();
			$this->Template->settingARs = $arrSettingARs;
			$this->Template->settingSizes = $arrSettingSizes;



			if (version_compare(VERSION, '3.5', '>')) {
				// funktioniert unter 4.4
				$this->Template->formAction = "contao?";
			} else {
				// funktioniert unter 3.5
				$this->Template->formAction = ampersand($this->Environment->script, ENCODE_AMPERSANDS);
			}


			$strHtml .= $this->Template->parse();
		}

		return $strHtml;

	}
	
}


?>
