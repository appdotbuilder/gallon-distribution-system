<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string $employee_id
 * @property string $name
 * @property string $grade
 * @property int $monthly_quota
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GallonRequest> $gallonRequests
 * @property-read int|null $gallon_requests_count
 * @property-read int $current_month_taken
 * @property-read int $remaining_quota
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMonthlyQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee active()
 * @method static \Database\Factories\EmployeeFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'grade',
        'monthly_quota',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'monthly_quota' => 'integer',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'current_month_taken',
        'remaining_quota',
    ];

    /**
     * Grade quota mapping.
     *
     * @var array<string, int>
     */
    public static array $gradeQuotas = [
        'G7' => 24,
        'G8' => 24,
        'G9' => 12,
        'G10' => 10,
        'G11' => 7,
        'G12' => 7,
        'G13' => 7,
    ];

    /**
     * Get the gallon requests for the employee.
     */
    public function gallonRequests(): HasMany
    {
        return $this->hasMany(GallonRequest::class);
    }

    /**
     * Get the current month's taken gallons.
     */
    public function getCurrentMonthTakenAttribute(): int
    {
        return $this->gallonRequests()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'completed')
            ->sum('quantity');
    }

    /**
     * Get the remaining quota for current month.
     */
    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->monthly_quota - $this->current_month_taken);
    }

    /**
     * Get pending pickup requests.
     */
    public function getPendingPickupRequests()
    {
        return $this->gallonRequests()
            ->where('status', 'ready')
            ->orderBy('ready_at', 'desc')
            ->get();
    }

    /**
     * Get complete gallon history.
     */
    public function getGallonHistory()
    {
        return $this->gallonRequests()
            ->orderBy('requested_at', 'desc')
            ->get();
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Set monthly quota based on grade.
     */
    public function setGradeAttribute($value): void
    {
        $this->attributes['grade'] = $value;
        $this->attributes['monthly_quota'] = self::$gradeQuotas[$value] ?? 7;
    }
}