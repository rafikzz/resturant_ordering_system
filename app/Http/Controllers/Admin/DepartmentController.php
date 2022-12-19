<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:coupon_list|department_create|department_edit|department_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:department_create', ['only' => ['create','store']]);
        $this->middleware('permission:department_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:department_delete', ['only' => ['destroy']]);
        $this->title = 'Department Management';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;
        $departments =Department::latest()->get();
        $breadcrumbs =[ 'Department'=>'#'];
        return view('admin.departments.index', compact('title','breadcrumbs','departments'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Department'=>route('admin.departments.index'), 'Create'=>'#'];


        return view('admin.departments.create', compact('title','breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepartmentRequest $request)
    {
        Department::create([
            'name' => $request->name,
        ]);

        if(isset($request->new))
        {
            return redirect()->route('admin.departments.create')->with("success", "Department saved successfully");

        }else{
            return redirect()->route('admin.departments.index')->with("success", "Department saved successfully");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Department'=>route('admin.departments.index'), 'Edit'=>'#'];


        return view('admin.departments.edit', compact('title','breadcrumbs','Department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->name =$request->name;


        $department->save();
        return redirect()->route('admin.departments.index')->with("success", "Department updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with("success", "Department deleted successfully");

    }
}
