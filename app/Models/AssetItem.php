<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AssetItem extends Model {
    use HasFactory;
    protected $guarded = [];
    public function category() { return $this->belongsTo(AssetCategory::class, 'asset_category_id'); }
    public function location() { return $this->belongsTo(AssetLocation::class, 'asset_location_id'); }
    public function maintenances() { return $this->hasMany(AssetMaintenance::class); }
}
