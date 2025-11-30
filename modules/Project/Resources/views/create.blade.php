<div class="row ps-4 pe-4">
    <div class="col-md-12 mt-3">
        <div class="row">
            <label for="project_type"
                class="col-form-label col-sm-3 col-md-12 col-xl-3 fw-semibold">{{ localize('select_type') }}<span
                    class="text-danger">*</span></label>
            <div class="col-sm-9 col-md-12 col-xl-9">

                <select name="project_type" id="project_type" class="form-control select-basic-single" required>
                    <option value="">--{{ localize('select') }}--</option>
                    <option value="Mosque Construction">{{ localize('Mosque Construction') }}</option>
                    <option value="Orphan">{{ localize('Orphan') }}</option>
                    <option value="Scholarship">{{ localize('Scholarship') }}</option>
                    <option value="Rehabilitation">{{ localize('Rehabilitation') }}</option>
                    <option value="Relief">{{ localize('Relief') }}</option>
                </select>

            </div>

            @if ($errors->has('project_type'))
                <div class="error text-danger m-2">{{ $errors->first('project_type') }}</div>
            @endif
        </div>
    </div>

    <div class="col-md-12 mt-3">
        <div class="row">
            <label for="project_name"
                class="col-form-label col-sm-3 col-md-12 col-xl-3 fw-semibold">{{ localize('project_name') }}<span
                    class="text-danger">*</span></label>
            <div class="col-sm-9 col-md-12 col-xl-9">
                <input type="text" class="form-control" id="project_name" name="name"
                    placeholder="{{ localize('project_name') }}" value="{{ old('project_name') }}" required>
            </div>

            @if ($errors->has('project_name'))
                <div class="error text-danger m-2">{{ $errors->first('project_name') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="row">
            <label for="project_status"
                class="col-form-label col-sm-3 col-md-12 col-xl-3 fw-semibold">{{ localize('select_status') }}<span
                    class="text-danger">*</span></label>
            <div class="col-sm-9 col-md-12 col-xl-9">

                <select name="status" id="status" class="form-control select-basic-single" required>
                    <option value="">--{{ localize('select') }}--</option>
                    <option value="On Going Project" data-project-type="Mosque Construction">{{ localize('On Going Project') }}</option>
                    <option value="Completed Project" data-project-type="Mosque Construction">{{ localize('Completed Project') }}</option>
                    <option value="Current Orphan" data-project-type="Orphan">{{ localize('Current Orphan') }}</option>
                    <option value="Previous Orphan" data-project-type="Orphan">{{ localize('Previous Orphan') }}</option>
                    <option value="Family" data-project-type="Scholarship">{{ localize('Family') }}</option>
                    <option value="Student" data-project-type="Scholarship">{{ localize('Student') }}</option>
                    <option value="Campaign" data-project-type="Rehabilitation">{{ localize('Campaign') }}</option>
                </select>

            </div>

            @if ($errors->has('status'))
                <div class="error text-danger m-2">{{ $errors->first('status') }}</div>
            @endif
        </div>
    </div>

</div>

<script src="{{ asset('backend/assets/plugins/Bootstrap-5-Tag-Input/tagsinput.js') }}"></script>
<script>
    $(document).ready(function() {
        function filterStatusOptions() {
            var selectedProjectType = $('#project_type').val();
            $('#status option').each(function() {
                var optionProjectType = $(this).data('project-type');
                if (!optionProjectType || optionProjectType === selectedProjectType) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#status').val(''); // Reset status selection
        }

        $('#project_type').change(function() {
            filterStatusOptions();
        });

        // Initial filter on page load
        filterStatusOptions();
    });
</script>