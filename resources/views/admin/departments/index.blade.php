@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Department List</h2>
                    <div class="card-tools form-inline">
                        @can('department_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.departments.create') }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body  table-responsive">
                    <table class="table table-bordered" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="tablecontents">
                            @foreach ($departments as $department)
                                <tr>
                                    <td>{{ $department->id }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->created_at->diffForHumans() }}</td>
                                    <td class="form-inline">
                                        <form action="{{ route('admin.departments.destroy', $department->id) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            @can('department_edit')
                                                <a href="{{ route('admin.departments.edit', $department->id) }}"
                                                    class="btn btn-warning btn-xs"  data-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                            @endcan

                                            @can('department_delete')
                                                @if (!$department->staffs_count)
                                                    <button type="submit" class="btn btn-xs btn-danger btn-delete"  data-toggle="tooltip" title="Delete"><i
                                                            class="fa fa-trash-alt"></i></button>
                                                @endif
                                            @endcan
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
                text: "Do you want to  delete department?",
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
