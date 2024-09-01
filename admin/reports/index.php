
<?php 
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d",strtotime(date("Y-m-d"). ' -1 week'));;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");
$send_by = isset($_GET['send_by']) ? $_GET['send_by'] : 0;
$recive_by = isset($_GET['recive_by']) ? $_GET['recive_by'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

?>
<style>
	.hide-print{
		display:none;
	}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Transaction Reports</h3>
		<div class="card-tools">
			<!-- <button href="?page=history/manage_record" class="btn btn-flat btn-primary"><span class="fas fa-print"></span>  Create New</button> -->
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="col-12">
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
				<?php if($_settings->userdata('type') == 3) { ?>
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

					<div class="form-group col-md-4">
						<label for="recive_by" class="control-label">status</label>
						<select name="status" id="status" class="custom-select  select2">
						<option value="">please select</option>
						<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>pending</option>
						<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Received</option>
					</select>
					</div>
					
				</div>
				<?php }?>
				</form>
			</div>
			<div class="container-fluid" id="print_out">
				<div class="hide-print">
					<?php if($date_from == $date_to): ?>
						<h3 class="text-center">As of <?php echo date("F d, Y",strtotime($date_from)) ?></h3>
					<?php else: ?>
					<h3 class="text-center"></h3>
						<h3 class="text-center">As of <?php echo date("F d, Y",strtotime($date_from)) ?> - <?php echo date("F d, Y",strtotime($date_to)) ?></h3>
					<?php endif; ?>
				</div>
			<table class="table table-bordered table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="25%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Transaction Code</th>
						<th>Sender</th>
						<th>reciver</th>
						<th>Info</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$branch_qry =$conn->query("SELECT * FROM `branch_list` ");
					$res = $branch_qry->fetch_all(MYSQLI_ASSOC);
					$branch_arr = array_column($res,'name','id');
					$user_qry =$conn->query("SELECT *,concat(firstname,' ',lastname) as `name` FROM `users`");
					$user_res = $user_qry->fetch_all(MYSQLI_ASSOC);
					$user_arr = array_column($user_res,'name','id');
					// $i = 1;
					// $uwhere =" where date(date_created) BETWEEN '{$date_from}' and '{$date_to}' ";
					// if($_settings->userdata('type') == 2 && $_settings->userdata('branch_id') != null  )
					// $uwhere .= " and (( user_id = '{$_settings->userdata('id')}' and branch_id = '{$_settings->userdata('branch_id')}') or id in (SELECT transaction_id FROM `transaction_meta` where meta_field = 'receive_user_id' and meta_value = '{$_settings->userdata('id')}') )";
					// $sql = "SELECT * from `transaction_list` {$uwhere} order by unix_timestamp(`date_created`) desc ";
					// $qry = $conn->query($sql);
					// while($row = $qry->fetch_assoc()):
						$i = 1;
						$uwhere = "WHERE date(date_created) BETWEEN '{$date_from}' AND '{$date_to}'";

						if($_settings->userdata('type') == 2 && $_settings->userdata('branch_id') != null) {
							$uwhere .= " AND ((user_id = '{$_settings->userdata('id')}' or branch_id = '{$_settings->userdata('branch_id')}') OR id IN (SELECT transaction_id FROM `transaction_meta` WHERE meta_field = 'receive_user_id' AND meta_value = '{$_settings->userdata('id')}'))";
						}
						elseif($_settings->userdata('type') == 3){
							// $uwhere = "WHERE date(date_created) BETWEEN '{$date_from}' AND '{$date_to}' and  sender_agent_id = '{$send_by}' and reciver_agent_id = '{$recive_by}' ";
							$uwhere = "WHERE date(date_created) BETWEEN '{$date_from}' AND '{$date_to}'";
							if ($send_by != 0) {
								$uwhere .= " AND sender_agent_id = '{$send_by}'";
							}
							if ($recive_by != 0) {
								$uwhere .= " AND reciver_agent_id = '{$recive_by}'";
							}
							// echo $status;
							if ($status != '') {
								// echo 'an';
								$uwhere .= " AND status = '{$status}'";
							}
						}
						 else {
							$uwhere .= " AND ( reciver_agent_id = '{$_settings->userdata('id')}') or sender_agent_id = '{$_settings->userdata('id')}'";
							// $uwhere .= "WHERE reciver_agent_id = '{$_settings->userdata('id')}' or sender_agent_id = '{$_settings->userdata('id')}'";

							// $uwhere .= " AND (( user_id = '{$_settings->userdata('id')}' or
							//  user_id in (select id from users where user_id = '{$_settings->userdata('id')}'))
							//   or id IN (SELECT transaction_id FROM `transaction_meta` WHERE meta_field = 'receive_user_id' AND meta_value  in (select id from users where user_id = '{$_settings->userdata('id')}' or id = '{$_settings->userdata('id')}' )))";
						}

						$sql = "SELECT *  FROM `transaction_list` {$uwhere} ORDER BY unix_timestamp(`date_created`) DESC";
						$sqlUSD = "SELECT sum(sending_amount) as 'total', sum(fee) as 'fee' FROM `transaction_list` {$uwhere} and  id IN (SELECT transaction_id FROM `transaction_meta` WHERE meta_field = 'currency' AND meta_value  = 'USD') ORDER BY unix_timestamp(`date_created`) DESC";
						$sqlYR = "SELECT sum(sending_amount) as 'total', sum(fee) as 'fee' FROM `transaction_list` {$uwhere} and  id IN (SELECT transaction_id FROM `transaction_meta` WHERE meta_field = 'currency' AND meta_value  = 'YR') ORDER BY unix_timestamp(`date_created`) DESC";
						$sqlSR = "SELECT sum(sending_amount) as 'total', sum(fee) as 'fee' FROM `transaction_list` {$uwhere} and  id IN (SELECT transaction_id FROM `transaction_meta` WHERE meta_field = 'currency' AND meta_value  = 'SR') ORDER BY unix_timestamp(`date_created`) DESC";
						$qry = $conn->query($sql);

						while($row = $qry->fetch_assoc()):
						$meta = array();
						$qry_meta = $conn->query("SELECT * FROM transaction_meta where transaction_id = '{$row['id']}'");
						while($mrow = $qry_meta->fetch_array()){
							$meta[$mrow['meta_field']] = $mrow['meta_value'];
						}
						$sender_name = $meta['sender_name'] ;
						$receiver_name = $meta['receiver_name'];
						$currency =  $meta['currency'];
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['tracking_code'] ?></td>
							<td><?php echo $sender_name ?></td>
							<td><?php echo $receiver_name ?></td>
							<td class="lh-1">
								<span class="text-muted">Processed By:</span> <span><?php echo isset($row['user_id']) ? $user_arr[$row['user_id']] : '' ?></span> <br>
								<span class="text-muted">Processed At:</span> <span><?php echo isset($row['branch_id']) ? $branch_arr[$row['branch_id']] : '' ?></span> <br>
								
                                <?php if($row['status'] == 0): ?>
                                    <span class="text-muted">Status: </span><span class="badge badge-primary rounded-pill">Pending</span>
								<?php elseif($row['status'] == 1): ?>
									<span class="text-muted">Received At:</span> <span><?php echo isset($meta['received_branch_id']) ? $branch_arr[$meta['received_branch_id']] : 'N/A' ?></span> <br>
									<span class="text-muted">Processed (R) By:</span> <span><?php echo isset($meta['receive_user_id']) ? $user_arr[$meta['receive_user_id']] : '' ?></span> <br>
                                    <span class="text-muted">Status: </span><span class="badge badge-success rounded-pill">Received</span>
                                <?php endif; ?>
								
                            </td>
							<td class="text-right"><?php echo number_format($row['sending_amount']+$row['fee'],2) ; echo $currency?></td>
						</tr>
						
					<?php endwhile; ?>
				</tbody>
			</table>
			<div style="gap: 20px;" class="d-flex gap-3  justify-content-center">
			<?php
			$qry = $conn->query($sqlUSD);

			while($row = $qry->fetch_assoc()):
			?>
			<P class="text-center" style="font-size: 20px;">Total-USD =  <?php echo $row['total'] +  $row['fee'] ?> </P>
			<?php endwhile; ?>

			<?php
			$qry = $conn->query($sqlYR);

			while($row = $qry->fetch_assoc()):
			?>
			<P class="text-center" style="font-size: 20px;">Total-YR =  <?php echo $row['total'] +  $row['fee'] ?> </P>
			<?php endwhile; ?>

			<?php
			$qry = $conn->query($sqlSR);

			while($row = $qry->fetch_assoc()):
			?>
			<P class="text-center" style="font-size: 20px;">Total-SR =  <?php echo $row['total'] +  $row['fee'] ?> </P>
			<?php endwhile; ?>

			
			</div>
			

			
		</div>
		</div>
	</div>
</div>
<script>
	$(function(){
		$('#filter').submit(function(e){
			e.preventDefault();
			location.href="./?page=reports&"+$(this).serialize();
		})
		$('#print').click(function(){
            start_loader()
            var _el = $('<div>')
            var _head = $('head').clone()
                _head.find('title').text("Transaction Details - Print View")
            var p = $('#print_out').clone()
            p.find('hr.border-light').removeClass('.border-light').addClass('border-dark')
            p.find('.btn').remove()
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?php echo $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Transaction Report</h4>'+
                      '</div>'+
                      '<div class="col-1 text-right">'+
                      '</div>'+
                      '</div><hr/>')
            _el.append(p.html())
            var nw = window.open("","","width=1200,height=900,left=250,location=no,titlebar=yes")
                     nw.document.write(_el.html())
                     nw.document.close()
                     setTimeout(() => {
                         nw.print()
                         setTimeout(() => {
                            nw.close()
                            end_loader()
                         }, 200);
                     }, 500);

        })
	})
	
</script>