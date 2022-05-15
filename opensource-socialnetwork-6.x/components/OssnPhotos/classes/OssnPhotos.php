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
class OssnPhotos extends OssnFile {
		/**
		 * Add photo to album
		 *
		 * @params = $album Album guid
		 *           $photo Photo type
		 *           $access Private or Public ( its actually depend on album privacy)
		 *
		 * @return bool;
		 */
		public function AddPhoto($album, $photo = 'ossnphoto', $access = OSSN_PUBLIC) {
				//check access
				if(!in_array($access, ossn_access_types())) {
						$access = OSSN_PUBLIC;
				}
				//album initialize
				$this->album = new OssnAlbums();
				$this->album = $this->album->GetAlbum($album);

				//check if album guid is not less than 0 and validate photo uploader
				if(!empty($album) && $album > 0 && $this->album->album->owner_guid == ossn_loggedin_user()->guid) {
						//initialize variables
						$this->owner_guid = $album;
						$this->type       = 'object';
						$this->subtype    = 'ossn:aphoto';
						$this->setFile($photo);
						if(ossn_file_is_cdn_storage_enabled()) {
								$this->setStore('cdn');
						}
						$this->permission = $access;
						$this->setPath('album/photos/');
						$this->setExtension(array(
								'jpg',
								'png',
								'jpeg',
								'jfif',
								'gif',
								'webp',
						));

						//add file
						if($fileguid = $this->addFile()) {
								$sizes  = ossn_photos_sizes();
								$resize = $this->getFiles();

								if(isset($resize->{0}->value)) {
										$datadir   = ossn_get_userdata("object/{$album}/{$resize->{0}->value}");
										$file_name = str_replace('album/photos/', '', $resize->{0}->value);
										//crop photos and create new photos from source
										$sizes = ossn_photos_sizes();
										foreach($sizes as $size => $params) {
												$params = explode('x', $params);
												$width  = $params[1];
												$height = $params[0];

												if($size !== 'view') {
														$resized = ossn_resize_image($this->file['tmp_name'], $width, $height, true);
												} else {
														$resized = ossn_resize_image($this->file['tmp_name'], $width, $height, false);
												}
												//create newly created image
												$image = ossn_get_userdata("object/{$album}/album/photos/{$size}_{$file_name}");
												if(!ossn_file_is_cdn_storage_enabled()) {
														file_put_contents($image, $resized);
												} else {
														$dirlocalpath = "object/{$album}/album/photos/";
														$filename     = "{$size}_{$this->newfilename}";

														$cdn           = new \CDNStorage\Controller($dirlocalpath, $fileguid);
														$cdn->mimeType = mime_content_type($this->file['tmp_name']);
														$cdn->upload($resized, $filename, 'public-read', false);
												}
										}
										//return true if photos is added to database
										$args['guid']  = $fileguid;
										$args['album'] = $this->album;
										ossn_trigger_callback('ossn:photo', 'add', $args);
										return $fileguid;
								}
						}
						return false;
				}
		}

		/**
		 * Get photos of album
		 *
		 * @params = $album Album guid
		 *
		 * @return object;
		 */
		public function GetPhotos($album) {
				$this->owner_guid = $album;
				$this->subtype    = 'ossn:aphoto';
				$this->type       = 'object';
				return $this->getFiles();
		}

		/**
		 * Get photo of album
		 *
		 * @params = $photo Photo id
		 *
		 * @return object;
		 */
		public function GetPhoto($photo) {
				$this->guid = $photo;
				$this->type    = 'object';
				return $this->getFile();
		}

		/**
		 * Delete profile photo
		 *
		 * @return bool;
		 */
		public function deleteProfilePhoto() {
				if(isset($this->photoid)) {
						$this->guid    = $this->photoid;
						$this->entity  = new OssnEntities();
						$file          = $this->getFile();
						$source        = ossn_get_userdata("user/{$file->owner_guid}/{$file->value}");

						//delete cropped photos
						unlink($source);
						foreach(ossn_user_image_sizes() as $size => $dimensions) {
								$filename = str_replace('profile/photo/', '', $file->value);
								$filename = ossn_get_userdata("user/{$file->owner_guid}/profile/photo/{$size}_{$filename}");
								if(is_file($filename)) {
										unlink($filename);
								}
						}
						//delete photo from database
						if($this->deleteEntity($file->guid)) {
								$params['photo'] = get_object_vars($file);
								ossn_trigger_callback('delete', 'profile:photo', $params);
								return true;
						}
				}
				return false;
		}
		/**
		 * Delete profile cover photo
		 *
		 * @return bool;
		 */
		public function deleteProfileCoverPhoto() {
				if(isset($this->photoid)) {
						$this->guid    = $this->photoid;
						$this->entity  = new OssnEntities();
						$file          = $this->getFile();
						$source        = ossn_get_userdata("user/{$file->owner_guid}/{$file->value}");

						if($this->deleteEntity($this->guid)) {
								//delete file
								unlink($source);
								$params['photo'] = get_object_vars($file);
								ossn_trigger_callback('delete', 'profile:cover:photo', $params);
								return true;
						}
				}
				return false;
		}

		/**
		 * Delete profile photo
		 *
		 * @params = $this->photoid Id of photo
		 *
		 * @return bool;
		 */
		public function deleteAlbumPhoto() {
				if(isset($this->photoid)) {
						$this->guid = $this->photoid;
						$this->entity  = new OssnEntities();
						$file          = $this->getFile();
						$source        = ossn_get_userdata("object/{$file->owner_guid}/{$file->value}");

						//delete croped photos
						unlink($source);
						foreach(ossn_photos_sizes() as $size => $dimensions) {
								$filename = str_replace('album/photos/', '', $file->value);
								$filename = ossn_get_userdata("object/{$file->owner_guid}/album/photos/{$size}_{$filename}");
								unlink($filename);
						}
						//delete photo from database
						if($this->deleteEntity($file->guid)) {
								$params['photo'] = get_object_vars($file);
								ossn_trigger_callback('delete', 'album:photo', $params);
								return true;
						}
				}
				return false;
		}
		/**
		 * Add images to wall
		 *
		 * @param integer $itemguid A album guid
		 *
		 * @return boolean
		 */
		public function addWall($itemguid = '', array $images_guid = array()): bool {
				$album = ossn_get_object($itemguid);
				if(!$album || !class_exists('OssnWall')) {
						return false;
				}
				$this->wall                     = new OssnWall();
				$this->wall->type               = 'user';
				$this->wall->item_type          = 'album:photos:wall';
				$this->wall->owner_guid         = ossn_loggedin_user()->guid;
				$this->wall->poster_guid        = ossn_loggedin_user()->guid;
				$this->wall->item_guid          = $itemguid;
				$this->wall->data->photos_guids = implode(',', $images_guid);
				if($this->wall->Post('null:data', '', '', $album->access)) {
						return true;
				}
				return false;
		}
		/**
		 * Search photos
		 *
		 * @param array $param Option values
		 *
		 * @return boolean|array
		 */
		public function searchPhotos($params): array | bool {
				return $this->searchFiles($params);
		}
		/**
		 * get photo view URL
		 *
		 * @return boolean
		 */
		public function getURL(): bool|string {
				if($this->subtype == 'file:ossn:aphoto' || $this->subtype == 'file:profile:photo' || $this->subtype == 'file:profile:cover') {
						$image = str_replace('album/photos/', '', $this->value);
						if($this->isCDN()) {
								$manifest = $this->getManifest();
								$image    = $manifest['filename'];
						}
						$usertype = false;
						return ossn_add_cache_to_url(ossn_site_url("album/getphoto/{$this->guid}/{$image}"));
				}
				return false;
		}
		
		/**
		 * Get user profile photos album
		 *
		 * @param integer $user User guid
		 *
		 * @return object
		 */
		public function GetUserProfilePhotos($user) {
				$this->owner_guid = $user;
				$this->type       = 'user';
				$this->subtype    = 'profile:photo';
				$this->order_by   = 'guid DESC';
				return $this->getFiles();
		}
		/**
		 * Get user cover photos album
		 *
		 * @param integer $user User guid
		 *
		 * @return object
		 */
		public function GetUserCoverPhotos($user) {
				$this->owner_guid = $user;
				$this->type       = 'user';
				$this->subtype    = 'profile:cover';
				$this->order_by   = 'guid DESC';
				return $this->getFiles();
		}			
} //class
