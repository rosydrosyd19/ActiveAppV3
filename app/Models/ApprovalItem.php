<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ApprovalItem extends Model {
    use HasFactory;
    protected $guarded = [];
    public function request() { return $this->belongsTo(ApprovalRequest::class, 'approval_request_id'); }
}
