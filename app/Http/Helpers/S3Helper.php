<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class S3Helper
{
    static $s3Folder = 'test';
	static $s3FolderThumbs = 'test/thumbs';

    /**
	 *  Upload a file to s3
	 *
	 * @param type $file
	 * @return type
	 */
	static function uploadToS3($file, $nameFile = '') {
		//get file extension
		//$extension = $file->getClientOriginalExtension();
		//filename to store
		$filenametostore = self::getFIleUrl($nameFile);
		//Upload File to s3

		if (Storage::disk('s3')->put($nameFile, $file, 'public')) {
			return array(
				'error' => false,
				'name' => $filenametostore,
			);
		} else {
			return array(
				'error' => true,
				'file' => $file->getClientOriginalName(),
				'message' => __('upload error'),
			);
		}
	}


    /**
	 * Generate the correct url for the uploads
	 *
	 * @param type $extension
	 * @param AdPreview $ad
	 * @return string
	 */
	static function getFIleUrl($nameFile = '') {
 
        $s3FolderSave = self::$s3Folder;
        $name = $nameFile;
    
		if (config('app.env') == 'local') {
			$url = "$s3FolderSave/" . 'test/' . $name;
		} else {
            $url = "$s3FolderSave/" . $name;
		}
		return $url;
	}

    static function deleteFromS3($url) {
		if (
			Storage::disk('s3')->exists('/' . $url) &&
		    Storage::disk('s3')->delete('/' . $url)
		) {
			return true;
		}
        return false;
    }



}
