<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationPlace extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'violation_places';
    protected $fillable = ['name', 'is_active', 'is_selectable', 'parent_id'];
    public $timestamps = false; // Отключить использование временных меток
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
