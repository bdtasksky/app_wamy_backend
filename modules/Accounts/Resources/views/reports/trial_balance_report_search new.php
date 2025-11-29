<link rel="stylesheet" href="<?php echo base_url('application/modules/Accounts/assets/reports/trial_balance.css'); ?>">
<div class="row">
     <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd">
            <?php echo include($this->load->view('accounts/reports/financial_report_header')) ?>
                    <div class="panel-heading">
                        <div >
                            <h4><?php echo "Trial Balance With Filter"; ?></h4>
                        </div>
                    </div>

                    <div class="panel-body"> 
                        <?php echo form_open('accounts/AccReportController/trial_balance_report_search',array('class' => 'form-inline','method'=>'post'))?>
                        <div class="form-group form-group-new">
                            <label for="dtpFromDate"><?php echo display('financial_year') ?> :</label>
                        </div>
                            <div class="form-group form-group-new empdropdown">
                                <select id="financial_year" class="form-control" name="dtpYear">
                                    <option value="">Select Financial Year</option>
                                    <?php foreach ($financial_years as $year): ?>
                                        <option 
                                            value="<?php echo $year['title']; ?>" 
                                            data-start_date="<?php echo $year['start_date']; ?>" 
                                            data-end_date="<?php echo $year['end_date']; ?>"
                                            <?php echo (isset($dtpYear) && $dtpYear == $year['title']) ? 'selected' : ''; ?>>
                                            <?php echo $year['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group form-group-new">
                                <label for="dtpFromDate"><?php echo display('from_date')?> :</label>
                                <input type="text" name="dtpFromDate"  value="<?php echo isset($dtpFromDate)? $dtpFromDate : ''; ?>" class="form-control" id="from_date" readonly/>
                            </div> 
                          <div class="form-group form-group-new">
                                <label for="dtpToDate"><?php echo display('to_date')?> :</label>
                                <input type="text" class="form-control" name="dtpToDate" value="<?php echo  isset($dtpToDate)? $dtpToDate : ''; ?>"  id="to_date" readonly/>
                            </div> 

                           
                            <div class="form-group form-group-new">
                                <input tabindex="8" type="radio" id="withOpeningBalance1" name="withDetails" value="0" checked>
                                <label for="withOpeningBalance1">Basic</label>
                                <input tabindex="7" type="radio" id="withDetails" name="withDetails" value="1">
                                <label for="withDetails">{{ __('language.with_details') }}</label>
                                <input tabindex="8" type="radio" id="withOpeningBalance" name="withDetails" value="2">
                                <label for="withOpeningBalance">With Opening Balance</label>
                            </div>

                            <button type="submit" class="btn btn-success"><?php echo display('search') ?></button> 
                       <?php echo form_close()?>
                    </div>
             </div>
       </div>
 </div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div >
                    <h4><?php echo display('trial_balance')?><span class="pull-right"></span></h4>
                </div>
            </div>
            <?php
				$path = base_url((!empty($setting->logo)?$setting->logo:'assets/img/icons/mini-logo.png'));
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				$newformDate = date("d-M-Y", strtotime($dtpFromDate));  
 				$newToDate = date("d-M-Y", strtotime($dtpToDate));
				?>
            <div id="printArea">
                <div class="panel-body">
                	<div class="text-center">
                    	<img src="<?php echo  $path; ?>" alt="logo">
                        <h2 class="mb-0"><?php echo $setting->title;?></h2>
                        <h3 class="mt-10"><?php echo display('trial_balance').' '.display('report')?></h3>
                        <h5>As on <?php echo (!empty($newformDate)?$newformDate:''); ?> To <?php echo (!empty($newToDate)?$newToDate:''); ?></h5>
                    </div>
                    <div class="table-responsive">
                    <table width="99%" align="center" class="table table-bordered table-hover" title="TrialBalanceReport<?php echo $dtpFromDate; ?><?php echo display('to_date');?><?php echo $dtpToDate;?>">
                        <thead>
                            <tr class="voucherList">
                                <th style=" background: #22376d!important ; color:#fff; text-align:center;"><?php echo display('account_name');?></th>
                                <th style=" background: #22376d!important ; color:#fff; text-align:right;" colspan="2"><?php echo display('closing_balance');?></th>
                            </tr>
                            <tr>
                                <th style="text-align:center;"></th>
                                <th style="text-align:right;">(<?php echo $currency;?>)<?php echo display('debit');?></th>
                                <th style="text-align:right;">(<?php echo $currency;?>)<?php echo display('credit');?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($trial_balance_data as $nature_name => $groups): ?>
                                <!-- Nature Name as the 1st Level -->
                                <tr>
                                    <td style="background: #22376d!important;"><strong><?php echo $nature_name; ?></strong></td>
                                    <td style="background: #22376d!important; text-align:right;"><?php echo number_format($groups[0]['nature_amount_debit'], 2); ?></td>
                                    <td style="background: #22376d!important; text-align:right;"><?php echo number_format($groups[0]['nature_amount_credit'], 2); ?></td>
                                </tr>

                                <?php foreach ($groups as $sub_groups): ?>
                                    <?php foreach ($sub_groups as $ledgers): ?>
                                        <!-- Ledger Name as the 4th Level -->
                                        <?php foreach ($ledgers as $ledger): ?>
                                            <tr>
                                                <td style="padding-left: 40px;"><?php echo $ledger['ledger_name']; ?></td>
                                                <td style="text-align:right;"><?php echo number_format($ledger['debit'], 2); ?></td>
                                                <td style="text-align:right;"><?php echo number_format($ledger['credit'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>

                </div>
            </div>
          <div class="text-center trial_balance_with_opening_btn" id="print">
                <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="Print" onclick="printDiv();"/>
                <input type="button" class="btn btn-success"  value="PDF" onclick="getPDF('printArea');"/>
                <!-- <a href="<?php //echo base_url($pdf)?>"download>
                    <input type="button" class="btn btn-success" name="btnPdf" id="btnPdf" value="PDF"/>
                </a> -->
                <!-- <a href="<?php //echo base_url()?>accounts/accounts/trialbalance_report_excel/<?php echo $dtpFromDate; ?>/<?php echo $dtpToDate;?>/<?php if(isset($withzero)){ echo 0;}else{ echo 1;} ?>" target="_blank">
                <button class="btn btn-primary btn-md" name="excel" id="excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                </a> -->
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('application/modules/Accounts/assets/reports/trial_balance.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('application/modules/Accounts/Resources/assets/js/canvas-pdf/jspdf.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('application/modules/Accounts/Resources/assets/js/canvas-pdf/html2canvas.js'); ?>" type="text/javascript"></script>