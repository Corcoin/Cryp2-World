<?php
/**
 * Open Source Social Network
 *
 * @package   (openteknik.com).ossn
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */

/**
 * Get file
 * 
 * @param integer $guid A file guid
 * 
 * @return object|boolean
 */
function ossn_get_file($guid) {
		$file       = new OssnFile;
		$file->guid = $guid;
		$file       = $file->getFile();
		if($file) {
				return $file;
		}
		return false;
}
/**
 * Check storage type
 *
 * @return boolean
 */
function ossn_file_is_cdn_storage_enabled(): bool {
		 $site = new \OssnSite();
		 $settings = $site->getSettings('cdnstorage.status');
		 if($settings && $settings == 'enabled'){
			 	return true;
		 }
		 return false;
}