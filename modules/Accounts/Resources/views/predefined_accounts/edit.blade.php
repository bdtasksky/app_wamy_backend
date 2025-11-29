@extends('backend.layouts.app')
@section('title', __('language.predefined_accounts'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')
@include('accounts::settings_header')
    <div class="card mb-4 fixed-tab-body">
        <div class="card-title px-4 py-3 d-flex justify-content-between">
            <h4>{{ $title }}
                <div class="btn-group pull-right form-inline">
                    @can('read', App\Models\Account::class)
                        <div class="form-group">
                            <a href="{{ route('accounts.predefined.accounts') }}" class="btn btn-primary btn-md pull-right">
                                <i class="fa fa-list" aria-hidden="true"></i>
                                {{ __('language.predefined_accounts') }}
                            </a>
                        </div>
                    @endcan
                </div>
            </h4>
        </div>
        <div class="card-body" style="margin-top: 10px;">
            <form action="{{ route('accounts.predefined.update', $predefineSettings->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="col-md-6">
                    <div class="form-group mb-2 mx-0 row">
                        <label for="predefined_seeting_name" class="col-sm-3 col-form-label ps-0">
                            {{ __('language.predefined_name') }}<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="predefined_seeting_name" id="predefined_seeting_name"
                                value="{{ $predefineSettings->predefined_seeting_name }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="predefined_seeting_description" class="col-sm-3 col-form-label ps-0">
                            {{ __('language.description') }}
                        </label>
                        <div class="col-lg-9">
                            <textarea name="predefined_seeting_description" id="predefined_seeting_description" class="form-control">{{ $predefineSettings->predefined_seeting_description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="is_active" class="col-sm-3 col-form-label">
                            {{ __('language.status') }} <i class="text-danger">*</i>
                        </label>
                        <div class="col-sm-9">
                            <label class="radio-inline my-1">
                                <input type="radio" name="is_active" value="1" id="is_active"
                                    {{ $predefineSettings->is_active == 1 ? 'checked' : '' }} required>
                                {{ __('language.active') }}
                            </label>
                            <label class="radio-inline my-2">
                                <input type="radio" name="is_active" value="0" id="is_active"
                                    {{ $predefineSettings->is_active == 0 ? 'checked' : '' }} required>
                                {{ __('language.inactive') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-2 mx-0 row">
                        <label for="predefined_id" class="col-sm-3 col-form-label ps-0">
                            {{ __('language.predefined_accounts') }}<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-9">
                            <select name="predefined_id" id="predefined_id" class="form-control select-basic-single"
                                required>
                                <option value="">{{ __('language.select_one') }}</option>
                                @foreach ($predefineCode as $predefine)
                                    <option value="{{ $predefine->id }}"
                                        {{ $predefineSettings->predefined_id == $predefine->id ? 'selected' : '' }}>
                                        {{ $predefine->predefined_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-2 mx-0 row">
                        <label for="acc_coa_id" class="col-sm-3 col-form-label ps-0">
                            {{ __('language.coa_head') }}<span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-9">
                            <select name="acc_coa_id" id="acc_coa_id" class="form-control select-basic-single" required>
                                <option value="">{{ __('language.select_one') }}</option>
                                @foreach ($allheads as $allhead)
                                    <option value="{{ $allhead->id }}"
                                        {{ $predefineSettings->acc_coa_id == $allhead->id ? 'selected' : '' }}>
                                        {{ $allhead->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success submit_button pull-right" id="create_submit">
                        {{ __('language.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
