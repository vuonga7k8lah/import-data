<?php

namespace ImportData\Helper;

use JetBrains\PhpStorm\NoReturn;

trait TraitCustomer
{

	public function createUser(array $aData)
	{
		try {
			$userid = wp_insert_user($aData);
			if (is_wp_error($userid)) {
				throw new \Exception($userid->get_error_message());
			}
			return $userid ;
		}
		catch (\Exception $exception) {
			error_log($exception->getMessage() . '<br>');
		}
	}

	public function addComment($aData)
	{
		try {
			$commentId = wp_insert_comment($aData);
			if (!$commentId) {
				throw new \Exception('insert data error' . json_encode($aData) . '<br>');
			}
		}
		catch (\Exception $exception) {
			error_log($exception->getMessage() . '<br>');
		}
	}

	public function getCountUser(): int
	{
		global $wpdb;
		$query = $wpdb->get_results("SELECT users.id FROM {$wpdb->users} as users", ARRAY_A);
		return !empty($query) ? count($query) : 0;
	}

	public function getCountComment(): int
	{
		global $wpdb;
		$query = $wpdb->get_results("SELECT cmm.comment_ID FROM {$wpdb->comments} as cmm where comment_type='comment'",
			ARRAY_A);
		return !empty($query) ? count($query) : 0;
	}

	public function getCountReview(): int
	{
		global $wpdb;
		$query = $wpdb->get_results("SELECT cmm.comment_ID FROM {$wpdb->comments} as cmm where comment_type='review'",
			ARRAY_A);
		return !empty($query) ? count($query) : 0;
	}

	public function createOrder(array $aAddress, array $aProductId)
	{
		global $woocommerce;

		// Now we create the order
		$order = wc_create_order();

		// add product
		foreach ($aProductId as $productId) {

			$order->add_product(wc_get_product($productId), rand(1, 5));
		}

		$order->set_address($aAddress);
		//
		$order->calculate_totals();
		$order->update_status("Completed", 'Imported order', TRUE);
	}

	public function getCountOrder(): int
	{
		global $wpdb;

		$the_query = new \WP_Query([
			'post_type'      => 'shop_order',
			'posts_per_page' => '-1',
			'post_status'    => ['wc-pending', 'wc-completed']
		]);
		wp_reset_postdata();
		return count($the_query->posts);
	}
}