<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:user_list|user_create|user_edit|user_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:user_create', ['only' => ['create','store']]);
        $this->middleware('permission:user_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:user_delete', ['only' => ['destroy','restore','forceDelete']]);

        $this->title = 'User Management';
    }
    public function index(Request $request)
    {
        // $users = User::with('roles')->orderBy('id', 'desc')->get();
        $title = $this->title;
        $breadcrumbs =[ 'User'=>route('admin.users.index')];


        return view('admin.users.index', compact('title'));
    }


    public function create()
    {
        $roles = Role::all();
        $title = $this->title;
        $breadcrumbs =[ 'User'=>route('admin.users.index')];

        return view('admin.users.create', compact('roles', 'title','breadcrumbs'));
    }


    public function store(StoreUserRequest $request)
    {
        $data = $request->except(['_token', '_method', 'roles']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->syncRoles($request->roles);

        if(isset($request->new))
        {
            return redirect()->route('admin.users.create')->with("success", "User saved successfully");
        }else{
            return redirect()->route('admin.users.index')->with("success", "User saved successfully");

        }
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'User'=>route('admin.users.index'),'Show'=>'#'];


        return view('admin.users.show', compact('user', 'title','breadcrumbs'));
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $title = $this->title;
        $breadcrumbs =[ 'User'=>route('admin.users.index'),'Edit'=>'#'];


        return view('admin.users.edit', compact('user', 'roles', 'title','breadcrumbs'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->except(['_token', '_method', 'roles','confirm-password']);
        if($request->password)
        {
         $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($request->roles);
        return redirect()->route('admin.users.index')->with("success", "User updated successfully");
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with("success", "User deleted successfully");
    }

    public function forceDelete($id)
    {
        $user= User::onlyTrashed()->whereId($id)->firstorFail();
        $user->forceDelete();
        return redirect()->route('admin.users.index')->with("success", "User deleted permanently");
    }

    public function restore($id)
    {
        $user= User::onlyTrashed()->whereId($id)->firstorFail();
        $user->restore();
        return redirect()->route('admin.users.index')->with("success", "User restored successfully");
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if ($request->mode == 0) {
                $data = User::select('*')->with('roles')->withCount('created_orders')->withCount('updated_orders');
            } else {
                $data = User::select('*')->with('roles')->onlyTrashed();
            }

            $canEdit=auth()->user()->can('user_edit');
            $canDelete =auth()->user()->can('user_delete');
            return DataTables::of($data)
                ->editColumn('created_at', function (User $user) {
                    return [
                        'display' => $user->created_at->diffForHumans(),
                        'timestamp' => $user->created_at
                     ];
                })
                ->addColumn('roles', function (User $user) {
                    $var='';
                    foreach ($user->roles as $role) {
                        $var =$var.' '. '<label class="badge badge-info">' . $role->name . '</label>';
                    }
                    return $var;

                })
                ->addColumn(
                    'action',
                    function ($row, Request $request) use($canEdit,$canDelete) {
                        if($canEdit){
                            if ($request->mode == 0) {
                                $editBtn =  $canEdit ? '<a class="btn btn-xs btn-warning" href="' . route('admin.users.edit', $row->id) . '"   data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-alt"></i></a>' : '';
                                if(!($row->created_orders_count || $row->updated_orders_count))
                                {
                                    $deleteBtn =  $canDelete ? '<button type="submit" class="btn btn-xs btn-danger btn-delete"   data-toggle="tooltip" title="Delete"><i class="fa fa-trash-alt"></i></button>' : '';

                                }
                                else{
                                    $deleteBtn = null;
                                }
                                $formStart = '<form action="' . route('admin.users.destroy', $row->id) . '" method="POST">
                                ' . csrf_field() . '
                                 <input type="hidden" name="_method" value="delete" />';
                                $formEnd = '</form>';
                                $btn = $formStart . $editBtn . ' ' . $deleteBtn . $formEnd;


                                return $btn;
                            } else {
                                $deleteBtn =  $canDelete ? '<button type="submit" class="btn btn-xs btn-danger btn-delete"   data-toggle="tooltip" title="Delete">Delete</button>' : '';
                                $formStart = '<form action="' . route('admin.users.forceDelete', $row->id) . '" method="POST">
                                ' . csrf_field() . '
                                 <input type="hidden" name="_method" value="delete" />';
                                 $restoreBtn =  $canDelete ? '<a class="btn btn-xs btn-success" href="' . route('admin.users.restore', $row->id) . '" data-toggle="tooltip" title="Restore" >Restore</a>' : '';
                                $formEnd = '</form>';
                                $btn = $formStart .$restoreBtn .'  '.$deleteBtn . $formEnd;


                                return $btn;
                            }
                        }
                        return 'No Action';
                    }
                )
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }
    }
}
