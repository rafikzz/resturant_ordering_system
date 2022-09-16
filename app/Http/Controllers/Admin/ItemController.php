<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    use ImageTrait;

    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:item_list|item_create|item_edit|item_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:item_create', ['only' => ['create','store']]);
        $this->middleware('permission:item_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:item_delete', ['only' => ['destroy','restore','forceDelete']]);
        $this->title = 'Item Management';
    }
    public function index(Request $request)
    {


        $title = $this->title;
        $breadcrumbs =[ 'Item'=>route('admin.items.index')];

        return view('admin.items.index', compact('title','breadcrumbs'));
    }


    public function create()
    {
        $title = $this->title;
        $categories = Category::where('status',1)->get();
        $breadcrumbs =[ 'Item'=>route('admin.items.index'),'Create'=>'#'];

        return view('admin.items.create', compact('title', 'categories','breadcrumbs'));
    }


    public function store(StoreItemRequest $request)
    {
        if ($image = $request->file('image')) {
            $path = 'items/';
            $imagePath = $this->uploads($image, $path);
        }
        Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => isset($request->status)?1:0,
            'category_id' => $request->category_id,
            'image' => isset($imagePath) ? $imagePath : ''

        ]);
        if(isset($request->new))
        {
            return redirect()->route('admin.items.create')->with("success", "Item saved successfully");

        }else{
            return redirect()->route('admin.items.index')->with("success", "Item saved successfully");

        }
    }


    public function show($id)
    {
        $breadcrumbs =[ 'Item'=>route('admin.items.index'),'Show'=>'#'];

        $item = Item::findOrFail($id);
        $title = $this->title;

        return view('admin.items.show', compact('item', 'title','breadcrumbs'));
    }


    public function edit($id)
    {
        $breadcrumbs =[ 'Item'=>route('admin.items.index'),'Edit'=>'#'];

        $item = Item::findOrFail($id);
        $title = $this->title;
        $categories = Category::where('status',1)->get();

        return view('admin.items.edit', compact('item', 'categories', 'title','breadcrumbs'));
    }


    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->name = $request->name;
        $item->price = $request->price;
        $item->order = $request->order;
        $item->category_id = $request->category_id;

        if ($image = $request->file('image')) {
            $path = 'items/';
            $this->deleteImage($item->image);
            $imagePath = $this->uploads($image, $path);
            $item->image = $imagePath;
        }
        $item->save();
        return redirect()->route('admin.items.index')->with("success", "Item updated successfully");
    }


    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('admin.items.index')->with("success", "Item deleted successfully");
    }

    public function forceDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $this->deleteImage($item->image);
        $item->forceDelete();
        return redirect()->route('admin.items.index')->with("success", "Item deleted permanently");
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();
        return redirect()->route('admin.items.index')->with("success", "Item restored successfully");
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if ($request->mode == 0) {
                    if(!$request->order){
                    $data = Item::select('*')->with('category:id,title')->orderBy('order');
                    }else{
                        $data = Item::select('*')->with('category:id,title');
                    }
            } else {
                $data = Item::select('*')->with('category:id,title')->onlyTrashed();
            }
            return DataTables::of($data)
                ->setRowClass('row1')
                ->setRowAttr([
                    'data-id' => function ($item) {
                        return $item->id;
                    },
                ])
                ->editColumn('image', function ($item) {
                    return $item->image();
                })
                ->addColumn('category', function ($item) {
                    return $item->category->title;
                })
                ->editColumn('status', function ($item) {

                    return ($item->status) ?
                        '<div class="custom-control custom-switch  ">
                    <input type="checkbox" class="custom-control-input changeStatus" checked data-id="' . $item->id . '" id="' . $item->id . '" >
                    <label class="custom-control-label"  for="' . $item->id . '">Active</label>' :

                        '<div class="form-group ">
                    <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input  changeStatus"  data-id="' . $item->id . '" id="' . $item->id . '" >
                    <label class="custom-control-label" for="' . $item->id . '">Inactive</label>
                   ';
                })
                ->editColumn('created_at', function ($item) {
                    return [
                        'display' => $item->created_at->diffForHumans(),
                        'timestamp' => $item->created_at
                    ];
                })
                ->addColumn(
                    'action',
                    function ($row, Request $request) {
                        if (auth()->user()->can('item_edit') || auth()->user()->can('item_delete')) {
                            if ($request->mode == 0) {
                                $editBtn =  auth()->user()->can('item_edit') ? '<a class="btn btn-xs btn-primary" href="' . route('admin.items.edit', $row->id) . '"><i class="fa fa-pencil-alt"></i></a>' : '';
                                $deleteBtn =  auth()->user()->can('item_delete') ? '<button type="submit" class="btn btn-xs btn-danger btn-delete"><i class="fa fa-trash-alt"></i></button>' : '';
                                $formStart = '<form action="' . route('admin.items.destroy', $row->id) . '" method="POST">
                                <input type="hidden" name="_method" value="delete">' . csrf_field();
                                $formEnd = '</form>';
                                $btn = $formStart . $editBtn . ' ' . $deleteBtn . $formEnd;


                                return $btn;
                            } else {
                                $deleteBtn =  auth()->user()->can('item_delete') ? '<button type="submit" class="btn btn-xs btn-danger btn-delete"><i class="fa fa-trash-alt"></i></button>' : '';
                                $formStart = '<form action="' . route('admin.items.forceDelete', $row->id) . '" method="POST">
                                ' . csrf_field() . '
                                <input type="hidden" name="_method" value="delete" />';
                                $restoreBtn =  auth()->user()->can('item_delete') ? '<a class="btn btn-xs btn-success" href="' . route('admin.items.restore', $row->id) . '">Restore</a>' : '';
                                $formEnd = '</form>';
                                $btn = $formStart . $restoreBtn . '  ' . $deleteBtn . $formEnd;

                                return $btn;
                            }
                        } else {
                            return 'No Action';
                        }
                    }
                )
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function changeStatus(Request $request)
    {
        $itemId = $request->item_id;
        $status =  $request->status;
        Item::whereId($itemId)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'status' => $status == 1 ? 'Active' : 'Inactive',
            'checked' => $status == 1 ? true : false,
            'message' => 'Status Successfully Changed!',
        ]);
    }

    public function updateOrder(Request $request)
    {
        $items = Item::get();

        foreach ($items as $item) {
            foreach ($request->order as $order) {
                if ($order['id'] == $item->id) {
                    $item->update(['order' => $order['position']]);
                }
            }
        }

        return response('Update Successfully.', 200);
    }

    public function getCategoryItemsData(Request $request){
        if($request->ajax()){
            $category_id =$request->category_id;
            $categoryItems =Item::where('category_id',$category_id)->where('status',1)->orderBy('category_id','desc')->get();
            if($categoryItems->count()){
                return response()->json([
                    'items'=>$categoryItems,
                    'status'=>'success',
                    'message'=>'success'
                ],200);
            }else{
                return response()->json([
                    'message'=>'No items for this category available',
                    'status'=>'fail',
                ],200);
            }
        }
    }
}
