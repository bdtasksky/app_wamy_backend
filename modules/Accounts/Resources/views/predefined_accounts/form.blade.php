@extends('backend.layouts.app')
@section('title', __('language.predefined_accounts'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')
@include('accounts::settings_header')
<div class="card mb-4 fixed-tab-body">
    <div class="card-title px-4 py-5 d-flex justify-content-between">
        <h4>{{ $title }}</h4>
        <div class="btn-group form-inline">
            <div class="form-group">
                <a href="{{ route('accounts.predefined.accounts') }}" class="btn btn-primary btn-md">
                    <i class="fa fa-list" aria-hidden="true"></i> {{ __('language.predefined_accounts') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body" style="margin-top: 10px;">
        <!-- Open Form -->
        <form action="{{ route('accounts.predefined.save') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Predefined Name -->
            <div class="form-group mb-2">
                <label for="predefined_seeting_name" class="col-form-label">{{ __('language.predefined_name') }}</label>
                <input type="text" name="predefined_seeting_name" id="predefined_seeting_name" class="form-control" required />
            </div>

            <!-- Description -->
            <div class="form-group mb-2">
                <label for="predefined_seeting_description" class="col-form-label">{{ __('language.description') }}</label>
                <textarea name="predefined_seeting_description" id="predefined_seeting_description" class="form-control"></textarea>
            </div>

            <!-- Status (Active/Inactive) -->
            <div class="form-group mb-2">
                <label for="status" class="col-form-label">{{ __('language.status') }}</label>
                <div class="form-check">
                    <input type="radio" name="is_active" id="is_active" value="1" checked="checked" class="form-check-input" required />
                    <label for="is_active" class="form-check-label">{{ __('language.active') }}</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="is_active" id="is_inactive" value="0" class="form-check-input" required />
                    <label for="is_inactive" class="form-check-label">{{ __('language.inactive') }}</label>
                </div>
            </div>

            <div class="row">
                <!-- Predefined Accounts -->
                <div class="form-group mb-2 col-md-6">
                    <label for="predefined_id" class="col-form-label">{{ __('language.predefined_accounts') }}</label>
                    <select name="predefined_id" id="predefined_id" class="select-basic-single" required>
                        <option value="">{{ __('language.select_one') }}</option>
                        @foreach ($predefineCode as $predefine)
                            <option value="{{ $predefine->id }}">{{ $predefine->predefined_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- COA Head -->
                <div class="form-group mb-2 col-md-6">
                    <label for="acc_coa_id" class="col-form-label">{{ __('language.coa_head') }}</label>
                    <select name="acc_coa_id" id="acc_coa_id" class="select-basic-single" required>
                        <option value="">{{ __('language.select_one') }}</option>
                        @foreach ($allheads as $allhead)
                            <option value="{{ $allhead->id }}">{{ $allhead->account_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success submit_button pull-right">{{ __('language.save') }}</button>
        </form>
    </div>
</div>
@endsection

