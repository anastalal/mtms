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
?>
<div class="card card-outline card-info" dir=>
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Add New " ?> Transaction</h3>
	</div>
	<div class="card-body">
		<form action="" id="transaction-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <fieldset>
                <legend>Sender Details</legend>
            <div class="form-group ">
                <div class="col-md-4d">
                    <div class="form-group">
                        <label for="sender_name" class="control-label"> Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="sender_lastname" required name="sender_name" value="<?php echo isset($meta['sender_name']) ? $meta['sender_name'] :'' ?>">
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="sender_firstname" class="control-label">First Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="sender_firstname" required name="sender_firstname" value="<?php echo isset($meta['sender_firstname']) ? $meta['sender_firstname'] :'' ?>">
                    </div>
                </div> -->
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="sender_middlename" class="control-label">Middle Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="sender_middlename" name="sender_middlename" value="<?php echo isset($sender['sender_middlename']) ? $sender['sender_middlename'] :'' ?>" placeholder="optional">
                    </div>
                </div> -->
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sender_contact" class="control-label">Contact #</label>
                        <input type="text" pattern="[0-9\s\/+]+" class="form-control form-control-sm rounded-0" id="sender_contact" required name="sender_contact" value="<?php echo isset($meta['sender_contact']) ? $meta['sender_contact'] :'' ?>">
                    </div>
                </div>
               
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sender_address" class="control-label">Address</label>
                        <textarea rows="2" class="form-control form-control-sm rounded-0" id="sender_address" required name="sender_address" style="resize:none"><?php echo isset($meta['sender_address']) ? $meta['sender_address'] :'' ?></textarea>
                    </div>
                </div>
            </div>
            </fieldset>
    <hr class="border-light">
            <fieldset>
                <legend>Receiver Details</legend>
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="receiver_name" class="control-label"> Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="receiver_name" required name="receiver_name" value="<?php echo isset($meta['receiver_name']) ? $meta['receiver_name'] :'' ?>">
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiver_firstname" class="control-label">First Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="receiver_firstname" required name="receiver_firstname" value="<?php echo isset($meta['receiver_firstname']) ? $meta['receiver_firstname'] :'' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiver_middlename" class="control-label">Middle Name</label>
                        <input type="text" class="form-control form-control-sm rounded-0" id="receiver_middlename" name="receiver_middlename" value="<?php echo isset($sender['receiver_middlename']) ? $sender['receiver_middlename'] :'' ?>" placeholder="optional">
                    </div>
                </div> -->
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="receiver_contact" class="control-label">Contact #</label>
                        <input type="text" pattern="[0-9\s\/+]+" class="form-control form-control-sm rounded-0" id="receiver_contact" required name="receiver_contact" value="<?php echo isset($meta['receiver_contact']) ? $meta['receiver_contact'] :'' ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="receiver_address" class="control-label">Address</label>
                        <textarea rows="2" class="form-control form-control-sm rounded-0" id="receiver_address" required name="receiver_address" style="resize:none"><?php echo isset($meta['receiver_address']) ? $meta['receiver_address'] :'' ?></textarea>
                    </div>
                </div>
            </div>
            </fieldset>
            <hr class="border-light">
            <fieldset>
                <legend>Details</legend>
                <div class="form-group row">
                <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency" class="control-label">Currency</label>
                            <select  required name="currency" value="<?php echo isset($meta['currency']) ? $meta['currency'] : "" ?>" class="form-control form-control-sm rounded-0 text-right" id="currency">
                                <option  value="">select</option>
                                <option <?php echo isset($meta['currency']) && $meta['currency'] == 'USD' ? 'selected' : "" ?> value="USD">USD</option>
                                <option <?php echo isset($meta['currency']) && $meta['currency'] == 'YR' ? 'selected' : "" ?> value="YR">YR</option>
                                <option <?php echo isset($meta['currency']) && $meta['currency'] == 'SR' ? 'selected' : "" ?> value="SR">SR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sending_amount" class="control-label">Amount to Send</label>
                            <input type="text" pattern="[0-9.]+" class="form-control form-control-sm rounded-0 text-right" id="sending_amount" required name="sending_amount" value="<?php echo isset($sending_amount) ? $sending_amount :0 ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fee" class="control-label">Transaction Fee</label>
                            <input type="text" pattern="[0-9.]+" class="form-control form-control-sm rounded-0 text-right" id="fee" required name="fee" value="<?php echo isset($fee) ? $fee :0 ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payable_amount" class="control-label">Payable Amount</label>
                            <input type="text" pattern="[0-9.]+" class="form-control form-control-sm rounded-0 text-right" id="payable_amount" required value="<?php echo isset($sending_amount) && isset($fee) ? $fee + $sending_amount :0 ?>" readonly>
                        </div>
                    </div>
                   
                </div>
               
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="purpose" class="control-label">Purpose/Remarks</label>
                            <textarea rows="2" class="form-control form-control-sm rounded-0" id="purpose" name="purpose" style="resize:none"><?php echo isset($purpose) ? $purpose :'' ?></textarea>
                        </div>
                    </div>
                </div>
                <?php if($_settings->userdata('type') == 1): ?>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="branch_id">Branch</label>
                        <select name="branch_id" id="branch_id" class="custom-select custom-select-sm select2">
                            <option value="" disabled <?php echo !isset($meta['branch_id']) ? "selected" :'' ?>></option>
                            <?php 
                                $branch_qry = $conn->query("SELECT * FROM branch_list where `status` = 1 ".(isset( $meta['branch_id']) &&  $meta['branch_id'] > 0 ? " OR id = '{$meta['branch_id']}'" : '' )." order by `name` asc ");
                                while($row = $branch_qry->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($branch_id) && $branch_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
				</div>
                <?php else: ?>
                    <input type="hidden" name="branch_id" value="<?php echo $_settings->userdata('branch_id') ?>">
                <?php endif; ?>
            </fieldset>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="transaction-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=product">Cancel</a>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('.select2').select2({
			width:'resolve'
		})
        $('#sending_amount').on('input',function(e){
            var amount = $(this).val()
            var currency = $('#currency').val();
            console.log(currency);
            if(currency != ''){
                $.ajax({
                url:_base_url_+'classes/Master.php?f=get_fee',
                method:'POST',
                data:{amount:amount,currency:currency},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured while fetching the transaction amount fee.",'error')
                },
                success:function(resp){
                    if(resp.status =='success'){
                        $('#fee').val(resp.fee)
                        $('#payable_amount').val(resp.payable)
                    }else{
                        console.log(resp)
                        alert_toast("An error occured while fetching the transaction amount fee.",'error')
                    }
                }
            })
            }
            else{
                alert_toast("Please Select the currency first.",'error')

            }
        
        })
		$('#transaction-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_transaction",
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
						location.href = "./?page=transaction";
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
</script>