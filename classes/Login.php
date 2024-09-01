<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	// public function login(){
	// 	extract($_POST);
	// 	$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') ");
	// 	if($qry->num_rows > 0){
	// 	//	$branch_id = $qry->fetch_column('branch_id');
	// 	// 	if($branch_id > 0){
	// 	// $qry2 = $this->conn->query("SELECT * from branch_list where id = '$branch_id' and status = 1");
	// 	// if($qry2->num_rows < 0){
	// 	// 	return json_encode(array('status'=>'incorrect'));
	// 	// }

	// 	// 	}
	// 		foreach($qry->fetch_array() as $k => $v){
	// 			if(!is_numeric($k) && $k != 'password'){
	// 				$this->settings->set_userdata($k,$v);
	// 			}

	// 		}
	// 		$this->settings->set_userdata('login_type',1);
	// 	return json_encode(array('status'=>'success'));
	// 	}else{
	// 	return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') "));
	// 	}
	// }

	public function login() {
		// Extract POST data
		$username = $_POST['username'] ?? null;
		$password = $_POST['password'] ?? null;
	
		if (!$username || !$password) {
			return json_encode(array('status' => 'missing_fields'));
		}
	
		// Hash the password before binding it
		$hashed_password = md5($password);
	
		// Prepare and execute the query safely to prevent SQL injection
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
		$stmt->bind_param("ss", $username, $hashed_password);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
			$branch_id = $user['branch_id'];
	
			if ($branch_id > 0) {
				$stmt2 = $this->conn->prepare("SELECT * FROM branch_list WHERE id = ? AND status = 1");
				$stmt2->bind_param("i", $branch_id);
				$stmt2->execute();
				$branch_result = $stmt2->get_result();
	
				if ($branch_result->num_rows === 0) {
					return json_encode(array('status' => 'missing_fields'));
				}
			}
	
			// Set user data in session, excluding the password
			foreach ($user as $key => $value) {
				if ($key !== 'password') {
					$this->settings->set_userdata($key, $value);
				}
			}
	
			$this->settings->set_userdata('login_type', 1);
			return json_encode(array('status' => 'success'));
		} else {
			return json_encode(array('status' => 'incorrect', 'last_qry' => "SELECT * FROM users WHERE username = ? AND password = ?"));
		}
	}
	
	public function logout(){
		if($this->settings->sess_des() && $this->settings->sess_des2() ){
			redirect('admin/login.php');
		}
	}
	function login_user(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * from clients where email = '$email' and password = md5('$password') ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				$this->settings->set_userdata($k,$v);
			}
			$this->settings->set_userdata('login_type',1);
		$resp['status'] = 'success';
		}else{
		$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	default:
		echo $auth->index();
		break;
}

