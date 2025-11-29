@extends('backend.layouts.app')
@section('title', localize('custom_code'))
@push('css')
@endpush
@section('content')
    @include('backend.layouts.common.validation')
    @include('backend.layouts.common.message')
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-17 fw-semi-bold mb-0">{{ localize('custom_code') }}</h6>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="table_customize">

                <form id="projectDetailsNonModalForm" action="{{ route('seo.custom.code.store') }}" method="POST">
                    @csrf

                    <div class="row ps-4 pe-4">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="tags" class="col-form-label col-sm-3 col-md-12 col-xl-2 fw-semibold">{{ ucwords(localize('custom_code')) }}</label>
                                <div class="col-12 col-md-9 col-xl-10">
                                    <div class="row">
                                        <div class="col-12">
                                            <textarea class="form-control" name="tags" id="tags" rows="4" placeholder="<meta name='google-site-verification' content='F_Q9nuMHlFCzzIgz2Ow-5bJM2ZVqoAYYIIVDwbsjOTI' />">{{ old('tags', $custom_code->tags ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    @if ($errors->has('tags'))
                                        <div class="error text-danger m-2">{{ $errors->first('tags') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="row">
                                <label class="col-form-label col-sm-2 col-md-12 col-xl-2 fw-semibold" for="alert"></label>
                                <div class="col-sm-10 col-md-12 col-xl-10">
                                    <div class="alert alert-danger alert-large" id="alert">
                                        <strong>{{ localize('note') }}:</strong>&nbsp;
                                        {{ localize('custom_code_note') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <label class="col-form-label col-sm-2 col-md-12 col-xl-2 fw-semibold" for="status">{{ localize('status') }}</label>
                                <div class="col-sm-10 col-md-12 col-xl-10">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input auto-post-checkbox me-2" id="status" name="status" value="1"
                                        {{ old('status', $custom_code->status ?? false) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="card-footer form-footer text-start">
                            <button type="submit" class="btn btn-success me-2"></i>{{ localize('update') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
