@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Coupon List</h2>
                    <div class="card-tools form-inline">
                        @can('coupon_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.coupons.create') }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body  table-responsive">
                    <table class="table table-bordered" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Discount Amount</th>
                            <th>Expiry Date</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="tablecontents">
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->id }}</td>
                                    <td>{{ $coupon->title }}</td>
                                    <td>{{ $coupon->discount }}</td>
                                    <td>{{ $coupon->expiry_date }}</td>
                                    <td>{{ $coupon->created_at->diffForHumans() }}</td>
                                    <td class="form-inline">
                                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            @can('coupon_edit')
                                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}"  data-toggle="tooltip" title="Detail"
                                                    class="btn btn-warning btn-xs">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                            @endcan
                                            {{-- <button type="submit" class="btn btn-xs btn-danger btn-delete"><i class="fa fa-trash-alt"></i></button> --}}
                                        </form>
                                    </td>
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
        $('#table').DataTable({
            "aaSorting": []
        });

        //for delete btn
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let form = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to  delete coupons?",
                icon: 'danger',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest("form").submit();
                }
            });
        });
    </script>
@endsection
