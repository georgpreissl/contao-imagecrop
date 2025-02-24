<?php

namespace GeorgPreissl\ImageCrop\Controller;

use Contao\DataContainer;
use Contao\Backend;
use Contao\BackendUser;
use Contao\BackendTemplate;
use Contao\File;
use Contao\FilesModel;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Contao\StringUtil;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\LogLevel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;


class Cropper extends Backend
{




	public function cropImage(DataContainer $dc)
	{

		$contaoVersion = (method_exists(ContaoCoreBundle::class, 'getVersion') ? ContaoCoreBundle::getVersion() : VERSION); 
// dump($contaoVersion);

		// $requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue(); 

		// $request = $this->requestStack->getCurrentRequest();
		$request = System::getContainer()->get('request_stack')->getCurrentRequest();

		$refererId = System::getContainer()->get('request_stack')->getCurrentRequest()->get('_contao_referer_id');
		// $refererId ändert sich bei jedem Seiten-Refresh und ist zb.: 'YYUxTU43'

		$requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();
		// $requestToken ist bspw: "d6c0ad4.phv5ks3npnoDgefcD5uUHMGURIEVRwO8-KEMKXuWhPs.x2_I04OWwhBV7IPkWfrldICkccd6EFDjztFlHTHesczJepH_-qniP3LPsg"

		$route = System::getContainer()->get('request_stack')->getCurrentRequest()->get('_route'); 
		// $route ist 'contao_backend'

		$session = $request->getSession();


		if (Input::get('UpdateImage') || Input::get('DuplicateImage')) 
		{
			// crop-form has been submitted, so lets crop the image

			$rootDir = System::getContainer()->getParameter('kernel.project_dir');

			// get the new dimensions
			$strW = Input::get('data_width');
			$strH = Input::get('data_height');
			$strX = Input::get('data_x');
			$strY = Input::get('data_y');

			// get the submitted scale values for flipping ("1" or "-1")
			$strScaleX = Input::get('scale_x');
			$strScaleY = Input::get('scale_y');

			// get the submitted rotation value
			$strRotation = Input::get('rotation');

			// get the path of the source file
			$strPath = Input::get('id');
			// e.g. "files/theme/image-3.jpg"

			$strExt = $this->checkFileExtension($strPath);
			// e.g. "image/jpeg"


			// create the source image (image resource identifier)
			if($strExt == 'image/png'){
				$objResImgSrc = imagecreatefrompng($rootDir.'/'.$strPath);
			} else if($strExt == 'image/gif'){
				$objResImgSrc = imagecreatefromgif($rootDir.'/'.$strPath);
			} else if($strExt == 'image/jpeg'){
				$objResImgSrc = imagecreatefromjpeg($rootDir.'/'.$strPath);
			}
 			
 
			// if cropping is not desired, leave the original dimensions
			if ($strW == "0" || $strH == "0") {
				$strW  = imagesx($objResImgSrc);
				$strH = imagesy($objResImgSrc);

			}

			// flip the image vertical
			if ($strScaleY == "-1") {
				imageflip($objResImgSrc, IMG_FLIP_VERTICAL);
			}

			// flip the image horizontal
			if ($strScaleX == "-1") {
				imageflip($objResImgSrc, IMG_FLIP_HORIZONTAL);
			}

			// rotate the image
			if ($strRotation !== "0") {
				$intRotation = intval($strRotation);
				$intRotation = 360 - $intRotation;
				$objResImgSrc = imagerotate($objResImgSrc, $intRotation, 0);
				// if ($intRotation == 90 || $intRotation == 270) {
				// 	$strW  = imagesx($objResImgSrc);
				// 	$strH = imagesy($objResImgSrc);
				// }

			}



 			// create the new image (image resource identifier)
 			// returns an image identifier representing a black image of the specified size
			$objResImgNew = imagecreatetruecolor($strW, $strH);

 			// copy part of the source image into the new image
			imagecopy($objResImgNew, $objResImgSrc, 0, 0, $strX, $strY, $strW, $strH);



			
			$error = false;

			if (Input::get('UpdateImage'))
			{
				// update the image
				
				// create a file from the image resource 
				header ('Content-Type: ' . $strExt);
				if($strExt == 'image/png'){
					imagepng($objResImgNew, $rootDir.'/'.$strPath);
				} else if($strExt == 'image/gif'){
					imagegif($objResImgNew, $rootDir.'/'.$strPath);
				} else if($strExt == 'image/jpeg'){
					imagejpeg($objResImgNew, $rootDir.'/'.$strPath);
				}

				// frees any memory associated with the image
				imagedestroy($objResImgNew);


			} 
			
			if (Input::get('DuplicateImage'))
			{
				// create a copy of the image

				// create a contao file object of the source image
				$objFile = new File($strPath);

				// put the new path together
				$strNewPath = $objFile->dirname.'/'.$objFile->filename.'_copy_'.time().'.'.$objFile->extension;
				
				// create a file from the image resource 
				header ('Content-Type: ' . $strExt);
				if($strExt == 'image/png'){
					imagepng($objResImgNew, $strNewPath);
				} else if($strExt == 'image/gif'){
					imagegif($objResImgNew, $strNewPath);
				} else if($strExt == 'image/jpeg'){
					imagejpeg($objResImgNew, $strNewPath);
				}

				// frees any memory associated with the image resource
				imagedestroy($objResImgNew);

				// create a contao file object of the new image
				if (is_file($rootDir . '/' . $strNewPath))
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
				if (version_compare($contaoVersion, '4', '>')) {
					// works with contao 5
					System::getContainer()->get('monolog.logger.contao.cron')->info('File "'.Input::get('id').'" has been cropped');
				} else {
					// works with contao 4
					$this->log('Image file "'.Input::get('id').'" has been cropped', 'tl_image_cropper imageCrop()', TL_FILES);
				}				
			}

			// redirect back to the contao files manager

			// if (version_compare($contaoVersion, '3.5', '>')) {
				// works with contao 4
				$this->redirect('contao?do=files');
			// } else {
			// 	// works with contao 3.5
			// 	$this->redirect($this->Environment->script.'?do=files');
			// }
			
		}






		// Setup the form

		// create the token (e.g. '8d7cfa67389c2df17e192965f7121793')
		$strToken = md5(uniqid('', true));

		// $this->Session::set('tl_imagecrop', $strToken);
		$session->set('tl_imagecrop', $strToken);

		// get the predefined ratios and sizes

		$arrSettingSizes = array();
		if(isset($GLOBALS['TL_CONFIG']['useImagecropSizes'])){
			$arrSettingSizes = StringUtil::deserialize($GLOBALS['TL_CONFIG']['imagecropSizes'],true);
		}

		$arrSettingARs = array();
		if(isset($GLOBALS['TL_CONFIG']['useImagecropARs'])){
			$arrSettingARs = StringUtil::deserialize($GLOBALS['TL_CONFIG']['imagecropAspectRatios'],true);
		} 
		


		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/georgpreisslimagecrop/js/cropper.min.js'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/font-awesome.min.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/bootstrap.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/cropper.min.css|static'; 
		$GLOBALS['TL_CSS'][] = 'bundles/georgpreisslimagecrop/css/cropper-custom.css|static'; 

		$this->Template = new BackendTemplate('be_imagecrop');
		
		// $this->Template->back = $this->Environment->base . preg_replace('/&(amp;)?(id|key|submit|imagecrop|token)=[^&]*/', '', $this->Environment->request);
		
		$this->Template->requestToken = $requestToken;
		$this->Template->imageSrc = $dc->id;
		$this->Template->inputDo = Input::get('do');
		$this->Template->inputKey = Input::get('key');
		$this->Template->inputId = Input::get('id');
		$this->Template->token = $strToken;
		// $this->Template->messages = $this->getMessages();
		$this->Template->settingARs = $arrSettingARs;
		$this->Template->settingSizes = $arrSettingSizes;

		// if (version_compare(VERSION, '3.5', '>')) {
			// funktioniert unter 4.4
			// $this->Template->formAction = "http://devc5.test/contao?";
			$this->Template->formAction = "/contao?";
			// $this->Template->formAction = "orf.at";
		// } else {
		// 	// funktioniert unter 3.5
		// 	$this->Template->formAction = ampersand($this->Environment->script, ENCODE_AMPERSANDS);
		// }

		return $this->Template->parse();



		
	}

	// check the file type of the given file
	private function checkFileExtension($fname) {
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

	
}



