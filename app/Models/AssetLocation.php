<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AssetLocation extends Model {
    use HasFactory;
    protected $guarded = [];
    public function assetItems() { return $this->hasMany(AssetItem::class); }
}
