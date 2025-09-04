<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\GallonRequest
 *
 * @property int $id
 * @property int $employee_id
 * @property int $quantity
 * @property string $status
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $ready_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int|null $approved_by
 * @property int|null $prepared_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $preparedBy
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest wherePreparedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereReadyAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest pending()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest ready()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest completed()
 * @method static \Illuminate\Database\Eloquent\Builder|GallonRequest today()
 * @method static \Database\Factories\GallonRequestFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class GallonRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'quantity',
        'status',
        'requested_at',
        'approved_at',
        'ready_at',
        'completed_at',
        'approved_by',
        'prepared_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'quantity' => 'integer',
        'employee_id' => 'integer',
        'approved_by' => 'integer',
        'prepared_by' => 'integer',
    ];

    /**
     * Get the employee that made the request.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved the request.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who prepared the request.
     */
    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include ready requests.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope a query to only include completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include today's requests.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('requested_at', today());
    }

    /**
     * Approve the request.
     */
    public function approve(User $user): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);
    }

    /**
     * Mark as ready for pickup.
     */
    public function markReady(User $user): void
    {
        $this->update([
            'status' => 'ready',
            'ready_at' => now(),
            'prepared_by' => $user->id,
        ]);
    }

    /**
     * Complete the pickup.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}