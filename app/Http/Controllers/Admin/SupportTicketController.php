<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = SupportTicket::with(['client', 'replies'])
            ->orderBy('created_at', 'desc');
            
        // Filter by status if provided
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $tickets = $query->paginate(15);
        
        return view('admin.tickets.index', compact('tickets', 'status'));
    }
    
    /**
     * Display the specified ticket.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['client', 'replies.user']);
        
        return view('admin.tickets.show', compact('ticket'));
    }
    
    /**
     * Update the status of the ticket.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting,closed',
        ]);
        
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        
        // If the ticket is being closed, set the closed_at and closed_by
        if ($request->status === SupportTicket::STATUS_CLOSED && !$ticket->isClosed()) {
            $ticket->closed_at = now();
            $ticket->closed_by_id = Auth::id();
            $ticket->closed_by_type = 'admin';
        }
        
        $ticket->save();
        
        // Add a system reply about the status change
        if ($oldStatus !== $request->status) {
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => "Ticket status changed from {$oldStatus} to {$request->status} by admin.",
            ]);
        }
        
        return redirect()->route('admin.tickets.show', $ticket)
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
            'user_type' => TicketReply::USER_TYPE_ADMIN,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);
        
        // Update the ticket's last reply timestamp
        $ticket->last_reply_at = now();
        
        // If the ticket was closed, reopen it
        if ($ticket->isClosed()) {
            $ticket->status = SupportTicket::STATUS_IN_PROGRESS;
            
            // Add a system reply about reopening
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => 'Ticket reopened due to new admin reply.',
            ]);
        }
        
        $ticket->save();
        
        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Reply added successfully.');
    }
    
    /**
     * Forward a ticket to super admin.
     */
    public function forwardToSuperAdmin(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        // Create a system reply indicating the ticket was forwarded
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_SYSTEM,
            'user_id' => null,
            'message' => 'Ticket forwarded to Super Admin for review.',
        ]);
        
        // Add the admin's message as a reply
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_ADMIN,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        // Update the ticket to mark it as forwarded to super admin
        // We'll use a custom field or status for this
        $ticket->status = 'forwarded_to_super_admin'; // You might want to add this status to the model constants
        $ticket->last_reply_at = now();
        $ticket->save();
        
        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket forwarded to Super Admin successfully.');
    }
}