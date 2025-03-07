<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $payments;
    public $pdf;

    public function __construct($payments, $pdf)
    {
        $this->payments = $payments;
        $this->pdf = $pdf;
    }

    public function build()
    {
        //dd($this->payments);
        return $this->subject('Invoice for Your Payment')
                    ->cc('kushchhatbar19@gmail.com')
                    ->view('emails.invoice')
                    ->attachData($this->pdf, 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
