<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index() {

		$data = array(
			'get_course' => $this->model->get_course()
		);	

		$this->load->view('welcome_to_ci_julius', header_info($data));
	}

	public function saveUser() {

		$fullname = $this->input->post("fullname");
		$email = $this->input->post("email");
		$course = $this->input->post("course");

		$this->model->get_saveUser($fullname, $email, $course);

		$data = array(
			'success' => 1
		);

		generate_json($data);

	}

	public function saveEditUser() {
		$id = $this->input->post("hiddenID");
		$fullname = $this->input->post("editfullname");
		$email = $this->input->post("editemail");
		$course = $this->input->post("editcourse");

		$this->model->get_saveEditUser($fullname, $email, $course, $id);

		$data = array(
			'success' => 1
		);

		generate_json($data);

	}

	 

	public function loadRecords() {
		$search = $this->input->post("search");
		$course = $this->input->post("course");
		$query = $this->model->get_loadRecords($search, $course);


		$data = array(
			'result' => $query->result(),
			'success' => 1
		);

		generate_json($data);

	}

	public function getUserRow() {

		$id = $this->input->post("id");
		$query = $this->model->get_getUserRow($id);


		$data = array(
			'result' => $query->result(),
			'success' => 1
		);

		generate_json($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */