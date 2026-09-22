<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TicketReply extends Model {
    use HasFactory;
    protected $guarded = [];
    public function issue() { return $this->belongsTo(TicketIssue::class, 'ticket_issue_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
