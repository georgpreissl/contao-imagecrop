<?php

namespace GeorgPreissl\IC;

use Contao\DataContainer;
use Contao\Backend;
use Contao\BackendUser;
use Contao\BackendTemplate;
use Contao\File;
use Contao\FilesModel;
use Contao\Image;

class ImageCrop extends Backend
{

	public function croppingImage(DataContainer $dc)
	{

		// crop-form has been submitted, so lets crop the image

		if (strlen($this->Input->get('token')) && $this->Input->get('token') == $this->Session->get('tl_imagecrop'))
		{			
			// get the new dimensions
			$strW = $this->Input->get('data_width');
			$strH = $this->Input->get('data_height');
			$strX = $this->Input->get('data_x');
			$strY = $this->Input->get('data_y');

			// get the submitted scale values for flipping ("1" or "-1")
			$strScaleX = $this->Input->get('scale_x');
			$strScaleY = $this->Input->get('scale_y');

			// get the submitted rotation value
			$strRotation = $this->Input->get('rotation');

			// get the path of the source file
			$strPath = $this->Input->get('id');
			// e.g. "files/theme/image-3.jpg"



			// check the file type of the given file
			function checkFileExtension($fname) {
			    $fh = fopen($fname,'rb');
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

			$strExt = checkFileExtension($strPath);
			// e.g. "image/jpeg"


			// create the source image (image resource identifier)
			if($strExt == 'image/png'){
				$resImgSrc = imagecreatefrompng(TL_ROOT.'/'.$strPath);
			} else if($strExt == 'image/gif'){
				$resImgSrc = imagecreatefromgif(TL_ROOT.'/'.$strPath);
			} else if($strExt == 'image/jpeg'){
				$resImgSrc = imagecreatefromjpeg(TL_ROOT.'/'.$strPath);
			}
 			
 
			// if cropping is not desired, leave the original dimensions
			if ($strW == "0" || $strH == "0") {
				$strW  = imagesx($resImgSrc);
				$strH = imagesy($resImgSrc);

			}

			// flip the image vertical
			if ($strScaleY == "-1") {
				imageflip($resImgSrc, IMG_FLIP_VERTICAL);
			}

			// flip the image horizontal
			if ($strScaleX == "-1") {
				imageflip($resImgSrc, IMG_FLIP_HORIZONTAL);
			}

			// rotate the image
			if ($strRotation !== "0") {
				$intRotation = intval($strRotation);
				$intRotation = 360 - $intRotation;
				$resImgSrc = imagerotate($resImgSrc, $intRotation, 0);
				// if ($intRotation == 90 || $intRotation == 270) {
				// 	$strW  = imagesx($resImgSrc);
				// 	$strH = imagesy($resImgSrc);
				// }

			}



 			// create the new image (image resource identifier)
 			// returns an image identifier representing a black image of the specified size
			$resImgNew = imagecreatetruecolor($strW, $strH);

 			// copy part of the source image into the new image
			imagecopy($resImgNew, $resImgSrc, 0, 0, $strX, $strY, $strW, $strH);



			
			$error = false;

			if ($this->Input->get('UpdateImage'))
			{
				// update the image
				
				// create a file from the image resource 
				header ('Content-Type: ' . $strExt);
				if($strExt == 'image/png'){
					imagepng($resImgNew, TL_ROOT.'/'.$strPath);
				} else if($strExt == 'image/gif'){
					imagegif($resImgNew, TL_ROOT.'/'.$strPath);
				} else if($strExt == 'image/jpeg'){
					imagejpeg($resImgNew, TL_ROOT.'/'.$strPath);
				}

				// frees any memory associated with the image
				imagedestroy($resImgNew);


			} else {
				// create a copy of the image

				// create a contao file object of the source image
				$objFile = new File($strPath);

				// put the new path together
				$strNewPath = $objFile->dirname.'/'.$objFile->filename.'_copy_'.time().'.'.$objFile->extension;
				
				// create a file from the image resource 
				header ('Content-Type: ' . $strExt);
				if($strExt == 'image/png'){
					imagepng($resImgNew, $strNewPath);
				} else if($strExt == 'image/gif'){
					imagegif($resImgNew, $strNewPath);
				} else if($strExt == 'image/jpeg'){
					imagejpeg($resImgNew, $strNewPath);
				}

				// frees any memory associated with the image resource
				imagedestroy($resImgNew);

				// create a contao file object of the new image
				if (is_file(TL_ROOT . '/' . $strNewPath))
				{
					$objFileNew = new File($strNewPath);

					$objModel = new FilesModel();
					$objModel->pid       = $strPid;
					$objModel->tstamp    = time();
					$objModel->name      = $objFileNew->name;
					$objModel->type      = 'file';
					$objModel->path      = $objFileNew->path;
					$objModel->extension = $objFileNew->extension;
					$objModel->found     = 2;
					$objModel->hash      = $objFileNew->hash;
					$objModel->uuid      = $objDatabase->getUuid();
					$objModel->save();
				}


				
			}

			if (!$error) 
			{
				$this->log('Image file "'.$this->Input->get('id').'" has been cropped', 'tl_image_cropper imageCrop()', TL_FILES);
			}

			// redirect back to the contao files manager

			if (version_compare(VERSION, '3.5', '>')) {
				// works with contao 4
				$this->redirect('contao?do=files');
			} else {
				// works with contao 3.5
				$this->redirect($this->Environment->script.'?do=files');
			}
			
		}






		// Setup the form

		// create the token (e.g. '8d7cfa67389c2df17e192965f7121793')
		$strToken = md5(uniqid('', true));

		$this->Session->set('tl_imagecrop', $strToken);

		// get the predefined ratios and sizes

		$arrSettingSizes = array();
		if(isset($GLOBALS['TL_CONFIG']['useImagecropSizes'])){
			$arrSettingSizes = deserialize($GLOBALS['TL_CONFIG']['imagecropSizes'],true);
		}

		$arrSettingARs = array();
		if(isset($GLOBALS['TL_CONFIG']['useImagecropARs'])){
			$arrSettingARs = deserialize($GLOBALS['TL_CONFIG']['imagecropAspectRatios'],true);
		} 
		


		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/georgpreisslimagecrop/js/cropper.min.js'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/font-awesome.min.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/bootstrap.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/cropper.min.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/cropper-custom.css|static'; 

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

		return $this->Template->parse();

	}



	
}



