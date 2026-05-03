<div class="text-center py-5 w-100">
    <i class="las la-inbox" style="font-size: 80px; color: #ccc;"></i>
    <h4 class="mt-3">{{ $title ?? 'No Items found' }}</h4>
    @if ($message)
        <p class="text-muted">{{ $message }}</p>
    @endif
</div>
