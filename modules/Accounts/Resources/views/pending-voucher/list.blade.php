<div class="row">
    <!--  table area -->
    <div class="col-sm-12">
        <div class="panel panel-default thumbnail">
            
            <div class="panel-heading panel-aligner">
                <div class="panel-title">
                    <h4><?php echo display('voucher_approval') ?>
                        <div class="btn-group pull-right form-inline">
                            <?php if ($this->permission->method('accounts', 'create')->access()): ?>
                                <div class="form-group">
                                <?php $attributes = array('id' => 'voucherApprovalForm');
                                    echo form_open_multipart('accounts/AccPendingVoucherController/voucher_approved', $attributes); ?>
                                <button type="submit" class="btn btn-success"><?php echo display('approved_all_check');?></button>  
                                <?php echo  form_close() ?>
                                <?php endif; ?>
                            </div> 
                            <div class="form-group">
                            <select name="voucher_type" id="voucher_type" class="form-control select-basic-single" required>
                                    <option value="0" selected>All Vouchers</option>
                                    <?php foreach ($voucher_types as $key => $type) { ?>
                                        <option value="<?php echo  $type->id; ?>" ><?php echo $type->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" id="filterordlist"><?php echo display('search') ?></button>
                                <button class="btn btn-warning" id="filterordlistrst"><?php echo display('reset') ?></button>
                            </div>
                            <?php if ($this->permission->method('accounts', 'create')->access()): ?>
                            <div class="form-group">
                                <a href="<?php echo base_url("accounts/AccVoucherController/voucher_form") ?>" class="btn btn-primary btn-md pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    <?php echo display('create_voucher'); ?></a>
                            </div>
                        <?php endif; ?>
                        </div>
                    </h4>
                </div>
            </div>
            <style>
                #voucher_list td:nth-child(6){
                    text-align: right;
                }
                #voucher_list td:nth-child(7){
                    text-align: center;
                }
            </style>
            <div class="panel-body">
                <table width="100%" class="table table-striped table-bordered table-hover" id="voucher_list">
                    <thead>
                        <tr>
                            <th><?php echo display('sl_no') ?></th>
                            <th width="10%">
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="selectall">
                                    <label class="form-check-label" for="selectall">
                                    <?php echo display('check_all') ?>
                                    </label>
                                  </div>
                            </th>
                            <th><?php echo display('voucher_no') ?></th>
                            <th><?php echo display('date') ?></th>
                            <th><?php echo display('remark') ?></th>
                            <th class="text-end"><?php echo display('amount') ?></th>
                            <th class="text-center"><?php echo display('status') ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
           
        </div>
    </div>
</div>
<?php echo include($this->load->view('accounts/modal/voucher_details')) ?>
<script src="<?php echo base_url('application/modules/Accounts/assets/vouchers/pending-voucher.js'); ?>" type="text/javascript"></script>