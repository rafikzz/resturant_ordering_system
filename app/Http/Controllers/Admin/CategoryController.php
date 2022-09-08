<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    use ImageTrait;

    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:category_list|category_create|category_edit|category_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:category_create', ['only' => ['create','store']]);
        $this->middleware('permission:category_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:category_delete', ['only' => ['destroy','restore','forceDelete']]);
        $this->title = 'Category Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Category'=>'#'];
        return view('admin.categories.index', compact('title','breadcrumbs'));
    }


    public function create()
    {

        $title = $this->title;
        $breadcrumbs =[ 'Category'=>route('admin.categories.index'), 'Create'=>'#'];


        return view('admin.categories.create', compact('title','breadcrumbs'));
    }


    public function store(StoreCategoryRequest $request)
    {
        if ($image = $request->file('image')) {
            $path = 'categories/';
            $imagePath = $this->uploads($image, $path);
        }
        Category::create([
            'title' => $request->title,
            'image' => isset($imagePath)?$imagePath:'',
        ]);
        if(isset($request->new))
        {
            return redirect()->route('admin.categories.create')->with("success", "Category saved successfully");

        }else{
            return redirect()->route('admin.categories.index')->with("success", "Category saved successfully");

        }
    }


    public function show($id)
    {
        $category = Category::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Category'=>route('admin.categories.index'), 'Show'=>'#'];

        return view('admin.categories.show', compact('category', 'title'));
    }


    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Category'=>route('admin.categories.index'), 'Edit'=>'#'];


        return view('admin.categories.edit', compact('category', 'title','breadcrumbs'));
    }


    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->title =$request->title;
        $category->order =$request->order;
        if ($image = $request->file('image')) {
            $path = 'categories/';
            $this->deleteImage($category->image);
            $imagePath = $this->uploads($image, $path);
            $category->image =$imagePath;

        }

        $category->save();
        return redirect()->route('admin.categories.index')->with("success", "Category updated successfully");
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with("success", "Category deleted successfully");
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $this->deleteImage($category->image);
        $category->forceDelete();
        return redirect()->route('admin.categories.index')->with("success", "Category deleted permanently");
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('admin.categories.index')->with("success", "Category restored successfully");
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if ($request->mode == 0) {
                if(!$request->order){
                    $data = Category::select('*')->orderBy('order');
                }else{
                    $data = Category::select('*');
                }
            } else {
                $data = Category::select('*')->onlyTrashed()->orderBy('order');
            }
            return DataTables::of($data)
                ->setRowClass('row1')
                ->setRowAttr([
                    'data-id' => function ($category) {
                        return $category->id;
                    },
                ])
                ->editColumn('status', function ($category) {

                    return ($category->status) ?
                        '<div class="custom-control custom-switch  ">
                    <input type="checkbox" class="custom-control-input changeStatus" checked data-id="' . $category->id . '" id="' . $category->id . '" >
                    <label class="custom-control-label"  for="' . $category->id . '">Active</label>' :

                        '<div class="form-group ">
                    <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input  changeStatus"  data-id="' . $category->id . '" id="' . $category->id . '" >
                    <label class="custom-control-label" for="' . $category->id . '">Inactive</label>
                   ';
                })
                ->editColumn('created_at', function ($category) {
                    return [
                        'display' => $category->created_at->diffForHumans(),
                        'timestamp' => $category->created_at
                    ];
                })
                ->editColumn('image', function ($category) {
                    return $category->image();
                })
                ->addColumn(
                    'action',
                    function ($row, Request $request) {
                        if (auth()->user()->can('category_edit') || auth()->user()->can('category_delete')) {
                            if ($request->mode == 0) {
                                $editBtn =  auth()->user()->can('category_edit') ? '<a class="btn btn-sm btn-primary" href="' . route('admin.categories.edit', $row->id) . '">Edit</a>' : '';
                                $deleteBtn =  auth()->user()->can('category_delete') ? '<button type="submit" class="btn btn-sm btn-danger btn-delete">Delete</button>' : '';
                                $formStart = '<form action="' . route('admin.categories.destroy', $row->id) . '" method="POST">
                                <input type="hidden" name="_method" value="delete">' . csrf_field();
                                $formEnd = '</form>';
                                $btn = $formStart . $editBtn . ' ' . $deleteBtn . $formEnd;

                                return $btn;
                            } else {
                                $deleteBtn =  auth()->user()->can('category_delete') ? '<button type="submit" class="btn btn-sm btn-danger btn-delete">Delete</button>' : '';
                                $formStart = '<form action="' . route('admin.categories.forceDelete', $row->id) . '" method="POST">
                                ' . csrf_field() . '
                                <input type="hidden" name="_method" value="delete" />';
                                $restoreBtn =  auth()->user()->can('category_delete') ? '<a class="btn btn-sm btn-success" href="' . route('admin.categories.restore', $row->id) . '">Restore</a>' : '';
                                $formEnd = '</form>';
                                $btn = $formStart . $restoreBtn . '  ' . $deleteBtn . $formEnd;

                                return $btn;
                            }
                        }
                        return 'No Action';
                    }
                )
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function changeStatus(Request $request)
    {
        $categoryId = $request->category_id;
        $status =  $request->status;
        Category::whereId($categoryId)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'status' => $status == 1 ? 'Active' : 'Inactive',
            'checked' => $status == 1 ? true : false,
            'message' => 'Status Successfully Changed!',
        ]);
    }

    public function updateOrder(Request $request)
    {
        $categories = Category::get();

        foreach ($categories as $category) {
            foreach ($request->order as $order) {
                if ($order['id'] == $category->id) {
                    $category->update(['order' => $order['position']]);
                }
            }
        }

        return response('Update Successfully.', 200);
    }
}
