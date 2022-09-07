<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $table ='table_settings';

    protected $fillable =['logo','contact_information','office_location'];


    public function logo()
    {
        return (Storage::disk('public')->exists($this->logo)) ? '/storage/' . $this->logo : asset('noimgavialable.png');
    }
}
