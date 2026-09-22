<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TicketIssue extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['resolved_at' => 'datetime'];
    public function category() { return $this->belongsTo(TicketCategory::class, 'ticket_category_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function assignees() { return $this->belongsToMany(User::class, 'ticket_issue_user', 'ticket_issue_id', 'user_id'); }
    public function replies() { return $this->hasMany(TicketReply::class); }
}
