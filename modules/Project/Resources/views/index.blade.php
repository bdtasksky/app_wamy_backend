@extends('backend.layouts.app')
@section('title', localize('projects'))
@push('css')
    <link href="{{ asset('backend/assets/plugins/Bootstrap-5-Tag-Input/tagsinput.css') }}" rel="stylesheet">
@endpush
@section('content')
    @include('backend.layouts.common.validation')
    @include('backend.layouts.common.message')
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ localize('projects') }}</h6>
                </div>
                <div class="text-end">
                    <div class="actions">
                        {{-- @can('create_project') --}}
                            <a href="#" class="btn btn-success btn-sm" onclick="addProjectDetails()"><i
                                    class="fa fa-plus-circle"></i>&nbsp;{{ localize('new_project') }}</a>
                        {{-- @endcan --}}


                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table_customize">
                {{ $dataTable->table() }}
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="projectDetailsModal" aria-labelledby="addProjectDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectDetailsModalLabel"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal">×</button>
                </div>
                <form id="projectDetailsForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger-soft me-2"
                            data-bs-dismiss="modal">{{ localize('close') }}</button>
                        <button type="submit" class="btn btn-success me-2"></i>{{ localize('save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <input type="hidden" id="project_create" value="{{ route('project.create') }}">
        <input type="hidden" id="project_store" value="{{ route('project.store') }}">
        <input type="hidden" id="lang_add_project" value="{{ ucwords(localize('add_project')) }}">

        <input type="hidden" id="project_edit" value="{{ route('project.edit', ':project') }}">
        <input type="hidden" id="project_update" value="{{ route('project.update', ':project') }}">
        <input type="hidden" id="lang_update_project" value="{{ ucwords(localize('update_project')) }}">

        <input type="hidden" id="get_data_table_id" value="project-table">

    </div>

@endsection
@push('js')

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script src="{{ module_asset('Project/js/project.js') }}"></script>

@endpush
