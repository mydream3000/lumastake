<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'number' => $this->id, // Will be overridden by collection index
            'type' => ucfirst(str_replace('_', ' ', $this->type)),
            'amount' => '$' . number_format($this->amount, 2),
            'status' => $this->status,
            'status_badge' => $this->getStatusBadge(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge(): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded bg-yellow-500/20 text-yellow-500">Pending</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded bg-green-500/20 text-green-500">Completed</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded bg-red-500/20 text-red-500">Failed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded bg-gray-500/20 text-gray-500">Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }
}
