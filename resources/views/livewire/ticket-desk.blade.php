<div class="max-w-3xl mx-auto p-6" dir="rtl">
    <h1 class="text-2xl font-semibold mb-4">نظام تصنيف تذاكر الدعم الفني</h1>

    <form wire:submit="submitTicket" class="mb-6">
        <textarea
            wire:model="rawText"
            rows="3"
            placeholder="اكتبي نص شكوى أو طلب العميل هنا..."
            class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
        ></textarea>

        @error('rawText')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submitTicket"
            class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="submitTicket">صنّفي التذكرة</span>
            <span wire:loading wire:target="submitTicket">جاري التصنيف وتوليد الرد...</span>
        </button>
    </form>

    @if ($errorMessage)
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 mb-4 text-sm">
            {{ $errorMessage }}
        </div>
    @endif

    <ul class="space-y-3">
        @forelse ($tickets as $ticket)
            <li class="border rounded-lg p-4 {{ $ticket->status === 'resolved' ? 'opacity-50' : '' }}">
                <div class="flex flex-wrap gap-2 mb-2 text-xs">
                    <span class="px-2 py-0.5 rounded-full
                        @class([
                            'bg-red-100 text-red-700' => $ticket->priority === 'urgent',
                            'bg-orange-100 text-orange-700' => $ticket->priority === 'high',
                            'bg-yellow-100 text-yellow-700' => $ticket->priority === 'medium',
                            'bg-gray-100 text-gray-600' => $ticket->priority === 'low',
                        ])">
                        {{ $ticket->priority }}
                    </span>

                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                        {{ $ticket->category }}
                    </span>

                    @if ($ticket->needsHumanReview())
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                            يحتاج مراجعة بشرية (ثقة منخفضة)
                        </span>
                    @endif
                </div>

                <p class="text-sm">{{ $ticket->raw_text }}</p>

                @if ($ticket->suggested_reply)
                    <div class="mt-3 bg-gray-50 border-r-4 border-blue-300 p-3 text-sm">
                        <span class="text-xs text-gray-500 block mb-1">رد مقترح:</span>
                        {{ $ticket->suggested_reply }}
                    </div>
                @endif

                @if ($ticket->status !== 'resolved')
                    <button
                        wire:click="markResolved({{ $ticket->id }})"
                        class="mt-2 text-xs text-green-700 underline"
                    >
                        تم الحل
                    </button>
                @endif
            </li>
        @empty
            <li class="text-gray-400 text-center py-6">لا توجد تذاكر بعد.</li>
        @endforelse
    </ul>
</div>
