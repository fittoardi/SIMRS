<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'visit_id',
        'patient_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_status',
        'notes',
    ];

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ym') . '-';
        $last = static::where('number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('number');

        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $next = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $prefix . $next;
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => PaymentStatus::class,
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
