
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
	if($_settings->userdata('type') == 3){
		$user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
	}else{
		$user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}' and  user_id = '{$_settings->userdata('id')}'");
	}
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body d-flexs justify-content-centers">
		<div class="container-flud  mx-auto">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
				<div class="form-group col-6">
					<label for="name">First Name</label>
					<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
				</div>
				<div class="form-group col-6">
					<label for="name">Last Name</label>
					<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
				</div>
				<div class="form-group col-6">
					<label for="username">Username</label>
					<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
				</div>
				<div class="form-group col-6">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
                    <?php if(isset($_GET['id'])): ?>
					<small class="text-info"><i>Leave this blank if you dont want to change the password.</i></small>
                    <?php endif; ?>
				</div>
				<?php
				if($_settings->userdata('type') == 3)
				{ ?>
					<div class="form-group col-6">
					<label for="type">Login Type</label>
					
					<select name="type" id="type" class="custom-select">
						<option value="2" >Branch user</option>
						<!-- <option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>Staff</option> -->
					</select>
				</div>
				<div class="form-group col-6">
					<label for="user_id">Agent</label>
					<select name="user_id" id="user_id" class="custom-select  select2">
						<?php 
							$user_qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where `type` = 1  order by `name` asc ");
							while($row = $user_qry->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($user_id) && $user_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<?php 
				}
				else {
					?>
                     <input type="hidden" name="type" value="2">
                     <input type="hidden" name="user_id" value="<?php echo ($_settings->userdata('id'))   ?>">
					<?php
				}
				?>
				<?php if($_settings->userdata('type') == 3){  ?>
				<div class="form-group col-6">
					<label for="branch_id">Branch</label>
					<select required name="branch_id" id="branch_id" class="custom-select custom-select-sm select2">
						<option value="" disabled <?php echo !isset($meta['branch_id']) ? "selected" :'' ?>></option>
						<?php 
						?>
					</select>
				</div> 
				<?php }?>

				<?php if($_settings->userdata('type') == 1){  ?>
				<div class="form-group col-6">
					<label for="branch_id2">Branch</label>
					<select required name="branch_id" id="branch_id2" class="custom-select custom-select-sm select2">
						<option value="" disabled <?php echo !isset($meta['branch_id']) ? "selected" :'' ?>></option>
						<?php 
						if($_settings->userdata('type') == 1 ) {
							$branch_qry = $conn->query("SELECT * FROM branch_list WHERE `status` = 1 AND user_id = '{$_settings->userdata('id')}' ");
							// $branch_qry = $conn->query("SELECT * FROM branch_list WHERE `status` = 1 AND user_id = '{$_settings->userdata('id')}' ".(isset($meta['branch_id']) && $meta['branch_id'] > 0 ? " OR id = '{$meta['branch_id']}'" : '' )." ORDER BY `name` ASC");
						} 
						while($row = $branch_qry->fetch_assoc()){
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($meta['branch_id']) && $meta['branch_id'] == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php
						}
					  ?>
					</select>
				</div>
            <?php }  ?>
				<div class="form-group col-6">
					<label for="" class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Choose file</label>
		            </div>
				</div>
				<div class="form-group col-6 d-flex justify-content-center">
					<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
			
		</div>
		
	</div>
	
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary " form="manage-user">Save</button>
					<a class="btn btn-sm btn-secondary mr-2" href="./?page=user/list">Cancel</a>
				</div>
			</div>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	
	console.log('ad');
	$(function(){
		$('.select2').select2({
			width:'resolve'
		})

		$('#user_id').change(function(){
		// console.log($(this).children("option:selected").val());
		<?php $user_id =  '$(this).children("option:selected").val()';?> 
		var  user  =  <?php echo $user_id;?> ;
		console.log(user)

		$.ajax({
    url: _base_url_ + 'classes/Master.php?f=get_branches',
    data: {
        'user_id': user // Replace with your data's
    },
    cache: false,
    dataType: 'json',
    method: 'GET',
    success: function(data) {              
        console.log('Data: ' + JSON.stringify(data)); // Success callback
        
        // تأكد من أن data مصفوفة، إذا لم تكن مصفوفة، قم بتحويلها إلى مصفوفة
        if (!Array.isArray(data)) {
            data = [data];
        }

        // الآن قم بإضافة الخيارات إلى القائمة
        data.forEach(function(e) {
            $('#branch_id').append(`<option value="${e.id}">${e.name}</option>`);
        });
    },
    error: function(request, error){
        console.log("Request: " + JSON.stringify(request)); // Error callback
    }
});


		
				//$('#branch_id').val("").trigger("change")
				$('#branch_id').attr('required',false)
				$('#branch_id').parent().hide('slow')
			
				$('#branch_id').attr('required',false)
				$('#branch_id').parent().show('slow')
				//$('#branch_id').val("<?php echo isset($meta['branch_id']) && $meta['branch_id'] > 0 ? $meta['branch_id'] : '' ?>").trigger("change")
		})
		
		
		$('#user_id').trigger('change')
	})
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
		var _this = $(this)
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp ==1){
					location.href = './?page=user/list';
				}else{
					$('#msg').html('<div class="alert alert-danger">Username already exist</div>')
					$("html, body").animate({ scrollTop: 0 }, "fast");
				}
                end_loader()
			}
		})
	})

</script>