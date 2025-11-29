<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel">
            <?php //d($intinfo); ?>
            <div class="panel-body">
                <?php echo  form_open('accounts/subcontroller/subcode_create') ?>
                <?php echo form_hidden('id', (!empty($intinfo->id) ? $intinfo->id : null)) ?>
                <div class="form-group row">
                    <label for="subtype_id" class="col-sm-4 col-form-label"><?php echo display('subcode');?></label>
                    <div class="col-sm-8">
                        <select name="subtype_id" id="subtype_id" class="form-control"
                            onchange="subTypeCheck(this.value)">>
                            <option value="" selected="selected">Select Option</option>
                            <?php foreach($getSubType as $subtype){ ?>
                            <option value="<?php echo $subtype->code; ?>" <?php echo (($intinfo->subTypeID == $subtype->code) ? 'selected' : ''); ?>>
                                <?php echo ((!empty($subtype->name)) ? $subtype->name : ''); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-sm-4 col-form-label"><?php echo display('name') ?> *</label>
                    <div class="col-sm-8">
                        <input name="name" class="form-control name" type="text"
                            placeholder="Add <?php echo display('name') ?>" id="name"
                            value="<?php echo (!empty($intinfo->name) ? $intinfo->name:null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="refCode" class="col-sm-4 col-form-label">Ref <?php echo display('code') ?>
                    </label>
                    <div class="col-sm-8">
                        <input name="refCode" class="form-control refCode" type="text"
                            placeholder="Ref <?php echo display('code') ?>" id="refCode" value="<?php echo (!empty($intinfo->refCode) ? $intinfo->refCode : ''); ?>">
                    </div>
                </div>

                <div class="form-group text-end">
                    <button type="submit" class="btn btn-success w-md m-b-5 submit-btn"><?php echo display('update') ?></button>
                </div>
                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>

<script>
// function subTypeCheck(subtype_id) {
//     var fd = new FormData();
//     var csrf_test_name = $("[name=csrf_test_name]").val();
//     var base_url = "<?php echo base_url(); ?>";

//     fd.append("subtype_id", subtype_id);
//     fd.append("csrf_test_name", csrf_test_name);

//     $.ajax({
//         url: base_url + "accounts/subcontroller/subTypeCheck",
//         type: "POST",
//         data: fd,
//         enctype: "multipart/form-data",
//         processData: false,
//         contentType: false,
//         dataType: "json",
//         success: function(r) {
//             if (r == 1) {
//                 $('.name').prop('disabled', true);
//                 $('.refCode').prop('disabled', true);
//                 $('.submit-btn').prop('disabled', true);
//             } else {
//                 $('.name').prop('disabled', false);
//                 $('.refCode').prop('disabled', false);
//                 $('.submit-btn').prop('disabled', false);
//             }
//         },
//     });
// }
</script>