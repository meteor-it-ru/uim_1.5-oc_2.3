<?php
$time_start = microtime(true);

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors','on');
ini_set('error_log', __DIR__ . '/Logs/error.log');

ini_set('soap.wsdl_cache_enabled', '0');
ini_set('soap.wsdl_cache_ttl', '0');

require_once 'init.php';
require_once 'OneC/Wsdl/Server.php';
require_once 'OneC/Wsdl/Client.php';

class OneCGateway extends Controller {

	private $enable_logs = 0;
	private $image_dir = 'catalog/export/';

	public  $category_ids = array();
	public  $product_ids = array();
	//public  $product_option_ids = array();

	//private $_model = null;
	private $setting = array();
	private $template_tags = array('[category_name]', '[all_category_name]', '[manufacturer_name]', '[product_name]', '[model_name]', '[price]');
	private $all_category_sep = '|';
	private $language_default = 'ru-ru';


	public function __construct() {
		parent::__construct($GLOBALS['registry']);

		$this->setting = $this->_loadSetting();
	}

	/**
	 * @return array
	 */
	private function _loadSetting() {

	$this->load->model('setting/setting');
	$setting = $this->model_setting_setting->getSetting('exchange_1c', 0);
	$result = array();

	// category
	$result['cat']['url']['load'] 				= isset($setting['exchange_1c_load_url_cat']) ? (int)$setting['exchange_1c_load_url_cat'] : 1;
	$result['cat']['url']['rewrite'] 			= isset($setting['exchange_1c_rewrite_url_cat']) ? (int)$setting['exchange_1c_rewrite_url_cat'] : 1;
	$result['cat']['url']['generate'] 			= isset($setting['exchange_1c_generate_url_cat']) ? (int)$setting['exchange_1c_generate_url_cat'] : 0;
	$result['cat']['url']['template'] 			= isset($setting['exchange_1c_template_url_cat']) ? (string)$setting['exchange_1c_template_url_cat'] : '[category_name]';
	$result['cat']['url']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_url_cat']) ? (string)$setting['exchange_1c_all_category_sep_url_cat'] : ' ';
	$result['cat']['url']['clear'] 				= isset($setting['exchange_1c_clear_url_cat']) ? (string)$setting['exchange_1c_clear_url_cat'] : '';
	$result['cat']['url']['replace']			= isset($setting['exchange_1c_replace_url_cat']) ? (string)$setting['exchange_1c_replace_url_cat'] : '';
	$result['cat']['url']['transliterate'] 			= isset($setting['exchange_1c_transliterate_url_cat']) ? (int)$setting['exchange_1c_transliterate_url_cat'] : 1;
	$result['cat']['url']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_url_cat']) ? (int)$setting['exchange_1c_transliterate_type_url_cat'] : 1;

	$result['cat']['h1']['load'] 				= isset($setting['exchange_1c_load_h1_cat']) ? (int)$setting['exchange_1c_load_h1_cat'] : 1;
	$result['cat']['h1']['rewrite'] 			= isset($setting['exchange_1c_rewrite_h1_cat']) ? (int)$setting['exchange_1c_rewrite_h1_cat'] : 1;
	$result['cat']['h1']['generate'] 			= isset($setting['exchange_1c_generate_h1_cat']) ? (int)$setting['exchange_1c_generate_h1_cat'] : 0;
	$result['cat']['h1']['template'] 			= isset($setting['exchange_1c_template_h1_cat']) ? (string)$setting['exchange_1c_template_h1_cat'] : '[category_name]';
	$result['cat']['h1']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_h1_cat']) ? (string)$setting['exchange_1c_all_category_sep_h1_cat'] : ' ';
	$result['cat']['h1']['clear'] 				= isset($setting['exchange_1c_clear_h1_cat']) ? (string)$setting['exchange_1c_clear_h1_cat'] : '';
	$result['cat']['h1']['replace']				= isset($setting['exchange_1c_replace_h1_cat']) ? (string)$setting['exchange_1c_replace_h1_cat'] : '';
	$result['cat']['h1']['transliterate'] 			= isset($setting['exchange_1c_transliterate_h1_cat']) ? (int)$setting['exchange_1c_transliterate_h1_cat'] : 0;
	$result['cat']['h1']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_h1_cat']) ? (int)$setting['exchange_1c_transliterate_type_h1_cat'] : 0;

	$result['cat']['title']['load'] 			= isset($setting['exchange_1c_load_title_cat']) ? (int)$setting['exchange_1c_load_title_cat'] : 1;
	$result['cat']['title']['rewrite'] 			= isset($setting['exchange_1c_rewrite_title_cat']) ? (int)$setting['exchange_1c_rewrite_title_cat'] : 1;
	$result['cat']['title']['generate'] 			= isset($setting['exchange_1c_generate_title_cat']) ? (int)$setting['exchange_1c_generate_title_cat'] : 0;
	$result['cat']['title']['template'] 			= isset($setting['exchange_1c_template_title_cat']) ? (string)$setting['exchange_1c_template_title_cat'] : '[category_name]';
	$result['cat']['title']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_title_cat']) ? (string)$setting['exchange_1c_all_category_sep_title_cat'] : ' ';
	$result['cat']['title']['clear'] 			= isset($setting['exchange_1c_clear_title_cat']) ? (string)$setting['exchange_1c_clear_title_cat'] : '';
	$result['cat']['title']['replace']			= isset($setting['exchange_1c_replace_title_cat']) ? (string)$setting['exchange_1c_replace_title_cat'] : '';
	$result['cat']['title']['transliterate'] 		= isset($setting['exchange_1c_transliterate_title_cat']) ? (int)$setting['exchange_1c_transliterate_title_cat'] : 0;
	$result['cat']['title']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_title_cat']) ? (int)$setting['exchange_1c_transliterate_type_title_cat'] : 0;

	$result['cat']['mkey']['load'] 				= isset($setting['exchange_1c_load_mkey_cat']) ? (int)$setting['exchange_1c_load_mkey_cat'] : 1;
	$result['cat']['mkey']['rewrite'] 			= isset($setting['exchange_1c_rewrite_mkey_cat']) ? (int)$setting['exchange_1c_rewrite_mkey_cat'] : 1;
	$result['cat']['mkey']['generate'] 			= isset($setting['exchange_1c_generate_mkey_cat']) ? (int)$setting['exchange_1c_generate_mkey_cat'] : 0;
	$result['cat']['mkey']['template'] 			= isset($setting['exchange_1c_template_mkey_cat']) ? (string)$setting['exchange_1c_template_mkey_cat'] : '';
	$result['cat']['mkey']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_mkey_cat']) ? (string)$setting['exchange_1c_all_category_sep_mkey_cat'] : ' ';
	$result['cat']['mkey']['clear'] 			= isset($setting['exchange_1c_clear_mkey_cat']) ? (string)$setting['exchange_1c_clear_mkey_cat'] : '';
	$result['cat']['mkey']['replace']			= isset($setting['exchange_1c_replace_mkey_cat']) ? (string)$setting['exchange_1c_replace_mkey_cat'] : '';
	$result['cat']['mkey']['transliterate'] 		= isset($setting['exchange_1c_transliterate_mkey_cat']) ? (int)$setting['exchange_1c_transliterate_mkey_cat'] : 0;
	$result['cat']['mkey']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_mkey_cat']) ? (int)$setting['exchange_1c_transliterate_type_mkey_cat'] : 0;

	$result['cat']['mdesc']['load'] 			= isset($setting['exchange_1c_load_mdesc_cat']) ? (int)$setting['exchange_1c_load_mdesc_cat'] : 1;
	$result['cat']['mdesc']['rewrite'] 			= isset($setting['exchange_1c_rewrite_mdesc_cat']) ? (int)$setting['exchange_1c_rewrite_mdesc_cat'] : 1;
	$result['cat']['mdesc']['generate'] 			= isset($setting['exchange_1c_generate_mdesc_cat']) ? (int)$setting['exchange_1c_generate_mdesc_cat'] : 0;
	$result['cat']['mdesc']['template'] 			= isset($setting['exchange_1c_template_mdesc_cat']) ? (string)$setting['exchange_1c_template_mdesc_cat'] : '';
	$result['cat']['mdesc']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_mdesc_cat']) ? (string)$setting['exchange_1c_all_category_sep_mdesc_cat'] : ' ';
	$result['cat']['mdesc']['clear'] 			= isset($setting['exchange_1c_clear_mdesc_cat']) ? (string)$setting['exchange_1c_clear_mdesc_cat'] : '';
	$result['cat']['mdesc']['replace']			= isset($setting['exchange_1c_replace_mdesc_cat']) ? (string)$setting['exchange_1c_replace_mdesc_cat'] : '';
	$result['cat']['mdesc']['transliterate'] 		= isset($setting['exchange_1c_transliterate_mdesc_cat']) ? (int)$setting['exchange_1c_transliterate_mdesc_cat'] : 0;
	$result['cat']['mdesc']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_mdesc_cat']) ? (int)$setting['exchange_1c_transliterate_type_mdesc_cat'] : 0;

	$result['cat']['desc']['load'] 				= isset($setting['exchange_1c_load_desc_cat']) ? (int)$setting['exchange_1c_load_desc_cat'] : 1;
	$result['cat']['desc']['rewrite'] 			= isset($setting['exchange_1c_rewrite_desc_cat']) ? (int)$setting['exchange_1c_rewrite_desc_cat'] : 1;
	$result['cat']['desc']['generate'] 			= isset($setting['exchange_1c_generate_desc_cat']) ? (int)$setting['exchange_1c_generate_desc_cat'] : 0;
	$result['cat']['desc']['template'] 			= isset($setting['exchange_1c_template_desc_cat']) ? (string)$setting['exchange_1c_template_desc_cat'] : '';
	$result['cat']['desc']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_desc_cat']) ? (string)$setting['exchange_1c_all_category_sep_desc_cat'] : ' ';
	$result['cat']['desc']['clear'] 			= isset($setting['exchange_1c_clear_desc_cat']) ? (string)$setting['exchange_1c_clear_desc_cat'] : '';
	$result['cat']['desc']['replace']			= isset($setting['exchange_1c_replace_desc_cat']) ? (string)$setting['exchange_1c_replace_desc_cat'] : '';
	$result['cat']['desc']['transliterate'] 		= isset($setting['exchange_1c_transliterate_desc_cat']) ? (int)$setting['exchange_1c_transliterate_desc_cat'] : 0;
	$result['cat']['desc']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_desc_cat']) ? (int)$setting['exchange_1c_transliterate_type_desc_cat'] : 0;

	// product
	$result['prod']['url']['load'] 				= isset($setting['exchange_1c_load_url_prod']) ? (int)$setting['exchange_1c_load_url_prod'] : 1;
	$result['prod']['url']['rewrite'] 			= isset($setting['exchange_1c_rewrite_url_prod']) ? (int)$setting['exchange_1c_rewrite_url_prod'] : 1;
	$result['prod']['url']['generate'] 			= isset($setting['exchange_1c_generate_url_prod']) ? (int)$setting['exchange_1c_generate_url_prod'] : 0;
	$result['prod']['url']['template'] 			= isset($setting['exchange_1c_template_url_prod']) ? (string)$setting['exchange_1c_template_url_prod'] : '[product_name]';
	$result['prod']['url']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_url_prod']) ? (string)$setting['exchange_1c_all_category_sep_url_prod'] : ' ';
	$result['prod']['url']['clear'] 			= isset($setting['exchange_1c_clear_url_prod']) ? (string)$setting['exchange_1c_clear_url_prod'] : '';
	$result['prod']['url']['replace']			= isset($setting['exchange_1c_replace_url_prod']) ? (string)$setting['exchange_1c_replace_url_prod'] : '';
	$result['prod']['url']['transliterate'] 		= isset($setting['exchange_1c_transliterate_url_prod']) ? (int)$setting['exchange_1c_transliterate_url_prod'] : 1;
	$result['prod']['url']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_url_cat']) ? (int)$setting['exchange_1c_transliterate_type_url_prod'] : 1;

	$result['prod']['h1']['load'] 				= isset($setting['exchange_1c_load_h1_prod']) ? (int)$setting['exchange_1c_load_h1_prod'] : 1;
	$result['prod']['h1']['rewrite'] 			= isset($setting['exchange_1c_rewrite_h1_prod']) ? (int)$setting['exchange_1c_rewrite_h1_prod'] : 1;
	$result['prod']['h1']['generate'] 			= isset($setting['exchange_1c_generate_h1_prod']) ? (int)$setting['exchange_1c_generate_h1_prod'] : 0;
	$result['prod']['h1']['template'] 			= isset($setting['exchange_1c_template_h1_prod']) ? (string)$setting['exchange_1c_template_h1_prod'] : '[product_name]';
	$result['prod']['h1']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_h1_prod']) ? (string)$setting['exchange_1c_all_category_sep_h1_prod'] : ' ';
	$result['prod']['h1']['clear'] 				= isset($setting['exchange_1c_clear_h1_prod']) ? (string)$setting['exchange_1c_clear_h1_prod'] : '';
	$result['prod']['h1']['replace']			= isset($setting['exchange_1c_replace_h1_prod']) ? (string)$setting['exchange_1c_replace_h1_prod'] : '';
	$result['prod']['h1']['transliterate'] 			= isset($setting['exchange_1c_transliterate_h1_prod']) ? (int)$setting['exchange_1c_transliterate_h1_prod'] : 0;
	$result['prod']['h1']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_h1_cat']) ? (int)$setting['exchange_1c_transliterate_type_h1_prod'] : 0;

	$result['prod']['title']['load'] 			= isset($setting['exchange_1c_load_title_prod']) ? (int)$setting['exchange_1c_load_title_prod'] : 1;
	$result['prod']['title']['rewrite'] 			= isset($setting['exchange_1c_rewrite_title_prod']) ? (int)$setting['exchange_1c_rewrite_title_prod'] : 1;
	$result['prod']['title']['generate'] 			= isset($setting['exchange_1c_generate_title_prod']) ? (int)$setting['exchange_1c_generate_title_prod'] : 0;
	$result['prod']['title']['template'] 			= isset($setting['exchange_1c_template_title_prod']) ? (string)$setting['exchange_1c_template_title_prod'] : '[product_name]';
	$result['prod']['title']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_title_prod']) ? (string)$setting['exchange_1c_all_category_sep_title_prod'] : ' ';
	$result['prod']['title']['clear'] 			= isset($setting['exchange_1c_clear_title_prod']) ? (string)$setting['exchange_1c_clear_title_prod'] : '';
	$result['prod']['title']['replace']			= isset($setting['exchange_1c_replace_title_prod']) ? (string)$setting['exchange_1c_replace_title_prod'] : '';
	$result['prod']['title']['transliterate'] 		= isset($setting['exchange_1c_transliterate_title_prod']) ? (int)$setting['exchange_1c_transliterate_title_prod'] : 0;
	$result['prod']['title']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_title_cat']) ? (int)$setting['exchange_1c_transliterate_type_title_prod'] : 0;

	$result['prod']['mkey']['load'] 			= isset($setting['exchange_1c_load_mkey_prod']) ? (int)$setting['exchange_1c_load_mkey_prod'] : 1;
	$result['prod']['mkey']['rewrite'] 			= isset($setting['exchange_1c_rewrite_mkey_prod']) ? (int)$setting['exchange_1c_rewrite_mkey_prod'] : 1;
	$result['prod']['mkey']['generate'] 			= isset($setting['exchange_1c_generate_mkey_prod']) ? (int)$setting['exchange_1c_generate_mkey_prod'] : 0;
	$result['prod']['mkey']['template'] 			= isset($setting['exchange_1c_template_mkey_prod']) ? (string)$setting['exchange_1c_template_mkey_prod'] : '';
	$result['prod']['mkey']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_mkey_prod']) ? (string)$setting['exchange_1c_all_category_sep_mkey_prod'] : ' ';
	$result['prod']['mkey']['clear'] 			= isset($setting['exchange_1c_clear_mkey_prod']) ? (string)$setting['exchange_1c_clear_mkey_prod'] : '';
	$result['prod']['mkey']['replace']			= isset($setting['exchange_1c_replace_mkey_prod']) ? (string)$setting['exchange_1c_replace_mkey_prod'] : '';
	$result['prod']['mkey']['transliterate'] 		= isset($setting['exchange_1c_transliterate_mkey_prod']) ? (int)$setting['exchange_1c_transliterate_mkey_prod'] : 0;
	$result['prod']['mkey']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_mkey_cat']) ? (int)$setting['exchange_1c_transliterate_type_mkey_prod'] : 0;

	$result['prod']['mdesc']['load'] 			= isset($setting['exchange_1c_load_mdesc_prod']) ? (int)$setting['exchange_1c_load_mdesc_prod'] : 1;
	$result['prod']['mdesc']['rewrite'] 			= isset($setting['exchange_1c_rewrite_mdesc_prod']) ? (int)$setting['exchange_1c_rewrite_mdesc_prod'] : 1;
	$result['prod']['mdesc']['generate'] 			= isset($setting['exchange_1c_generate_mdesc_prod']) ? (int)$setting['exchange_1c_generate_mdesc_prod'] : 0;
	$result['prod']['mdesc']['template'] 			= isset($setting['exchange_1c_template_mdesc_prod']) ? (string)$setting['exchange_1c_template_mdesc_prod'] : '';
	$result['prod']['mdesc']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_mdesc_prod']) ? (string)$setting['exchange_1c_all_category_sep_mdesc_prod'] : ' ';
	$result['prod']['mdesc']['clear'] 			= isset($setting['exchange_1c_clear_mdesc_prod']) ? (string)$setting['exchange_1c_clear_mdesc_prod'] : '';
	$result['prod']['mdesc']['replace']			= isset($setting['exchange_1c_replace_mdesc_prod']) ? (string)$setting['exchange_1c_replace_mdesc_prod'] : '';
	$result['prod']['mdesc']['transliterate'] 		= isset($setting['exchange_1c_transliterate_mdesc_prod']) ? (int)$setting['exchange_1c_transliterate_mdesc_prod'] : 0;
	$result['prod']['mdesc']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_mdesc_cat']) ? (int)$setting['exchange_1c_transliterate_type_mdesc_prod'] : 0;

	$result['prod']['desc']['load'] 			= isset($setting['exchange_1c_load_desc_prod']) ? (int)$setting['exchange_1c_load_desc_prod'] : 1;
	$result['prod']['desc']['rewrite'] 			= isset($setting['exchange_1c_rewrite_desc_prod']) ? (int)$setting['exchange_1c_rewrite_desc_prod'] : 1;
	$result['prod']['desc']['generate'] 			= isset($setting['exchange_1c_generate_desc_prod']) ? (int)$setting['exchange_1c_generate_desc_prod'] : 0;
	$result['prod']['desc']['template'] 			= isset($setting['exchange_1c_template_desc_prod']) ? (string)$setting['exchange_1c_template_desc_prod'] : '';
	$result['prod']['desc']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_desc_prod']) ? (string)$setting['exchange_1c_all_category_sep_desc_prod'] : ' ';
	$result['prod']['desc']['clear'] 			= isset($setting['exchange_1c_clear_desc_prod']) ? (string)$setting['exchange_1c_clear_desc_prod'] : '';
	$result['prod']['desc']['replace']			= isset($setting['exchange_1c_replace_desc_prod']) ? (string)$setting['exchange_1c_replace_desc_prod'] : '';
	$result['prod']['desc']['transliterate'] 		= isset($setting['exchange_1c_transliterate_desc_prod']) ? (int)$setting['exchange_1c_transliterate_desc_prod'] : 0;
	$result['prod']['desc']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_desc_cat']) ? (int)$setting['exchange_1c_transliterate_type_desc_prod'] : 0;

	$result['prod']['tag']['load'] 				= isset($setting['exchange_1c_load_tag_prod']) ? (int)$setting['exchange_1c_load_tag_prod'] : 1;
	$result['prod']['tag']['rewrite'] 			= isset($setting['exchange_1c_rewrite_tag_prod']) ? (int)$setting['exchange_1c_rewrite_tag_prod'] : 1;
	$result['prod']['tag']['generate'] 			= isset($setting['exchange_1c_generate_tag_prod']) ? (int)$setting['exchange_1c_generate_tag_prod'] : 0;
	$result['prod']['tag']['template'] 			= isset($setting['exchange_1c_template_tag_prod']) ? (string)$setting['exchange_1c_template_tag_prod'] : '';
	$result['prod']['tag']['all_category_sep']		= isset($setting['exchange_1c_all_category_sep_tag_prod']) ? (string)$setting['exchange_1c_all_category_sep_tag_prod'] : ' ';
	$result['prod']['tag']['clear'] 			= isset($setting['exchange_1c_clear_tag_prod']) ? (string)$setting['exchange_1c_clear_tag_prod'] : '';
	$result['prod']['tag']['replace']			= isset($setting['exchange_1c_replace_tag_prod']) ? (string)$setting['exchange_1c_replace_tag_prod'] : '';
	$result['prod']['tag']['transliterate'] 		= isset($setting['exchange_1c_transliterate_tag_prod']) ? (int)$setting['exchange_1c_transliterate_tag_prod'] : 0;
	$result['prod']['tag']['transliterate_type']		= isset($setting['exchange_1c_transliterate_type_tag_cat']) ? (int)$setting['exchange_1c_transliterate_type_tag_prod'] : 0;

	// product params
	$result['model']	 				= isset($setting['exchange_1c_model']) ? (int)$setting['exchange_1c_model'] : 1;
	$result['subtract'] 					= isset($setting['exchange_1c_subtract']) ? (int)$setting['exchange_1c_subtract'] : 1;
	$result['shipping'] 					= isset($setting['exchange_1c_shipping']) ? (int)$setting['exchange_1c_shipping'] : 1;
	$result['only_1c_options'] 				= isset($setting['exchange_1c_only_1c_options']) ? (int)$setting['exchange_1c_only_1c_options'] : 1;
	$result['attribute_group'] 				= isset($setting['exchange_1c_attribute_group']) ? (int)$setting['exchange_1c_attribute_group'] : 0;
	$result['update_date_available'] 			= isset($setting['exchange_1c_update_date_available']) ? (int)$setting['exchange_1c_update_date_available'] : 0;
	$result['status_unavailable'] 				= isset($setting['exchange_1c_status_unavailable']) ? (int)$setting['exchange_1c_status_unavailable'] : 0;

	$result['stock_statuses']				= array(
									'0' => isset($setting['exchange_1c_stock0']) ? (int)$setting['exchange_1c_stock0'] : 0,
									'1' => isset($setting['exchange_1c_stock1']) ? (int)$setting['exchange_1c_stock1'] : 0,
									'2' => isset($setting['exchange_1c_stock2']) ? (int)$setting['exchange_1c_stock2'] : 0
								);

	// langs
	$result['translit_name'] 				= isset($setting['exchange_1c_translit_name']) ? (int)$setting['exchange_1c_translit_name'] : 0;
	$result['save_other_lang'] 				= isset($setting['exchange_1c_save_other_lang']) ? (int)$setting['exchange_1c_save_other_lang'] : 0;

	// secure
	$result['secure_id']					= isset($setting['exchange_1c_secure_id']) ? (string)$setting['exchange_1c_secure_id'] : '';
	$result['secure_login']					= isset($setting['exchange_1c_secure_login']) ? (string)$setting['exchange_1c_secure_login'] : '';
	$result['secure_pswd']					= isset($setting['exchange_1c_secure_pswd']) ? (string)$setting['exchange_1c_secure_pswd'] : '';


	return $result;
	}

	/**
	 * @param string $signature
	 * @return boolean
	 */
	private function _validateSignature($signature) {
		$hash = md5($this->setting['secure_id'].';'.$this->setting['secure_login'].';'.$this->setting['secure_pswd']);
		return $hash === $signature;
	}

	/**
	 * @param array $args
	 * @param string $template
	 * @param string $clear
	 * @param string $replace
	 * @return string
	 */
	private function _parseTemplate($args, $template, $clear, $replace, $all_category_sep) {
		$args[1] = str_replace($this->all_category_sep, $all_category_sep, $args[1]);

		$clear_arr = explode('|', $clear);
		$replace_arr = explode('|', $replace);
		foreach ($args as $k => $v) {
			$args[$k] = str_replace($clear_arr, (sizeof($replace_arr) > 1) ? $replace_arr : $replace, $v);
		}

		return str_replace($this->template_tags, $args, $template);
	}

	/**
	 * @param array $old_info
	 * @param array $new_info
	 * @param array $template_args
	 * @param array $setting
	 * @param string $arg_name
	 * @return string
	 */
	private function _prepareArg($old_info, $new_info, $template_args, $setting, $arg_name) {
		if ($setting['rewrite'] == 0 && isset($old_info[$arg_name]) && $old_info[$arg_name]) {
			$result = $old_info[$arg_name];
		} else {
			if ($setting['load'] == 1 && isset($new_info[$arg_name]) && $new_info[$arg_name]) {
				$result = $new_info[$arg_name];
			} else {
				if ($setting['generate'] == 1) {

					$result = $this->_parseTemplate($template_args, $setting['template'], $setting['clear'], $setting['replace'], $setting['all_category_sep']);
					if ($setting['transliterate'] == 1) {
						$result = $this->_transliterateString($result, $setting['transliterate_type']);
					}
				} else {
					$result = '';
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $code
	 * @return mixed
	 */
	private function _getLanguageId($code = false) {
		$this->load->model("localisation/language");

		$data = $this->model_localisation_language->getLanguages();

		if (!$code) {
			return $data;
		}

		foreach ($data as $lang) {
			if ($lang["code"] == $code) {
				return $lang["language_id"];
			}
		}

		return 0;
	}

	/**
	 * @return array
	 */
	private function _getStores() {
		$stores[] = 0;

		$this->load->model('setting/store');

		foreach ($this->model_setting_store->getStores() as $store) {
			$stores[] = (int)$store['store_id'];
		}

		return $stores;
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function sendCategories($args, $signature) {
		// Пишем логи
		$this->log($args, 'sendCategories(): $args', "send_categories_data.log");

		// Права доступа (цифровая подпись)
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'sendCategories():');
			return array('error' => 'Signature is not correct');
		}

		// Подключаем файл для работы с базой
		$this->load->model('catalog/category');

		// Языки
		$languages = $this->_getLanguageId(); // id всех языков
		$this->log($languages, 'sendCategories(): $languages');

		// Язык по умолчанию
		$lang = $this->_getLanguageId($this->language_default); // id языка с iso_code = $this->language_default
		$this->log($lang, 'sendCategories(): $lang');

		$args = (array)$args; // Объявляем массив для данных из 1с

		if ($args['category']) {
			$this->_saveCategoriesTree($args['category'], 0, $languages, $lang);
		}

		//$this->cache->delete('category');

		$this->log("Операция завершена", 'sendCategories():');

		return array('error' => '', 'category' => $this->category_ids);
	}

	/**
	 * @param integer $category_id
	 * @return string
	 */
	private function _getCategory($category_id) {
		return $this->model_catalog_category->getCategory($category_id);
	}

	/**
	 * @param mixed $args
	 * @param array $languages
	 * @param integer $lang
	 * @return integer
	 */
	private function _saveCategory($args, $languages, $lang) {
		$category_id = (int)$args['id'];

		if ($category_id) {
			$category_description = $this->model_catalog_category->getCategoryDescriptions($category_id);
			if (!empty($category_description[$lang])) {
				$category_info = array_merge($this->model_catalog_category->getCategory($category_id), $category_description[$lang]);
			} else {
				$category_info = array_merge($this->model_catalog_category->getCategory($category_id), $category_description[(int)$this->config->get('config_language_id')]);
			}
		} else {
			$category_info = array();
			$category_description = array();
		}

		$template_args 		= array($args['name'], $this->_getAllCatsForCat($args), '', '', '', '');
		$seo_h1 		= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['h1'], 'seo_h1');
		$seo_title 		= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['title'], 'seo_title');
		$meta_keyword 		= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['mkey'], 'meta_keyword');
		$meta_description 	= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['mdesc'], 'meta_description');
		$description 		= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['desc'], 'description');
		$keyword 		= $this->_prepareArg($category_info, $args, $template_args, $this->setting['cat']['url'], 'keyword');
		$keyword 		= $this->_makeUniqueKeyword($keyword, 'category_id', $category_id);
		$descriptions 		= array();

		foreach ($languages as $k => $v) {
			if ($v["language_id"] == $lang) {
				$descriptions[$v["language_id"]] = array(
					'name' 				=> $args['name'],
					'seo_h1' 			=> $seo_h1,
					'seo_title' 			=> $seo_title,
					'meta_keyword' 			=> $meta_keyword,
					'meta_description' 		=> $meta_description,
					'description' 			=> $description,
					'meta_title'			=> $seo_title,
				);
			} else {
				$descriptions[$v["language_id"]] = (isset($category_description[$v["language_id"]]) && $this->setting['save_other_lang']) ? $category_description[$v["language_id"]] : array(
					'name' 				=> $this->setting['translit_name'] ? $this->_transliterateString($args['name'], false) : '',//$this->_transliterateString($args['name'], false), //: '',
					'seo_h1' 			=> '',
					'seo_title' 			=> '',
					'meta_keyword' 			=> '',
					'meta_description' 		=> '',
					'meta_title' 			=> '',
					'description' 			=> '',
				);
			}
		}

		if (isset($args['dir_image']) && $args['dir_image']) {
			$args['dir_image'] = $this->image_dir . $args['dir_image'];
		}

		if (isset($args['dir_image']) && $args['dir_image'] && $args['image']) {
			$this->_saveImage($args['dir_image'], $args['image']);
		}

		$data = array(
			'parent_id' 			=> $args['parent_id'],
			'top' 				=> $args['top'],
			'status' 			=> $args['status'],
			'image' 			=> isset($args['dir_image']) ? $args['dir_image'] : '',
			'sort_order' 			=> $args['sort_order'],
			'keyword' 			=> $keyword,
			'category_description' 		=> $descriptions,
			'category_store' 		=> $this->_getStores(),
			'column' 			=> '0',
		);

		$this->log($data, '_saveCategory(): $data', "send_categories_data.log");

		if ($args['delete'] == '0') {
			if ($category_id) {
				$this->model_catalog_category->editCategory($category_id, $data);
			} else {
				$category_id = $this->model_catalog_category->addCategory($data);
				$this->category_ids[$args['id_1c']] = $category_id;
			}
		} else {
			if ($category_id) {
				$this->model_catalog_category->deleteCategory($category_id);
				$category_id = 0;
			}
		}
		return $category_id;
	}

	/**
	 * @param 
	 * @param 
	 * @param 
	 * @param 
	 */
	private function _saveCategoriesTree($categories, $parent_id, $languages, $lang) {
		foreach ($categories as $category) {
			if ($category) {
				$category = (array)$category;
				if (!$this->_getCategory((int)$category['id'])) {
					$category['id'] = '0';
				}
				$category['parent_id'] = $parent_id;
				$category_id = $this->_saveCategory($category, $languages, $lang);
				if (isset($category['category']) && $category['category'] && is_array($category['category'])) {
					$this->_saveCategoriesTree($category['category'], $category_id, $languages, $lang);
				}
			}
		}
	}

	/**
	* @param mixed $args
	* @param string $signature
	* @return mixed
	*/
	public function sendProducts($args, $signature) {
		$this->log($args, 'sendProducts(): $args', "send_products_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'sendProducts():');
			return array('error' => 'Signature is not correct');
		}

		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/module/exchange_1c');

		$languages = $this->_getLanguageId();
		$this->log($languages, 'sendProducts(): $languages');

		$lang = $this->_getLanguageId($this->language_default);
		$this->log($lang, 'sendProducts(): $lang');

		$args = (array)$args;
		$products = $args['product'];

		if ($products) {
			foreach ($products as $product) {
				if ($product) {
					$product = (array)$product;
					if (!$this->_getProduct((int)$product['id'])) {
						$this->log("Товар не найден", '_getProduct(' . $product['id'] . '):');

						$product['id'] = '0';
					}
					$this->_saveProduct($product, $languages, $lang);
				}
			}
		}

		$this->log("Операция завершена", 'sendProducts():');

		return array('error' => '', 'product' => $this->product_ids);
	}

	/**
	 * @param integer $product_id
	 * @return mixed
	 */
	private function _getProduct($product_id) {
		return $this->model_catalog_product->getProduct($product_id);
	}

	/**
	 * @param
	 * @param
	 * @param
	 * @return
	 */
	private function _saveProduct($args, $languages, $lang) {
		$this->log($args, '_saveProduct(): $args', "send_products_data.log");

		$product_id = (int)$args['id'];

		if ($product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			$product_description = $this->model_catalog_product->getProductDescriptions($product_id);
			$product_attributes = $this->model_extension_module_exchange_1c->getProductAttributes($product_id);
			$product_discounts = $this->model_catalog_product->getProductDiscounts($product_id);
			$product_specials = $this->model_catalog_product->getProductSpecials($product_id);
		} else {
			$product_info = array();
			$product_description = array();
			$product_attributes = array();
			$product_discounts = array();
			$product_specials = array();
		}

		$template_args = array($args['name'], $this->_getAllCatsForProd($args['main_category_id']), $args['manufacturer'], $args['name'], $args['model'], $this->currency->format($args['price'], $this->config->get('config_currency')));

		$seo_h1 			= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['h1'], 'seo_h1');
		$seo_title 			= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['title'], 'seo_title');
		$meta_keyword 			= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['mkey'], 'meta_keyword');
		$meta_description 		= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['mdesc'], 'meta_description');
		$description 			= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['desc'], 'description');
		$tag		 		= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['tag'], 'tag');
		$keyword 			= $this->_prepareArg($product_info, $args, $template_args, $this->setting['prod']['url'], 'keyword');

		if ($keyword) {
			$keyword = $this->_makeUniqueKeyword($keyword, 'product_id', $product_id);
		}

		$descriptions = array();

		foreach ($languages as $k => $v) {
			if ($v["language_id"] == $lang) {
				$descriptions[$v["language_id"]] = array(
					'name' 			=> $args['name'],
					'seo_h1' 		=> $seo_h1,
					'seo_title' 		=> $seo_title,
					'meta_keyword' 		=> $meta_keyword,
					'meta_description' 	=> $meta_description,
					'description' 		=> $description,
					'tag' 			=> $tag,
					'meta_title' 		=> $seo_title,
				);
			} else {
				$descriptions[$v["language_id"]] = (isset($product_description[$v["language_id"]]) && $this->setting['save_other_lang']) ? $product_description[$v["language_id"]] : array(
					'name' 			=> $this->setting['translit_name'] ? $this->_transliterateString($args['name'], false) : '',
					'seo_h1' 		=> '',
					'seo_title' 		=> '',
					'meta_keyword' 		=> '',
					'meta_description' 	=> '',
					'description' 		=> '',
					'tag' 			=> '',
					'meta_title' 		=> '',
				);
			}
		}

		$manufacturer_id = 0;

		if ($args['manufacturer']) {
			$manufacturer_info = $this->model_extension_module_exchange_1c->getManufacturerByName((string)$args['manufacturer']);

			if (!$manufacturer_info) {
				$manufacturer_data['name'] = (string)$args['manufacturer'];
				$manufacturer_data['sort_order'] = 0;
				$manufacturer_data['manufacturer_description'] = array();
				$manufacturer_keyword = $this->_transliterateString((string)$args['manufacturer'], true);
				$manufacturer_data['keyword'] = $this->_makeUniqueKeyword($manufacturer_keyword, 'manufacturer_id', 0);
				$manufacturer_data['manufacturer_store'] = $this->_getStores();

				foreach ($languages as $k => $v) {
					if ($v["language_id"] == $lang) {
						$manufacturer_data['manufacturer_description'][$v["language_id"]] = array('description' => (string)$args['manufacturer'], 'meta_keyword' => '', 'meta_description' => '', 'seo_title' => '', 'seo_h1' => '');
					} else {
						$manufacturer_data['manufacturer_description'][$v["language_id"]] = array('description' => $this->_transliterateString((string)$args['manufacturer'], false), 'meta_keyword' => '', 'meta_description' => '', 'seo_title' => '', 'seo_h1' => '');
					}
				}

				$manufacturer_id = $this->model_catalog_manufacturer->addManufacturer($manufacturer_data);
			} else {
				$manufacturer_id = $manufacturer_info['manufacturer_id'];
			}
		}

		if (isset($args['dir_image']) && $args['dir_image'] && $args['image']) {
			$args['dir_image'] = $this->image_dir . $args['dir_image'];
		}

		if (isset($args['dir_image']) && $args['dir_image'] && $args['image']) {
			$this->_saveImage($args['dir_image'], $args['image']);
		}

		$data = array(
			'status' 		=> $args['status'],
			'image' 		=> isset($args['dir_image']) ? $args['dir_image'] : '',
			'product_image' 	=> isset($args['product_image']) ? $this->_productImages((array)$args['product_image']) : array(),
			'keyword' 		=> $keyword,
			'quantity' 		=> $args['quantity'],
			'price' 		=> $args['price'],
			'main_category_id' 	=> $args['main_category_id'],
			'model' 		=> $args['model'] ? $args['model'] : ($this->setting['model'] == 1 ? $args['name'] : $args['scu']),
			'product_description' 	=> $descriptions,
			'sku' 			=> $args['scu'],
			'upc' 			=> $args['upc'],
			'product_category' 	=> $this->_productCategories((array)$args['categories']),
			'manufacturer_id' 	=> $manufacturer_id,
			'sort_order' 		=> isset($args['sort_order']) ? $args['sort_order'] : '0',
			'product_related'	=> $this->_relatedProducts((array)$args['product_related']),
			'stock_status_id' 	=> isset($this->setting['stock_statuses'][$args['stock_status']]) ? $this->setting['stock_statuses'][$args['stock_status']] : $this->setting['stock_status_id'],
			'weight' 		=> isset($args['weight']) ? $args['weight'] : 0,
			'length' 		=> isset($args['size_length']) ? $args['size_length'] : '0',
			'width' 		=> isset($args['size_width']) ? $args['size_width'] : '0',
			'height' 		=> isset($args['size_height']) ? $args['size_height'] : '0',

			'product_store' 	=> $this->_getStores(),
			'shipping' 		=> $this->setting['shipping'],
			'subtract' 		=> $this->setting['subtract'],
			'date_available' 	=> (!$product_id || $this->setting['update_date_available'] == 1) ? date('Y-m-d H:i:s') : $product_info['date_available'],
			'length_class_id' 	=> 1,
			'weight_class_id' 	=> 1,
			'ean' 			=> '',
			'jan' 			=> '',
			'isbn' 			=> '',
			'mpn' 			=> '',
			'location' 		=> '',
			'minimum' 		=> '0',
			'points' 		=> '0',
			'tax_class_id' 		=> '0',

			'product_discount'	=> $product_discounts,
			'product_special'	=> $product_specials,
		);

		if ($data['quantity'] == 0 && $this->setting['status_unavailable'] == 1) {
			$data['status'] = 0;
		}

		$this->log($data, '_saveProduct(): $data', "send_products_data.log");

		$option_exchange = $this->model_extension_module_exchange_1c->getProductOptionValueExchange($product_id);

		if ($args['delete'] == '0') {
			if ($product_id) {
				$this->log("Вызов model_catalog_product->editProduct()", '_saveProduct():');

				if (!$this->setting['only_1c_options']) {
					$non_1c_product_options = $this->_getNon1cProductOptions($product_id, $option_exchange);
				}

				$this->model_catalog_product->editProduct($product_id, $data);
			} else {
				$this->log("Вызов model_catalog_product->addProduct()", '_saveProduct():');

				$product_id = $this->model_catalog_product->addProduct($data);
				$this->product_ids[$args['id_1c']] = $product_id;
			}
		} else {
			if ($product_id) {
				$this->log("Вызов model_catalog_product->deleteProduct()", '_saveProduct():');

				$this->model_catalog_product->deleteProduct($product_id);
				$product_id = 0;
			}
		}

		if ($product_id) {
			$this->_saveProductAttributes($product_id, (array)$args['attributes'], $product_attributes);

			$this->_saveProductOptions($product_id, (array)$args['options'], $option_exchange);

			if (!$this->setting['only_1c_options']) {
				$this->_saveNon1cProductOptions($product_id, (array)$non_1c_product_options);
			}
		}

		return $product_id;
	}

	/**
	 * @param mixed $images
	 * @return array
	 */
	private function _productImages($images) {
		$buffer = array();
		$img_dir = $this->image_dir . '/product/';

		if (isset($images['image']) && is_array($images['image'])) {
			$last = array_pop($images['image']);

			foreach($images['image'] as $k => $v) {
				if ($images['dir_image'][$k]) {
					if ($v) {
						$this->_saveImage($img_dir . $images['dir_image'][$k], $v);
					}

					$buffer[] = array(
						'image' => $img_dir . $images['dir_image'][$k],
						'sort_order' => $k
					);
				}
			}
		}
		return $buffer;
	}

	/**
	 * @param
	 * @param
	 * @return
	 */
	private function _saveImage($name, $data) {
		$this->log($name, '_saveImage(): $name');
		$this->log($data, '_saveImage(): $data');

		if (!is_dir(dirname(DIR_IMAGE . $name))) {
			mkdir(dirname(DIR_IMAGE . $name), 0755, true);
		}

		if ($fp = fopen(DIR_IMAGE . $name, "wb")) {
			$wlen = 0;

			for ($written = 0; $written < strlen(base64_decode($data)); $written += $wlen ) {
				$wlen = fwrite($fp, substr(base64_decode($data), $written));
				if ($wlen === false) {
					return 0;
				}
			}

			fclose($fp);
			chmod(DIR_IMAGE . $name, 0644);
		}
	}

	/**
	 * @return integer
	 */
	private function _addGroupAttribute() {
		$name = "Характеристики";
		$sort_order = 1;
		$lang = $this->_getLanguageId($this->language_default);
		$languages = $this->_getLanguageId();

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$find = $db->query("SELECT attribute_group_id FROM " . DB_PREFIX . "attribute_group_description WHERE name='$name' limit 0,1");
		$name1 = $this->_transliterateString($name, false);

		if($find->num_rows == 0) {
			$find1 = $db->query("SELECT attribute_group_id FROM " . DB_PREFIX . "attribute_group_description WHERE name='$name1' limit 0,1");
			if ($find1->num_rows == 0){
				$db->query("INSERT INTO " . DB_PREFIX . "attribute_group (sort_order) VALUES ($sort_order);");
				$attribute_group_id = $db->getLastId();

				foreach ($languages as $k => $v) {
					if ($v["language_id"] == $lang) {
						$db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description VALUES ($attribute_group_id, $lang, '$name');");
					} else {
						$name_lang = $this->setting['translit_name'] ? $this->_transliterateString($name, false) : '';
						$db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description VALUES ($attribute_group_id, " . $v["language_id"] . ", '$name_lang');");
					}
				}
				return $attribute_group_id;
			} else {
				return $find1->row["attribute_group_id"];
			}
		} else {
			return $find->row["attribute_group_id"];
		}
	}

	/**
	 * @param
	 * @param
	 * @param
	 */
	private function _saveProductAttributes($product_id, $attributes, $product_attributes) {
		$attribute_names = is_array($attributes['property']) ? $attributes['property'] : array();
		$attribute_values = is_array($attributes['value']) ? $attributes['value'] : array();

		array_pop($attribute_names);
		array_pop($attribute_values);

		foreach ($attribute_names as $k => $attribute_name) {
			$attribute_data = array();

			foreach ($product_attributes as $attr) {
				if ($attr['name'] == $attribute_name) {
					$attribute_data = $attr;
				}
			}

			$attribute_value = $attribute_values[$k];

			$sort_order = 1;
			$lang = $this->_getLanguageId($this->language_default);
			$languages = $this->_getLanguageId();

			$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
			$rg = new Registry();
			$rg->set('db', $db);

			$find = $db->query("SELECT attribute_id FROM " . DB_PREFIX ."attribute_description WHERE name='$attribute_name' AND language_id=$lang limit 0,1");

			if ($find->num_rows == 0) {
				$attr_name = $this->_transliterateString($attribute_name, false);

				$find1 = $db->query("SELECT attribute_id FROM " . DB_PREFIX ."attribute_description WHERE name='$attr_name' AND language_id= " . (int)$this->config->get('config_language_id') . " limit 0,1");

				if ($find1->num_rows == 0) {

					$attribute_group_id = $this->_addGroupAttribute();

					$db->query("INSERT INTO " . DB_PREFIX . "attribute (attribute_group_id, sort_order) VALUES ($attribute_group_id, $sort_order);");
					$attribute_id = $db->getLastId();

					foreach ($languages as $k => $v) {
						if ($v["language_id"] == $lang) {
							$db->query("INSERT INTO " . DB_PREFIX . "attribute_description VALUES ($attribute_id, $lang, '$attribute_name');");
						} else {
							$attribute_name_lang = $this->setting['translit_name'] ? $this->_transliterateString($attribute_name, false) : '';
							$db->query("INSERT INTO " . DB_PREFIX . "attribute_description VALUES ($attribute_id, " . $v["language_id"] . ", '$attribute_name_lang');");
						}
					}
				} else {
					$attribute_id = $find1->row["attribute_id"];
				}
			} else {
					$attribute_id = $find->row["attribute_id"];
			}

			$db->query("delete FROM " . DB_PREFIX . "product_attribute WHERE product_id=$product_id AND attribute_id=$attribute_id");

			foreach ($languages as $k => $v) {
				if ($v["language_id"] == $lang) {
					$db->query("INSERT INTO " . DB_PREFIX . "product_attribute VALUES ($product_id, $attribute_id, $lang, '$attribute_value');");
				} else {
					$attribute_value_lang = (isset($attribute_data['product_attribute_description'][$v["language_id"]]['text']) && $this->setting['save_other_lang']) ? $attribute_data['product_attribute_description'][$v["language_id"]]['text'] :
						($this->setting['translit_name'] ? $this->_transliterateString($attribute_value, false) : '');
					$db->query("INSERT INTO " . DB_PREFIX . "product_attribute VALUES ($product_id, $attribute_id, " . $v["language_id"] . ", '$attribute_value_lang');");
				}
			}
		}
	}

	/**
	 * @param integer $product_id
	 * @param mixed $option_exchange
	 * @return mixed
	 */
	private function _getNon1cProductOptions($product_id, $option_exchange = null) {
		$product_options = $this->model_catalog_product->getProductOptions($product_id);
		$this->log($product_options, '_getNon1cProductOptions(): $product_options', "non_1c_product_options_data.log");

		if (!$option_exchange) {
			$option_exchange = $this->model_extension_module_exchange_1c->getProductOptionValueExchange($product_id);
			$this->log($option_exchange, '_getNon1cProductOptions(): $option_exchange', "non_1c_product_options_data.log");
		}

		foreach ($product_options as $po_key => $product_option) {
			$this->log('11111', '$product_options as $po_key => $product_option', "options-x.log");

			foreach ($product_option['product_option_value'] as $pov_key => $product_option_value) {
				$this->log('22222', '$product_option[product_option_value] as $pov_key => $product_option_value', "options-x.log");

				foreach ($option_exchange as $option_exchange_data) {
					$this->log('33333', '$option_exchange as $option_exchange_data', "options-x.log");

					if ($option_exchange_data['product_option_value_id'] == $product_option_value['product_option_value_id']) {
						$this->log('=====', "$po_key $pov_key", "options-x.log");

						unset($product_options[$po_key]['product_option_value'][$pov_key]);

						if (count($product_options[$po_key]['product_option_value']) == 0) {
							unset($product_options[$po_key]);
						}

						break;
					}
				}
			}
		}

		$this->log($product_options, '_getNon1cProductOptions(): $product_options (cleaned)', "non_1c_product_options_data.log");

		return $product_options;
	}

	/**
	 * @param integer $product_id
	 * @param mixed $data
	 * @return null
	 */
	private function _saveNon1cProductOptions($product_id, $data) {
		if (count($data) > 0) {
			foreach ($data as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}
	}

	/**
	 * @param integer $product_id
	 * @param mixed $args
	 * @param mixed $option_exchange
	 * @return mixed
	 */
	private function _saveProductOptions($product_id, $args, $option_exchange) {
		$this->log($product_id, "product_id: ", "save_product_options_data.log");
		$this->log($args, "args: ", "save_product_options_data.log");
		$this->log($option_exchange, "option_exchange: ", "save_product_options_data.log");

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$this->model_extension_module_exchange_1c->clearProductOption($product_id);

		$related = $this->model_extension_module_exchange_1c->existExtension('module', 'related_options'); // checking if module related_option is installed

		$language_id = $this->_getLanguageId($this->language_default);
		$languages = $this->_getLanguageId();
		$sort_order = 1;
		$required = 1;
		$type = 'select';

		$option_names = is_array($args['option_name']) ? $args['option_name'] : array();
		$option_values = is_array($args['option_value']) ? $args['option_value'] : array();
		$option_links = is_array($args['option_link']) ? $args['option_link'] : array();
		$option_prices = is_array($args['option_price']) ? $args['option_price'] : array();
		$option_weight = is_array($args['option_weight']) ? $args['option_weight'] : array();
		$option_quantity = is_array($args['option_quantity']) ? $args['option_quantity'] : array();
		$option_orders = is_array($args['option_order']) ? $args['option_order'] : array();
		$ids_1c = (isset($args['option_id_1c']) && is_array($args['option_id_1c'])) ? $args['option_id_1c'] : array();

		array_pop($option_names);
		array_pop($option_values);
		array_pop($option_links);
		array_pop($option_prices);
		array_pop($option_weight);
		array_pop($option_quantity);
		array_pop($ids_1c);

		$result = array();
		$result_option = array();

		foreach ($option_names as $key => $option_name) {

			$option_value = $option_values[$key];
			$option_link = $option_links[$key];
			$option_price = $option_prices[$key];
			$option_w = $option_weight[$key];
			$option_q = $option_quantity[$key];
			$option_order = $option_orders[$key];
			$id_1c = $ids_1c[$key];

			$trimmed_option_value = trim($option_value);

			if (empty($trimmed_option_value)) {
				continue;
			}

			$find_result = array();

			foreach ($result as $res) {
				if ($res['option_name'] == $option_name) {
					$find_result = $res;
					break;
				}
			}

			// (1) option and product option
			if (!isset($option_exchange[$id_1c]) && !$find_result) {
				$find = $db->query("SELECT option_id FROM " . DB_PREFIX . "option_description WHERE name = '$option_name'");

				if ($find->num_rows == 0) {
					$opt_name = $this->_transliterateString($option_name, false);
					$find1 = $db->query("SELECT option_id FROM " . DB_PREFIX . "option_description WHERE name = '$opt_name'");

					if ($find1->num_rows == 0) {
						$db->query("INSERT INTO `" . DB_PREFIX . "option` (`type`, `sort_order`) VALUES ('$type', $option_order);");

						$option_id = $db->getLastId();

						foreach ($languages as $k => $v) {
							if ($v["language_id"] == $language_id) {
								$db->query("INSERT INTO " . DB_PREFIX . "option_description (`option_id`, `language_id`, `name`) VALUES ($option_id, $language_id, '$option_name');");
							} else {
								$option_name_lang = $this->setting['translit_name'] ? $this->_transliterateString($option_name, false) : '';
								$db->query("INSERT INTO " . DB_PREFIX . "option_description (`option_id`, `language_id`, `name`) VALUES ($option_id, " . $v["language_id"] . ", '$option_name_lang');");
							}
						}
					} else {
						$option_id = $find1->row['option_id'];
					}
				} else {
					$option_id = $find->row['option_id'];
				}

				$db->query("INSERT INTO " . DB_PREFIX . "product_option (`product_id`, `option_id`, `value`, `required`) VALUES ($product_id, $option_id, '', $required);");
				$product_option_id = $db->getLastId();
			} elseif (!$find_result) {
				$option_id = $option_exchange[$id_1c]['option_id'];
				$product_option_id = $option_exchange[$id_1c]['product_option_id'];

				$db->query("INSERT INTO " . DB_PREFIX . "product_option (`product_option_id`, `product_id`, `option_id`, `value`, `required`) VALUES ($product_option_id, $product_id, $option_id, '', $required);");
			} else {
				$option_id = $find_result['option_id'];
				$product_option_id = $find_result['product_option_id'];
			}

			// (2) related option
			if ($related && $option_link !== '') {
				$master_option = isset($result[(int)$option_link]) ? $result[(int)$option_link]['option_id'] : 0;
				$db->query("UPDATE " . DB_PREFIX . "product_option SET `master_option` = $master_option WHERE `product_option_id` = $product_option_id;");
			}

			$find_result = array();

			foreach ($result as $res) {
				if ($res['option_name'] == $option_name && $res['option_value'] == $option_value) {
					$find_result = $res;
					break;
				}
			}

			// (3) option value and product option value

			// price
			$sgn = $option_price < 0 ? '-' : '+';
			$pr = abs($option_price);

			// weight
			$sgn_wght = $option_w < 0 ? '-' : '+';
			$wght = abs($option_w);

			// quantity
			$quan = (int)$option_q;

			if (!isset($option_exchange[$id_1c]) && !$find_result) {
				$find = $db->query("SELECT option_value_id FROM " . DB_PREFIX . "option_value_description WHERE name = '$option_value' AND option_id = $option_id");
				if ($find->num_rows == 0) {
					$db->query("INSERT INTO " . DB_PREFIX . "option_value (`option_id`, `sort_order`) VALUES ($option_id, $sort_order);");
					$option_value_id = $db->getLastId();

					foreach ($languages as $k => $v) {
						if ($v["language_id"] == $language_id) {
							$db->query("INSERT INTO " . DB_PREFIX . "option_value_description (`option_value_id`, `language_id`, `option_id`, `name`) VALUES ($option_value_id, $language_id, $option_id, '$option_value');");
						} else {
							$option_value_lang = $this->setting['translit_name'] ? $this->_transliterateString($option_value, false) : '';
							$db->query("INSERT INTO " . DB_PREFIX . "option_value_description (`option_value_id`, `language_id`, `option_id`, `name`) VALUES ($option_value_id, " . $v["language_id"] . ", $option_id, '$option_value_lang');");
						}
					}
				} else {
					$option_value_id = $find->row['option_value_id'];
				}

				$db->query("INSERT INTO " . DB_PREFIX . "product_option_value (`product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`)
						VALUES ($product_option_id, $product_id, $option_id, $option_value_id, $quan, 1, $pr, '$sgn', 0, '+', $wght, '$sgn_wght');");
				$product_option_value_id = $db->getLastId();
			} elseif (!$find_result) {
				$option_value_id = $option_exchange[$id_1c]['option_value_id'];
				$product_option_value_id = $option_exchange[$id_1c]['product_option_value_id'];

				$db->query("INSERT INTO " . DB_PREFIX . "product_option_value (`product_option_value_id`, `product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`)
						VALUES ($product_option_value_id, $product_option_id, $product_id, $option_id, $option_value_id, $quan, 1, $pr, '$sgn', 0, '+', $wght, '$sgn_wght');");
			} else {
				$option_value_id = $find_result['option_value_id'];

				// this option_value already exists, we will add it only if it is value of related option
				if ($related && $option_link !== '') {
					if (!isset($option_exchange[$id_1c])) {
						$db->query("INSERT INTO " . DB_PREFIX . "product_option_value (`product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`)
							VALUES ($product_option_id, $product_id, $option_id, $option_value_id, $quan, 1, $pr, '$sgn', 0, '+', $wght, '$sgn_wght');");
						$product_option_value_id = $db->getLastId();
					} else {
						$product_option_value_id = $option_exchange[$id_1c]['product_option_value_id'];
						$db->query("INSERT INTO " . DB_PREFIX . "product_option_value (`product_option_value_id`, `product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`)
							VALUES ($product_option_value_id, $product_option_id, $product_id, $option_id, $option_value_id, $quan, 1, $pr, '$sgn', 0, '+', $wght, '$sgn_wght');");
					}
				} else {
					$product_option_value_id = $find_result['product_option_value_id'];
				}
			}

			// (4) related option value
			if ($related && $option_link !== '') {
				$master_option_value = isset($result[(int)$option_link]) ? $result[(int)$option_link]['product_option_value_id'] : 0;
				$db->query("UPDATE " . DB_PREFIX . "product_option_value SET `master_option_value` = $master_option_value WHERE `product_option_value_id` = $product_option_value_id;");
			}

			// (5) save exchange data
			$product_option_value_exchange_id = isset($option_exchange[$id_1c]) ? $option_exchange[$id_1c]['product_option_value_exchange_id'] : null;
			$this->model_extension_module_exchange_1c->saveProductOptionValueExchange($product_id, $option_id, $option_value_id, $product_option_id, $product_option_value_id, $id_1c, $product_option_value_exchange_id);

			$data = array(
				'option_id' => $option_id,
				'option_value_id' => $option_value_id,
				'product_option_id' => $product_option_id,
				'product_option_value_id' => $product_option_value_id,
				'option_name' => $option_name,
				'option_value' => $option_value
			);

			$result[$key] = $data;
		}

		$this->log($result, '$result: ', "save_product_options_data.log");
	}

	/**
	 * @param
	 * @param
	 * @return
	 */
	private function _productCategories($categories) {
		$result = array();

		if (isset($categories['id']) && is_array($categories['id'])) {
			$last = array_pop($categories['id']);
			$result = $categories['id'];
		}

		$result = array_diff($result, array(0));

		return $result;
	}

	/**
	 * @param
	 * @return
	 */
	private function _relatedProducts($product_related) {
		$result = array();

		if (isset($product_related['related']) && is_array($product_related['related'])) {
			$last = array_pop($product_related['related']);
			$result = $product_related['related'];
		}

		return $result;
	}

	/**
	 * @param int $category_id
	 * @return string
	 */
	private function _getAllCatsForProd($category_id) {
		$this->load->model('catalog/category');

		$category_description = $this->model_catalog_category->getCategoryDescriptions($category_id);
		$category_info = array_merge(isset($category_description[$this->_getLanguageId($this->language_default)]) ? $category_description[$this->_getLanguageId($this->language_default)] : array(), $this->model_catalog_category->getCategory($category_id));

		$parent_categories = array(isset($category_info['name']) ? $category_info['name'] : '');
		$category = $category_info;

		if (isset($category['parent_id'])) {
			while ($category['parent_id'] > 0) {
				$category_description = $this->model_catalog_category->getCategoryDescriptions($category['parent_id']);
				$category = array_merge($category_description[$this->_getLanguageId($this->language_default)], $this->model_catalog_category->getCategory($category['parent_id']));

				array_unshift($parent_categories, $category['name']);
			}
		}

		return implode($this->all_category_sep, $parent_categories);
	}

	/**
	 * @param array $category_info
	 * @return string
	 */
	private function _getAllCatsForCat($category_info) {
		$this->load->model('catalog/category');

		$parent_categories = array(isset($category_info['name']) ? $category_info['name'] : '');
		$category = $category_info;

		if (isset($category['parent_id'])) {
			while ($category['parent_id'] > 0) {
				$category_description = $this->model_catalog_category->getCategoryDescriptions($category['parent_id']);
				$lang1 = $this->_getLanguageId($this->language_default);

				if (!empty($lang1)){
					$category = array_merge($category_description[$this->_getLanguageId($this->language_default)], $this->model_catalog_category->getCategory($category['parent_id']));
				} else {
					$category = array_merge($category_description[(int)$this->config->get('config_language_id')], $this->model_catalog_category->getCategory($category['parent_id']));
				}

				array_unshift($parent_categories, $category['name']);
			}
		}

		return implode($this->all_category_sep, $parent_categories);
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function sendSeries($args, $signature) {
		$this->log($args, 'sendSeries(): $args', "send_series_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'sendSeries():');
			return array('error' => 'Signature is not correct');
		}
		/*
		$args = (array)$args;
		$series = $args['series'];

		if (!$series || !is_array($series))
			return array('error' => 'Empty input params');

		// if module productSeries is not installed
		$this->load->model('extension/exchange_1c');
		if (!$this->model_extension_exchange_1c->existExtension('module', 'pds')) {
			if ($this->enable_logs) {
				$fp = fopen( "./log_send_series.log", "a+" );
				fwrite($fp, 'no module'."\r\n");
				fclose($fp);
			}
			return array('error' => 'Module Product Series is not installed');
		}

		array_pop($series);
		foreach ($series as $v) {
			$v = (array)$v;
			$product_ids = $v['id'];
			array_pop($product_ids);

			$master_product_id = array_shift($product_ids);

			if (!$master_product_id)
				return;

			$this->load->model('catalog/product_master');

			// delete all existing links
			$this->model_catalog_product_master->deleteLink(array(
				'product_id' => $master_product_id
			));

			// add series
			if ($product_ids) {
				// set master product
				$this->model_catalog_product_master->editLink(array(
					'product_id' => $master_product_id,
					'master_product_id' => '0', //0 is master
					'special_attribute_group_id' => '2' //2 is image
				));

				$this->load->model('catalog/product_special_attribute');
				$this->load->model('catalog/special_attribute');
				$data = array(
					'product_series_image' => '',
					'special_attribute_group_id' => '2' //2 is image
				);

				// other products
				foreach($product_ids as $product_id) {
					//update set product master
					$psa = $this->model_catalog_product_special_attribute->getProductSpecialAttribute($product_id, '2');

					if(!isset($psa))
					{
						$this->model_catalog_product_special_attribute->editProductSpecialAttribute(array(
							'product_id' => $product_id,
							'special_attribute_id' => $this->model_catalog_special_attribute->getImageId($data)
						));
					}

					$this->model_catalog_product_master->editLink(array(
						'product_id' => $product_id,
						'master_product_id' => $master_product_id,
						'special_attribute_group_id' => '2' //2 is image
					));
				}
			}
		}
		*/

		//$this->cache->delete('product');
		return array('error' => '');
	}

	/**
	* @param mixed $args
	* @param string $signature
	* @return mixed
	*/
	public function sendPriceAndQuantity($args, $signature) {
		$this->log($args, 'sendPriceAndQuantity(): $args', "send_price_and_quantity_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'sendPriceAndQuantity():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$this->load->model('extension/module/exchange_1c');
		$this->load->model('catalog/product');

		$args = (array)$args;

		$products = $args['product'];

		if ($products) {
			foreach ($products as $product) {
				if ($product) {
					$product = (array)$product;

					$db->query("UPDATE " . DB_PREFIX . "product
						SET quantity = '" . (int)$product['quantity'] . "',
						price = '" . (float)$product['price'] . "',
						weight = '" . (float)$product['weight'] . "'
						WHERE product_id = " . $product['id'] . ";"
					);

					$this->cache->delete("product");

					$option_exchange = $this->model_extension_module_exchange_1c->getProductOptionValueExchange($product['id']);

					if (!$this->setting['only_1c_options']) {
						$non_1c_product_options = $this->_getNon1cProductOptions($product['id'], $option_exchange);
					}

					$this->_saveProductOptions($product['id'], (array)$product['options'], $option_exchange);

					if (!$this->setting['only_1c_options']) {
						$this->_saveNon1cProductOptions($product['id'], (array)$non_1c_product_options);
					}
				}
			}
		}

		return array('error' => '');
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function editCustomerStatus($args, $signature) {
		$this->log($args, 'editCustomerStatus(): $args', "edit_customer_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'editCustomerStatus():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$args = (array)$args;
		$customers = $args['customer'];

		if ($customers) {
			foreach ($customers as $customer) {
				if ($customer) {
					$customer = (array)$customer;
					$db->query("UPDATE " . DB_PREFIX . "customer SET is_new = " . (int)$customer['is_new'] . " WHERE customer_id = " . (int)$customer['id'] . ";");
				}
			}
		}

		return array('error' => '');
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function setOrderLoaded($args, $signature) {
		$this->log($args, 'setOrderLoaded(): $args', "edit_order_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'setOrderLoaded():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$args = (array)$args;
		$orders = $args['id'];

		if ($orders) {
			foreach ($orders as $order) {
				if ($order) {
					$db->query("UPDATE `" . DB_PREFIX . "order` SET is_new = 0 WHERE order_id = " . (int)$order . ";");
				}
			}
		}

		return array('error' => '');
	}

	/**
	 * @param string $signature
	 * @return mixed
	 */
	public function getOrderStatuses($signature) {
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'getOrderStatuses():');
			return array('error' => 'Signature is not correct');
		}

		$lang = $this->_getLanguageId($this->language_default);

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$buf = array();
		$rg->set('db', $db);

		$query = $db->query("SELECT order_status_id AS status_id, name FROM `" . DB_PREFIX . "order_status` WHERE language_id = $lang;");

		$this->log($query->rows, 'getOrderStatuses(): $query->rows', "get_order_statuses_data.log");

		return array('error' => '', 'status' => $query->rows);
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function editOrderStatus($args, $signature) {
		$this->log($args, 'editOrderStatus(): $args', "edit_order_data.log");

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'editOrderStatus():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$rg->set('db', $db);

		$args = (array)$args;
		$orders = $args['order'];

		if ($orders) {
			foreach ($orders as $order) {
				if ($order) {
					$order = (array)$order;
					$db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = " . (int)$order['status_id'] . " WHERE order_id = " . (int)$order['order_id'] . ";");
				}
			}
		}

		return array('error' => '');
	}

	/**
	 * @param mixed $args
	 * @param string $signature
	 * @return mixed
	 */
	public function editOrder($args, $signature) {
		$this->log($args, 'editOrder(): $args', "edit_order_data.log");

		// NOTICE: rewards and taxes are not realized

		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'editOrder():');
			return array('error' => 'Signature is not correct');
		}

		$this->load->model('sale/order');
		$this->load->model('catalog/product');
		$this->load->model('extension/module/exchange_1c');

		$lang = $this->_getLanguageId($this->language_default);

		$comment = 'M-IT: Exchange 1C' . "\r\n";

		$args = (array)$args;
		$orders = $args['order'];

		if ($orders) {
			foreach ($orders as $order) {
				if ($order) {
					$order = (array)$order;

					// (1) all order data
					$data = $this->model_sale_order->getOrder($order['order_id']);
					$data['order_product'] = array();

					$comment .= 'Id заказа: ' . $order['order_id'] . "\r\n" . 'Товары:';

					// (2) order products data
					$sub_total_value = 0;
					foreach ($order['product'] as $product) {
						if ($product) {
							$product = (array)$product;
							$product_data = array(
								'order_product_id' => '',
								'product_id' => $product['product_id'],
								'quantity' => $product['quantity'],
								'price' => $product['price'],
								'tax' => 0,
								'reward' => 0,
								'order_option' => array()
							);

							$product_data_old = $this->model_catalog_product->getProduct($product['product_id']);
							$product_data_old = array_merge($product_data_old, $this->model_catalog_product->getProductDescriptions($product['product_id']));
							$product_data['name'] = $product_data_old['name'];
							$product_data['model'] = $product_data_old['model'];

							$comment .= "\r\n" . $product_data['product_id'] . ' ' . $product_data['name'] . ' (' . $product_data['quantity'] . ' шт, цена за единицу ' . $product_data['price'] . ')';

							$product['options'] = (array)$product['options'];

							if (isset($product['options']['option']) && is_array($product['options']['option'])) {
								$comment .= ', опции: ';

								foreach ($product['options']['option'] as $option_id_1c) {
									if ($option_id_1c) {
										$product_option_value = $this->model_extension_module_exchange_1c->getProductOptionValueExchange($product['product_id'], $option_id_1c);
										$product_option_data = $this->model_extension_module_exchange_1c->getProductOptionAndValue($product['product_id'], $product_option_value[$option_id_1c]['product_option_value_id'], $lang);

										$product_data['order_option'][] = array(
											'order_option_id' => '',
											'product_option_id' => $product_option_data['product_option_id'],
											'product_option_value_id' => $product_option_value[$option_id_1c]['product_option_value_id'],
											'name' => $product_option_data['name'],
											'value' => $product_option_data['value'],
											'type' => $product_option_data['type']
										);
										$comment .= $product_option_data['name'] . ':' . $product_option_data['value'] . ' ';
									}
								}
							}

							// (3) order product total data
							$product_data['total'] = $product['price'] * $product['quantity'];
							$sub_total_value += $product_data['total'];

							$data['order_product'][] = $product_data;
						}
					}

					// totals
					$order_total = $this->model_sale_order->getOrderTotals($order['order_id']);
					$total_total = array();
					$total_total_value = 0;
					foreach ($order_total as $total) {
						if ($total['code'] == 'sub_total') {
							$total['value'] = $sub_total_value;
							$total['text'] = $this->currency->format($sub_total_value, $this->config->get('config_currency'));
						} elseif ($total['code'] == 'total') {
							$total_total = $total;
						}

						if ($total['code'] != 'total') {
							$total_total_value += $total['value'];
							$data['order_total'][] = $total;
						}
					}

					if ($total_total) {
						$total_total['value'] = $total_total_value;
						$total_total['text'] = $this->currency->format($total_total_value, $this->config->get('config_currency'));
						$data['order_total'][] = $total_total;
					}

					// edit order
					$this->model_sale_order->editOrder($order['order_id'], $data);

					// write history
					$history = array(
						'order_status_id' => $data['order_status_id'],
						'notify' => 0,
						'comment' => $comment
					);

					$this->model_sale_order->addOrderHistory($order['order_id'], $history);
				}
			}
		}

		return array('error' => '');
	}

	/**
	 * @param string $signature
	 * @return mixed
	 */
	public function getShipping($signature) {
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'getShipping():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$buf = array();
		$rg->set('db', $db);

		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE type = 'shipping';");

		$result = array();
		foreach ($query->rows as $shipping) {
			$extension = basename($shipping['code'], '.php');
			$this->load->language('shipping/' . $extension);
			$result[] = array(
				'name' => $this->language->get('heading_title'),
				'code' => $shipping['code'] . '.' . $shipping['code']
			);
		}

		$this->log($result, 'getShipping(): $result', "get_shipping_data.log");

		return array('error' => '', 'shipping' => $result);
	}

	/**
	 * @param string $signature
	 * @return mixed
	 */
	public function getPayment($signature) {
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'getPayment():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$buf = array();
		$rg->set('db', $db);

		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE type = 'payment';");

		$result = array();

		foreach ($query->rows as $payment) {
			$extension = basename($payment['code'], '.php');
			$this->load->language('payment/' . $extension);

			$result[] = array(
				'name' => $this->language->get('heading_title'),
				'code' => $payment['code']
			);
		}

		$this->log($result, 'getPayment(): $result', "get_payment_data.log");

		return array('error' => '', 'payment' => $result);
	}

	/**
	 * @param string $keyword
	 * @return string
	 */
	private function _makeUniqueKeyword($keyword, $query_begin, $current_id) {
		$this->load->model('extension/module/exchange_1c');
		$keywords = $this->model_extension_module_exchange_1c->getAllKeywords($query_begin, $keyword, $current_id);

		$result = $keyword;
		$index = 1;

		while (in_array($result, $keywords)) {
			$result = $keyword . '-' . $index;
			$index++;
		}

		return $result;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function _rusToTranslit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',    'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);

		return strtr($string, $converter);
	}

	/**
	 * @param string $str
	 * @return string
	 */
	private function _transliterateString($str, $to_url = true) {
		$str = $this->_rusToTranslit($str);

		if ($to_url) {
			$str = strtolower($str);
			$preg_str = '~[^-a-z0-9_]+~u';
		} else {
			$preg_str = '~[^-a-zA-Z0-9_ ]+~u';
		}

		$str = preg_replace($preg_str, '-', $str);
		$str = $this->_removeDuplicates('--', '-', $str);
		$str = trim($str, "-");

		return $str;
	}

	/**
	 * @param string $str
	 * @return string
	 */
	private function _removeDuplicates($search, $replace, $subject) {
		$i = 0;
		$pos = strpos($subject, $search);

		while ($pos !== false) {
			$subject = str_replace($search, $replace, $subject);
			$pos = strpos($subject, $search);

			$i++;

			if ($i > 150) {
				die('_removeDuplicates() loop error');
			}
		}

		return $subject;
	}

	/**
	 * @param string $signature
	 * @return mixed
	 */
	public function getNewCustomer($signature) {
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'getNewCustomer():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$buf = array();
		$rg->set('db', $db);

		$query = $db->query("SELECT t1.customer_id, t1.firstname, t1.lastname, t1.email, t1.telephone, t1.status, t2.company, t2.address_1, t2.address_2, t2.city, t2.postcode FROM " . DB_PREFIX . "customer t1 LEFT JOIN " . DB_PREFIX . "address t2 using( address_id ) WHERE is_new > 0;");

		$this->log($query->rows, 'getNewCustomer(): $query->rows', "get_new_customer_data.log");

		return array('error' => '', 'customer' => $query->rows);
	}

	/**
	 * @param string $signature
	 * @return mixed
	 */
	public function getNewOrder($signature) {
		if (!$this->_validateSignature($signature)) {
			$this->log("Signature is not correct", 'getNewOrder():');
			return array('error' => 'Signature is not correct');
		}

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$rg = new Registry();
		$buf = array();
		$rg->set('db', $db);

		$query = $db->query("
			SELECT o.*, op.*, o.total AS order_total, ot.value AS shipping_price, ot2.value AS coupon_price FROM `" . DB_PREFIX . "order` o
			LEFT JOIN `" . DB_PREFIX . "order_product` op ON o.order_id = op.order_id
			LEFT JOIN `" . DB_PREFIX . "order_total` ot ON o.order_id = ot.order_id AND ot.code = 'shipping'
			LEFT JOIN `" . DB_PREFIX . "order_total` ot2 ON o.order_id = ot2.order_id AND ot2.code = 'coupon'
			WHERE o.is_new = 1 AND o.order_status_id > 0;
		");

		$result = $query->rows;
		foreach ($result as $k =>$v) {
			$options_query = $db->query("SELECT oo.*, pove.exchange_id FROM " . DB_PREFIX . "order_option oo
							LEFT JOIN " . DB_PREFIX . "product_option_value_exchange pove
							ON oo.product_option_id = pove.product_option_id
							AND oo.product_option_value_id = pove.product_option_value_id
							WHERE oo.order_id = '" . (int)$v['order_id'] . "' AND oo.order_product_id = '" . (int)$v['order_product_id'] . "'
						");

			$options = array();

			foreach ($options_query->rows as $option) {
				if ($option['type'] != 'file') {
					//$options[] = $option['exchange_id'];
					$options[] = $option['name'];
					$options[] = $option['value'];
				}
			}

			$result[$k]['options'] = implode(';', $options);
		}

		$this->log($result, 'getNewOrder(): $result', "get_new_order_data.log");

		return array('error' => '', 'order' => $result);
	}

	/**
	 * @param mixed $data
	 * @param string $title
	 * @param string $filename
 	 */
	public function log($data, $title = null, $filename = "main.log") {
		if ($this->enable_logs) {
		//if ($this->config->get('uim_debug')) {
			//$this->log->write('Express debug (' . $title . '): ' . json_encode($data));
			file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . $filename,
					 "[" . date('Y-m-d G:i:s') . "]" . ' - ' . $title . "\r\n",
					 FILE_APPEND);
			file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . $filename,
					 print_r($data, true) . "\r\n\r\n",
					 FILE_APPEND);
		}
	}
}

$server = new OneC_Wsdl_Server();
$server->setService('OneCGateway');
$server->handle();

// Записываем в лог время выполнения скрипта и максимальное использование памяти
if (0) {
	$log_file = __DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR . 'performance.log';
	$peak_memory_usage = round(memory_get_peak_usage() / 1024 / 1024, 4);
	$script_execution_time = round(microtime(true) - $time_start, 4);

	file_put_contents($log_file, "[" . date('Y-m-d G:i:s') . "]\r\n", FILE_APPEND);
	file_put_contents($log_file, "Пиковое использование памяти: " . $peak_memory_usage . " мб.\r\n", FILE_APPEND);
	file_put_contents($log_file, "Время выполнения скрипта: " . $script_execution_time ." сек.\r\n\r\n", FILE_APPEND);
}
?>
