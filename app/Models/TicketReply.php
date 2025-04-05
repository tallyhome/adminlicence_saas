<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_type',
        'user_id',
        'message',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * The possible user types for a ticket reply.
     */
    const USER_TYPE_CLIENT = 'client';
    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_SYSTEM = 'system';

    /**
     * Get the support ticket that owns the reply.
     */
    public function supportTicket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get the user who created the reply.
     * This is a polymorphic relationship.
     */
    public function user()
    {
        if ($this->user_type === self::USER_TYPE_CLIENT) {
            return Client::find($this->user_id);
        } elseif ($this->user_type === self::USER_TYPE_ADMIN) {
            return Admin::find($this->user_id);
        }
        
        return null;
    }

    /**
     * Scope a query to only include client replies.
     */
    public function scopeFromClient($query)
    {
        return $query->where('user_type', self::USER_TYPE_CLIENT);
    }

    /**
     * Scope a query to only include admin replies.
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('user_type', self::USER_TYPE_ADMIN);
    }

    /**
     * Scope a query to only include system replies.
     */
    public function scopeFromSystem($query)
    {
        return $query->where('user_type', self::USER_TYPE_SYSTEM);
    }
}