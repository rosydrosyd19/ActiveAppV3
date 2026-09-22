<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class HrLeave extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];
    public function employee() { return $this->belongsTo(HrEmployee::class, 'hr_employee_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
