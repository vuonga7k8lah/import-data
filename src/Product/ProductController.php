<?php

namespace ImportData\Product;

use ImportData\Helper\Option;
use ImportData\Helper\TraitUploadImage;

class ProductController
{
	use TraitUploadImage;

	public function __construct()
	{
		add_action('admin_menu', [$this, 'registerMenu']);
		add_action( 'tn_import_init_data_product', [$this,'initSetupData'] );
	}
	public function initSetupData(){
		error_log("----------------------------");
		error_log("handleImportProduct ghi vao:");
		error_log(date("Y-m-d H:i:s"));
		$this->handleImportProduct();
		error_log("handleImportProduct xong:");
		error_log(date("Y-m-d H:i:s"));
		error_log("----------------------------");
	}
	public function registerMenu()
	{
		add_menu_page(
			'Import Data Tool',
			'Import Data Tool',
			'publish_posts',
			'kma_dashboard',
			[$this, 'renderSettings'],
			'dashicons-cover-image'
		);
	}


	public function renderSettings()
	{
		$this->saveOption();
		$this->aOptions = Option::getAuthSettings();

		include TN_IMPORT_DATA_PATH . 'Views/view.php';
	}

	public function saveOption()
	{
		$aValues = [];
		if (isset($_POST['auth-field']) && !empty($_POST['auth-field'])) {
			if (wp_verify_nonce($_POST['auth-field'], 'auth-action')) {
				if (isset($_POST['kmaImport']) && !empty($_POST['kmaImport'])) {
					foreach ($_POST['kmaImport'] as $key => $val) {
						$aValues[sanitize_text_field($key)] = sanitize_text_field(trim($val));
					}
				}
				Option::saveAuthSettings($aValues);
			}
		}
	}

	public function handleImportProduct()
	{
		$aProducts = json_decode(file_get_contents(TN_IMPORT_DATA_PATH . 'assets/data/product.json'));
		$aProductID = [];
		foreach ($aProducts as $oProduct) {
			$aProductID[] = $this->insertProduct($oProduct);
		}
		file_put_contents(TN_IMPORT_DATA_PATH . 'assets/data/listProductIds.json', json_encode($aProductID));
	}

	public function insertProduct($oProduct)
	{
		$post = [
			'post_author'  => 1,
			'post_content' => $oProduct->src??'',
			'post_status'  => 'publish',
			'post_title'   => $oProduct->title??'',
			'post_parent'  => '',
			'post_type'    => 'product',
		];

		$post_id = wp_insert_post($post);
		$aGallery = [];
		if ($post_id) {
			if (is_array($oProduct->img)) {
				foreach ($oProduct->img as $url) {
					$aGallery[] = $this->UploadImageFromUrl($url);
				}
				$attach_id = $aGallery[0];
			} else {
				$attach_id = $this->UploadImageFromUrl($oProduct->img);
			}
			add_post_meta($post_id, '_thumbnail_id', $attach_id);
		}

//		wp_set_object_terms($post_id, 'Races', 'product_cat');
//		wp_set_object_terms($post_id, 'simple', 'product_type');

		update_post_meta($post_id, '_visibility', 'visible');
		update_post_meta($post_id, '_stock_status', 'instock');
//		update_post_meta($post_id, 'total_sales', '0');
//		update_post_meta($post_id, '_downloadable', 'yes');
//		update_post_meta($post_id, '_virtual', 'yes');
		update_post_meta($post_id, '_regular_price', str_replace(['VND', 'đ'], '', $oProduct->price));
//		update_post_meta($post_id, '_sale_price', "1");
//		update_post_meta($post_id, '_purchase_note', "");
//		update_post_meta($post_id, '_featured', "no");
//		update_post_meta($post_id, '_weight', "");
//		update_post_meta($post_id, '_length', "");
//		update_post_meta($post_id, '_width', "");
//		update_post_meta($post_id, '_height', "");
		update_post_meta($post_id, '_sku', sanitize_title($oProduct->title));
//		update_post_meta($post_id, '_product_attributes', []);
//		update_post_meta($post_id, '_sale_price_dates_from', "");
//		update_post_meta($post_id, '_sale_price_dates_to', "");
		update_post_meta($post_id, '_price', str_replace(['VND', 'đ'], '', $oProduct->price));
//		update_post_meta($post_id, '_sold_individually', "");
//		update_post_meta($post_id, '_manage_stock', "no");
//		update_post_meta($post_id, '_backorders', "no");
//		update_post_meta($post_id, '_stock', "");

// file paths will be stored in an array keyed off md5(file path)
//		$downdloadArray = ['name' => "Test", 'file' => $uploadDIR['baseurl'] . "/video/" . $video];
//
//		$file_path = md5($uploadDIR['baseurl'] . "/video/" . $video);
//
//
//		$_file_paths[$file_path] = $downdloadArray;
// grant permission to any newly added files on any existing orders for this product
// do_action( 'woocommerce_process_product_file_download_paths', $post_id, 0, $downdloadArray );
//		update_post_meta($post_id, '_downloadable_files', $_file_paths);
//		update_post_meta($post_id, '_download_limit', '');
//		update_post_meta($post_id, '_download_expiry', '');
//		update_post_meta($post_id, '_download_type', '');
		if (!empty($aGallery)) {
			update_post_meta($post_id, '_product_image_gallery', implode(',', $aGallery));
		}

		return $post_id;

	}
}