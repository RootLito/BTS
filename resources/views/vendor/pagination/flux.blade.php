@if ($paginator->hasPages())
    <div class="flex items-center justify-between py-3">
        {{-- Left Side: Status Text --}}
        <div class="text-sm text-zinc-500">
            Showing page <b>{{ $paginator->currentPage() }}</b> of <b>{{ $paginator->lastPage() }}</b>
            <span class="ml-1 text-zinc-400">({{ $paginator->total() }} total)</span>
        </div>

        {{-- Right Side: Pagination Buttons --}}
        <div class="flex items-center gap-1">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <flux:button variant="ghost" icon="chevron-left" size="sm" class="!px-2" disabled />
            @else
                <flux:button variant="ghost" icon="chevron-left" size="sm" class="!px-2" :href="$paginator->previousPageUrl()" />
            @endif

            {{-- Sliding Window Logic --}}
            @php
                $start = max($paginator->currentPage() - 2, 1);
                $end = min($start + 4, $paginator->lastPage());
                if ($end - $start < 4) {
                    $start = max($end - 4, 1);
                }
            @endphp

            @for ($i = $start; $i <= $end; $i++)
                <flux:button 
                    variant="{{ $i == $paginator->currentPage() ? 'filled' : 'ghost' }}" 
                    size="sm"
                    :href="$paginator->url($i)"
                    color="{{ $i == $paginator->currentPage() ? 'emerald' : '' }}"
                    class="{{ $i == $paginator->currentPage() ? 'pointer-events-none' : '' }} min-w-[32px]"
                >
                    {{ $i }}
                </flux:button>
            @endfor

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <flux:button variant="ghost" icon="chevron-right" size="sm" class="!px-2" :href="$paginator->nextPageUrl()" />
            @else
                <flux:button variant="ghost" icon="chevron-right" size="sm" class="!px-2" disabled />
            @endif
        </div>
    </div>
@endif