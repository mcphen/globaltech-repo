<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'subject',
        'description',
        'status',
        'notes'
    ];

     protected $casts = [
        'status' => 'string',
    ];

        /**
     * Scope for pending contacts.
     */
        public function scopePending($query)
        {
            return $query->where('status', 'pending');
        }

        public function scopeProcessed($query)
        {
            return $query->where('status', 'processed');
        }

        /**
         * Scope for archived contacts.
         */
        public function scopeArchived($query)
        {
            return $query->where('status', 'archived');
        }

         /**
         * Check if contact is pending.
         */
        public function isPending(): bool
        {
            return $this->status === 'pending';
        }

        /**
         * Check if contact is processed.
         */
        public function isProcessed(): bool
        {
            return $this->status === 'processed';
        }

        /**
         * Check if contact is archived.
         */
        public function isArchived(): bool
        {
            return $this->status === 'archived';
        }


        


    /**
     * Get the client that owns the contact.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
