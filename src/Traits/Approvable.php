<?php

namespace Mahmoued\Approval\Traits;

use Mahmoued\Approval\Models\Review;
use Carbon\Carbon;
use Mahmoued\Approval\Exceptions\InvalidStatusException;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Approvable
 *
 * @version March 30, 2023, 01:28 AM UTC
 */
trait Approvable
{
    /**
     * Number of approval levels
     * */
    public static $REQUIRED_NUMBER_OF_APPROVALS = 1;

    /**
     * Status which model switches to when fully approved
     * */
    public static $APPROVED_STATUS = null;

    /**
     * Status which model start approval cycle from "value"
     * */
    public static $PENDING_APPROVAL_STATUS = null;

    /**
     * Status which model start approval cycle from "text"
     * */
    public static $PENDING_APPROVAL_STATUS_TEXT = null;

    /**
     * Status which model ends approval cycle to incase of rejected "value"
     * */
    public static $REJECTED_STATUS = null;

    /**
     * Status which model ends approval cycle to incase of rejected "text"
     * */
    public static $REJECTED_STATUS_TEXT = null;

    /**
     * Support External Statuses
     * */
    public static $SUPPORT_EXTERNAL_MESSAGES = false;

    /**
     * Status which model start approval cycle from "text" for External
     * */
    public static $PENDING_APPROVAL_STATUS_TEXT_EXTERNAL = null;

    /**
     * Status which model ends approval cycle to incase of rejected "text" for External
     * */
    public static $REJECTED_STATUS_TEXT_EXTERNAL = null;

    /**
     * Get all of the model's reviews.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'approvable');
    }

    /**
     * Approve Function
     * Add Review Entity for Model
     * Change Status of Model to Approved Status if Fully Reached Final Approval
     */
    public function approve(int $currentRank, string $reviewed_by, Carbon $reviewed_at)
    {
        $reviews = $this->reviews;
        if ($currentRank == $reviews->count() + 1 && $this->getRawOriginal('status') === static::$PENDING_APPROVAL_STATUS) {
            $this->reviews()->create([
                'number' => $reviews->count() + 1,
                'reviewed_by' => $reviewed_by,
                'reviewed_at' => $reviewed_at,
                'accepted' => true,
            ]);
            if ($reviews->count() + 1 == static::$REQUIRED_NUMBER_OF_APPROVALS) {
                $this->status = static::$APPROVED_STATUS;
                $this->save();
            }
        } else {
            throw new InvalidStatusException();
        }
    }

    /**
     * Reject Function
     * Add Review Entity for Model
     * Change Status of Model to Rejected Status if Rejected
     */
    public function reject(int $currentRank, string $rejection_reason, string $reviewed_by, Carbon $reviewed_at)
    {
        $reviews = $this->reviews;
        if ($currentRank == $reviews->count() + 1 && $this->getRawOriginal('status') === static::$PENDING_APPROVAL_STATUS) {
            $this->reviews()->create([
                'number' => $reviews->count() + 1,
                'reviewed_by' => $reviewed_by,
                'reviewed_at' => $reviewed_at,
                'accepted' => false,
            ]);
            $this->status = static::$REJECTED_STATUS_TEXT;
            $this->rejection_reason = $rejection_reason;
            $this->save();
        } else {
            throw new InvalidStatusException();
        }
    }

    /**
     * Auto Replace Status Attribute Based on Original Status + Number of Reviews
     */
    public function getStatusAttribute($value)
    {
        if (static::$SUPPORT_EXTERNAL_MESSAGES && request()->auth_p_id != 1) {
            if ($value === static::$PENDING_APPROVAL_STATUS && static::$PENDING_APPROVAL_STATUS_TEXT) {
                return static::$PENDING_APPROVAL_STATUS_TEXT_EXTERNAL;
            } elseif ($value === static::$REJECTED_STATUS && static::$REJECTED_STATUS_TEXT) {
                return static::$REJECTED_STATUS_TEXT_EXTERNAL;
            }
        }
        if ($value === static::$PENDING_APPROVAL_STATUS && static::$PENDING_APPROVAL_STATUS_TEXT) {
            return str_replace('{rank}', $this->reviews->count() + 1, static::$PENDING_APPROVAL_STATUS_TEXT);
        } else if ($value === static::$REJECTED_STATUS && static::$REJECTED_STATUS_TEXT) {
            return str_replace('{rank}', $this->reviews->count(), static::$REJECTED_STATUS_TEXT);
        }
        return parent::getStatusAttribute($value);
    }


    /**
     * Auto Replace Status Attribute Based on Original Status + Number of Reviews
     */
    public function getNextApprovalRankAttribute()
    {
        if ($this->getRawOriginal('status') === static::$PENDING_APPROVAL_STATUS) {
            return $this->reviews->count() + 1;
        }

        return null;
    }

    /**
     * Return Shadow Status Value for Approval given rank
     */
    public static function getApprovalStatusByRank(int $rank): string
    {
        return str_replace('{rank}', $rank, static::$PENDING_APPROVAL_STATUS_TEXT);
    }

    /**
     * Return Shadow Status Value for Rejected given rank
     */
    public static function getRejectedStatusByRank(int $rank): string
    {
        return str_replace('{rank}', $rank, static::$REJECTED_STATUS_TEXT);
    }
}
