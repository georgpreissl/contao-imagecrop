<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * 
 *
 * @author     Georg Preissl <http://www.georg-preissl.at> 
 * @package    imagecrop
 * @author	   Cropper.js by Fengyuan Chen <http://chenfengyuan.com>       
 * @author	   based on the extension Moo_imagecropper by Lightive (erwan.ripoll)       
 * @license    GPL 
 * 
 */

class ImageCrop extends Backend
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


			// Create two variables of the type resource
			// resource -> a special type of variable, holding a reference to an external resource

			// Create a new image resource identifier (the source image)
			$resImgSrc = imagecreatefromjpeg($strPath);
 			
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
				imagejpeg($resImgDest, TL_ROOT.'/'.$this->Input->get('id'));
				imagedestroy($resImgDest);
				$thumbnail = new File($thumbnail);
				$thumbnail->delete();

			} else {
				// create a copy of the image
				$strNewPath = $objFile->dirname.'/'.$objFile->filename.'_croppedCopy_'.time().'.'.$objFile->extension;
				
				// create a JPEG file from the image resource 
				imagejpeg($resImgDest, $strNewPath);

				// frees any memory associated with the image resource
				imagedestroy($resImgDest);
			}

			if (!$error) 
			{
				$this->log('Image file "'.$this->Input->get('id').'" has been cropped', 'tl_image_cropper imageCrop()', TL_FILES);
			}
			$this->redirect($this->Environment->script.'?do=files');
			
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
			$this->Template->formAction = ampersand($this->Environment->script, ENCODE_AMPERSANDS);

			$strHtml .= $this->Template->parse();
		}

		return $strHtml;

	}
	
}


?>