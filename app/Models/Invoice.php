<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'description'
    ];

    protected $dates = ['deleted_at'];

    public function item()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public static function generateInvoiceNumber()
    {
        $invoiceNumber = strtoupper(Str::random(3)) . rand(1,100);

        while(self::where('invoice_number',  $invoiceNumber)->exists()) {
            $invoiceNumber = strtoupper(Str::random(3)) . rand(1,100);
        }
        return $invoiceNumber;
    }
}
