<?php
class ModelExtensionModuleExchange1c extends Model {
	
	public function getKeywords($query_begin, $current_id = 0) {
		$query = $this->db->query("SELECT keyword FROM `" . DB_PREFIX . "url_alias` WHERE query like'" . $query_begin . "=%' AND query <> '" . $query_begin . "=" . $current_id . "'");
		$result = array();
		foreach ($query->rows as $row) {
			$result[] = $row['keyword'];
		}
		return $result;
	}
	
	public function getAllKeywords($query_begin, $keyword, $current_id = 0) {
		$query = $this->db->query("SELECT keyword FROM `" . DB_PREFIX . "url_alias` WHERE keyword like'" . $this->db->escape($keyword) . "%' AND query <> '" . $query_begin . "=" . $current_id . "'");
		$result = array();
		foreach ($query->rows as $row) {
			$result[] = $row['keyword'];
		}
		return $result;
	}
	
	public function getManufacturerByName($name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($name) . "'");
		
		return $query->row;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();
		
		$product_attribute_query = $this->db->query("SELECT pa.attribute_id, ad.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY pa.attribute_id");
		
		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();
			
			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");
			
			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}
			
			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'name'                          => $product_attribute['name'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}
		
		return $product_attribute_data;
	}

	public function existExtension($type, $code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
		return $query->num_rows !== 0;
	}

	public function getProductOptionAndValue($product_id, $product_option_value_id, $language_id) {
		$query = $this->db->query("SELECT o.type as type, od.name as name, ovd.name as value, po.product_option_id as product_option_id
			FROM `" . DB_PREFIX . "option` o 
			LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) 
			LEFT JOIN " . DB_PREFIX . "option_value ov ON (o.option_id = ov.option_id) 
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) 
			LEFT JOIN " . DB_PREFIX . "product_option po ON (o.option_id = po.option_id) 
			LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (ov.option_value_id = pov.option_value_id) 
			WHERE po.product_id = '" . (int)$product_id . "' 
			AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' 
			AND od.language_id = '" . (int)$language_id . "'
			AND ovd.language_id = '" . (int)$language_id . "'");
		
		return $query->row;
	}

	public function clearProductOption($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = $product_id;");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = $product_id;");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_exchange WHERE product_id = $product_id;");
	}

	public function getProductOptionValueExchange($product_id, $exchange_id = false) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value_exchange WHERE product_id = " . (int)$product_id . ($exchange_id ? " AND exchange_id = '" . $this->db->escape($exchange_id) . "'" : ""));
		$result = array();
		foreach ($query->rows as $row) {
			$result[$row['exchange_id']] = $row;
		}
		return $result;
	}

	public function saveProductOptionValueExchange($product_id, $option_id, $option_value_id, $product_option_id, $product_option_value_id, $exchange_id) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value_exchange (`product_id`, `option_id`, `option_value_id`, `product_option_id`, `product_option_value_id`, `exchange_id`) 
			VALUES (" . (int)$product_id . ", " . (int)$option_id . ", " . (int)$option_value_id . ", " . (int)$product_option_id . ", " . (int)$product_option_value_id . ", '" . $this->db->escape($exchange_id) . "')");
	}
	
}