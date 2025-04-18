<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Notification extends Model
{
    use HasFactory;
    
    // Constantes pour les types de cible
    const TARGET_ALL = 'all';
    const TARGET_ADMINS = 'admins';
    const TARGET_USERS = 'users';
    const TARGET_SPECIFIC = 'specific';
    
    // Constantes pour les types d'expéditeur
    const SENDER_ADMIN = 'admin';
    const SENDER_SYSTEM = 'system';

    protected $fillable = [
        'title',
        'message',
        'sender_id',
        'sender_type',
        'target_type',
        'target_ids',
        'read',
        'read_by',
        'read_at',
        'importance',
        'data',
    ];
    
    protected $casts = [
        'target_ids' => 'array',
        'read' => 'boolean',
        'read_by' => 'array',
        'data' => 'array',
    ];
    
    protected $dates = [
        'read_at',
        'created_at',
        'updated_at',
    ];
    
    // Cette méthode a été supprimée pour éviter la duplication
    
    /**
     * Marque la notification comme lue par un utilisateur spécifique
     */
    public function markAsReadBy($userId)
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->read_by = $readBy;
            $this->save();
        }
        
        return $this;
    }
    
    /**
     * Vérifie si la notification a été lue par un utilisateur spécifique
     */
    public function isReadBy($userId)
    {
        $readBy = $this->read_by ?? [];
        return in_array($userId, $readBy);
    }
    
    /**
     * Relation avec l'expéditeur (admin)
     */
    public function sender()
    {
        if ($this->sender_type === self::SENDER_ADMIN) {
            return $this->belongsTo(Admin::class, 'sender_id');
        }
        
        return null;
    }
    
    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
    
    /**
     * Check if the notification is for a specific user.
     */
    public function isForUser($userId)
    {
        if ($this->target_type === 'all' || $this->target_type === 'users') {
            return true;
        }
        
        if ($this->target_type === 'specific' && is_array($this->target_ids)) {
            return in_array($userId, $this->target_ids);
        }
        
        return false;
    }
    
    /**
     * Check if the notification is for a specific admin.
     */
    public function isForAdmin($adminId, $isSuperAdmin = false)
    {
        if ($this->target_type === 'all' || $this->target_type === 'admins') {
            return true;
        }
        
        if ($isSuperAdmin) {
            return true; // Super admins see all notifications
        }
        
        if ($this->target_type === 'specific' && is_array($this->target_ids)) {
            return in_array($adminId, $this->target_ids);
        }
        
        return false;
    }
}
