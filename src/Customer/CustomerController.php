<?php

namespace ImportData\Customer;

use Faker\Factory;
use ImportData\Helper\Option;
use ImportData\Helper\TraitCustomer;

class CustomerController
{
	use TraitCustomer;

	public $oFaker;

	public function __construct()
	{
		add_action('init', [$this, 'handleScheduleSingle']);
		add_filter('cron_schedules', [$this, 'isa_add_every_five_minutes']);
		add_action('tn_import_init_data_customer', [$this, 'initSetupDataCustomer']);
		add_action('tn_import_init_data', [$this, 'initSetupData']);
		$this->oFaker = Factory::create();
		//add_action( 'init', [$this,'test'] );
	}

	function isa_add_every_five_minutes($schedules)
	{
		$schedules['every_five_minutes'] = [
			'interval' => 300,
			'display'  => __('Every 5 Minutes', 'KMA-Import-Data')
		];
		$schedules['every_three_minutes'] = [
			'interval' => 180,
			'display'  => __('Every 3 Minutes', 'KMA-Import-Data')
		];
		$schedules['every_15_minutes'] = [
			'interval' => 900,
			'display'  => __('Every 15 Minutes', 'KMA-Import-Data')
		];

		return $schedules;
	}

	public function handleScheduleSingle()
	{
		//$this->clearSchedule();
		//set update user after 3 minutes
		//wp_schedule_single_event(time() + 180, 'tn_import_init_data_customer');
		//set update user after 5 minutes
		//wp_schedule_single_event(time() + 300, 'tn_import_init_data_product');
		//set update user after 10 minutes
		wp_schedule_single_event(time() + 600, 'tn_import_init_data');

	}

	public function clearSchedule()
	{

		if (wp_next_scheduled('tn_import_init_data_customer')) {
			wp_clear_scheduled_hook('tn_import_init_data_customer');
		}
		if (wp_next_scheduled('tn_import_init_data_product')) {
			wp_clear_scheduled_hook('tn_import_init_data_product');
		}
		if (wp_next_scheduled('tn_import_init_data')) {
			wp_clear_scheduled_hook('tn_import_init_data');
		}
	}

	public function handleSchedule()
	{
		if (!wp_next_scheduled('isa_add_every_five_minutes')) {
			wp_schedule_event(time(), 'every_five_minutes', 'isa_add_every_five_minutes');
		}
		if (!wp_next_scheduled('isa_add_every_three_minutes')) {
			wp_schedule_event(time(), 'every_three_minutes', 'isa_add_every_three_minutes');
		}
		if (!wp_next_scheduled('isa_add_every_15_minutes')) {
			wp_schedule_event(time(), 'every_15_minutes', 'isa_add_every_15_minutes');
		}
	}

	function test()
	{
		$aUser = json_decode(file_get_contents(TN_IMPORT_DATA_PATH . 'assets/data/dataUser.json'), true);
		$aUserRandom = $aUser[rand(0, count($aUser))];
		var_dump($aUserRandom);
		die();
	}

	function initSetupData()
	{
		error_log("----------------------------");
		error_log("handleAddComment ghi vao:");
		error_log(date("Y-m-d H:i:s"));
		$this->handleAddComment();
		error_log("handleAddComment xong:");
		error_log(date("Y-m-d H:i:s"));
		error_log("----------------------------");

		error_log("----------------------------");
		error_log("handleAddReview ghi vao:");
		error_log(date("Y-m-d H:i:s"));
		$this->handleAddReview();
		error_log("handleAddReview xong:");
		error_log(date("Y-m-d H:i:s"));
		error_log("----------------------------");

		error_log("----------------------------");
		error_log("handleAddOrder ghi vao:");
		error_log(date("Y-m-d H:i:s"));
		$this->handleAddOrder();
		error_log("handleAddOrder xong:");
		error_log(date("Y-m-d H:i:s"));
		error_log("----------------------------");
	}

	function initSetupDataCustomer()
	{
		error_log("----------------------------");
		error_log("initSetupDataCustomer ghi vao:");
		error_log(date("Y-m-d H:i:s"));
		$this->handleAddUser();
		error_log("initSetupDataCustomer xong:");
		error_log(date("Y-m-d H:i:s"));
		error_log("----------------------------");

	}

	public function handleAddUser()
	{
		$generator = new \Nubs\RandomNameGenerator\All(
			[
				new \Nubs\RandomNameGenerator\Alliteration(),
				new \Nubs\RandomNameGenerator\Vgng()
			]
		);

		if ($this->getCountUser() < (int)Option::getNumberCustomer()) {
			$aUser = [];
			for ($i = 0; $i < 200; $i++) {
				try {
					$aDataUser = [
						'user_login'   => $generator->getName(),
						'first_name'   => $generator->getName(),
						'nickname'     => $generator->getName(),
						'display_name' => $generator->getName(),
						'user_pass'    => md5('admin'),
						'user_email'   => $generator->getName() . uniqid() . '@gmail.com',
					];
					$this->createUser($aDataUser);
					$aUser[] = $aDataUser;
				}
				catch (\Exception $exception) {
					continue;
				}
			}
			file_put_contents(TN_IMPORT_DATA_PATH . 'assets/data/dataUser.json', json_encode($aUser));
		}
	}

	public function exportFileJsonDataUser()
	{
		$user_query = new \WP_User_Query([
			'number' => 20,
			'role'   => 'subscriber',
			//			'fields' => [
			//				'ID', 'user_login', 'user_email', 'user_url'
			//			]
		]);
		$aDataUser = [];
		if (!empty($user_query->get_results())) {
			foreach ($user_query->get_results() as $oUser) {
				$aDataUser[] = [
					'ID'            => $oUser->ID,
					'user_login'    => $oUser->user_login,
					'user_nicename' => $oUser->user_nicename,
					'user_email'    => $oUser->user_email,
					'display_name'  => $oUser->display_name,
					'user_url'      => $oUser->user_url,
				];
			}
		}
		file_put_contents(TN_IMPORT_DATA_PATH . 'assets/data/dataUser.json', json_encode($aDataUser));
	}

	public function handleAddComment()
	{
		$aUser = json_decode(file_get_contents(TN_IMPORT_DATA_PATH . 'assets/data/dataUser.json'), true);
		$limitImport = (int)Option::getNumberComment();
		if ($this->getCountComment() < $limitImport) {
			for ($i = 0; $i < 200; $i++) {
				//random user
				$aUserRandom = $aUser[rand(0, count($aUser))];
				$this->addComment([
					'comment_post_ID'      => $this->randomProductId() ?? 33,
					'comment_content'      => $this->oFaker->text,
					'user_id'              => $aUserRandom['ID'] ?? '',
					'comment_author'       => $aUserRandom['user_login'] ?? '',
					'comment_author_email' => $aUserRandom['user_email'] ?? ''
				]);
			}
		}
	}

	public function handleAddReview()
	{
		$aUser = json_decode(file_get_contents(TN_IMPORT_DATA_PATH . 'assets/data/dataUser.json'), true);
		$limitImport = (int)Option::getNumberReview();
		if ($this->getCountReview() < $limitImport) {
			for ($i = 0; $i < 100; $i++) {
				//random user
				$aUserRandom = $aUser[rand(0, count($aUser) - 1)];
				$this->addComment([
					'comment_post_ID'      => $this->randomProductId() ?? 33,
					'comment_content'      => $this->oFaker->text(),
					'user_id'              => $aUserRandom['ID'] ?? '',
					'comment_author'       => $aUserRandom['user_login'] ?? '',
					'comment_author_email' => $aUserRandom['user_email'] ?? '',
					'comment_author_url'   => '',
					'comment_type'         => 'review',
					'comment_meta'         => [
						//random rating
						'rating' => rand(1, 5)
					]
				]);
			}
		}
	}

	public function handleAddOrder()
	{
		$limitImport = (int)Option::getNumberOrder();
		if ($this->getCountOrder() < $limitImport) {
			for ($i = 0; $i < 100; $i++) {
				//random user
				$aAddress = [
					'first_name' => $this->oFaker->firstName(),
					'last_name'  => $this->oFaker->lastName(),
					'company'    => $this->oFaker->company(),
					'email'      => $this->oFaker->email(),
					'phone'      => $this->oFaker->phoneNumber(),
					'address_1'  => $this->oFaker->address(),
					'address_2'  => $this->oFaker->address(),
					'city'       => $this->oFaker->city(),
					'state'      => $this->oFaker->streetAddress(),
					'postcode'   => $this->oFaker->postcode(),
					'country'    => $this->oFaker->country()
				];
				$aProductId = [];
				//random product in order
				for ($i = 0; $i < rand(1, 4); $i++) {
					$aProductId[] = $this->randomProductId();
				}
				$this->createOrder($aAddress, $aProductId);
			}
		}
	}

	public function randomProductId(): int
	{
		$aListProductId = json_decode(file_get_contents(TN_IMPORT_DATA_PATH . 'assets/data/listProductIds.json'), true)
			??
			[];
		$countProduct = count($aListProductId) - 1;
		return $aListProductId[rand(0, $countProduct)];
	}
}