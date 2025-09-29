<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    /**
     * Prevent a customer from creating multiple bookings for the same ticket.
     *
     * Usage on route: ->middleware(['auth:sanctum','role:customer','prevent.double.booking'])
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Retrieve ticket id from route parameter: ticket or id
        $ticket = $request->route('ticket') ?? $request->route('id') ?? null;

        // If route-model binding provided a Ticket model, use it; otherwise try to find by id
        if ($ticket instanceof Ticket) {
            $ticketId = $ticket->id;
        } else {
            $ticketId = (int)($ticket ?? 0);
        }

        if (!$ticketId) {
            // No ticket id in route — let controller/service handle validation
            return $next($request);
        }

        // Check existing bookings by this user for this ticket in pending/confirmed states
        $existing = \App\Models\Booking::where('user_id', $user->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a booking for this ticket (pending or confirmed).',
            ], 409); // 409 Conflict
        }

        return $next($request);
    }
}
