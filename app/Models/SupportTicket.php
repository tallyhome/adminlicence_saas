<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'attachments',
        'last_reply_at',
        'closed_at',
        'closed_by_id',
        'closed_by_type'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attachments' => 'array',
        'last_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * The ticket statuses.
     */
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING = 'waiting';
    const STATUS_CLOSED = 'closed';

    /**
     * The ticket priorities.
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the client that owns the ticket.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the replies for the ticket.
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * Scope a query to only include open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include in progress tickets.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope a query to only include waiting tickets.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    /**
     * Scope a query to only include closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Check if the ticket is open.
     */
    public function isOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if the ticket is in progress.
     */
    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the ticket is waiting.
     */
    public function isWaiting()
    {
        return $this->status === self::STATUS_WAITING;
    }

    /**
     * Check if the ticket is closed.
     */
    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }
}