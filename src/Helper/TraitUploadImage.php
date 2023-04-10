<?php

namespace ImportData\Helper;

trait TraitUploadImage
{
	function UploadImageFromUrl($url, $title = null)
	{
		require_once(ABSPATH . "/wp-load.php");
		require_once(ABSPATH . "/wp-admin/includes/image.php");
		require_once(ABSPATH . "/wp-admin/includes/file.php");
		require_once(ABSPATH . "/wp-admin/includes/media.php");

		// Download url to a temp file
		$tmp = download_url($url);
		if (is_wp_error($tmp)) return false;

		// Get the filename and extension ("photo.png" => "photo", "png")
		$filename = pathinfo($url, PATHINFO_FILENAME);
		$extension = pathinfo($url, PATHINFO_EXTENSION);

		// An extension is required or else WordPress will reject the upload
		if (!$extension) {
			// Look up mime type, example: "/photo.png" -> "image/png"
			$mime = mime_content_type($tmp);
			$mime = is_string($mime) ? sanitize_mime_type($mime) : false;

			// Only allow certain mime types because mime types do not always end in a valid extension (see the .doc example below)
			$mime_extensions = [
				// mime_type         => extension (no period)
				'text/plain'         => 'txt',
				'text/csv'           => 'csv',
				'application/msword' => 'doc',
				'image/jpg'          => 'jpg',
				'image/jpeg'         => 'jpeg',
				'image/gif'          => 'gif',
				'image/png'          => 'png',
				'video/mp4'          => 'mp4',
			];

			if (isset($mime_extensions[$mime])) {
				// Use the mapped extension
				$extension = $mime_extensions[$mime];
			} else {
				// Could not identify extension
				@unlink($tmp);
				return false;
			}
		}


		// Upload by "sideloading": "the same way as an uploaded file is handled by media_handle_upload"
		$args = [
			'name'     => "$filename.$extension",
			'tmp_name' => $tmp,
		];

		// Do the upload
		$attachment_id = media_handle_sideload($args, 0, $title);

		// Cleanup temp file
		@unlink($tmp);

		// Error uploading
		if (is_wp_error($attachment_id)) return false;

		// Success, return attachment ID (int)
		return (int)$attachment_id;
	}
}