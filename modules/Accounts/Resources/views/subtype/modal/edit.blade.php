<form class="validateEditForm" action="{{route('subtypes.update',$type->id)}}" method="POST" >
@csrf
@method('PATCH')
<div class="modal-body">
    <div class="row">
        <div class="form-group mb-2 mx-0 row">
            <label for="subtype_name" class="col-lg-3 col-form-label ps-0 label_subtype_name">
                {{ __('language.account_subtype_name') }}
                            <span class="text-danger">*</span>
                    </label>
            <div class="col-lg-9">
                <input name="name" type="text" value="{{$type->name}}" placeholder="{{ __('language.account_subtype_name') }}" class="form-control" required>
            </div>
        </div>
        <div class="form-group mb-2 mx-0 row">

            <label for="is_system" class="col-lg-3 col-form-label ps-0">
                {{__('language.is_system') }}
            </label>

            <div class="col-lg-9 text-start">
                <input type="checkbox" name="isSystem" id="is_system" value="1" {{ ($type->isSystem==1?'checked':'')}} />
            </div>
        </div>
        {{-- @radio(['input_name' => 'status','data_set' => [1 => 'Active' ,0 => 'Inactive'],'value' => $type->status]) --}}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('language.close') }}</button>
    <button  class="btn btn-primary submit_button" id="create_submit">{{ __('language.update')}}</button>
</div>
</form>
