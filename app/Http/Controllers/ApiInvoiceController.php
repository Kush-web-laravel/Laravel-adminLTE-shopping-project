<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Validator;

class ApiInvoiceController extends Controller
{
    //
    public function create(Request $request)
    {
        $data = json_decode($request->input('data'), true);
        $rules = [
            'description' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*' => 'required|string|max:20',
            'gross_weight' => 'required|array',
            'gross_weight.*' => 'required|numeric|min:1',
            'less_weight' =>  'required|array',
            'less_weight.*' => 'required|numeric|min:1',
            'touch' => 'required|array',
            'touch.*' => 'required|numeric|min:0|max:100',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }else{
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'description' => $data['description'],
            ]);

            foreach($data['items'] as $index => $itemName){
                $invoice->item()->create([
                    'item_name' => $itemName,
                    'gross_weight' => $data['gross_weight'][$index],
                    'less_weight' => $data['less_weight'][$index],
                    'net_weight' => $data['gross_weight'][$index] - $data['less_weight'][$index],
                    'touch' => $data['touch'][$index],
                    'final_weight' =>  (($data['gross_weight'][$index] - $data['less_weight'][$index]) *  $data['touch'][$index]) / 100,
                ]);
            }

            $responseData = [
                'invoice' =>  $invoice->load('item'),
                
            ];

            return response()->json([
                'status' => 'success',
                'status_code' => 200, 
                'message' => 'Invoice created successfully',
                'data' =>  $responseData,
            ]);
        }
    }

    public function fetchall()
    {
        $invoices = Invoice::with('item')->get();

        $responseData = [
            'invoices' => $invoices,
        ];

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Invoices fetched successfully',
            'data' => $responseData,
        ]);
    }

    public function showInvoice(Request $request)
    {
        $id = $request->query('q');

        $invoice = Invoice::with('item')->find($id);
        
        $responseData = [
            'invoice' =>  $invoice,
        ];
        if($invoice){
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Invoice details retreived successfully',
                'data' =>  $responseData,
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Invoice details not found',
                'data' => null
            ]);
        }
    }

    public function  updateInvoice(Request $request)
    {
        $id = $request->query('q');

        $data = json_decode($request->input('data'), true);

        $rules = [
            'description' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*' => 'required|string|max:20',
            'gross_weight' => 'required|array',
            'gross_weight.*' => 'required|numeric|min:1',
            'less_weight' =>  'required|array',
            'less_weight.*' => 'required|numeric|min:1',
            'touch' => 'required|array',
            'touch.*' => 'required|numeric|min:0|max:100',
        ];


        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }else{
            $invoice = Invoice::find($id);
            $invoice->update([
                'description' => $data['description'],
            ]);

            $invoice->item()->delete();

            foreach($data['items'] as $index => $itemName){
                $invoice->item()->create([
                    'item_name' => $itemName,
                    'gross_weight' => $data['gross_weight'][$index],
                    'less_weight' => $data['less_weight'][$index],
                    'net_weight' => $data['gross_weight'][$index] - $data['less_weight'][$index],
                    'touch' => $data['touch'][$index],
                    'final_weight' =>  (($data['gross_weight'][$index] - $data['less_weight'][$index]) *  $data['touch'][$index]) / 100,
                ]);
            }

            $responseData = [
                'invoice' =>  $invoice->load('item'),
            ];

            return response()->json([
                'status' => 'success',
                'status_code' => 200, 
                'message' => 'Invoice details updated successfully',
                'data' =>  $responseData,
            ]);
        }
    }

    public function destroyInvoice(Request $request)
    {
        $id = $request->query('q');

        $invoice = Invoice::find($id);

        if($invoice){

            $invoice->delete();
            $invoice->item()->delete();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Invoice details deleted successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' =>  'Invoice details not found',
                'data' => null
            ]);
        }
    }
}
