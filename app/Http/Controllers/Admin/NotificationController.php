<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de pagination
        $perPage = $request->input('per_page', 10);
        $validPerPage = in_array($perPage, [10, 25, 50, 100, 500, 1000]) ? $perPage : 10;
        
        $admin = Auth::guard('admin')->user();
        
        // Filtrer les notifications en fonction du rôle de l'utilisateur
        $query = Notification::query();
        
        if (!$admin->is_super_admin) {
            // Les admins normaux ne voient que les notifications qui leur sont destinées
            $query->where(function($q) use ($admin) {
                $q->where('target_type', 'all')
                  ->orWhere('target_type', 'admins')
                  ->orWhere(function($subq) use ($admin) {
                      $subq->where('target_type', 'specific')
                           ->whereJsonContains('target_ids', $admin->id);
                  });
            });
        }
        
        // Appliquer le filtre si spécifié
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            if ($filter === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($filter === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate($validPerPage);
        
        return view('admin.notifications.index', compact('notifications', 'validPerPage'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent créer des notifications.');
        }
        
        $admins = Admin::where('is_super_admin', false)->get();
        $users = User::all();
        
        return view('admin.notifications.create', compact('admins', 'users'));
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent créer des notifications.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,admins,users,specific',
            'target_ids' => 'required_if:target_type,specific|array',
            'importance' => 'nullable|in:normal,high,urgent',
        ]);
        
        $notification = new Notification();
        // Générer un UUID pour l'ID
        $notification->id = \Illuminate\Support\Str::uuid()->toString();
        // Ajouter les champs requis par Laravel
        $notification->type = 'App\\Notifications\\GeneralNotification';
        $notification->notifiable_type = 'App\\Models\\Admin';
        $notification->notifiable_id = Auth::guard('admin')->id();
        // Ajouter les données de la notification
        $notification->data = ['message' => $request->message];
        // Ajouter les champs personnalisés
        $notification->title = $request->title;
        $notification->message = $request->message;
        $notification->sender_id = Auth::guard('admin')->id();
        $notification->sender_type = 'admin';
        $notification->importance = $request->importance ?? 'normal';
        
        // Déterminer les destinataires
        switch ($request->target_type) {
            case 'all':
                $notification->target_type = 'all';
                break;
            case 'admins':
                $notification->target_type = 'admins';
                break;
            case 'users':
                $notification->target_type = 'users';
                break;
            case 'specific':
                $notification->target_type = 'specific';
                $notification->target_ids = $request->target_ids;
                break;
        }
        
        try {
            $notification->save();
            
            // Ajouter un log pour faciliter le débogage
            \Illuminate\Support\Facades\Log::info('Notification créée', [
                'id' => $notification->id,
                'title' => $notification->title,
                'sender_id' => $notification->sender_id,
                'target_type' => $notification->target_type,
                'target_ids' => $notification->target_ids
            ]);
            
            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notification créée et envoyée avec succès.');
        } catch (\Exception $e) {
            // Journaliser l'erreur pour faciliter le débogage
            \Illuminate\Support\Facades\Log::error('Erreur lors de la création de la notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Erreur lors de la création de la notification : ' . $e->getMessage());
        }
    }

    /**
     * Get unread notifications for the current user.
     */
    public function getUnread()
    {
        try {
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                return response()->json([
                    'notifications' => [],
                    'count' => 0,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $query = Notification::query();
            
            // Filtrer par notifications non lues
            $query->whereNull('read_at');
            
            // Récupérer les notifications pour cet admin
            if (!$admin->is_super_admin) {
                // Les admins normaux voient les notifications qui leur sont destinées
                $query->where(function($q) use ($admin) {
                    $q->where('target_type', 'all')
                      ->orWhere('target_type', 'admins')
                      ->orWhere(function($subq) use ($admin) {
                          $subq->where('target_type', 'specific')
                               ->whereJsonContains('target_ids', (string)$admin->id);
                      });
                });
            }
            
            $notifications = $query->orderBy('created_at', 'desc')
                                  ->limit(10) // Limiter le nombre de notifications retournées
                                  ->get();
            
            // Formater les notifications pour l'affichage
            $formattedNotifications = $notifications->map(function($notification) {
                $data = $notification->data;
                if (is_string($data)) {
                    try {
                        $data = json_decode($data, true) ?: ['message' => $notification->message];
                    } catch (\Exception $e) {
                        $data = ['message' => $notification->message];
                    }
                } elseif (empty($data)) {
                    $data = ['message' => $notification->message];
                }
                
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $data,
                    'url' => $this->getNotificationUrl($notification),
                    'importance' => $notification->importance ?? 'normal'
                ];
            });
            
            return response()->json([
                'notifications' => $formattedNotifications,
                'count' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la récupération des notifications non lues', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'notifications' => [],
                'count' => 0,
                'error' => 'Erreur lors de la récupération des notifications'
            ], 500);
        }
    }

    /**
     * Générer l'URL appropriée pour une notification en fonction de son type
     */
    protected function getNotificationUrl($notification)
    {
        // URL par défaut
        $url = route('admin.notifications.index');
        
        // Déterminer l'URL en fonction des données de la notification
        if (isset($notification->data['ticket_id'])) {
            $url = route('admin.tickets.show', $notification->data['ticket_id']);
        } elseif (isset($notification->data['invoice_id'])) {
            $url = route('admin.invoices.show', $notification->data['invoice_id']);
        } elseif (isset($notification->data['serial_key'])) {
            // Rechercher la clé de série
            $serialKey = \App\Models\SerialKey::where('key', $notification->data['serial_key'])->first();
            if ($serialKey) {
                $url = route('admin.serial-keys.show', $serialKey->id);
            }
        }
        
        return $url;
    }
    
    /**
     * Version publique de getUnread qui ne nécessite pas d'authentification
     */
    public function getUnreadPublic()
    {
        try {
            // Utiliser l'ID de l'utilisateur depuis la session si disponible
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                return response()->json([
                    'notifications' => [],
                    'count' => 0
                ]);
            }
            
            $query = Notification::query();
            
            // Filtrer par notifications non lues
            $query->whereNull('read_at');
            
            // Récupérer les notifications pour cet admin
            if (!$admin->is_super_admin) {
                // Les admins normaux voient les notifications qui leur sont destinées
                $query->where(function($q) use ($admin) {
                    $q->where('target_type', 'all')
                      ->orWhere('target_type', 'admins')
                      ->orWhere(function($subq) use ($admin) {
                          $subq->where('target_type', 'specific')
                               ->whereJsonContains('target_ids', (string)$admin->id);
                      });
                });
            }
            
            $notifications = $query->orderBy('created_at', 'desc')
                                  ->limit(10)
                                  ->get();
            
            // Formater les notifications pour l'affichage
            $formattedNotifications = $notifications->map(function($notification) {
                $data = $notification->data;
                if (is_string($data)) {
                    try {
                        $data = json_decode($data, true) ?: ['message' => $notification->message];
                    } catch (\Exception $e) {
                        $data = ['message' => $notification->message];
                    }
                } elseif (empty($data)) {
                    $data = ['message' => $notification->message];
                }
                
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $data,
                    'url' => $this->getNotificationUrl($notification),
                    'importance' => $notification->importance ?? 'normal'
                ];
            });
            
            return response()->json([
                'notifications' => $formattedNotifications,
                'count' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la récupération des notifications non lues (public)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'notifications' => [],
                'count' => 0,
                'error' => 'Erreur lors de la récupération des notifications'
            ]);
        }
    }
    
    /**
     * Version publique de markAsRead qui ne nécessite pas d'authentification
     */
    public function markAsReadPublic($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
            }
            
            // Marquer comme lue si ce n'est pas déjà fait
            if (!$notification->read_at) {
                $notification->read_at = now();
                $notification->save();
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du marquage d\'une notification comme lue (public)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $id
            ]);
            
            return response()->json(['success' => false, 'message' => 'Erreur lors du marquage de la notification']);
        }
    }
    
    /**
     * Version publique de markAllAsRead qui ne nécessite pas d'authentification
     */
    public function markAllAsReadPublic()
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
            }
            
            $now = now();
            
            if ($admin->is_super_admin) {
                // Les superadmins peuvent marquer toutes les notifications comme lues
                Notification::whereNull('read_at')->update(['read_at' => $now]);
            } else {
                // Les admins normaux ne peuvent marquer que leurs notifications comme lues
                Notification::whereNull('read_at')
                    ->where(function($query) use ($admin) {
                        $query->where('target_type', 'all')
                            ->orWhere('target_type', 'admins')
                            ->orWhere(function($q) use ($admin) {
                                $q->where('target_type', 'specific')
                                    ->whereJsonContains('target_ids', (string)$admin->id);
                            });
                    })
                    ->update(['read_at' => $now]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du marquage de toutes les notifications comme lues (public)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Erreur lors du marquage des notifications']);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Vérifier que l'utilisateur a le droit de marquer cette notification comme lue
        $admin = Auth::guard('admin')->user();
        if (!$admin->is_super_admin) {
            // Vérifier que la notification est destinée à cet admin
            $canAccess = false;
            if ($notification->target_type === 'all' || $notification->target_type === 'admins') {
                $canAccess = true;
            } elseif ($notification->target_type === 'specific') {
                $targetIds = $notification->target_ids ?: [];
                $canAccess = in_array($admin->id, $targetIds);
            }
            
            if (!$canAccess) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }
        }
        
        // Marquer comme lue si ce n'est pas déjà fait
        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Méthode de débogage pour les notifications
     */
    public function debug()
    {
        return response()->json([
            'success' => true,
            'message' => 'API de notifications fonctionnelle',
            'user' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->email : 'Non authentifié',
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $admin = Auth::guard('admin')->user();
        $now = now();
        
        if ($admin->is_super_admin) {
            // Les superadmins peuvent marquer toutes les notifications comme lues
            Notification::whereNull('read_at')->update(['read_at' => $now]);
        } else {
            // Les admins normaux ne peuvent marquer que leurs notifications comme lues
            Notification::whereNull('read_at')
                ->where(function($query) use ($admin) {
                    $query->where('target_type', 'all')
                        ->orWhere('target_type', 'admins')
                        ->orWhere(function($q) use ($admin) {
                            $q->where('target_type', 'specific')
                                ->whereJsonContains('target_ids', (string)$admin->id);
                    });
                })
                ->update(['read_at' => $now]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent supprimer des notifications.');
        }
        
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->notification_preferences = $request->preferences;
        $admin->save();
        
        return response()->json(['success' => true]);
    }
}
