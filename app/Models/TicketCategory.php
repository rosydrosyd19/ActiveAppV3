<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TicketCategory extends Model {
    use HasFactory;
    protected $guarded = [];
    public function issues() { return $this->hasMany(TicketIssue::class); }
}
