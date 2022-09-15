<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:role_list|role_create|role_edit|role_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:role_create', ['only' => ['create','store']]);
        $this->middleware('permission:role_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:role_delete', ['only' => ['destroy']]);
        $this->title = 'Role Management';
    }
    public function index(Request $request)
    {
        // $roles = Role::with('roles')->orderBy('id', 'desc')->get();
        $title = $this->title;

        $breadcrumbs =[ 'Role'=>route('admin.roles.index')];

        return view('admin.roles.index', compact('title','breadcrumbs'));
    }


    public function create()
    {
        $permissions = Permission::all();
        $title = $this->title;
        $breadcrumbs =[ 'Role'=>route('admin.roles.index'),'Create'=>'#'];

        return view('admin.roles.create', compact('permissions', 'title','breadcrumbs'));
    }


    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name'=>$request->name]);
        $role->syncPermissions($request->permissions);
        if(isset($request->new))
        {
            return redirect()->route('admin.roles.create')->with("success", "Role saved successfully");

        }else{
            return redirect()->route('admin.roles.index')->with("success", "Role saved successfully");
        }

    }


    public function show($id)
    {
        $role = Role::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Role'=>route('admin.roles.index'),'Show'=>'#'];


        return view('admin.roles.show', compact('role', 'title','breadcrumbs'));
    }


    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $breadcrumbs =[ 'Role'=>route('admin.roles.index'),'Edit'=>'#'];

        $rolePermissions = $role->permissions->pluck('name','name')->toArray();
        $permissions = Permission::all();
        $title = $this->title;

        return view('admin.roles.edit', compact('rolePermissions','permissions', 'role', 'title','breadcrumbs'));
    }


    public function update(UpdateRoleRequest $request,Role $role)
    {
        $role->update(['name'=>$request->name]  );
        $role->syncPermissions($request->permissions);
        return redirect()->route('admin.roles.index')->with("success", "Role updated successfully");
    }


    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with("success", "Role deleted successfully");
    }



    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');

            return DataTables::of($data)
                ->editColumn('created_at', function (Role $role) {
                    return [
                        'display' => $role->created_at->diffForHumans(),
                        'timestamp' => $role->created_at
                     ];
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        if (auth()->user()->can('role_edit') || auth()->user()->can('role_delete')) {
                        $editBtn =  auth()->user()->can('role_edit') ? '<a class="btn btn-xs btn-primary" href="' . route('admin.roles.edit', $row->id) . '">Edit</a>' : '';
                        $deleteBtn =  auth()->user()->can('role_delete') ? '<button type="submit" class="btn btn-xs btn-danger btn-delete">Delete</button>' : '';
                        $formStart = '<form action="' . route('admin.roles.destroy', $row->id) . '" method="POST">
                                ' . csrf_field() . '
                                 <input type="hidden" name="_method" value="delete" />';
                        $formEnd = '</form>';
                        $btn = $formStart . $editBtn . ' ' . $deleteBtn . $formEnd;


                        return $btn;
                        }else{
                            return 'No Action';
                        }
                    }
                )
                ->make(true);
        }
    }
}
