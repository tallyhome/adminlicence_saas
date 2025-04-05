<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminTicketController extends Controller
{
    /**
     * Constructor to ensure only super admins can access these methods
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::guard('admin')->user()->is_super_admin) {
                abort(403, 'Unauthorized action. Super Admin access required.');
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the tickets forwarded to super admin.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = SupportTicket::with(['client', 'replies'])
            ->where('status', 'forwarded_to_super_admin')
            ->orderBy('created_at', 'desc');
            
        // Additional filters if needed
        if ($status !== 'all' && $status !== 'forwarded_to_super_admin') {
            $query->where('status', $status);
        }
        
        $tickets = $query->paginate(15);
        
        return view('admin.super.tickets.index', compact('tickets', 'status'));
    }
    
    /**
     * Display the specified ticket.
     */
    public function show(SupportTicket $ticket)
    {
        // Ensure the ticket is forwarded to super admin or allow super admin to see all tickets
        $ticket->load(['client', 'replies.user']);
        
        return view('admin.super.tickets.show', compact('ticket'));
    }
    
    /**
     * Update the status of the ticket.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting,closed,forwarded_to_super_admin,resolved_by_super_admin',
        ]);
        
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        
        // If the ticket is being closed, set the closed_at and closed_by
        if ($request->status === SupportTicket::STATUS_CLOSED && !$ticket->isClosed()) {
            $ticket->closed_at = now();
            $ticket->closed_by_id = Auth::id();
            $ticket->closed_by_type = 'admin'; // Super admin is still an admin type
        }
        
        $ticket->save();
        
        // Add a system reply about the status change
        if ($oldStatus !== $request->status) {
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => "Ticket status changed from {$oldStatus} to {$request->status} by super admin.",
            ]);
        }
        
        return redirect()->route('admin.super.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }
    
    /**
     * Store a reply to the ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);
        
        $attachments = [];
        
        // Handle file uploads if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }
        
        // Create the reply
        $reply = TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_ADMIN, // Super admin is still an admin type
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);
        
        // Update the ticket's last reply timestamp
        $ticket->last_reply_at = now();
        
        // If the ticket was closed, reopen it or change status as needed
        if ($ticket->isClosed()) {
            $ticket->status = 'resolved_by_super_admin'; // Custom status for super admin resolution
            
            // Add a system reply about the status change
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => 'Ticket resolved by super admin.',
            ]);
        }
        
        $ticket->save();
        
        return redirect()->route('admin.super.tickets.show', $ticket)
            ->with('success', 'Reply added successfully.');
    }
    
    /**
     * Return a ticket to regular admin handling.
     */
    public function returnToAdmin(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        // Create a system reply indicating the ticket was returned to admin
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_SYSTEM,
            'user_id' => null,
            'message' => 'Ticket returned to regular admin handling by super admin.',
        ]);
        
        // Add the super admin's message as a reply
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_ADMIN,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        // Update the ticket status
        $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
        $ticket->last_reply_at = now();
        $ticket->save();
        
        return redirect()->route('admin.super.tickets.index')
            ->with('success', 'Ticket returned to regular admin successfully.');
    }
    
    /**
     * Assign the ticket to a specific admin.
     */
    public function assignToAdmin(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'message' => 'nullable|string',
        ]);
        
        $admin = Admin::find($request->admin_id);
        
        // Create a system reply indicating the ticket was assigned
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_SYSTEM,
            'user_id' => null,
            'message' => "Ticket assigned to admin {$admin->name} by super admin.",
        ]);
        
        // Add the super admin's message as a reply if provided
        if ($request->filled('message')) {
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_ADMIN,
                'user_id' => Auth::id(),
                'message' => $request->message,
            ]);
        }
        
        // Here you might want to add a field to track assigned admin
        // For now, we'll just update the status and add it to the system message
        $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
        $ticket->last_reply_at = now();
        $ticket->save();
        
        return redirect()->route('admin.super.tickets.index')
            ->with('success', "Ticket assigned to {$admin->name} successfully.");
    }
}