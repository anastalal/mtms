<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_branch(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `branch_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Branch Name already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `branch_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `branch_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Branch successfully saved.");
			else
				$this->settings->set_flashdata('success',"Branch successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_branch(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `branch_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Branch successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_fee(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `fee_list` where `amount_from` = '{$amount_from}' and `amount_to` = '{$amount_to}' and currency= '{$currency}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Amount Range already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `fee_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `fee_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Amount Charge/Fee successfully saved.");
			else
				$this->settings->set_flashdata('success',"Amount Charge/Fee successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_fee(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `fee_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Amount Charge/Fee  successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	// function get_fee(){
	// 	extract($_POST);
	// 	$fee_qry = $this->conn->query("SELECT fee FROM fee_list WHERE `amount_from` <= '{$amount}' AND `amount_to` >= '{$amount}' and currency ='{$currency}'  ORDER BY unix_timestamp(`date_created`) DESC LIMIT 1");
	// 	$fee_percentage = 0;
	// 	$fee = 0;
		
	// 	if ($fee_qry->num_rows > 0) {
	// 		// Get the fee as a percentage
	// 		$fee_percentage = $fee_qry->fetch_array()['fee'];
	// 	}
	
	// 	// Calculate the fee based on the percentage
	// 	$fee = ($fee_percentage / 100) * $amount;
	
	// 	$resp['status'] = 'success';
	// 	$resp['fee'] = $fee;
	// 	$resp['payable'] = floatval($fee) + floatval($amount);
		
	// 	return json_encode($resp);
	// }

	function get_fee(){
		extract($_POST);
		$fee_qry = $this->conn->query("SELECT fee FROM fee_list WHERE `amount_from` <= '{$amount}' AND `amount_to` >= '{$amount}' and currency ='{$currency}'  ORDER BY unix_timestamp(`date_created`) DESC LIMIT 1");
		// $fee_qry = $this->conn->query("SELECT fee FROM fee_list where amount_from <= '{$amount}'  and amount_to >= '{$amount}' order by unix_timestamp(date_created) desc limit 1 ");
		$fee = 0;
		if($fee_qry->num_rows > 0){
			$fee = $fee_qry->fetch_array()['fee'];
		}
		$resp['status'] = 'success';
		$resp['fee'] = $fee;
		$resp['payable'] = floatval($fee) + floatval($amount);
		return json_encode($resp);
	}
	
	function save_transaction(){
		if(empty($_POST['id'])){
			$prefix = substr(str_shuffle(implode("",range("A","Z"))),0,3);
			$code = $prefix."-".(sprintf("%'.012d",rand(1,999999999999)));
			while(true){
				$check_code = $this->conn->query("SELECT * FROM `transaction_list` where tracking_code ='{$code}' ")->num_rows;
				if($check_code > 0){
					$code = $prefix."-".(sprintf("%'.012d",rand(1,999999999999)));
				}else{
					break;
				}
			}
			$_POST['tracking_code'] = $code;
		}
		$_POST['user_id'] = $this->settings->userdata('id');
		if( $this->settings->userdata('type') == 1){
			$_POST['sender_agent_id'] = $this->settings->userdata('id');
			// $_POST['reciver_agent_id'] = $this->settings->userdata('id');
		}elseif($this->settings->userdata('type') == 2){
			$_POST['sender_agent_id'] = $this->settings->userdata('user_id');
			// $_POST['reciver_agent_id'] = $this->settings->userdata('user_id');
		}else{
			$_POST['sender_agent_id'] = $this->settings->userdata('id');
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('tracking_code','purpose','status','sending_amount','fee','user_id','branch_id','sender_agent_id'))){
				$v= $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .=" `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `transaction_list` set {$data}";
		}else{
			$sql = "UPDATE `transaction_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$tid = empty($id) ? $this->conn->insert_id : $id;
			$data = "";
			$resp['status'] = 'success';
			foreach($_POST as $k =>$v){
				if(!in_array($k,array('id','tracking_code','purpose','status','sending_amount','fee','user_id'))){
					$v= $this->conn->real_escape_string($v);
					if(!empty($data)) $data .=", ";
					$data .=" ('{$tid}', '{$k}', '{$v}') ";
				}
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `transaction_meta` WHERE transaction_id = '{$id}'");
				$sql2 = "INSERT INTO `transaction_meta` (`transaction_id`,`meta_field`,`meta_value`) VALUES {$data}";
				$save2 = $this->conn->query($sql2);
				if(!$save2){
					$resp['status'] = 'failed';
					$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success'," Transaction's Details Successfully updated.");

		return json_encode($resp);
	}
	function delete_transaction(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `transaction_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"transaction's Details Successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function find_transaction(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM `transaction_list` where tracking_code = '{$tracking_code}' ");
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			if($res['status'] != 1){
				$resp['status'] ='success';
				$resp['id'] =$res['id'];
			}else{
				$resp['status'] ='failed';
				$resp['msg'] ="Transaction already received.";
			}
		}else{
			$resp['status'] ='failed';
			$resp['msg'] ="Unkown Tracking Code.";
		}
		return json_encode($resp);
	}

	function get_branches(){
		extract($_GET);
		// $qry = $this->conn->query("SELECT * FROM `branch_list` where user_id = 8 ");
		$qry = $this->conn->query("SELECT * FROM `branch_list` where user_id = '{$user_id}' ");
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
		// 	if($res['status'] != 1){
		// 		$resp['status'] ='success';
		// 		$resp['id'] =$res['id'];
		// 	}else{
		// 		$resp['status'] ='failed';
		// 		$resp['msg'] ="Transaction already received.";
		// 	}
		// }else{
		// 	$resp['status'] ='failed';
		// 	$resp['msg'] ="Unkown Tracking Code.";
		return json_encode($res);
		}
		return json_encode($res);
	}
	function save_receive(){
		$_POST['receive_user_id'] = $this->settings->userdata('id');
		if( $this->settings->userdata('type') == 1){
			$_POST['reciver_agent_id'] = $this->settings->userdata('id');
		}elseif($this->settings->userdata('type') == 2){
			$_POST['reciver_agent_id'] = $this->settings->userdata('user_id');
		}else{
			$_POST['reciver_agent_id'] = $this->settings->userdata('id');
		}
		extract($_POST);
		$sql = "UPDATE `transaction_list` set status = 1 , reciver_agent_id =  '{$reciver_agent_id}' where id = '{$id}'";
		$save = $this->conn->query($sql);
		if($save){
			$data = "";
			$resp['status'] = 'success';
			foreach($_POST as $k =>$v){
				if(!in_array($k,array('id'))){
					$v= $this->conn->real_escape_string($v);
					if(!empty($data)) $data .=", ";
					$data .=" ('{$id}', '{$k}', '{$v}') ";
				}
			}
			if(!empty($data)){
				$sql2 = "INSERT INTO `transaction_meta` (`transaction_id`,`meta_field`,`meta_value`) VALUES {$data}";
				$save2 = $this->conn->query($sql2);
				if(!$save2){
					$resp['status'] = 'failed';
					$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
				}
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = 'An error occured. Error: '.$this->conn->error;
			}
			if($resp['status'] == 'success')
			$this->settings->set_flashdata('success'," Transaction's Details Successfully updated.");
	
			return json_encode($resp);
		}

	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
// $sysset = new SystemSettings();
switch ($action) {
	case 'save_branch':
		echo $Master->save_branch();
	break;
	case 'delete_branch':
		echo $Master->delete_branch();
	break;
	case 'save_fee':
		echo $Master->save_fee();
	break;
	case 'delete_fee':
		echo $Master->delete_fee();
	break;
	case 'get_fee':
		echo $Master->get_fee();
	break;
	case 'save_transaction':
		echo $Master->save_transaction();
	break;
	case 'delete_transaction':
		echo $Master->delete_transaction();
	break;
	case 'find_transaction':
		echo $Master->find_transaction();
	break;
	case 'get_branches':
		echo $Master->get_branches();
	break;
	case 'save_receive':
		echo $Master->save_receive();
	break;
	default:
		// echo $sysset->index();
		break;
}