@method('PUT')
<div class="row ps-4 pe-4">
    <div class="col-md-12 mt-3">
        <div class="row">
            <label for="project_type"
                class="col-form-label col-sm-3 col-md-12 col-xl-3 fw-semibold">{{ localize('select_type') }}<span
                    class="text-danger">*</span></label>
            <div class="col-sm-9 col-md-12 col-xl-9">

                <select name="project_type" id="project_type" class="form-control select-basic-single" required>
                    <option value="">--{{ localize('select') }}--</option>
                    <option value="Mosque Construction" {{ $project->project_type == 'Mosque Construction' ? 'selected' : '' }}>{{ localize('Mosque Construction') }}</option>
                    <option value="Orphan" {{ $project->project_type == 'Orphan' ? 'selected' : '' }}>{{ localize('Orphan') }}</option>
                    <option value="Scholarship" {{ $project->project_type == 'Scholarship' ? 'selected' : '' }}>{{ localize('Scholarship') }}</option>
                    <option value="Rehabilitation" {{ $project->project_type == 'Rehabilitation' ? 'selected' : '' }}>{{ localize('Rehabilitation') }}</option>
                    <option value="Relief" {{ $project->project_type == 'Relief' ? 'selected' : '' }}>{{ localize('Relief') }}</option>
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
                    placeholder="{{ localize('project_name') }}" value="{{ old('name') ?? $project->name }}" required>
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
                    <option value="On Going Project" data-project-type="Mosque Construction" {{ $project->status == 'On Going Project' ? 'selected' : '' }}>{{ localize('On Going Project') }}</option>
                    <option value="Completed Project" data-project-type="Mosque Construction" {{ $project->status == 'Completed Project' ? 'selected' : '' }}>{{ localize('Completed Project') }}</option>
                    <option value="Current Orphan" data-project-type="Orphan" {{ $project->status == 'Current Orphan' ? 'selected' : '' }}>{{ localize('Current Orphan') }}</option>
                    <option value="Previous Orphan" data-project-type="Orphan" {{ $project->status == 'Previous Orphan' ? 'selected' : '' }}>{{ localize('Previous Orphan') }}</option>
                    <option value="Family" data-project-type="Scholarship" {{ $project->status == 'Family' ? 'selected' : '' }}>{{ localize('Family') }}</option>
                    <option value="Student" data-project-type="Scholarship" {{ $project->status == 'Student' ? 'selected' : '' }}>{{ localize('Student') }}</option>
                    <option value="Campaign" data-project-type="Rehabilitation" {{ $project->status == 'Campaign' ? 'selected' : '' }}>{{ localize('Campaign') }}</option>
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
    document.addEventListener('DOMContentLoaded', function () {
        const projectTypeSelect = document.getElementById('project_type');
        const statusSelect = document.getElementById('status');

        function filterStatusOptions() {
            const selectedProjectType = projectTypeSelect.value;

            Array.from(statusSelect.options).forEach(option => {
                const optionProjectType = option.getAttribute('data-project-type');
                if (option.value === "" || optionProjectType === selectedProjectType) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Reset status selection if the current selection is not valid
            if (statusSelect.value && statusSelect.options[statusSelect.selectedIndex].style.display === 'none') {
                statusSelect.value = '';
            }
        }

        projectTypeSelect.addEventListener('change', filterStatusOptions);

        // Initial filtering on page load
        filterStatusOptions();
    });
</script>