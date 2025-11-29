<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel">

            <div class="panel-body">
                <?php echo  form_open('accounts/subcontroller/create') ?>
                <?php echo form_hidden('id', (!empty($intinfo->id) ? $intinfo->id : null)) ?>
                <div class="form-group row">
                    <label for="name" class="col-sm-4 col-form-label"><?php echo display('name') ?> *</label>
                    <div class="col-sm-8">
                        <input name="name" class="form-control" type="text"
                            placeholder="Add <?php echo display('name') ?>" id="name"
                            value="<?php echo (!empty($intinfo->name) ? $intinfo->name:null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="code" class="col-sm-4 col-form-label"><?php echo display('code') ?>
                    </label>
                    <div class="col-sm-8">
                        <input name="code" class="form-control" type="text"
                            placeholder="Add <?php echo display('code') ?>" id="code" value="<?php echo (!empty($intinfo->code) ? $intinfo->code : ''); ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="isSystem" class="col-sm-4 col-form-label">Is System</label>
                    <div class="col-sm-8">
                        <select name="isSystem" id="isSystem" class="form-control">
                            <option value="" selected="selected">Select Option</option>
                            <option value="1"
                                <?php if(!empty($intinfo)){if($intinfo->isSystem == 1){echo "Selected";}} else{echo "Selected";} ?>
                                >Active</option>
                            <option value="0"
                                <?php if(!empty($intinfo)){if($intinfo->isSystem == 0){echo "Selected";}} ?>>
                                Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group text-end">
                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('update') ?></button>
                </div>
                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>