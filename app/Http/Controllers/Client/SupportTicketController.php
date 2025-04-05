<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the client's tickets.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Auth::user()->supportTickets()
            ->with('replies')
            ->orderBy('created_at', 'desc');
            
        // Filter by status if provided
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $tickets = $query->paginate(10);
        
        return view('client.tickets.index', compact('tickets', 'status'));
    }
    
    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        return view('client.tickets.create');
    }
    
    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'nullable|string|max:100',
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
        
        // Create the ticket
        $ticket = SupportTicket::create([
            'client_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => $request->priority,
            'category' => $request->category,
            'attachments' => !empty($attachments) ? $attachments : null,
            'last_reply_at' => now(),
        ]);
        
        // Create the initial reply (the description)
        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_type' => TicketReply::USER_TYPE_CLIENT,
            'user_id' => Auth::id(),
            'message' => $request->description,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);
        
        return redirect()->route('client.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }
    
    /**
     * Display the specified ticket.
     */
    public function show(SupportTicket $ticket)
    {
        // Ensure the ticket belongs to the authenticated client
        if ($ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $ticket->load('replies');
        
        return view('client.tickets.show', compact('ticket'));
    }
    
    /**
     * Store a reply to the ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        // Ensure the ticket belongs to the authenticated client
        if ($ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
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
            'user_type' => TicketReply::USER_TYPE_CLIENT,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);
        
        // Update the ticket's last reply timestamp
        $ticket->last_reply_at = now();
        
        // If the ticket was closed, reopen it
        if ($ticket->isClosed()) {
            $ticket->status = SupportTicket::STATUS_OPEN;
            
            // Add a system reply about reopening
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => 'Ticket reopened due to new client reply.',
            ]);
        }
        
        $ticket->save();
        
        return redirect()->route('client.tickets.show', $ticket)
            ->with('success', 'Reply added successfully.');
    }
    
    /**
     * Close the ticket.
     */
    public function close(SupportTicket $ticket)
    {
        // Ensure the ticket belongs to the authenticated client
        if ($ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only allow closing if the ticket is not already closed
        if (!$ticket->isClosed()) {
            $ticket->status = SupportTicket::STATUS_CLOSED;
            $ticket->closed_at = now();
            $ticket->closed_by_id = Auth::id();
            $ticket->closed_by_type = 'client';
            $ticket->save();
            
            // Add a system reply about closing
            TicketReply::create([
                'support_ticket_id' => $ticket->id,
                'user_type' => TicketReply::USER_TYPE_SYSTEM,
                'user_id' => null,
                'message' => 'Ticket closed by client.',
            ]);
            
            return redirect()->route('client.tickets.show', $ticket)
                ->with('success', 'Ticket closed successfully.');
        }
        
        return redirect()->route('client.tickets.show', $ticket)
            ->with('info', 'Ticket is already closed.');
    }
}