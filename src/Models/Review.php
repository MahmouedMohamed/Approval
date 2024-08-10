<?php

namespace Mahmoued\Approval\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class Review extends Model
{
    use HasFactory, GeneratesUuid;

    public $fillable = [
        'number',
        'reviewed_by',
        'reviewed_at',
        'accepted',
        'id_number',
        'status',
    ];

    /**
     * The UUID version to use.
     *
     * @var int
     */
    protected $uuidVersion = 'uuid1';

    public function uuidColumn(): string
    {
        return 'uuid';
    }

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    /**
     * Get the parent reviewable model.
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }
}
