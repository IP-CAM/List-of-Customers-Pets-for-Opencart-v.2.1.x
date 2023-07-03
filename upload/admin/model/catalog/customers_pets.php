<?php
class ModelCatalogCustomersPets extends Model {
    public function addPet($data) {



    }

	public function install() {

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "customer_pets` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`customer_id` INT(11) NOT NULL,
				`pet_type` VARCHAR(255) NOT NULL,
				`breed_name` VARCHAR(255) NOT NULL,				
                `gender` ENUM('male', 'female') NOT NULL,
                `age_months` INT(11) NOT NULL,
                `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                 PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $this->db->query(" CREATE INDEX idx_customer_id ON customer_pets ('customer_id')");


	}

//	public function uninstall() {
//		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_base_category`");
//		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_base_category_to_category`");
//	}

}
