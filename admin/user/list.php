<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of System Users</h3>
		<div class="card-tools">
			<a href="?page=user/manage_user" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Username</th>
						<?php if($_settings->userdata('type') == 3){
							echo "<th>Agent</th>";
						} ?>
						<th>Branch</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						if($_settings->userdata('type') == 3){
							$branch_qry = $conn->query("SELECT * FROM branch_list");
							$result = $branch_qry->fetch_all(MYSQLI_ASSOC);
							$branch_arr = array_column($result,'name','id');
							// $user_qry = $conn->query("SELECT * FROM user where type =1");
							// $result = $branch_qry->fetch_all(MYSQLI_ASSOC);
							// $branch_arr = array_column($result,'name','id');
							$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name from `users` where  id != '{$_settings->userdata('id')}'  order by concat(firstname,' ',lastname) asc ");
						}
						else{
							$branch_qry = $conn->query("SELECT * FROM branch_list where user_id = '{$_settings->userdata('id')}'");
						$result = $branch_qry->fetch_all(MYSQLI_ASSOC);
						$branch_arr = array_column($result,'name','id');
						$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name from `users` where user_id =  '{$_settings->userdata('id')}' and  id != '{$_settings->userdata('id')}' and `type` != 3 and type != 1 order by concat(firstname,' ',lastname) asc ");
						}
						while($row = $qry->fetch_assoc()):
							if($_settings->userdata('type') == 3):
								$usr_qry = $conn->query("SELECT *, concat(firstname,' ',lastname) as name from `users` where id= '{$row['user_id']}'  ");
								// $usr_qry = $conn->query("SELECT *, users.concat(firstname,' ',lastname) as name from `users` inner join `branch_list` on users.id =  branch_list.user_id   where users.user_id = '{$row['id']}' and type =1");
								$user_result = $usr_qry->fetch_array(); 
								// echo($user_result['name']);
								// echo (isset($user_result['name'])) ? $user_result['name']  : $row['name'];

								endif;


					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
							<td><?php echo ucwords($row['name']) ?></td>
							<td ><p class="m-0 truncate-1"><?php echo $row['username'] ?></p></td>
							<?php
							if($_settings->userdata('type') == 3){
								?>
							<td >
								<p class="m-0 truncate-1" >
								<?php 
								echo (  isset($user_result['name'])) ? $user_result['name']  : $row['name'];
								 ?>
								</p>
							</td>

								<?php
							}
							?>
							<td ><p class="m-0 truncate-1" title="<?php echo ($row['branch_id'] != null && isset($branch_arr[$row['branch_id']])) ? $branch_arr[$row['branch_id']] : 'N/A' ?>"><?php echo ($row['branch_id'] != null && isset($branch_arr[$row['branch_id']])) ? $branch_arr[$row['branch_id']] : 'N/A' ?></p></td>
							<td><?php echo ($row['type'] == 1) ? 'Agent Manager' : 'Branch User' ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
									<?php if($_settings->userdata('type') == 3): ?>
									<?PHP if($row['type'] == 2) :  ?>
				                    <a class="dropdown-item" href="?page=user/manage_user&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
									<?php else : ?>
									<a class="dropdown-item" href="?page=agent/manage_agent&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
									<?php endif;?>
									<?php else : ?>
				                    <a class="dropdown-item" href="?page=user/manage_user&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
									<?php endif; ?>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
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
			_conf("Are you sure to delete this User permanently?","delete_user",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_user($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
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
					branch.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>