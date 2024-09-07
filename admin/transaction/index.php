<?php 
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d",strtotime(date("Y-m-d"). ' -1 week'));;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");
$send_by = isset($_GET['send_by']) ? $_GET['send_by'] : 0;
$recive_by = isset($_GET['recive_by']) ? $_GET['recive_by'] : 0;

?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Transaction</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
		<?php if($_settings->userdata('type') == 3) { ?>
		<form action="" id="filter">
				<div class="row align-items-end">
					<div class="form-group col-md-4">
						<label for="date_from" class="control-label">Date From</label>
						<input type="date" name="date_from" class="form-control" value="<?php echo $date_from ?>" required>
					</div>
					<div class="form-group col-md-4">
						<label for="date_to" class="control-label">Date To</label>
						<input type="date" name="date_to" class="form-control" value="<?php echo $date_to ?>" required>
					</div>
					<div class="form-group col-md-4">
						<button class="btn btn-flat btn-primary">Filter</button>
						<button class="btn btn-flat btn-success" type="button" id="print"><i class="fa fa-print"></i> Print</button>
					</div>
				</div>
				<div class="row align-items-end">
					<div class="form-group col-md-4">
						<label for="send_by" class="control-label">Send by</label>
					<select name="send_by" id="send_by" class="custom-select  select2">
					<option value="0">please select</option>
						<?php 
							$user_qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where `type` = 1  order by `name` asc ");
							while($row = $user_qry->fetch_assoc()):
						?>
						<option  value="<?php echo $row['id'] ?>" <?php echo isset($send_by) && $send_by == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
					</div>
					<div class="form-group col-md-4">
						<label for="recive_by" class="control-label">Recive by</label>
						<select name="recive_by" id="recive_by" class="custom-select  select2">
						<option value="0">please select</option>
						<?php 
							$user_qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where `type` = 1  order by `name` asc ");
							while($row = $user_qry->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($recive_by) && $recive_by == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
					</div>
					
				</div>
				</form>
				<?php }?>
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Transaction Code</th>
						<th>Amount</th>
						<!-- <th>Currency</th> -->
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					// $i = 1;
					// $uwhere ="";
					// if($_settings->userdata('type') == 2 && $_settings->userdata('branch_id') != null)
					// $uwhere .= " where (( user_id = '{$_settings->userdata('id')}' and branch_id = '{$_settings->userdata('branch_id')}') or id in (SELECT transaction_id FROM `transaction_meta` where meta_field = 'receive_user_id' and meta_value = '{$_settings->userdata('id')}') )";
					// $qry = $conn->query("SELECT * from `transaction_list` {$uwhere} order by unix_timestamp(`date_created`) desc ");
					// while($row = $qry->fetch_assoc()):
						$i = 1;
						$uwhere = "";
						if($_settings->userdata('type') == 2 && $_settings->userdata('branch_id') != null) {
							$uwhere .= " WHERE user_id = '{$_settings->userdata('id')}' AND branch_id = '{$_settings->userdata('branch_id')}'";
						}
						elseif($_settings->userdata('type') == 3){
							// $uwhere = '';
							$uwhere = "WHERE date(date_created) BETWEEN '{$date_from}' AND '{$date_to}'";
							if ($send_by != 0) {
								$uwhere .= " AND sender_agent_id = '{$send_by}'";
							}
							if ($recive_by != 0) {
								$uwhere .= " AND reciver_agent_id = '{$recive_by}'";
							}
						}
						 else {
							$uwhere .= " WHERE reciver_agent_id = '{$_settings->userdata('id')}' or sender_agent_id = '{$_settings->userdata('id')}'";
						}
						$qry = $conn->query("SELECT * FROM `transaction_list` {$uwhere} ORDER BY unix_timestamp(`date_created`) DESC");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['tracking_code'] ?></td>
							<td class="text-right"><?php echo number_format($row['sending_amount'],2) ?></td>
							<td class="text-center">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge badge-primary rounded-pill">Pending</span>
								<?php elseif($row['status'] == 1): ?>
                                    <span class="badge badge-success rounded-pill">Received</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=transaction/view_details&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-light"></span> View</a>
									<?php if($row['status'] == 0): ?>
				                    <a class="dropdown-item" href="?page=transaction/send&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
									<?php endif; ?>
				                    <div class="dropdown-divider"></div>
				                    <!-- <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a> -->
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this transaction permanently?","delete_transaction",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_transaction($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_transaction",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}


	$(function(){
		$('#filter').submit(function(e){
			e.preventDefault();
			location.href="./?page=transaction&"+$(this).serialize();
		})
		
	})
	
</script>