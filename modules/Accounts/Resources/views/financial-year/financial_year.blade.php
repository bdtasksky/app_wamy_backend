@extends('backend.layouts.app')
@section('title', __('language.financial_year_list'))
@push('css')
<link rel="stylesheet" href="{{ module_asset('Accounts/css/settings_header.css?v_' . date('h_i')) }}">
@endpush
@section('content')

   {{-- @if ($this->permission->method('room_reservation', 'create')->access()) --}}
      @include('accounts::settings_header')
        <div class="card mb-4 fixed-tab-body">
        <div class="card-header">
          
          <div class="card-title">
            <h4>
              {{ __('language.financial_year_list') }}
            </h4>
          </div>
        </div>
        <div class="card-body">
          <table width="100%" id="exdatatable" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="5%">{{ __('language.sl') }}</th>
                <th>{{ __('language.title') }}</th>
                <th>{{ __('language.from_date') }}</th>
                <th>{{ __('language.to_date') }}</th>
                <th>{{ __('language.status') }}</th>
                <th width="15%">{{ __('language.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @if (!empty($yearlist))
                @php
                  $count_year = count($yearlist);
                @endphp
                @foreach ($yearlist as $index => $list)
                  <tr class="{{ $index % 2 === 0 ? 'odd gradeX' : 'even gradeC' }}">
                    <td>{{ $index + 1 }}</td>
                    <td id="title_{{ $list->fiyear_id }}">{{ $list->title }}</td>
                    <td id="start_{{ $list->fiyear_id }}">{{ $list->start_date }}</td>
                    <td id="end_{{ $list->fiyear_id }}">{{ $list->end_date }}</td>
                    <td id="status_{{ $list->fiyear_id }}">
                      @if ($list->is_active == 2)
                        {{ 'Ended' }}
                      @elseif ($list->is_active == 1)
                        {{ __('language.active') }}
                      @else
                        {{ __('language.inactive') }}
                      @endif
                    </td>
                    <td class="center">
                      @if ($count_year == 1 && $list->is_active == 1)
                        @can('update_financial_year')
                          <a href="javascript:void(0)" class="btn btn-info btn-sm financial_year_modal" 
                            data-year_id="{{ $list->fiyear_id }}" 
                            data-year_title="{{ $list->title }}" 
                            data-modal_type="1" 
                            data-toggle="tooltip" 
                            data-placement="left" 
                            title="Update">
                            <i class="ti-pencil-alt text-white" aria-hidden="true"></i>
                          </a>
                        @endcan
                      @endif
                      @if ($list->is_active == 1)
                          @can('create_financial_year')
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm financial_year_modal" 
                              data-year_id="{{ $list->fiyear_id }}" 
                              data-year_title="{{ $list->title }}" 
                              data-modal_type="3" 
                              data-toggle="tooltip" 
                              data-placement="left" 
                              title="Close">
                              <i class="ti-close text-white" aria-hidden="true"></i>
                            </a>
                          @endcan
                      @endif
                    </td>
                  </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        </div>
    
   {{-- @endif  --}}





<div class="modal fade" id="financialyearModal" tabindex="-1" aria-labelledby="moduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title font-weight-600" id="ModalLabel"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div> 
          <div class="modal-body" id="financial_year_view"></div>
          <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                  <i class="fa fa-cross"></i> Close
              </button>
          </div>
      </div>
  </div>
</div>


<!-- jQuery -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}



@endsection

@push('js')
<script>
  window.appData = {
    openBookUrl: "{{ route('accounts.openbook') }}" ,
    finYearUpdateUrl: "{{ route('accounts.financial.yearupdate') }}",
    finYearEndUrl: "{{ route('accounts.financial.yearend') }}",

  };
</script>
<script src="{{ module_asset('Accounts/js/financial_year.js') }}"></script> 
@endpush



