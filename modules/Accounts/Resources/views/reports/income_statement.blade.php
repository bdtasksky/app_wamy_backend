@extends('backend.layouts.app')
@section('title', __('language.income_statement'))
@push('css')
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12">
        @include('accounts::reports.financial_report_header')
        <div class="card mb-4 fixed-tab-body">
            <div class="card-header">
                <div class="card-title">
                    <h5>{{ __('language.income_statement') }}</h5>
                    <h5>{{ $branch_name }}</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('account.report.income.statement.search') }}" method="POST" class="form-inline" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-2">
                            <label for="dtpFromDate">{{ __('language.financial_year') }} :</label>
                            <div class="form-group form-group-new empdropdown">
                                <select id="financial_year" class="form-control" name="dtpYear">
                                    <option value="">{{ __('language.Select Financial Year') }}</option>
                                    @foreach ($financial_years as $year)
                                        <option 
                                            value="{{ $year->title }}" 
                                            data-start_date="{{ $year->start_date }}" 
                                            data-end_date="{{ $year->end_date }}"
                                            @if(isset($financialyears) && $financialyears->title == $year->title) selected @endif>
                                            {{ $year->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="col-sm-2 form-group form-group-new">
                                <label for="dtpFromDate">{{ __('language.from_date') }} :</label>
                                <input type="text" name="dtpFromDate" value="{{ isset($financialyears) ? $financialyears->start_date : '' }}" class="form-control" id="from_date"/>
                            </div>
                            <div class="col-sm-2 form-group form-group-new">
                                <label for="dtpToDate">{{ __('language.to_date') }} :</label>
                                <input type="text" class="form-control" name="dtpToDate" value="{{ isset($financialyears) ? $financialyears->end_date : '' }}" id="to_date"/>
                            </div>
                           
                            <div class="col-sm-2">
                                <button style="margin-top:10%" type="submit" class="btn btn-success">{{ __('language.search') }}</button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@php
    // $path = asset(!empty($setting->logo) ? $setting->logo : 'assets/img/icons/mini-logo.png');
    // $type = pathinfo($path, PATHINFO_EXTENSION);
    // $data = file_get_contents($path);
    // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
@endphp


<div class="card px-5 py-5">
    <div class="text-center">
        {{-- <img src="{{ $path }}" alt="logo"> --}}
        <h5 class="mb-0">{{ app_setting()->title }}</h5>
        <h5 class="mt-10">{{ __('language.income_statement') . ' ' . __('language.report') }}</h5>
        <h5>{{ __('language.as_on') }} {{ date('d-m-Y') }}</h5>
    </div>
    <table style="width: 70%;margin: auto;" class="table table-bordered">
        <thead style="background:#008d4b9e!important">
            <tr>
                <th>{{ __('language.description') }}</th>
                <th>{{ __('language.amount') }}</th>
                <th>{{ __('language.amount') }}</th>
                <th>{{ __('language.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @isset($income_statement)
                @foreach($income_statement as $income)
                    <tr>
                        <td><b>{!! $income->description !!}</b></td>
                        <td style="text-align:right"><b>{{ number_format($income->amountA, 2) }}</b></td>
                        <td style="text-align:right"><b>{{ number_format($income->amountB, 2) }}</b></td>
                        <td style="text-align:right"><b>{{ number_format($income->amountC, 2) }}</b></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>


@endsection
