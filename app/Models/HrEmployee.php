<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class HrEmployee extends Model {
    use HasFactory;
    protected $guarded = [];
    public function department() { return $this->belongsTo(HrDepartment::class, 'hr_department_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function attendances() { return $this->hasMany(HrAttendance::class); }
    public function leaves() { return $this->hasMany(HrLeave::class); }
    public function getFullNameAttribute() { return $this->first_name . ' ' . $this->last_name; }
}
