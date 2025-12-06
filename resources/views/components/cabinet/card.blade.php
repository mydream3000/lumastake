<div class="bg-white overflow-hidden shadow-sm rounded-lg lg:sm:rounded-lg border border-gray-06">
    @if (isset($header))
    <div class="p-4 lg:p-6 border-b border-gray-06 text-sm lg:text-base font-semibold">
        {{ $header }}
    </div>
    @endif
    <div class="p-4 lg:p-6">
        {{ $slot }}
    </div>
</div>
