@extends('invoice.app')

@section('content')

<div class="invoiceTable">

    <h2>Invoice</h2>
    
    <form id="updateInvoiceForm">
        <input type="hidden" name="invoiceId" id="invoice_id" value="{{ $invoice->id }}">

        <div class="invoice-number">
            <label for="invoiceNumber">Invoice Number : </label>
            <input type="text" name="invoice_number" id="invoiceNumber" placeholder="Invoice number ..." value="{{$invoice -> invoice_number}}"/>
        
        </div>
        <table id="invoice-table">
            <thead>
                <tr>
                    <th> No. </th>
                    <th> Item name. </th>
                    <th> Gross weight. </th>
                    <th> Less weight. </th>
                    <th> Net weight. </th>
                    <th> Touch. </th>
                    <th> Final weight. </th>
                    <th> Actions </th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->item as $index => $item)
                    <tr class="data-row">
                        <td class="row-number">{{ $index + 1 }}</td>
                        <td>
                            <input type="text" name="items[]" class="items" value="{{ $item->item_name }}" placeholder="Item name ..">
                            <small class="itemsErr"></small>
                        </td>
                        <td>
                            <input type="number" name="gross_weight[]" class="gross_weight" step="0.01" value="{{ $item->gross_weight }}" placeholder="Gross weight ..">
                            <small class="grossErr"></small>
                        </td>
                        <td>
                            <input type="number" name="less_weight[]" class="less_weight" step="0.01" value="{{ $item->less_weight }}" placeholder="Less weight .."/>
                            <small class="lessErr"></small>
                        </td>
                        <td>
                            <input type="number" name="net_weight[]" class="net_weight" step="0.01" value="{{ $item->net_weight }}" placeholder="Net weight .." disabled />
                        </td>
                        <td>
                            <input type="number" name="touch[]" class="touch" step="0.01" value="{{ $item->touch }}"  placeholder="Touch .." />
                            <small class="touchErr"></small>
                        </td>
                        <td>
                            <input type="number" name="final_weight[]" class="final_weight" value="{{ $item->final_weight }}" step="0.01" placeholder="Final weight .." disabled />
                        </td>
                        @if($index == 0)
                            <td><input type="button" class="add-btn" value="&#43;"></td>
                        @else
                            <td><input type="button" class="remove-btn" value="&minus;"></td>
                        @endif
                    </tr>
                @endforeach
                    <tr id="total">
                        <td colspan="2">Total:</td>
                        <td><span id="grossTotal"></span></td>
                        <td><span id="lessTotal"></span></td>
                        <td><span id="netTotal"></span></td>
                        <td><span id="touchTotal"></span></td>
                        <td colspan="2"><span id="finalTotal"></span></td>
                    </tr>
            </tbody>
        </table>
        <textarea  name="description" id="description" placeholder="Description ...">{{ $invoice->description }}</textarea><br/>
        <small id="descriptionErr"></small>

        <input type="submit" class="btn btn-primary" id = "update-btn" value="Update" />

    </form>
</div>

@endsection