<?php
	/**
	 * Project: feeio2
	 * File: ProfileImageCreator.php
	 *
	 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
	 * Date: 14.03.15
	 * Version: 1.0
	 */

	namespace AppBundle\DependencyInjection\ImageCreator;

	use AppBundle\DependencyInjection\Tool;
	use AppBundle\Entity\File;

	class ImageCreator {

		/**
		 * @param $text
		 *
		 * @return \AppBundle\Entity\File
		 */
		public function getImage($text){

			$image = new File();

			if(Tool::is_connected()){
				$imageString = file_get_contents($this->getImageUrl($text));
			}else{
				$imageString = rand(0, 100);
			}

			$save = file_put_contents($this->getImageDestinationPath($image), $imageString);

			return $image;

		}

		private function getImageDestinationPath(File $image){

			$image->setPath(md5(time().rand(0, 1000)).".png");

			return $image->getAbsolutePath();

		}

		private function getImageUrl($text){

			$background = Tool::getRGBColorDark();

			return "http://dummyimage.com/300/".$background."/fff.png&text=".$text;

		}






	}