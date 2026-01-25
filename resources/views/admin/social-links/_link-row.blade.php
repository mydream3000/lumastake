<tr>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-2">
            @if($link->platform === 'Instagram')
                <i class="fab fa-instagram text-pink-600 text-xl"></i>
            @elseif($link->platform === 'Facebook')
                <i class="fab fa-facebook text-blue-600 text-xl"></i>
            @elseif($link->platform === 'Twitter')
                <i class="fab fa-twitter text-blue-400 text-xl"></i>
            @elseif($link->platform === 'TikTok')
                <i class="fab fa-tiktok text-gray-900 text-xl"></i>
            @elseif($link->platform === 'YouTube')
                <i class="fab fa-youtube text-red-600 text-xl"></i>
            @elseif($link->platform === 'Telegram')
                <i class="fab fa-telegram text-blue-500 text-xl"></i>
            @endif
            <span class="text-sm font-medium text-gray-900">{{ $link->platform }}</span>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">
        @if($link->url)
            <a href="{{ $link->url }}" target="_blank" class="text-blue-600 hover:underline truncate block max-w-md">
                {{ $link->url }}
            </a>
        @else
            <span class="text-gray-400">Не указан</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($link->is_active)
            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
        @else
            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
        <button @click="showEditModal = true; editingLink = {{ $link->id }}"
                class="text-blue-600 hover:text-blue-900">
            Редактировать
        </button>
        <form method="POST" action="{{ route('admin.social-links.toggle-status', $link) }}" class="inline">
            @csrf
            <button type="submit" class="text-cabinet-orange hover:text-cabinet-orange/80">
                {{ $link->is_active ? 'Деактивировать' : 'Активировать' }}
            </button>
        </form>
    </td>
</tr>

<!-- Edit Modal for this link -->
<template x-teleport="body">
    <div x-show="showEditModal && editingLink === {{ $link->id }}"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div @click.away="showEditModal = false"
             class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Редактировать {{ $link->platform }}</h3>
            <form method="POST" action="{{ route('admin.social-links.update', $link) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                        <input type="url" name="url" value="{{ $link->url }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                               placeholder="https://{{ strtolower($link->platform) }}.com/yourprofile">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Отмена
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90">
                            Сохранить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
