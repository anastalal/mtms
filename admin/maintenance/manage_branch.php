<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
	if($_settings->userdata('type')  == 3){
		$qry = $conn->query("SELECT * from `branch_list` where id = '{$_GET['id']}'  ");
	}else{
		$qry = $conn->query("SELECT * from `branch_list` where id = '{$_GET['id']}' and  user_id = '{$_settings->userdata('id')}' ");
	}
    // $qry = $conn->query("SELECT * from `branch_list` where id = '{$_GET['id']}' and  user_id = '{$_settings->userdata('id')}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="branch-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">

		<?php
		if($_settings->userdata('type')  == 3):
		?>
		<div class="form-group col-12">
					<label for="branch_id">Agent</label>
					<select name="user_id" id="user_id" class="custom-select custom-select-sm select2">
						
						<?php 
							$user_qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where `type` = 1  order by `name` asc ");
							while($row = $user_qry->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($user_id) && $user_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
		<?php
		else : ?>
			<input type="hidden" name="user_id"  value="<?php echo $_settings->userdata('id') ?>"/>
         <?php
		
	 endif;?>
		<div class="form-group">
			<label for="name" class="control-label">Name</label>
			<textarea name="name" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($name) ? $name : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="address" class="control-label">Address</label>
			<textarea name="address" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($address) ? $address : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select name="status" id="status" class="custom-select selevt">
			<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
			<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
			</select>
		</div>
	</form>
</div>
<script>
  
	$(document).ready(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#branch-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_branch",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

        $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>