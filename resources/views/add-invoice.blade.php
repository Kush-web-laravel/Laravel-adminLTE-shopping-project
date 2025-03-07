@extends('invoice.app')

@section('content')

<div class="invoiceTable">

    <h2>Invoice</h2>
    
    <form id="invoiceForm">
        <div class="invoice-number">
            <label for="invoiceNumber">Invoice Number : </label>
            <input type="text" name="invoice_number" id="invoiceNumber" placeholder="Invoice number ..."/>
        
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
                <tr class="data-row">
                    <td class="row-number">1</td>
                    <td>
                        <input type="text" name="items[]" class="items" placeholder="Item name ..">
                        <small class="itemsErr"></small>
                    </td>
                    <td>
                        <input type="number" name="gross_weight[]" class="gross_weight" step="0.01" placeholder="Gross weight ..">
                        <small class="grossErr"></small>
                    </td>
                    <td>
                        <input type="number" name="less_weight[]" class="less_weight" step="0.01" placeholder="Less weight .."/>
                        <small class="lessErr"></small>
                    </td>
                    <td>
                        <input type="number" name="net_weight[]" class="net_weight" step="0.01" placeholder="Net weight .." disabled />
                    </td>
                    <td>
                        <input type="number" name="touch[]" class="touch" step="0.01"  placeholder="Touch .." />
                        <small class="touchErr"></small>
                    </td>
                    <td>
                        <input type="number" name="final_weight[]" class="final_weight" step="0.01" placeholder="Final weight .." disabled />
                    </td>
                    <td><input type="button" class="add-btn" value="&#43;"></td>
                </tr>
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
        <textarea  name="description" id="description" placeholder="Description ..."></textarea><br/>
        <small id="descriptionErr"></small>

        <input type="submit" class="btn btn-primary" value="Save" />

    </form>
</div>

@endsection