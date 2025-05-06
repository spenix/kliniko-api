<?php
if (!function_exists('convertBase64ToImage')) {
	function myTest($x = "")
	{
		return $x;
	}
}

if (!function_exists('img_enc_base64')) {
	function img_enc_base64($filepath = "")
	{
		if (file_exists(public_path($filepath))) {

			$filetype = pathinfo(public_path($filepath), PATHINFO_EXTENSION);

			if ($filetype === 'svg') {
				$filetype .= '+xml';
			}
			if (is_file($filepath)) {
				$get_img = file_get_contents(public_path($filepath));
				return 'data:image/' . $filetype . ';base64,' . base64_encode($get_img);
			}
		}
	}
}

if (!function_exists('convertBase64ToImage')) {

	function convertBase64ToImage($img = "", $img_name = "", $publicPath = "system-settings")
	{
		if (!file_exists(public_path($publicPath))) {
			mkdir(public_path($publicPath), 0777, true);
		}
		if (!is_null($img)) {
			$image_parts = explode(';base64', $img);
			$image_type_aux = explode('/', $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_base64 = base64_decode($image_parts[1]);
			$fileName = str_replace(" ", "-", $img_name) . '.' . $image_type;
			$homepage = file_put_contents(public_path($publicPath) . '/' . $fileName, $image_base64);
			return scanFileInDirectory(public_path($publicPath),  $img_name, '/' . $publicPath . '/' . $fileName);
		} else {
			$files = opendir(public_path($publicPath));
			if ($files) {
				while (($fileName = readdir($files)) !== FALSE) {
					if ($fileName != '.' && $fileName != '..') {
						$file_name = explode('.', $fileName);
						if ($file_name[0] == $img_name) {
							unlink(public_path($publicPath) . '/' . $fileName);
						}
					}
				}
			}

			return "";
		}
	}
}

if (!function_exists('removeFileExist')) {
	function removeFileExist($img_name = "", $publicPath = "system-settings"): bool
	{
		if (file_exists(public_path($publicPath))) {
			$files = opendir(public_path($publicPath));
			if ($files) {
				while (($fileName = readdir($files)) !== false) {
					if ($fileName != '.' && $fileName != '..') {
						$file_name = explode('.', $fileName);
						if ($file_name[0] == $img_name) {
							unlink(public_path($publicPath) . '/' . $fileName);
							return true;
						}
					}
				}
			}
		}

		return false;
	}
}

if (!function_exists('scanFileInDirectory')) {

	function scanFileInDirectory($dir_path = "", $img_name = "", $display_path = "")
	{
		$dir_files = [];

		if (is_dir($dir_path)) {
			$files = opendir($dir_path);
			if ($files) {
				while (($fileName = readdir($files)) !== FALSE) {
					if ($fileName != '.' && $fileName != '..') {
						$file_path = $dir_path . '/' . $fileName;
						$file_name = explode('.', $fileName);
						if ($file_name[0] == $img_name) {
							$dir_files = ['name' => $fileName, 'size' => filesize($file_path), 'path' => $display_path];
						}
					}
				}
			}
		}
		return $dir_files;
	}
}
