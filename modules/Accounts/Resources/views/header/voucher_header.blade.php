<style>

    .shadow-02{
        box-shadow: rgba(0, 0, 0, 0.24) 0px 0px 3px;
    }
    .btn-plane{
        font-size: 15px;
        font-weight: 600;
        padding: 10px 15px;
        border: 0px;
        background: transparent;
    }
    .btn-plane:hover{
        color: #019868;
    }
    .btn-plane.active{
        background-color: #019868;
        color: #fff;
        border-radius: 6px;
    }
    
</style>
<div class="panel panel-bd shadow-02">
    <div class="panel-body">
        <a href="<?php echo base_url('accounts/accounts/debit_voucher') ?>"><button class="btn-plane <?php echo ($this->uri->segment(3)=="debit_voucher"?'active':''); ?>"> <?php echo display('debit_voucher') ?></button> </a>
        <a href="<?php echo base_url('accounts/accounts/credit_voucher') ?>"><button class="btn-plane <?php echo ($this->uri->segment(3)=="credit_voucher"?'active':''); ?>"> <?php echo display('credit_voucher') ?></button></a>
        <a href="<?php echo base_url('accounts/accounts/contra_voucher') ?>"><button class="btn-plane <?php echo ($this->uri->segment(3)=="contra_voucher"?'active':''); ?>"> <?php echo display('contra_voucher') ?></button> </a>
        <a href="<?php echo base_url('accounts/accounts/journal_voucher') ?>"><button class="btn-plane <?php echo ($this->uri->segment(3)=="journal_voucher"?'active':''); ?>"> <?php echo display('journal_voucher') ?></button></a>
        <a href="<?php echo base_url('accounts/accounts/supplier_payments') ?>"><button class="btn-plane <?php echo ($this->uri->segment(3)=="supplier_payments"?'active':''); ?>"> <?php echo display('supplier_payment') ?></button></a>
    </div>
</div>