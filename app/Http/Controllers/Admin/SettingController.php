<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    use ImageTrait;

    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:setting_create', ['only' => ['create', 'store']]);
        $this->title = 'Setting Management';
    }


    public function create()
    {
        $title =  $this->title;
        $breadcrumbs = ['Setting' => route('admin.settings.create')];

        $setting = Setting::get()->first();

        return view('admin.settings.create', compact('setting', 'title', 'breadcrumbs'));
    }


    public function store(StoreSettingRequest $request)
    {
        $setting = Setting::get()->first();

        if (!isset($setting)) {
            $setting = new Setting;
        }
        if (isset($request->company_name)) {
            $setting->company_name = $request->company_name;
        }
        if (isset($request->contact_information)) {
            $setting->contact_information = $request->contact_information;
        }
        if (isset($request->office_location)) {
            $setting->office_location = $request->office_location;
        }
        if ($request->has('bill_no_prefix') ){
            $setting->bill_no_prefix = $request->bill_no_prefix;
        }
        if (isset($request->tax)) {
            $setting->tax = $request->tax;
        }
        $setting->tax_status = $request->tax_status ?: 0;
        $setting->service_charge_status = $request->service_charge_status ?: 0;
        $setting->delivery_charge_status = $request->delivery_charge_status ?: 0;


        if (isset($request->service_charge)) {
            $setting->service_charge = $request->service_charge;
        }
        if (isset($request->delivery_charge)) {
            $setting->delivery_charge = $request->delivery_charge;
        }
        if ($logo = $request->file('logo')) {
            $path = 'images/logo/';
            if (isset($setting->log)) {
                $this->deleteImage($setting->logo);
            }
            $imagePath = $this->uploads($logo, $path);
            $setting->logo = $imagePath;
        }
        $setting->save();

        return redirect()->route('admin.settings.create')->with('success', 'Settings Saved Successfully');
    }
}
