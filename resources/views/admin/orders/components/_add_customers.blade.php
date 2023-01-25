<div class="col-12 my-2 d-flex">
    <label for="">New or Existing: </label>
    <div class="form-check mx-2">
        <input class="form-check-input" type="radio" name="new_or_old" id="existingCustomer" value="existing"
            {{ old('new_or_old') !== 'new' ? 'checked' : '' }}>
        <label class="form-check-label" for="existingCustomer">
            Existing
        </label>
    </div>
    <div class="form-check mx-2">
        <input class="form-check-input" type="radio" name="new_or_old" id="newCustomer" value="new"
            {{ old('new_or_old') == 'new' ? 'checked' : '' }}>
        <label class="form-check-label" for="newCustomer">
            New
        </label>
    </div>
</div>
<div id="old" class="col-12 form-group  py-3"
    style="display:  {{ old('new_or_old') !== 'new' ? 'block' : 'none' }}">
    <select name="customer_id" class="form-control select2" style="width: 75%" id="oldCustomer" required
        {{ old('new_or_old') == 'new' ? 'disabled' : '' }} required>
        <option value="">--Please Select Customer--</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}"
                {{ old('customer_id', $customer_id) == $customer->id ? 'selected' : '' }}
                {{ $customer->id == 1 ? 'selected' : '' }}>
                @if (isset($customer->staff))
                    {{ $customer->staff->department->code }} {{ $customer->name }}({{ $customer->phone_no }})
                    Department: {{ $customer->staff->department->name }}
                @elseif (isset($customer->patient))
                    {{ $customer->name }}({{ $customer->phone_no }}) Register No:
                    {{ $customer->patient->register_no }}
                @else
                    {{ $customer->name }}({{ $customer->phone_no }})
                @endif


            </option>
        @endforeach
    </select>
    @error('customer_id')
        <span class="text-danger" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div id="new" class="col-12  py-3 form-group"
    style="display: {{ old('new_or_old') === 'new' ? 'block' : 'none' }};">
    <div class="row d-flex">
        <div class="col-6">
            <label for="customer_name">Customer Name @component('compoments.required')

                @endcomponent</label>
            <input class="form-control" name="customer_name" value="{{ old('customer_name') }}" type="text"
                placeholder="Enter Customer Name" autocomplete="off"
                {{ old('new_or_old') !== 'new' ? 'disabled' : '' }} required>
            @error('customer_name')
                <span class=" text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-6">
            <label for="customer_phone_no">Customer Number @component('compoments.required')

                @endcomponent</label>
            <input class="form-control" name="customer_phone_no" type="text" value="{{ old('customer_phone_no') }}"
                placeholder="Enter Customer Phone No" autocomplete="off" required
                {{ old('new_or_old') !== 'new' ? 'disabled' : '' }}>
            @error('customer_phone_no')
                <span class=" text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

    </div>
    <div class="row d-flex staff-block" {{ $customer_type_id == 2 ? '' : 'style=display:none' }}>
        <div class="col-md-6 staff-block" {{ $customer_type_id == 2 ? '' : 'style=display:none' }}>
            <label for="department_id">Department @component('compoments.required')

                @endcomponent</label>
            <select name="department_id" id="department_id" class="form-control" required
                {{ old('new_or_old') !== 'new' ? 'disabled' : '' }} {{ $customer_type_id == 2 ? '' : 'disabled' }}>
                <option value="">Choose Department</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <span class=" text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-6 staff-block" {{ $customer_type_id == 2 ? '' : 'style=display:none' }}>
            <label for="customer_phone_no">Code No @component('compoments.required')

                @endcomponent</label>
            <input class="form-control" name="code" type="text" value="{{ old('code', $code_no) }}"
                placeholder="Enter code" autocomplete="off" required
                {{ old('new_or_old') !== 'new' ? 'disabled' : '' }} {{ $customer_type_id == 2 ? '' : 'disabled' }}
                id="code">
            @error('code')
                <span class=" text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row col-6" {{ $customer_type_id == 3 ? '' : 'style=display:none' }} id="patient-reg">
        <label for="customer_phone_no">Patient Registred No @component('compoments.required')

            @endcomponent</label>
        <input class="form-control" name="patient_register_no" type="text" value="{{ old('patient_register_no') }}"
            placeholder="Enter Customer Phone No" autocomplete="off" required
            {{ old('new_or_old') !== 'new' ? 'disabled' : '' }} {{ $customer_type_id == 3 ? '' : 'disabled' }}
            id="patient_register_no">
        @error('patient_register_no')
            <span class=" text-danger" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
