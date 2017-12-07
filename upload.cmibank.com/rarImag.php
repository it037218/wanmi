<?php 
//图片裁剪 
class RarImag{
	/**
	 * 得到等比例缩放的长宽
	 */
	function getNewSize($maxWidth, $maxHeight, $srcWidth, $srcHeight) {
		if($srcWidth > $maxWidth) {
			$maxWidth = $maxWidth;
			if($srcHeight > $maxHeight) {
				$maxHeight = ($srcHeight/$srcHeight) * $maxWidth;
			} else {
				$maxHeight = $srcHeight;
			}
			return array('width' => $maxWidth,'height' => $maxHeight);
		}

		if($srcHeight > $maxHeight) {
			$maxHeight = $maxHeight;
			if($srcWidth > $maxWidth) {
				$maxWidth = ($srcWidth/$srcHeight) * $maxHeight;
			} else {
				$maxWidth = $srcWidth;
			}
			return array('width' => $srcWidth,'height' => $maxHeight);
		}

		return array('width' => $srcWidth,'height' => $srcHeight);
	}
		/**
		 * 等比例生成缩略图
		 *
		 * @param String $srcFile 原始文件路径
		 * @param String $dstFile 目标文件路径
		 * @param Integer $maxWidth 生成的目标文件的最大宽度
		 * @param Integer $maxHeight 生成的目标文件的最大高度
		 * @return Boolean 生成成功则返回true，否则返回false
		 */
	function makeThumb($srcFile, $dstFile, $maxWidth, $maxHeight) {
		if ($size = getimagesize($srcFile)) {
			$srcWidth = $size[0];
			$srcHeight = $size[1];
			$mime = $size['mime'];
			switch ($mime) {
				case 'image/jpeg';
				$isJpeg = true;
				break;
				case 'image/gif';
				$isGif = true;
				break;
				case 'image/png';
				$isPng = true;
				break;
				default:
					return false;
					break;
			}
			$arr = $this->getNewSize($maxWidth, $maxHeight, $srcWidth, $srcHeight);
			$thumbWidth = $arr['width'];
			$thumbHeight = $arr['height'];
			if (isset($isJpeg) && $isJpeg) {
				$dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
				$srcPic = imagecreatefromjpeg($srcFile);
				imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
				imagejpeg($dstThumbPic, $dstFile, 100);
				imagedestroy($dstThumbPic);
				imagedestroy($srcPic);
				return true;
			} elseif (isset($isGif) && $isGif) {
				$dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
				//创建透明画布
				imagealphablending($dstThumbPic, true);
				imagesavealpha($dstThumbPic, true);
				$trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
				imagefill($dstThumbPic, 0, 0, $trans_colour);
				$srcPic = imagecreatefromgif($srcFile);
				imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
				imagegif($dstThumbPic, $dstFile);
				imagedestroy($dstThumbPic);
				imagedestroy($srcPic);
				return true;
			} elseif (isset($isPng) && $isPng) {
				$dstThumbPic = imagecreatetruecolor($thumbWidth, $thumbHeight);
				//创建透明画布
				imagealphablending($dstThumbPic, true);
				imagesavealpha($dstThumbPic, true);
				$trans_colour = imagecolorallocatealpha($dstThumbPic, 0, 0, 0, 127);
				imagefill($dstThumbPic, 0, 0, $trans_colour);
				$srcPic = imagecreatefrompng($srcFile);
				imagecopyresampled($dstThumbPic, $srcPic, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);
				imagepng($dstThumbPic, $dstFile);
				imagedestroy($dstThumbPic);
				imagedestroy($srcPic);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

?>