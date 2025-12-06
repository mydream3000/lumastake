<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        $availableBalance = $user->balance;
        $minAmount = config('pools.min_withdraw_amount', 10);

        $network = $this->input('network', 'tron');
        $token = $this->input('token', 'USDT');

        $addressRegex = match ($network) {
            'ethereum', 'bsc' => '/^0x[a-fA-F0-9]{40}$/', // EVM
            default => '/^T[a-zA-Z0-9]{33}$/', // Tron (TRC20)
        };

        return [
            'token' => [
                'required',
                'in:USDT,USDC',
            ],
            'amount' => [
                'required',
                'numeric',
                "min:{$minAmount}",
                "max:{$availableBalance}",
            ],
            'network' => [
                'required',
                'in:tron,ethereum,bsc',
                // Валидация соответствия токена и сети
                function ($attribute, $value, $fail) use ($token) {
                    if ($token === 'USDT' && !in_array($value, ['ethereum', 'tron'])) {
                        $fail('USDT is only available on Ethereum (ERC-20) and TRON (TRC-20) networks');
                    }
                    if ($token === 'USDC' && !in_array($value, ['bsc', 'ethereum'])) {
                        $fail('USDC is only available on Ethereum (ERC-20) and BNB Chain (BEP-20) networks');
                    }
                },
            ],
            'receiver_address' => [
                'required',
                'string',
                "regex:{$addressRegex}",
            ],
        ];
    }

    public function messages(): array
    {
        $minAmount = config('pools.min_withdraw_amount', 10);

        return [
            'token.required' => 'Token is required',
            'token.in' => 'Unsupported token selected',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => "Minimum withdrawal amount is {$minAmount}",
            'amount.max' => 'Insufficient available balance',
            'network.required' => 'Network is required',
            'network.in' => 'Unsupported network selected',
            'receiver_address.required' => 'Receiver address is required',
            'receiver_address.regex' => 'Invalid address format for selected network',
        ];
    }
}
