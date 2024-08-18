<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `transaction_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
    $qry_meta = $conn->query("SELECT * FROM transaction_meta where transaction_id = '{$id}'");
    while($row = $qry_meta->fetch_array()){
        $meta[$row['meta_field']] = $row['meta_value'];
    }
}
$sender_name = $meta['sender_name'] ;
$receiver_name = $meta['receiver_name'];
$currency = $meta['currency'];
$branch_qry =$conn->query("SELECT * FROM `branch_list`");
$res = $branch_qry->fetch_all(MYSQLI_ASSOC);
$branch_arr = array_column($res,'name','id');

?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title">Receiving Transaction</h3>
	</div>
	<div class="card-body">
        <div class="container-fluid" id="print_out">
        <div id='transaction-printable-details' class='position-relative'>
            <style>
                #transaction-printable-details:before {
                    content: 'RECEIVED';
                    color: #00000014;
                    transform: rotate(-45deg);
                    font-size: 10em;
                    font-weight: 800;
                    position: absolute;
                    width: calc(100%);
                    left: 0;
                    display: flex;
                    top: 26%;
                    z-index: -1;
                    justify-content: center;
                    align-items: center;
                }
            </style>
                <label for="text-muted">Tracking Code:</label>
                <h3 class="fw-bolder ps-5 text-info"><larger><?php echo isset($tracking_code) ? $tracking_code : '' ?></larger></h3>
            <hr class="border-light">
            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend class="text-info">Sender Information</legend>
                        <div class="col-12">
                            <dl>
                                <dt class="text-muted">Name:</dt>
                                <dd class="pl-4"><?php echo isset($sender_name) ? $sender_name : '' ?></dd>
                                <dt class="text-muted">Contact:</dt>
                                <dd class="pl-4"><?php echo isset($meta['sender_contact']) ? $meta['sender_contact'] : '' ?></dd>
                                <dt class="text-muted">Address:</dt>
                                <dd class="pl-4"><?php echo isset($meta['sender_address']) ? $meta['sender_address'] : '' ?></dd>
                            </dl>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend class="text-info">Receiver Information</legend>
                        <div class="col-12">
                            <dl>
                                <dt class="text-muted">Name:</dt>
                                <dd class="pl-4"><?php echo isset($receiver_name) ? $receiver_name : '' ?></dd>
                                <dt class="text-muted">Contact:</dt>
                                <dd class="pl-4"><?php echo isset($meta['receiver_contact']) ? $meta['receiver_contact'] : '' ?></dd>
                                <dt class="text-muted">Address:</dt>
                                <dd class="pl-4"><?php echo isset($meta['receiver_address']) ? $meta['receiver_address'] : '' ?></dd>
                            </dl>
                        </div>
                    </fieldset>
                </div>
            </div>
            <hr class="border-light">
            <fieldset>
                <legend class="text-info">Details</legend>
                <div class="col-12">
                    <dl>
                        <dt class="text-muted">Amount:</dt>
                        <dd class="pl-4"><?php echo isset($sending_amount) ? number_format($sending_amount,2) : '' ?></dd>
                        <dt class="text-muted">Purpose/Remarks:</dt>
                        <dd class="pl-4"><?php echo isset($purpose) && !empty($purpose) ? $purpose : 'N/A' ?></dd>
                        <dt class="text-muted hide-print">Sent From:</dt>
                        <dd class="pl-4 hide-print"><?php echo isset($branch_id) ? $branch_arr[$branch_id] : '' ?></dd>
                        <dt class="text-muted">Currency:</dt>
                         <dd class="pl-4"><?php echo isset($meta['currency']) ? $meta['currency'] : '' ?></dd>
                    </dl>
                </div>
            </fieldset>
        </div>
        </div>
        <hr class="border-light">
		<form action="" id="receive-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <fieldset>
                <legend>Receiving Details</legend>
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="receiver_presented_id_type" class="control-label">Receiver Presented ID Type</label>
                            <input type="text" class="form-control form-control-sm rounded-0" id="receiver_presented_id_type" required name="receiver_presented_id_type" value="<?php echo isset($meta['receiver_presented_id_type']) ? $meta['receiver_presented_id_type'] :'' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="receiver_presented_id_num" class="control-label">ID #</label>
                            <input type="text" class="form-control form-control-sm rounded-0" id="receiver_presented_id_num" required name="receiver_presented_id_num" value="<?php echo isset($meta['receiver_presented_id_num']) ? $meta['receiver_presented_id_num'] :'' ?>">
                        </div>
                    </div>
                </div>
                <?php if($_settings->userdata('type') == 1): ?>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="received_branch_id">Branch</label>
                        <select name="received_branch_id" id="received_branch_id" class="custom-select custom-select-sm select2">
                            <option value="" disabled <?php echo !isset($meta['received_branch_id']) ? "selected" :'' ?>></option>
                            <?php 
                                $branch_qry = $conn->query("SELECT * FROM branch_list where `status` = 1 and user_id = '{$_settings->userdata('id')}'".(isset( $meta['received_branch_id']) &&  $meta['received_branch_id'] > 0 ? " OR id = '{$meta['received_branch_id']}'" : '' )." order by `name` asc ");
                                while($row = $branch_qry->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($received_branch_id) && $received_branch_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
				</div>
                <?php else: ?>
                    <input type="hidden" name="received_branch_id" value="<?php echo $_settings->userdata('branch_id') ?>">
                <?php endif; ?>
            </fieldset>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="receive-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=product">Cancel</a>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('.select2').select2({
			width:'resolve'
		})
		$('#receive-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_receive",
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
                        print();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
    function print(){
        var _el = $('<div>')
            var _head = $('head').clone()
                _head.find('title').text("Transaction Details - Print View")
            var p = $('#print_out').clone()
            p.find('hr.border-light').removeClass('.border-light').addClass('border-dark')
            p.find('.btn').remove()
            p.find('.hide-print').remove()
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?php echo $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Transaction Details</h4>'+
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
						    location.href = "./?page=transaction";
                         }, 200);
                     }, 500);

    }
</script>