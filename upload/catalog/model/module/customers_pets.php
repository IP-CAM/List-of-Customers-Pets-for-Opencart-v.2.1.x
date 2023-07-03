<?php
class ModelModuleCustomersPets extends Model {
    public function addPet($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_pets` SET customer_id = '" . (int)$data['customer_id'] . "', pet_type = '" . $this->db->escape($data['pet_type']) . "', breed_name = '" . $this->db->escape($data['breed_name']) . "', gender = '" . $this->db->escape($data['gender']) . "', age_months = '" . (int)$this->db->escape($data['age_months']) . "', date_added = NOW()");
        $customer_pet_id = $this->db->getLastId();

        return $customer_pet_id;
    }

    public function deletePet($pet_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "customer_pets` WHERE id = '" . (int)$pet_id . "'");
    }

    public function getPet($pet_id) {
        $query = $this->db->query("SELECT id, pet_type, breed_name, gender, age_months FROM `" . DB_PREFIX . "customer_pets` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND id = '" . (int)$pet_id . "'" );

        return $query->rows;
    }

    public function getPets() {
        $query = $this->db->query("SELECT id, pet_type, breed_name, gender, age_months FROM `" . DB_PREFIX . "customer_pets` WHERE customer_id = '" . (int)$this->customer->getId() . "'  ORDER BY id ASC");

        return $query->rows;
    }
}