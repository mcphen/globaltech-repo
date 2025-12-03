<?php

namespace App\Models;

use App\Models\Appel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'items_count',
        'subtotal',
        'total',
        'currency',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'invoice_number', 
        'invoice_date',   
        'invoice_path',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'items_count' => 'integer',
        'invoice_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

     /**
     * Relation polymorphique avec les appels
     */
    public function appels(): MorphMany
    {
        return $this->morphMany(Appel::class, 'callable');
    }

    /**
     * Générer un numéro de facture
     */
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        return "FACT-{$year}{$month}-{$sequence}";
    }



/**
     * Vérifier si la commande a une facture
     */
    public function hasInvoice(): bool
    {
        return !empty($this->invoice_number) && !empty($this->invoice_path);
    }

    /**
     * Récupérer les appels récents
     */
    public function recentAppels($limit = 5)
    {
        return $this->appels()->with('user')->latest()->take($limit)->get();
    }

}