<form id="leadForm" action="{{ route('subcodes.update', $code->id) }}" method="POST">
    @csrf
    @method('PATCH')
    <div class="modal-body">
        <div class="row">
            <div class="form-group mb-2 mx-0 row">
                <label for="subtype_id"
                    class="col-sm-3 col-form-label ps-0">{{ __('language.subtype') }}<span
                        class="text-danger">*</span></label>
                <div class="col-lg-9">
                    
                
                    
                    <select name="acc_subtype_id" class="form-select">
                        <option value=""> {{ __('language.select_subtype') }}</option>
                        @foreach ($subtypes as $key => $type)
                            <option value="{{ $type->id }}"

                                {{ $code->subTypeID == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}

                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('acc_subtype_id'))
                        <div class="error text-danger text-start">{{ $errors->first('acc_subtype_id') }}
                        </div>
                    @endif
                </div>
            </div>
            {{-- @input(['input_name' => 'name', 'value' => $code->name]) --}}


            <div class="form-group mb-2 mx-0 row">
                <label for="subcode_id"
                    class="col-sm-3 col-form-label ps-0">{{ __('language.subcode') }}<span
                        class="text-danger">*</span></label>
                <div class="col-lg-9">
                    <input type="text" id="subcode_id" name="name" class="form-control" value="{{ $code->name }}">
                </div>
            </div>
            {{-- @radio(['input_name' => 'status', 'data_set' => [1 => 'Active', 0 => 'Inactive'], 'value' => $code->status]) --}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger"
            data-bs-dismiss="modal">{{ __('language.close') }}</button>
        <button class="btn btn-primary submit_button" id="create_submit">{{ __('language.update') }}</button>
    </div>
</form>
