<?php
class ControllerExtensionModuleExchange1c extends Controller {
	private $error = array();

	public function install() {
		if (!file_exists(DIR_IMAGE."catalog/export")) {
			//$oldumask = umask(0);
			mkdir(DIR_IMAGE."catalog/export", 0777);
			//umask($oldumask);
		}
		if (!file_exists(DIR_IMAGE."catalog/export/product")) {
			//$oldumask = umask(0);
			mkdir(DIR_IMAGE."catalog/export/product", 0777);
			//umask($oldumask);
		}

		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order`
			ADD `is_new` int(11) NOT NULL DEFAULT '1';");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer`
			ADD `is_new` int(11) NOT NULL DEFAULT '1';");

		$this->db->query("UPDATE `" . DB_PREFIX . "order`
			SET `is_new` = 0;");
		$this->db->query("UPDATE `" . DB_PREFIX . "customer`
			SET `is_new` = 0;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_option_value_exchange` (
		  `product_option_value_exchange_id` int(11) NOT NULL AUTO_INCREMENT,
		  `product_id` int(11) NOT NULL,
		  `option_id` int(11) NOT NULL,
		  `option_value_id` int(11) NOT NULL,
		  `product_option_id` int(11) NOT NULL,
		  `product_option_value_id` int(11) NOT NULL,
		  `exchange_id` varchar(255) NOT NULL,
		  PRIMARY KEY (`product_option_value_exchange_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;");
	}

	public function uninstall() {
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order`
			DROP COLUMN `is_new`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer`
			DROP COLUMN `is_new`;");

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_option_value_exchange`");
	}

	public function index() {
		$this->load->language('extension/module/exchange_1c');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		$data['success'] = '';

		if (isset($this->request->get['install']) && $this->request->get['install']) {
			$this->model_setting_setting->editSetting('exchange_1c_installed', array('exchange_1c_installed' => 1));
			$this->install();
			$this->response->redirect($this->url->link('extension/module/exchange_1c', 'token=' . $this->session->data['token'], true));
		}

		if (isset($this->request->get['uninstall']) && $this->request->get['uninstall']) {
			$this->model_setting_setting->editSetting('exchange_1c_installed', array('exchange_1c_installed' => 0));
			$this->uninstall();
			$this->response->redirect($this->url->link('extension/module/exchange_1c', 'token=' . $this->session->data['token'], true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			foreach ($this->request->post['module_data'] as $store_id => $data1) {
				$this->model_setting_setting->editSetting('exchange_1c', $data1, $store_id);
			}

			$data['success'] = $this->language->get('text_success');
		}

		$data['installed'] = $this->config->get('exchange_1c_installed');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_install'] = $this->language->get('text_install');
		$data['text_uninstall'] = $this->language->get('text_uninstall');
		$data['text_template'] = $this->language->get('text_template');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_replace'] = $this->language->get('text_replace');

		$data['entry_seo'] = $this->language->get('entry_seo');
		$data['entry_product_params'] = $this->language->get('entry_product_params');
		$data['entry_other'] = $this->language->get('entry_other');
		$data['entry_secure'] = $this->language->get('entry_secure');

		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_h1'] = $this->language->get('entry_h1');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_tag'] = $this->language->get('entry_tag');
		$data['entry_seo_url'] = $this->language->get('entry_seo_url');
		$data['entry_load'] = $this->language->get('entry_load');
		$data['entry_rewrite'] = $this->language->get('entry_rewrite');
		$data['entry_generate'] = $this->language->get('entry_generate');
		$data['entry_template'] = $this->language->get('entry_template');
		$data['entry_all_category_sep'] = $this->language->get('entry_all_category_sep');
		$data['entry_clear'] = $this->language->get('entry_clear');
		$data['entry_replace'] = $this->language->get('entry_replace');
		$data['entry_transliterate'] = $this->language->get('entry_transliterate');
		$data['entry_transliterate_simple'] = $this->language->get('entry_transliterate_simple');
		$data['entry_transliterate_url'] = $this->language->get('entry_transliterate_url');

		$data['entry_attribute'] = $this->language->get('entry_attribute');
		$data['entry_attribute_group'] = $this->language->get('entry_attribute_group');

		$data['entry_stock'] = $this->language->get('entry_stock');
		$data['entry_stock0'] = $this->language->get('entry_stock0');
		$data['entry_stock1'] = $this->language->get('entry_stock1');
		$data['entry_stock2'] = $this->language->get('entry_stock2');

		$data['entry_lang'] = $this->language->get('entry_lang');
		$data['entry_other_lang'] = $this->language->get('entry_other_lang');
		$data['entry_translit_name'] = $this->language->get('entry_translit_name');
		$data['entry_save_other_lang'] = $this->language->get('entry_save_other_lang');

		$data['entry_additional'] = $this->language->get('entry_additional');
		$data['entry_subtract'] = $this->language->get('entry_subtract');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_product_name'] = $this->language->get('entry_product_name');
		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_update_date_available'] = $this->language->get('entry_update_date_available');
		$data['entry_status_unavailable'] = $this->language->get('entry_status_unavailable');

		$data['entry_secure_id'] = $this->language->get('entry_secure_id');
		$data['entry_secure_login'] = $this->language->get('entry_secure_login');
		$data['entry_secure_pswd'] = $this->language->get('entry_secure_pswd');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/exchange_1c', 'token=' . $this->session->data['token'], true),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('extension/module/exchange_1c', 'token=' . $this->session->data['token'], true);

		$data['install'] = $this->url->link('extension/module/exchange_1c', 'install=1&token=' . $this->session->data['token'], true);
		$data['uninstall'] = $this->url->link('extension/module/exchange_1c', 'uninstall=1&token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], true);

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['stores'][] = array('store_id' => 0, 'name' => $this->config->get('config_name'));
		$this->load->model('setting/store');
		$data['stores'] = array_merge($data['stores'], $this->model_setting_store->getStores());

		foreach ($data['stores'] as $store) {
			$data['module_data'][$store['store_id']] = $this->model_setting_setting->getSetting('exchange_1c', $store['store_id']);
		}

		$this->load->model('catalog/attribute_group');
		$data['attribute_groups'] = $this->model_catalog_attribute_group->getAttributeGroups();

		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		$this->template = 'extension/module/exchange_1c';
		$this->children = array(
			'common/header',
			'common/menu',
			'common/footer'
		);

		//$this->response->setOutput($this->render());
		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/exchange_1c', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/exchange_1c')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
