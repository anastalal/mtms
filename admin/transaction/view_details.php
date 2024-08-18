<?php 
 $qry = $conn->query("SELECT * from `transaction_list` where id = '{$_GET['id']}' ");
 if($qry->num_rows > 0){
     foreach($qry->fetch_assoc() as $k => $v){
         $$k=$v;
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
$user_qry =$conn->query("SELECT *,concat(firstname,' ',lastname) as `name` FROM `users`");
$user_res = $user_qry->fetch_all(MYSQLI_ASSOC);
$user_arr = array_column($user_res,'name','id');
?>
<div class="card card-outline card-primary">
    <div class="card-header d-flex">
        <h5 class="card-title col-auto flex-grow-1">Transaction's Details</h5>
        <div class="col-auto">
        <button class="btn btn-sm btn-success btn-flat mr-2" type="button" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid" id="print_out">
        <div id='transaction-printable-details' class='position-relative'>
            <?php if($status == 1): ?>
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
            <?php endif; ?>
            <div>
                <label for="text-muted">Tracking Code:</label>
                <h3 class="fw-bolder ps-5 text-info"><larger><?php echo isset($tracking_code) ? $tracking_code : '' ?></larger></h3>
            </div>
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
                        <dt class="text-muted hide-print">Proccessed By:</dt>
                        <dd class="pl-4 hide-print"><?php echo isset($user_id) ? $user_arr[$user_id] : '' ?></dd>
                        <dt class="text-muted">Currency:</dt>
                        <dd class="pl-4"><?php echo isset($meta['currency']) ? $meta['currency'] : '' ?></dd>
                    </dl>
                </div>
            </fieldset>
            <?php if($status == 1): ?>
            <hr class="border-light">
            <fieldset>
                <legend class="text-info">Receiving Details</legend>
                    <dl>
                        <dt class="text-muted">Receiver Presented ID Type:</dt>
                        <dd class="pl-4"><?php echo isset($meta['receiver_presented_id_type']) ? $meta['receiver_presented_id_type'] : '' ?></dd>
                        <dt class="text-muted">Receiver Presented ID #:</dt>
                        <dd class="pl-4"><?php echo isset($meta['receiver_presented_id_num']) ? $meta['receiver_presented_id_num'] : '' ?></dd>
                        <dt class="text-muted hide-print">Received At:</dt>
                        <dd class="pl-4 hide-print"><?php echo isset($meta['received_branch_id']) ? $branch_arr[$meta['received_branch_id']] : '' ?></dd>
                        <dt class="text-muted hide-print">Proccessed By:</dt>
                        <dd class="pl-4 hide-print"><?php echo isset($meta['receive_user_id']) ? $user_arr[$meta['receive_user_id']] : '' ?></dd>
                    </dl>
            </fieldset>
            <?php endif; ?>
        </div>
        </div>
    </div>
</div>

<script>
    $(function(){
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
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
                         }, 200);
                     }, 500);

        })
    })
</script>