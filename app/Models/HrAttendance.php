<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class HrAttendance extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['check_in_time' => 'datetime', 'check_out_time' => 'datetime'];
    public function employee() { return $this->belongsTo(HrEmployee::class, 'hr_employee_id'); }
}
