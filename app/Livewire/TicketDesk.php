<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Ticket;
use App\Services\ReplySuggestionService;
use App\Services\TicketClassifierService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TicketDesk extends Component
{
    #[Validate('required|string|min:10|max:2000')]
    public string $rawText = '';

    public bool $isProcessing = false;

    public ?string $errorMessage = null;

    public function submitTicket(
        TicketClassifierService $classifier,
        ReplySuggestionService $replier
    ): void {
        $this->validate();
        $this->errorMessage = null;
        $this->isProcessing = true;

        try {
            $classification = $classifier->classify($this->rawText);
            $suggestedReply = $replier->suggest(
                $this->rawText,
                $classification['category'],
                $classification['priority']
            );

            Ticket::create([
                'user_id' => Auth::id(),
                'raw_text' => $this->rawText,
                'category' => $classification['category'],
                'priority' => $classification['priority'],
                'category_confidence' => $classification['category_confidence'],
                'priority_confidence' => $classification['priority_confidence'],
                'suggested_reply' => $suggestedReply,
            ]);

            $this->reset('rawText');
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function markResolved(int $ticketId): void
    {
        Ticket::findOrFail($ticketId)->update(['status' => 'resolved']);
    }

    public function render()
    {
        $tickets = Ticket::urgentFirst()->latest()->get();

        return view('livewire.ticket-desk', ['tickets' => $tickets]);
    }
}
