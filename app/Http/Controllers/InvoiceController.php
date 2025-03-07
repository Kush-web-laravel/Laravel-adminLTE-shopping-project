<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Mpdf\Mpdf; 
use Twilio\Rest\Client;

class InvoiceController extends Controller
{
    //

    public function index()
    {
        return view('invoice');
    }

    public function addInvoice()
    {
        return view('add-invoice');
    }

   public function store(Request $request)
   {
        $validatedData = $request->validate([
            'invoice_number' => 'required',
            'description' => 'required|string|max:25',
            'items' => 'required|array',
            'items.*' => 'required|string|max:20',
            'gross_weight' => 'required|array',
            'gross_weight.*' => 'required|numeric|min:1',
            'less_weight' =>  'required|array',
            'less_weight.*' => 'required|numeric|min:1',
            'touch' => 'required|array',
            'touch.*' => 'required|numeric|min:0|max:100',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => $validatedData['invoice_number'],
            'description' => $validatedData['description'],
        ]);

        foreach($validatedData['items'] as $index => $itemName){

            $grossWeight = $validatedData['gross_weight'][$index];
            $lessWeight = $validatedData['less_weight'][$index];

            if($lessWeight > $grossWeight){
                return response()->json(['error' => 'Less weight cannot be greater than Gross weight']);
            }

            $netWeight = $grossWeight - $lessWeight;
            $touch = $validatedData['touch'][$index];
            $finalWeight = ($netWeight * $touch)/100;

            $invoiceItem = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_name' => $itemName,
                'gross_weight' => $grossWeight,
                'less_weight' => $lessWeight,
                'net_weight' => $netWeight,
                'touch' => $touch,
                'final_weight' => $finalWeight,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice created successfully',
            'redirect_url' => route('invoice.index')
        ]);
   }

   public function show()
   {
        $invoices = Invoice::select('id', 'invoice_number')->get();

        return response()->json($invoices);
   }

   public function edit($id)
   {
        $invoice = Invoice::with('item')->findOrFail($id);
        return view('edit-invoice',compact('invoice'));
   }

   public function update(Request $request, $id)
   {
        $validatedData = $request->validate([
            'invoice_number' => 'required',
            'description' => 'required|string|max:25',
            'items' => 'required|array',
            'items.*' => 'required|string|max:20',
            'gross_weight' => 'required|array',
            'gross_weight.*' => 'required|numeric|min:1',
            'less_weight' =>  'required|array',
            'less_weight.*' => 'required|numeric|min:1',
            'touch' => 'required|array',
            'touch.*' => 'required|numeric|min:0|max:100',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'invoice_number' => $validatedData['invoice_number'],
            'description' => $validatedData['description'],
        ]);

        $invoice->item()->delete();
        foreach($request->items as $index => $itemName){
            $invoice->item()->create([
                'item_name' => $itemName,
                'gross_weight' => $request->gross_weight[$index],
                'less_weight' => $request->less_weight[$index],
                'net_weight' => ($request->gross_weight[$index] - $request->less_weight[$index]),
                'touch' => $request->touch[$index],
                'final_weight' =>  (($request->gross_weight[$index] - $request->less_weight[$index]) *  $request->touch[$index] / 100)
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice updated successfully',
            'redirect_url' => route('invoice.index')
        ]);
    }    

    public function delete($id)
    {
        $invoice = Invoice::findOrFail($id);

        if($invoice){
            $invoice->delete();
            $invoice->item()->delete();
            return response()->json(['status' => 'success', 'message' => 'Invoice details deleted successfully']);
        }
            return response()->json(['status' => 'error', 'message' => 'Failed to delete invoice details']);
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::with('item')->findOrFail($id);

        $html = view('download-invoice', compact('invoice'))->render();
        
        $mpdf = new Mpdf();

        $mpdf->WriteHTML($html);

        return  $mpdf->Output("invoice-{$invoice->invoice_number}.pdf", 'D');
    }

    public function exportAsCsv($id)
    {
        $invoice = Invoice::with('item')->findOrFail($id);
        $csvFileName = $invoice->invoice_number.'_invoice.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

    
        ob_start();
        $handle = fopen('php://output', 'w');

    
        fputcsv($handle, ['Invoice Number', 'Description']);
        fputcsv($handle, [$invoice->invoice_number, $invoice->description]);

    
        fputcsv($handle, ['Item Name', 'Gross Weight (g)', 'Less Weight (g)', 'Net Weight (g)', 'Touch (%)', 'Final Weight (g)']);

    
        foreach ($invoice->item as $item) {
            fputcsv($handle, [
                $item->item_name,
                number_format($item->gross_weight, 2),
                number_format($item->less_weight, 2),
                number_format($item->net_weight, 2),
                number_format($item->touch, 2),
                number_format($item->final_weight, 2),
            ]);
        }

        fclose($handle);

    
        $csvContent = ob_get_clean();

        
        return response($csvContent, 200, $headers);
    }

    public function sendSMS(Request $request)
    {
        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);
 
        $message = $twilio->messages
            ->create($request->input('to'), // to
                     ['from' => env('TWILIO_PHONE_NUMBER'), 'body' => $request->input('message')]);
 
        return response()->json(['message' => 'SMS sent successfully', 'sid' => $message->sid]);
    }
}

