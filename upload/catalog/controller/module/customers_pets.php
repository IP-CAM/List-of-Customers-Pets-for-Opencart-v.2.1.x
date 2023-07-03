<?php
class ControllerModuleCustomersPets extends Controller {
    private $error = array();
	public function index() {

        if (!$this->customer->isLogged()) {
            return null;
        }
		$this->load->language('module/customers_pets');

		$data['heading_form'] = $this->language->get('heading_form');
		$data['heading_list'] = $this->language->get('heading_list');


        $this->load->model('module/customers_pets');

        $data['customer_id'] = $this->customer->getId();

        $data['pets']['cat'] = [
            'title' => 'Кошка',
            'breeds' => [
                ['id' => 0, 'breed_name' => "Абиссинская"],
                ['id' => 1, 'breed_name' => "Австралийский мист"],
                ['id' => 2, 'breed_name' => "Азиатская"],
            ]
        ];
        $data['pets']['dog'] = [
            'title' => 'Собака',
            'breeds' => [
                ['id' => 0, 'breed_name' => "Акита-ину"],
                ['id' => 1, 'breed_name' => "Алабай"],
                ['id' => 2, 'breed_name' => "Бернский зенненхунд"],
            ]
        ];
        $data['pets']['turtle'] = [
            'title' => 'Черепаха',
            'breeds' => [
                ['id' => 0, 'breed_name' => "Среднеазиатская сухопутная"],
                ['id' => 1, 'breed_name' => "Американская болотная"],
                ['id' => 2, 'breed_name' => "Звездчатая сухопутная"],
            ]
        ];
        $data['pets']['fish'] = [
            'title' => 'Рыбы',
            'breeds' => [
                ['id' => 0, 'breed_name' => "Петушок"],
                ['id' => 1, 'breed_name' => "Скалярия"],
                ['id' => 2, 'breed_name' => "Анциструс"],
            ]
        ];

        $data['gender']['male'] = $this->language->get('text_male');
        $data['gender']['female'] = $this->language->get('text_female');

        $data['customer_pets'] = $this->model_module_customers_pets->getPets();


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/customers_pets.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/module/customers_pets.tpl', $data);
        } else {
            return $this->load->view('default/template/module/customers_pets.tpl', $data);
        }

    }

    public function addPetOfCustomer() {
        $this->load->language('module/customers_pets');
        $json = [];
        if ($this->customer->isLogged() && $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

            $this->load->model('module/customers_pets');

            if (count($this->error) < 1 ) {
                $pet_data['customer_id'] = $this->customer->getId();
                $pet_data['pet_type'] = $this->request->post['pet_type'];
                $pet_data['breed_name'] = $this->request->post['breed'];
                $pet_data['gender'] = $this->request->post['gender'];
                $pet_data['age_months'] = $this->request->post['age'];

                $customer_pet_id = $this->model_module_customers_pets->addPet($pet_data);

                $pet = $this->model_module_customers_pets->getPet($customer_pet_id);

                $json['pet'] = $pet;
            }
        }
        $json['error'] = $this->error;
        if($json['error']) {
            $json['success'] = false;
        } else {
            $json['success'] = true;
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deletePetOfCustomer() {
        if ($this->customer->isLogged()) {

            $this->load->model('module/customers_pets');

            $pet_id = $this->request->post['pet_id'];

            $this->model_module_customers_pets->deletePet($pet_id);

            $json['success'] = true;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));

        }
    }

    public function getPetOfCustomer() {
        if ($this->customer->isLogged()) {

            $this->load->model('module/customers_pets');

            $pet_id = $this->request->post['pet_id'];

            $data = $this->model_module_customers_pets->getPet($pet_id);

            if($data) {
                $json['data'] = $data;
                $json['success'] = true;
            } else {
                $json['success'] = false;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));

        }
    }

    protected function validate() {

        if ((utf8_strlen(trim($this->request->post['pet_type'])) < 3) || (utf8_strlen(trim($this->request->post['pet_type'])) > 255)) {
            $this->error['pet_type'] = $this->language->get('error_pet_type');
        }

        if (!in_array($this->request->post['gender'] ,['female', 'male', ''])) {
            $this->error = $this->language->get('error_gender');
        }

        if (!is_numeric($this->request->post['age']) && $this->request->post['age'] > 0) {
            $this->error = $this->language->get('error_age');
        }

        return (!$this->error);
    }
}