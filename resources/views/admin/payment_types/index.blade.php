@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Payment Type</h2>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="tablecontents">
                            @foreach ($payment_types as $payment_type)
                                <tr>
                                    <td>{{ $payment_type->id }}</td>
                                    <td>{{ $payment_type->name }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input changeStatus"
                                                data-id="{{ $payment_type->id }}" id="{{ $payment_type->id }}"
                                                {{ $payment_type->status ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="{{$payment_type->id }}">{{ $payment_type->status ? 'Active' : 'Inactive' }}</label>
                                        </div>
                                    </td>
                                    <td>Action</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#table').DataTable({
                "aaSorting": []
            });
            //for changing status
            $(document).on('click', '.changeStatus', function(e) {
                e.preventDefault();
                let btn = $(this).closest('[type=checkbox]');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change category status?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let payment_type_id = btn.data('id');
                        let status = ($(this).is(":checked")) ? 0 : 1;
                        let clickedBtn = btn;
                        $.ajax({
                            type: "GET",
                            url: '{{ route('admin.payment_types.changeStatus') }}',
                            data: {
                                'status': status,
                                'payment_type_id': payment_type_id
                            },
                            success: function(res) {
                                if (res.success) {
                                    btn.parent().find('label').text(res.status).trigger(
                                        'change');
                                    btn.prop('checked', res.checked);
                                } else {
                                    Swal.fire('Something went wrong!');
                                }
                            },
                            error: function() {
                                alert('Internal Server Error !');
                            }
                        });
                    }
                });

            });
        });
    </script>
@endsection
