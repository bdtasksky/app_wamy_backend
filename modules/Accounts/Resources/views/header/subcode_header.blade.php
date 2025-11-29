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
        {{-- <a href="<?php //echo base_url('accounts/subcontroller/subcode') ?>"><button class="btn-plane <?php //echo ($this->uri->segment(3)=="subcode"?'active':''); ?>"> <?php //echo display('subcode') ?></button> </a> --}}
        {{-- <a href="<?php //echo base_url('accounts/subcontroller/subtype') ?>"><button class="btn-plane <?php //echo ($this->uri->segment(3)=="subtype"?'active':''); ?>"> <?php //echo display('subtype') ?></button></a> --}}
    </div>
</div>