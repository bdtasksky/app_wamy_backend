
        <div class="table-responsive">
            <table width="100%" class="table table-striped table-bordered table-hover" id="opb_list">
                <thead>
                    <tr>
                        {{-- <th>{{ __('language.SL') }}</th> --}}
                        <th>{{ __('language.year') }}</th>
                        <th>{{ __('language.date') }}</th>
                        <th>{{ __('language.accounting_type_name') }}</th>
                        <th>{{ __('language.account_name') }}</th>
                        <th>{{ __('language.sub_type') }}</th>
                        <th>{{ __('language.subcode') }}</th>
                        <th class="text-end">{{ __('language.debit') }}</th>
                        <th class="text-end">{{ __('language.credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 0;
                    @endphp
                    
                    @foreach ($result as $data)
                        @php
                            // $k++;
                            // $style = ($k % 2 == 0) ? '#efefef!important' : '';
                            $style =  '#efefef!important';
                        @endphp
                        <tr>
                            {{-- <td style="background: {{ $style }}">{{ $k }}</td> --}}
                            <td style="background: {{ $style }}">{{ $data->title }}</td>
                            <td style="background: {{ $style }}">
                                {{ $data->start_date ? \Carbon\Carbon::parse($data->start_date)->format('d-m-Y') : ' ' }}
                            </td>
                            <td style="background: {{ $style }}">{{ $data->account_type_name }}</td>
                            <td style="background: {{ $style }}">{{ $data->account_name }}</td>
                            <td style="background: {{ $style }}">{{ $data->subtype_name }}</td>
                            <td style="background: {{ $style }}">{{ $data->subcode_name }}</td>
                            <td style="background: {{ $style }}">{{ $data->debit }}</td>
                            <td style="background: {{ $style }}">{{ $data->credit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
   

<!-- Pagination Area -->
<div class="text-end" style="margin-right:2%">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            @php
                // $totalPages = ceil( $row / $totalRow);
                $totalPages = ceil($totalRow/$row);
                $currentPage = $page_n;
            @endphp

            {{-- Previous Page --}}
            @if ($currentPage > 1)
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage({{ $currentPage - 1 }})">{{ __('language.Previous') }}</a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" href="#">{{ __('language.previous') }}</a>
                </li>
            @endif

            {{-- Page Numbers --}}
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage({{ $i }})">{{ $i }}</a>
                </li>
            @endfor

            {{-- Next Page --}}
            @if ($currentPage < $totalPages)
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage({{ $currentPage + 1 }})">{{ __('language.Next') }}</a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" href="#">{{ __('language.next') }}</a>
                </li>
            @endif
        </ul>
    </nav>
</div>

<script>
    function changePage(pageNo) {
        var fiyear_id = $('#fiyear_id').find(":selected").val();
        var row = $('#row').find(":selected").val();
        var csrf = '{{ csrf_token() }}'; // Laravel CSRF Token
        var myurl = '{{ route('accounts.opening-balance.get') }}'; // Laravel route

        var dataString = {
            fiyear_id: fiyear_id,
            row: row,
            page: pageNo,
            csrf_test_name: csrf
        };

        $.ajax({
            type: "POST",
            url: myurl,
            data: dataString,
            success: function(data) {
                $('#getOpeningBalance').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    }
</script>
