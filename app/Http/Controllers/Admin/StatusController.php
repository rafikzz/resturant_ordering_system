<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStatusRequest;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Models\Status;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StatusController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:order_list|order_create|order_edit|order_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:order_edit', ['only' => ['edit','update',]]);
        $this->title = 'Status Management';
    }
    public function index()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Status'=>route('admin.statuses.index')];

        return view('admin.statuses.index', compact('title','breadcrumbs'));
    }


    // public function create()
    // {
    //     $title = $this->title;
    //     return view('admin.statuses.create', compact('title'));
    // }
    // public function store(StoreStatusRequest $request)
    // {

    //     Status::create([
    //         'title' => $request->title,
    //         'color' => $request->color,
    //     ]);
    //     return redirect()->route('admin.statuses.index')->with("success", "Status saved successfully");
    // }

    // public function show($id)
    // {
    //     $status = Status::findOrFail($id);
    //     $title = $this->title;

    //     return view('admin.statuses.show', compact('status', 'title'));
    // }

    public function edit($id)
    {
        $status = Status::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Status'=>route('admin.statuses.index'),'Edit'=>'#'];

        return view('admin.statuses.edit', compact('status', 'title','breadcrumbs'));
    }

    public function update(UpdateStatusRequest $request, Status $status)
    {
        $status->title = $request->title;
        $status->color = $request->color;
        $status->save();

        return redirect()->route('admin.statuses.index')->with("success", "Status updated successfully");
    }

    // public function destroy($id)
    // {
    //     $status = Status::findOrFail($id);
    //     $status->delete();
    //     return redirect()->route('admin.statuses.index')->with("success", "Status deleted successfully");

    // }


    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Status::select('*');

            return DataTables::of($data)
                ->editColumn('created_at', function (Status $status) {
                    return [
                        'display' => $status->created_at->diffForHumans(),
                        'timestamp' => $status->created_at
                    ];
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $editBtn =  auth()->user()->can('status_edit') ? '<a class="btn btn-sm btn-primary" href="' . route('admin.statuses.edit', $row->id) . '">Edit</a>' : 'No Action';
                        return $editBtn;
                    }
                )
                ->make(true);
        }
    }
}
