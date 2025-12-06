@extends('layouts.admin')

@section('title', 'Support Team Management')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'emails', showEmailModal: false, showBotModal: false, editingEmail: null, editingBot: null }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Support Team</h1>
            <p class="text-gray-600 mt-1">Управление email-адресами и Telegram ботами для контактной формы</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button
                @click="activeTab = 'emails'"
                :class="activeTab === 'emails' ? 'border-cabinet-orange text-cabinet-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Email Addresses
            </button>
            <button
                @click="activeTab = 'bots'"
                :class="activeTab === 'bots' ? 'border-cabinet-orange text-cabinet-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Telegram Bots
            </button>
            <button
                @click="activeTab = 'contacts'"
                :class="activeTab === 'contacts' ? 'border-cabinet-orange text-cabinet-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Contact Information
            </button>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Email Addresses Tab -->
    <div x-show="activeTab === 'emails'" class="space-y-4">
        <!-- Add Email Button -->
        <div>
            <button @click="showEmailModal = true; editingEmail = null"
                    class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Добавить Email
            </button>
        </div>

        <!-- Emails List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($supportEmails as $email)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $email->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $email->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($email->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <form method="POST" action="{{ route('admin.support-team.emails.toggle-status', $email) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                        {{ $email->is_active ? 'Деактивировать' : 'Активировать' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.support-team.emails.destroy', $email) }}" class="inline"
                                      onsubmit="return confirm('Удалить этот email-адрес?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Нет email-адресов. Добавьте первый!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Telegram Bots Tab -->
    <div x-show="activeTab === 'bots'" class="space-y-4" style="display: none;">
        <!-- Add Bot Button -->
        <div>
            <button @click="showBotModal = true; editingBot = null"
                    class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Добавить Telegram Бота
            </button>
        </div>

        <!-- Bots List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chat ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($telegramBots as $bot)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bot->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">{{ $bot->chat_id ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bot->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <form method="POST" action="{{ route('admin.support-team.bots.test', $bot) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Тест</button>
                                </form>
                                <form method="POST" action="{{ route('admin.support-team.bots.toggle-status', $bot) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                        {{ $bot->is_active ? 'Деактивировать' : 'Активировать' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.support-team.bots.destroy', $bot) }}" class="inline"
                                      onsubmit="return confirm('Удалить этого бота?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Нет Telegram ботов. Добавьте первого!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contact Information Tab -->
    <div x-show="activeTab === 'contacts'" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Public Contact Information</h3>
            <form method="POST" action="{{ route('admin.support-team.contact-info.update') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                            Email
                        </label>
                        <input type="email" name="email" value="{{ $contactInfo->email ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                               placeholder="support@lumastake.com">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>
                            Phone
                        </label>
                        <input type="text" name="phone" value="{{ $contactInfo->phone ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                               placeholder="+1 234 567 890">
                    </div>

                    <!-- Telegram -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-telegram text-gray-400 mr-2"></i>
                            Telegram
                        </label>
                        <input type="text" name="telegram" value="{{ $contactInfo->telegram ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                               placeholder="@lumastake_support">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            Address
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent"
                                  placeholder="123 Main St, City, Country">{{ $contactInfo->address ?? '' }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Сохранить изменения
                    </button>
                </div>
            </form>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">Информация</h4>
                        <p class="text-sm text-blue-800">
                            Эти контактные данные будут отображаться на публичной странице "Contact Us" и в подвале сайта.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div x-show="showEmailModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div @click.away="showEmailModal = false"
             class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Добавить Email Адрес</h3>
            <form method="POST" action="{{ route('admin.support-team.emails.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
                        <input type="text" name="name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked id="email_is_active"
                               class="rounded text-cabinet-orange focus:ring-cabinet-orange">
                        <label for="email_is_active" class="ml-2 text-sm text-gray-700">Активен</label>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showEmailModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Отмена
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90">
                            Добавить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bot Modal -->
    <div x-show="showBotModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div @click.away="showBotModal = false"
             class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Добавить Telegram Бота</h3>
            <form method="POST" action="{{ route('admin.support-team.bots.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bot Token *</label>
                        <input type="text" name="bot_token" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chat ID *</label>
                        <input type="text" name="chat_id" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cabinet-orange focus:border-transparent font-mono text-sm">
                        <p class="text-xs text-gray-500 mt-1">ID группы или чата, куда будут отправляться сообщения</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" id="bot_is_active"
                               class="rounded text-cabinet-orange focus:ring-cabinet-orange">
                        <label for="bot_is_active" class="ml-2 text-sm text-gray-700">Активен</label>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showBotModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Отмена
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90">
                            Добавить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
