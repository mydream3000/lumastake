# CryptocurrencyAPI.net - Документация для работы с USDT

## Содержание

1. [Общая информация](#общая-информация)
2. [Аутентификация](#аутентификация)
3. [Базовые методы API](#базовые-методы-api)
4. [Методы для работы с USDT пополнениями](#методы-для-работы-с-usdt-пополнениями)
5. [Проверка транзакций без IPN (polling)](#проверка-транзакций-без-ipn-polling)
6. [IPN (Instant Payment Notification) / WebHook](#ipn-instant-payment-notification--webhook)
7. [Примеры кода](#примеры-кода)

---

## Общая информация

**Base URL:** `https://new.cryptocurrencyapi.net/api/`

**Поддерживаемые сети:**
- `tron` (TRX) - для USDT TRC20
- `ethereum` (ETH) - для USDT ERC20
- `bsc` (BNB) - для USDT BEP20
- `polygon` (MATIC) - для USDT на Polygon
- bitcoin, litecoin, dogecoin, dash, bitcoincash, ripple, ton, solana

**Формат запросов:** GET или POST (form-urlencoded или JSON)

**Формат ответов:** JSON
```json
// Успех
{"result": "значение"}

// Ошибка
{"error": "код_ошибки", "value": "дополнительная_информация"}
```

**Rate Limit:** максимум 10 запросов за 2 секунды

---

## Аутентификация

### Получение API ключа

1. Зарегистрируйтесь на https://new.cryptocurrencyapi.net/
2. В личном кабинете, в разделе "Безопасность", сгенерируйте API ключ
3. **Обязательно укажите IP-адреса**, с которых разрешены запросы к API

### Способы передачи ключа

**Вариант 1: В параметре запроса**
```
GET https://new.cryptocurrencyapi.net/api/trx/.balance?key=YOUR_API_KEY
```

**Вариант 2: В HTTP заголовке (рекомендуется)**
```bash
curl -H "CCAPI-KEY: YOUR_API_KEY" https://new.cryptocurrencyapi.net/api/trx/.balance
```

---

## Базовые методы API

### Тестирование подключения

**Метод echo** - проверка работоспособности API

```bash
# GET запрос
https://new.cryptocurrencyapi.net/api/echo?a=1&b=2&key=YOUR_API_KEY

# POST запрос
curl -L 'https://new.cryptocurrencyapi.net/api/echo' \
  -H 'Content-Type: application/json' \
  -H 'CCAPI-KEY: YOUR_API_KEY' \
  -d '{"a": 1, "b": 2}'
```

**Ответ:**
```json
{
  "result": {
    "a": "1",
    "b": "2",
    "key": "YOUR_API_KEY"
  },
  "ip": "192.168.0.1"
}
```

---

## Методы для работы с USDT пополнениями

### 1. Генерация адреса для пополнения (метод `give`)

**Описание:** Создает уникальный адрес для приема USDT. Адрес может быть временным или постоянным.

**Endpoint:**
```
GET/POST https://new.cryptocurrencyapi.net/api/{сеть}/.give
```

**Параметры:**

| Параметр | Обязательный | Описание |
|----------|--------------|----------|
| `key` | Да | API ключ |
| `token` | Нет | Токен (для USDT используйте "USDT") |
| `uniqID` | Нет | Уникальный идентификатор пользователя/заказа. При повторном вызове с тем же uniqID вернется ранее выданный адрес |
| `label` | Нет | Метка/комментарий (передается в IPN) |
| `period` | Нет | Время до архивирования в минутах (период ожидания следующего поступления) |
| `waitPeriod` | Нет | Время до первого архивирования в минутах |
| `statusURL` | Нет | URL для IPN уведомлений. Если указать `-`, IPN не будет отправляться |
| `to` | Нет | Адрес для автоматической пересылки поступивших средств |
| `from` | Нет | Адрес, с которого брать комиссию на пересылку |
| `qr` | Нет | Возвращать QR-код (1 - да, 0 - нет) |

**Примеры запросов:**

**USDT TRC20 (Tron):**
```bash
# Простой вариант - генерация адреса
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/tron/.give?token=USDT&uniqID=user123&label=Deposit%20for%20User%20123"

# С периодом ожидания 30 минут
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/trx/.give?token=USDT&uniqID=order456&period=30&label=Order%20456"
```

**USDT ERC20 (Ethereum):**
```bash
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/ethereum/.give?token=USDT&uniqID=user789"
```

**Ответ:**
```json
{
  "result": {
    "address": "TKavpKP2VJbfV4AGyi3MrhT6FJAP5eJ2RR",
    "publicKey": "03b7a403f5ffce0292b8f17328b7f0dd0ca52ac5549292bc6fe1fe580ef40d183f",
    "privateKey": "***"
  }
}
```

**Важно:**
- Если `uniqID` повторяется - вернется ранее созданный адрес (но параметры адреса обновятся)
- Для работы с USDT TRC20 рекомендуется использовать сеть `tron` (или `trx`)
- Для USDT ERC20 - сеть `ethereum` (или `eth`)

### 2. Проверка баланса адреса (метод `balance`)

**Описание:** Получение текущего баланса адреса

**Endpoint:**
```
GET/POST https://new.cryptocurrencyapi.net/api/{сеть}/.balance
```

**Параметры:**

| Параметр | Обязательный | Описание |
|----------|--------------|----------|
| `key` | Да | API ключ |
| `from` или `address` | Нет | Адрес для проверки (если не указан - используется основной адрес) |
| `token` | Нет | Токен (для USDT указывайте "USDT") |

**Стоимость:** Бесплатно для ваших адресов, 1 единица для проверки чужих адресов

**Примеры:**

```bash
# Проверка баланса USDT TRC20
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/trx/.balance?from=TKavpKP2VJbfV4AGyi3MrhT6FJAP5eJ2RR&token=USDT"

# Проверка баланса основного адреса
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/tron/.balance?token=USDT"
```

**Ответ:**
```json
{
  "result": "12.345"
}
```

### 3. Отслеживание входящего платежа (метод `track`)

**Описание:** Создает отслеживание для конкретного адреса и ожидаемой суммы. При поступлении средств отправляет IPN уведомление.

**Endpoint:**
```
GET/POST https://new.cryptocurrencyapi.net/api/{сеть}/.track
```

**Параметры:**

| Параметр | Обязательный | Описание |
|----------|--------------|----------|
| `key` | Да | API ключ |
| `address` | Да | Адрес для отслеживания (может быть любой, даже чужой) |
| `token` | Нет | Токен (для USDT = "USDT") |
| `amount` | Нет | Ожидаемая сумма (если не указана - уведомления по любой сумме) |
| `step` | Нет | Шаг изменения суммы при совпадении |
| `period` | Нет | Период отслеживания в минутах [по умолчанию 60] |
| `statusURL` | Нет | URL для IPN |
| `label` | Да | Метка (передается в IPN) |
| `uniqID` | Нет | Уникальный ID (если повторить - заменит старое отслеживание) |

**Стоимость:** 1 единица

**Пример:**

```bash
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/trx/.track?address=TKavpKP2VJbfV4AGyi3MrhT6FJAP5eJ2RR&token=USDT&amount=100.50&label=order123&period=30"
```

**Ответ:**
```json
{
  "result": {
    "address": "TKavpKP2VJbfV4AGyi3MrhT6FJAP5eJ2RR",
    "amount": "100.499999"
  }
}
```

Сумма может немного отличаться от ожидаемой - это используется для точной идентификации платежа.

---

## Проверка транзакций без IPN (polling)

Если вы не хотите использовать IPN/webhook, можно периодически проверять баланс адреса методом `balance`.

### Алгоритм polling (без IPN)

```php
<?php
// 1. Создаем адрес для пользователя
$apiKey = 'YOUR_API_KEY';
$userId = 123;

$response = file_get_contents(
    "https://new.cryptocurrencyapi.net/api/trx/.give?key={$apiKey}&token=USDT&uniqID=user_{$userId}"
);
$data = json_decode($response, true);
$depositAddress = $data['result']['address'];

// Сохраняем в БД: user_id, deposit_address, initial_balance = 0

// 2. Периодически (каждые 30-60 секунд) проверяем баланс
while (true) {
    $balanceResponse = file_get_contents(
        "https://new.cryptocurrencyapi.net/api/trx/.balance?key={$apiKey}&from={$depositAddress}&token=USDT"
    );
    $balanceData = json_decode($balanceResponse, true);
    $currentBalance = (float)$balanceData['result'];

    // Получаем сохраненный баланс из БД
    $savedBalance = getBalanceFromDB($depositAddress);

    // Если баланс изменился - обрабатываем пополнение
    if ($currentBalance > $savedBalance) {
        $receivedAmount = $currentBalance - $savedBalance;

        // Зачисляем пользователю
        creditUser($userId, $receivedAmount);

        // Обновляем сохраненный баланс
        updateBalanceInDB($depositAddress, $currentBalance);
    }

    sleep(60); // Проверка каждые 60 секунд
}
```

**Недостатки polling:**
- Задержка в обработке (зависит от интервала проверки)
- Дополнительная нагрузка на ваш сервер
- Расход API лимитов при частых проверках

**Рекомендация:** Используйте IPN для мгновенных уведомлений

---

## IPN (Instant Payment Notification) / WebHook

### Что такое IPN

IPN (Instant Payment Notification) - это механизм уведомлений, при котором CryptocurrencyAPI автоматически отправляет POST-запрос на ваш сервер при поступлении средств на отслеживаемый адрес.

### Настройка IPN

**Способ 1: Глобальная настройка в личном кабинете**
1. Войдите в кабинет на https://new.cryptocurrencyapi.net/
2. В настройках укажите URL обработчика IPN
3. Все созданные адреса будут использовать этот URL

**Способ 2: Указание URL при создании адреса**
```bash
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/trx/.give?token=USDT&uniqID=user123&statusURL=https://yourdomain.com/ipn/crypto"
```

**Способ 3: Отключение IPN для конкретного адреса**
```bash
# Используйте statusURL=-
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/trx/.give?token=USDT&uniqID=user123&statusURL=-"
```

### Структура IPN запроса

Когда на отслеживаемый адрес поступают средства, CryptocurrencyAPI отправляет POST-запрос на ваш `statusURL`.

**Параметры запроса:**

| Параметр | Описание |
|----------|----------|
| `network` | Сеть (tron, ethereum, bsc, polygon и т.д.) |
| `token` | Токен (USDT, TRX, ETH и т.д.) или пусто для основной монеты |
| `address` | Адрес получателя |
| `amount` | Сумма поступления |
| `txid` | Hash транзакции |
| `confirmations` | Количество подтверждений |
| `label` | Метка, которую вы указали при создании адреса |
| `uniqID` | Уникальный ID, который вы указали |
| `publicKey` | Публичный ключ адреса |
| `sign` | Подпись для проверки подлинности запроса |

**Пример данных IPN:**
```
network=tron
token=USDT
address=TKavpKP2VJbfV4AGyi3MrhT6FJAP5eJ2RR
amount=100.50
txid=5f8a9b7c6d5e4f3a2b1c0d9e8f7a6b5c4d3e2f1a0b9c8d7e6f5a4b3c2d1e0f9a8b7
confirmations=19
label=user_deposit_123
uniqID=user123
publicKey=03b7a403f5ffce0292b8f17328b7f0dd0ca52ac5549292bc6fe1fe580ef40d183f
sign=304502210098abc...
```

### Проверка подлинности IPN

**ВАЖНО:** Всегда проверяйте подпись `sign` для защиты от подделки запросов!

**Алгоритм проверки:**

1. Получите все параметры, кроме `sign`
2. Отсортируйте их по ключам в алфавитном порядке
3. Сформируйте строку: `key1=value1&key2=value2&...`
4. Добавьте в начало ваш API ключ: `YOUR_API_KEY&key1=value1&key2=value2&...`
5. Вычислите SHA-256 хеш этой строки
6. Сравните с присланным `sign`

**Пример проверки на PHP:**

```php
<?php
function verifyIPNSignature($data, $apiKey) {
    // Извлекаем подпись
    $receivedSign = $data['sign'] ?? '';
    unset($data['sign']);

    // Сортируем параметры по ключу
    ksort($data);

    // Формируем строку для хеширования
    $params = [];
    foreach ($data as $key => $value) {
        if ($value !== '') {
            $params[] = $key . '=' . $value;
        }
    }
    $string = $apiKey . '&' . implode('&', $params);

    // Вычисляем хеш
    $calculatedSign = hash('sha256', $string);

    // Сравниваем
    return $calculatedSign === $receivedSign;
}

// Использование
$apiKey = 'YOUR_API_KEY';
$ipnData = $_POST; // или $_GET

if (verifyIPNSignature($ipnData, $apiKey)) {
    // Подпись верна - обрабатываем
    processDeposit($ipnData);
    echo 'OK';
} else {
    // Подпись неверна - игнорируем
    http_response_code(403);
    echo 'Invalid signature';
}
```

### Ответ обработчика IPN

Ваш обработчик ДОЛЖЕН вернуть:
- HTTP статус 200
- Тело ответа: `OK` (точно эта строка)

Если API не получит корректный ответ, он повторит отправку IPN несколько раз.

---

## Примеры кода

### Полный пример: Прием USDT TRC20 с IPN

#### 1. Создание адреса для пользователя

```php
<?php
// deposit.php - Создание адреса для пополнения

function generateDepositAddress($userId) {
    $apiKey = 'YOUR_API_KEY';
    $network = 'tron';
    $token = 'USDT';

    $url = "https://new.cryptocurrencyapi.net/api/{$network}/.give?" . http_build_query([
        'key' => $apiKey,
        'token' => $token,
        'uniqID' => "user_{$userId}",
        'label' => "Deposit for User {$userId}",
        'period' => 1440, // 24 часа
        'statusURL' => 'https://yourdomain.com/ipn/crypto-handler.php'
    ]);

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['result']['address'])) {
        $address = $data['result']['address'];

        // Сохраняем в БД
        saveDepositAddress($userId, $address, 0);

        return $address;
    }

    throw new Exception($data['error'] ?? 'Unknown error');
}

function saveDepositAddress($userId, $address, $initialBalance) {
    // Ваш код сохранения в БД
    // CREATE TABLE deposit_addresses (
    //     user_id INT,
    //     address VARCHAR(255),
    //     balance DECIMAL(18,8),
    //     created_at TIMESTAMP
    // );
}
```

#### 2. IPN обработчик

```php
<?php
// ipn/crypto-handler.php - Обработчик IPN уведомлений

$apiKey = 'YOUR_API_KEY';

// Функция проверки подписи
function verifySign($data, $apiKey) {
    $sign = $data['sign'] ?? '';
    unset($data['sign']);

    ksort($data);

    $params = [];
    foreach ($data as $key => $value) {
        if ($value !== '') {
            $params[] = $key . '=' . $value;
        }
    }

    $string = $apiKey . '&' . implode('&', $params);
    $calculatedSign = hash('sha256', $string);

    return $calculatedSign === $sign;
}

// Получаем данные
$ipnData = $_POST;

// Логируем для отладки
file_put_contents('ipn_log.txt', date('Y-m-d H:i:s') . ' - ' . json_encode($ipnData) . PHP_EOL, FILE_APPEND);

// Проверяем подпись
if (!verifySign($ipnData, $apiKey)) {
    http_response_code(403);
    die('Invalid signature');
}

// Извлекаем данные
$network = $ipnData['network'] ?? '';
$token = $ipnData['token'] ?? '';
$address = $ipnData['address'] ?? '';
$amount = (float)($ipnData['amount'] ?? 0);
$txid = $ipnData['txid'] ?? '';
$confirmations = (int)($ipnData['confirmations'] ?? 0);
$label = $ipnData['label'] ?? '';
$uniqID = $ipnData['uniqID'] ?? '';

// Проверяем, что это USDT на Tron
if ($network !== 'tron' || $token !== 'USDT') {
    echo 'OK'; // Все равно отвечаем OK
    exit;
}

// Минимальное количество подтверждений
if ($confirmations < 19) {
    echo 'OK'; // Ждем больше подтверждений
    exit;
}

// Извлекаем user_id из uniqID
if (preg_match('/^user_(\d+)$/', $uniqID, $matches)) {
    $userId = (int)$matches[1];

    // Проверяем, не обработана ли уже эта транзакция
    if (!isTransactionProcessed($txid)) {
        // Зачисляем пользователю
        creditUserBalance($userId, $amount);

        // Сохраняем транзакцию
        saveTransaction($userId, $address, $amount, $txid);

        // Отправляем уведомление пользователю
        notifyUser($userId, "Ваш депозит {$amount} USDT успешно зачислен");
    }
}

// ОБЯЗАТЕЛЬНО отвечаем OK
echo 'OK';

// Вспомогательные функции
function isTransactionProcessed($txid) {
    // Проверка в БД, обработана ли транзакция
    // SELECT COUNT(*) FROM transactions WHERE txid = ?
    return false; // Заглушка
}

function creditUserBalance($userId, $amount) {
    // UPDATE users SET balance = balance + ? WHERE id = ?
}

function saveTransaction($userId, $address, $amount, $txid) {
    // INSERT INTO transactions (user_id, address, amount, txid, created_at)
}

function notifyUser($userId, $message) {
    // Отправка email/push уведомления
}
```

#### 3. Проверка баланса (резервный метод без IPN)

```php
<?php
// check_balance.php - Периодическая проверка балансов (cron job)

$apiKey = 'YOUR_API_KEY';

// Получаем все адреса из БД, которые ожидают пополнение
$addresses = getPendingAddresses();

foreach ($addresses as $row) {
    $address = $row['address'];
    $userId = $row['user_id'];
    $savedBalance = (float)$row['balance'];

    // Проверяем текущий баланс
    $url = "https://new.cryptocurrencyapi.net/api/tron/.balance?" . http_build_query([
        'key' => $apiKey,
        'from' => $address,
        'token' => 'USDT'
    ]);

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['result'])) {
        $currentBalance = (float)$data['result'];

        // Если баланс увеличился
        if ($currentBalance > $savedBalance) {
            $depositAmount = $currentBalance - $savedBalance;

            // Зачисляем пользователю
            creditUserBalance($userId, $depositAmount);

            // Обновляем сохраненный баланс
            updateSavedBalance($address, $currentBalance);
        }
    }

    // Соблюдаем rate limit - не более 10 запросов за 2 секунды
    usleep(250000); // 0.25 секунды между запросами
}

function getPendingAddresses() {
    // SELECT user_id, address, balance FROM deposit_addresses WHERE status = 'pending'
    return [];
}

function updateSavedBalance($address, $newBalance) {
    // UPDATE deposit_addresses SET balance = ? WHERE address = ?
}
```

### Пример на JavaScript/Node.js

```javascript
// deposit.js - Создание адреса для пользователя

const axios = require('axios');
const crypto = require('crypto');

const API_KEY = 'YOUR_API_KEY';
const BASE_URL = 'https://new.cryptocurrencyapi.net/api';

async function createDepositAddress(userId) {
    const params = {
        key: API_KEY,
        token: 'USDT',
        uniqID: `user_${userId}`,
        label: `Deposit for User ${userId}`,
        period: 1440,
        statusURL: 'https://yourdomain.com/api/ipn/crypto'
    };

    const response = await axios.get(`${BASE_URL}/tron/.give`, { params });

    if (response.data.result) {
        const address = response.data.result.address;

        // Сохраняем в БД
        await saveDepositAddress(userId, address);

        return address;
    }

    throw new Error(response.data.error || 'Unknown error');
}

// ipn.js - Express обработчик IPN

const express = require('express');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.urlencoded({ extended: true }));

function verifySignature(data, apiKey) {
    const sign = data.sign;
    delete data.sign;

    const sortedKeys = Object.keys(data).sort();
    const params = sortedKeys
        .filter(key => data[key] !== '')
        .map(key => `${key}=${data[key]}`);

    const string = `${apiKey}&${params.join('&')}`;
    const calculatedSign = crypto.createHash('sha256').update(string).digest('hex');

    return calculatedSign === sign;
}

app.post('/api/ipn/crypto', async (req, res) => {
    const ipnData = req.body;

    // Проверяем подпись
    if (!verifySignature(ipnData, API_KEY)) {
        return res.status(403).send('Invalid signature');
    }

    const { network, token, address, amount, txid, confirmations, uniqID } = ipnData;

    // Проверяем тип и количество подтверждений
    if (network === 'tron' && token === 'USDT' && confirmations >= 19) {
        const match = uniqID.match(/^user_(\d+)$/);

        if (match) {
            const userId = parseInt(match[1]);
            const depositAmount = parseFloat(amount);

            // Проверяем, не обработана ли транзакция
            const isProcessed = await checkTransaction(txid);

            if (!isProcessed) {
                // Зачисляем
                await creditUser(userId, depositAmount);
                await saveTransaction(userId, address, depositAmount, txid);
                await notifyUser(userId, `Депозит ${depositAmount} USDT зачислен`);
            }
        }
    }

    // ОБЯЗАТЕЛЬНО отвечаем OK
    res.send('OK');
});

app.listen(3000, () => {
    console.log('IPN server running on port 3000');
});
```

---

## Дополнительная информация

### Поддерживаемые токены

Для получения списка всех поддерживаемых токенов используйте метод `tokens`:

```bash
curl -H "CCAPI-KEY: YOUR_API_KEY" \
  "https://new.cryptocurrencyapi.net/api/tron/.tokens"
```

### Стоимость операций

- **give** (генерация нового адреса): 1 единица
- **give** (возврат существующего адреса): бесплатно
- **balance** (для ваших адресов): бесплатно
- **balance** (для чужих адресов): 1 единица
- **track**: 1 единица

Единицы можно купить в личном кабинете или настроить автопокупку.

### Рекомендации по безопасности

1. **Всегда проверяйте подпись** в IPN обработчике
2. **Ограничьте IP-адреса** в настройках API ключа
3. **Используйте HTTPS** для statusURL
4. **Проверяйте количество подтверждений** перед зачислением
5. **Логируйте все IPN запросы** для отладки
6. **Проверяйте дубликаты транзакций** по txid
7. **Не храните приватные ключи** - API может возвращать их, но для депозитов они не нужны

### Минимальные подтверждения

| Сеть | Рекомендуемые подтверждения |
|------|----------------------------|
| Tron (TRC20) | 19 |
| Ethereum (ERC20) | 12 |
| BSC (BEP20) | 15 |
| Polygon | 128 |
| Bitcoin | 2-6 |

### Поддержка

- Telegram: @No_Problems_Bot
- Telegram 2: @EtherAPIBot
- Email: cryptocurrencyapi.net@gmail.com
- Новости: https://t.me/CryptoCurrencyAPInet

---

## Краткая справка

### Быстрый старт для приема USDT TRC20

1. **Получите API ключ** на https://new.cryptocurrencyapi.net/
2. **Создайте адрес для пользователя:**
   ```bash
   curl -H "CCAPI-KEY: YOUR_KEY" \
     "https://new.cryptocurrencyapi.net/api/trx/.give?token=USDT&uniqID=user123&statusURL=https://yourdomain.com/ipn"
   ```
3. **Покажите адрес пользователю** для пополнения
4. **Получите IPN уведомление** когда средства поступят
5. **Проверьте подпись** и зачислите баланс

---

## Changelog

- **2025-10-05**: Создана документация на основе официальной документации CryptocurrencyAPI.net
