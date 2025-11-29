<style type="text/css">
     table.table {       
        border-collapse: collapse;   
        margin-top: 50px;     
    }
    table.table td, table.table th {
        padding: 6px 15px;
    }
    table.table td, table.table th {
        border: 1px solid #ededed;
        border-collapse: collapse;
    }
table.table td.noborder {
    border: none;
    padding-top: 60px;
}
</style>
<div class="col-md-12" id="vaucherPrintArea">
    <div class="row">
        <div class="table-responsive">
            <table border="0" width="99%" >                                                
                <tr>
                    <td width="30%" align="left">
                        <?php
                        $path = base_url((!empty($settings_info->logo)?$settings_info->logo:'assets/img/icons/mini-logo.png'));
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        ?>
                        <img src="<?php echo  $path; ?>" alt="logo">
                    </td>
                    <td width="40%" align="center">  <h2><?php echo $settings_info->title; ?></h2>
                        <?php if($voucharhead->IsApprove==0){ ?>
                            <h4 class="text-danger">Pending Voucher</h4>
                        <?php }else{ ?>
                            <h4 class="text-success">Approved Voucher</h4>
                        <?php } ?>
                    </td>  
                        <td width="30%" align="right">
                        <div class="pull-right" style="margin-right:20px;">
                        <label class="font-weight-600 mb-0"><?php echo display('voucher_no')?></label> : <?php echo $voucharhead->VoucherNumber; ?><br>
                        <label class="font-weight-600 mb-0"><?php echo display('date')?></label> : <?php echo date('d/m/Y', strtotime($voucharhead->VoucherDate));?>
                        </div>
                    </td>
                </tr>  
            </table>       
        </div>
    </div>

    <table width="99%" class="table table-bordered table-sm mt-2">

        <thead>
            <tr>
                <th><?php echo display('particulars'); ?></th>
                <th>Comments</th>
                <th class="text-center"  style="text-align:right;"><?php echo display('debit') ?></th>
                <th class="text-center"  style="text-align:right;"><?php echo display('credit') ?></th>

            </tr>


        </thead>
        <tbody>
            <?php
            $Debit = 0;
            $Credit = 0;
            if (!empty($results)) {
                foreach ($results as $row) {
                    //print_r($row);
                    if ($row->Dr_Amount != 0) {
                        $Debit += $row->Dr_Amount;
                    }
                    if ($row->Cr_Amount != 0) {
                        $Credit += $row->Cr_Amount;
                    }
                    $subtypeinfo = $this->db->select('*')->from('acc_subcode')->where('id', $row->subcode_id)->where('subTypeID', $row->subtype_id)->get()->row();
            ?>
                    <tr>
                        <td><strong style="font-size: 15px;"><?php echo $row->account_name . ($row->subcode_id != 1 ? '(' . $subtypeinfo->name . ')' : ''); ?></strong><br>

                        </td>
                        <td class="text-end"><?php echo $row->LaserComments ?></td>
                        <td class="text-end"  style="text-align:right;"><?php echo $row->Dr_Amount; ?></td>
                        <td class="text-end"  style="text-align:right;"><?php echo $row->Cr_Amount; ?></td>
                    </tr>
                <?php } ?>
                
            <?php } else { ?>
                <tr>
                    <td colspan="5" class="text-center text-danger"><?php echo display('data_is_not_available'); ?></td>
                </tr>
            <?php } ?>

        </tbody>
        <tfoot>
            <tr>
                <th align="right" class="text-end" colspan="2"><?php echo display('total'); ?></th>
                <th align="right" class="text-end"><?php echo number_format($Debit, 2); ?></th>
                <th align="right" class="text-end"><?php echo number_format($Credit, 2); ?></th>
            </tr>
            <tr>

                <th class="" colspan="4" style="text-align:left;"><?php echo display('iword'); ?> : <?php
                                                                            echo ucwords(numberToWord($voucharhead->TranAmount)) . " Only."; ?></th>

            </tr>
            <tr>
                <th class="" colspan="4" style="text-align:left;"><?php echo display('remark') ?> : <?php echo $voucharhead->Remarks; ?></th>
            </tr>
        </tfoot>
        <tfoot>
            <tr >
            <td colspan="4" class="noborder">
                <table border="0" width="100%">                                                
                <tr>
                    <td width="25%" align="left" class="noborder">
                        <div class="border-top"><?php echo display('prepared_by')?></div>
                    </td>
                    <td width="25%"  align="center"  class="noborder"> <div class="border-top"><?php echo display('checked_by')?></div>
                    </td>  
                        <td width="25%"  align="right"  class="noborder">
                        <div class="border-top"><?php echo display('authorized_signature')?></div>
                    </td>
                    <td width="25%"  align="right"  class="noborder">
                        <div class="border-top"><?php echo "Pay By"?></div>
                    </td>
                </tr>  
                </table>  
            </td>                    
            </tr> 
        </tfoot>
    </table>
</div>