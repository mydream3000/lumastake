<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое сообщение из формы обратной связи</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF451C 0%, #05C982 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .field {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .field:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .field-value {
            color: #333;
            font-size: 16px;
        }
        .message-box {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #FF451C;
            border-radius: 4px;
            margin-top: 10px;
        }
        .footer {
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Новое сообщение из формы обратной связи</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Имя</div>
                <div class="field-value">{{ $data['name'] ?? ($data['first_name'] . ' ' . $data['last_name']) }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email</div>
                <div class="field-value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></div>
            </div>

            @if(!empty($data['phone']))
            <div class="field">
                <div class="field-label">Телефон</div>
                <div class="field-value">{{ $data['phone'] }}</div>
            </div>
            @endif

            @if(!empty($data['country']))
            <div class="field">
                <div class="field-label">Страна</div>
                <div class="field-value">{{ $data['country'] }}</div>
            </div>
            @endif

            <div class="field">
                <div class="field-label">Сообщение</div>
                <div class="message-box">
                    {{ $data['message'] }}
                </div>
            </div>

            <div class="field">
                <div class="field-label">Дата отправки</div>
                <div class="field-value">{{ now()->format('d.m.Y H:i:s') }}</div>
            </div>
        </div>
        <div class="footer">
            <p>Это автоматическое письмо от системы Lumastake.</p>
            <p>Для ответа используйте кнопку "Ответить" в вашем почтовом клиенте.</p>
        </div>
    </div>
</body>
</html>
