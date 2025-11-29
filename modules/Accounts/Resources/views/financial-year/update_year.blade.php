<div class="form-group row">
    <label for="vo_no" class="col-sm-2 col-form-label"> {{ __('language.title') }}</label>
    <div class="col-sm-4">
        <input type="text" name="yearname" id="title" value="" placeholder="" class="form-control">
    </div>
</div>

<div class="form-group row">
    <label for="date" class="col-sm-2 col-form-label"> {{ __('language.from_date') }}</label>
    <div class="col-sm-4">
        <input type="text" name="start_date" id="start_date" class="form-control datepicker5" value="">
    </div>
</div>

<div class="form-group row">
    <label for="txtRemarks" class="col-sm-2 col-form-label"> {{ __('language.to_date') }}</label>
    <div class="col-sm-4">
        <input type="text" name="end_date" id="end_date" class="form-control datepicker5" onchange="year()" value="" />
    </div>
</div>

<div class="form-group text-end">
    <span id="finsubmit" class="btn btn-success w-md m-b-5" hidden>{{ __('language.update') }}</span>
</div>

<input type="hidden" value="" id="finid">
