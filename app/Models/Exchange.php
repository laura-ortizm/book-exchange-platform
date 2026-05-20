<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['requester_id', 'book_id', 'offered_book_id', 'owner_id', 'message', 'status', 'requester_confirmed_at', 'owner_confirmed_at'])]
class Exchange extends Model
{
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function offeredBook(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'offered_book_id');
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    protected function casts(): array
    {
        return [
            'requester_confirmed_at' => 'datetime',
            'owner_confirmed_at'      => 'datetime',
        ];
    }
}
