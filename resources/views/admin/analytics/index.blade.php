@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Analytics & Bot Settings</h1>
            <p class="text-gray-600 mt-1">Настройки аналитического бота для отслеживания регистраций</p>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Как настроить бота:</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Создайте бота через <a href="https://t.me/BotFather" target="_blank" class="underline">@BotFather</a> и получите токен</li>
                        <li>Создайте группу в Telegram и добавьте туда бота</li>
                        <li>Получите Chat ID группы (используйте <a href="https://t.me/userinfobot" target="_blank" class="underline">@userinfobot</a>)</li>
                        <li>Введите данные ниже и протестируйте подключение</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Настройки Telegram бота</h2>
        <form action="{{ route('admin.analytics.bot-settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Bot Token -->
            <div class="mb-6">
                <label for="bot_token" class="block text-sm font-medium text-gray-700 mb-2">
                    Bot Token <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="bot_token"
                       name="bot_token"
                       value="{{ old('bot_token', $botSettings->bot_token ?? '') }}"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange font-mono text-sm"
                       placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                <p class="mt-1 text-sm text-gray-500">Токен от @BotFather</p>
                @error('bot_token')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Chat ID -->
            <div class="mb-6">
                <label for="chat_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Chat ID <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="chat_id"
                       name="chat_id"
                       value="{{ old('chat_id', $botSettings->chat_id ?? '') }}"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange font-mono text-sm"
                       placeholder="-1003364592831">
                <p class="mt-1 text-sm text-gray-500">ID группы или чата для отправки уведомлений о регистрациях</p>
                @error('chat_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $botSettings->is_active ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="ml-2 text-sm text-gray-700">Бот активен (отправка уведомлений включена)</span>
                </label>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Сохранить настройки
                </button>
            </div>
        </form>
    </div>

    <!-- Test Connection -->
    @if(isset($botSettings) && $botSettings->bot_token && $botSettings->chat_id)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Тестирование подключения</h2>
        <p class="text-sm text-gray-600 mb-4">
            Отправьте тестовое сообщение в группу, чтобы проверить правильность настроек.
        </p>
        <form action="{{ route('admin.analytics.bot-settings.test') }}" method="POST">
            @csrf
            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>
                Отправить тестовое сообщение
            </button>
        </form>
    </div>
    @endif

    <!-- Events Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Какие данные отправляются</h2>
        <p class="text-sm text-gray-600 mb-4">
            При регистрации нового пользователя бот автоматически отправляет следующие данные в группу:
        </p>
        <div class="space-y-3">
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-green-100 text-green-600 mr-3">
                    <i class="fas fa-envelope text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">Email</p>
                    <p class="text-sm text-gray-600">Адрес электронной почты пользователя</p>
                </div>
            </div>
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 mr-3">
                    <i class="fas fa-user text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">Имя</p>
                    <p class="text-sm text-gray-600">Имя пользователя (если указано)</p>
                </div>
            </div>
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 text-orange-600 mr-3">
                    <i class="fas fa-phone text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">Телефон</p>
                    <p class="text-sm text-gray-600">Номер телефона с кодом страны (если указан)</p>
                </div>
            </div>
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mr-3">
                    <i class="fas fa-calendar text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">Дата регистрации</p>
                    <p class="text-sm text-gray-600">Время и дата когда пользователь зарегистрировался</p>
                </div>
            </div>
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-red-100 text-red-600 mr-3">
                    <i class="fas fa-network-wired text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">IP адрес</p>
                    <p class="text-sm text-gray-600">IP адрес пользователя при регистрации</p>
                </div>
            </div>
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 mr-3">
                    <i class="fab fa-google text-sm"></i>
                </span>
                <div>
                    <p class="font-medium text-gray-900">Тип регистрации</p>
                    <p class="text-sm text-gray-600">Обычная регистрация или через Google OAuth</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
