<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $table ='table_categories';
    protected $fillable = ['title','order','status','image'];

    public function image()
    {
        return (Storage::disk('public')->exists($this->image)) ? '/storage/' . $this->image : asset('noimgavialable.png');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function setTitlesAttribute($value)
    {
        $this->attributes['title'] = strtolower($value);
    }

    public function getTitlesAttribute($title)
    {
        return  ucwords($title);
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($category) {
            $category->items()->delete();
        });

        static::restoring(function($category) {
            $category->items()->restore();
        });
    }
}
