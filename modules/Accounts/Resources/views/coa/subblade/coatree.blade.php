<div class="card card-body shadow-none border mb-4" id="coatree">
    <h5 class="fw-semi-bold border-bottom pb-2 mb-3 d-flex justify-content-between">
        <div class="align-middle">
            {{ __('language.chart_of_account') }}
        </div>
        {{-- <div>
            <a class="btn btn-success" href="{{ route('account.export-acc-coa') }}"><i class="fa fa-download"></i>
                {{ __('language.export') }}</a>
        </div> --}}
    </h5>
    {{-- Export Button --}}
    <div id="html" class="demo">
        <ul>
            <li data-jstree='{ "opened" : true }'>{{ __('language.chart_of_account') }}
                <ul>
                    @forelse ($accMainHead as $acHeadKye => $accHeadValue )
                        <li data-jstree='{ "opened" : true }' data-id="{{ $accHeadValue->id }}">
                            {{ $accHeadValue->account_name }}

                            @forelse ($accSecondLableHead as $acSecondHeadKye => $accSecondHeadValue)
                                @if ($accSecondHeadValue->parent_id == $accHeadValue->id)
                                    <ul>
                                        <li data-jstree='{ "selected" : false }'data-id="{{ $accSecondHeadValue->id }}">

                                            {{ $accSecondHeadValue->account_name }}

                                            @if ($accHeadWithoutFandS->where('parent_id', $accSecondHeadValue->id)->isNotEmpty())

                                                @forelse($accHeadWithoutFandS->where('parent_id',$accSecondHeadValue->id) as $allOrherKye => $accHeadWithoutFandSValue)
                                                    <ul>
                                                        <li data-jstree='{ "selected" : false }' data-id="{{ $accHeadWithoutFandSValue->id }}">
                                                            
                                                            {{ $accHeadWithoutFandSValue->account_name }}

                                                            @if ($accHeadWithoutFandS->where('parent_id', $accHeadWithoutFandSValue->id)->isNotEmpty())
                                                                @forelse ($accHeadWithoutFandS->where('parent_id',$accHeadWithoutFandSValue->id) as $allfourthKye => $fourthLavleValue)
                                                                    <ul>
                                                                        <li data-jstree='{ "selected" : false }' data-id="{{ $fourthLavleValue->id }}">
                                                                            {{ $fourthLavleValue->account_name }} @if($fourthLavleValue->is_active==0) <span class="text-danger">({{ __('language.inactive') }})</span> @endif
                                                                        </li>
                                                                    </ul>
                                                                @empty
                                                                @endforelse
                                                            @endif
                                                        </li>
                                                    </ul>

                                                @empty
                                                @endforelse
                                            @endif
                                        </li>
                                    </ul>
                                @endif

                            @empty
                            @endforelse
                            
                        </li>
                    @empty
                        <li>{{ __('language.empty_data') }}</li>
                    @endforelse


                </ul>
            </li>


        </ul>
    </div>
</div>
