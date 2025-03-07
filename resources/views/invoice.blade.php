@extends('invoice.app')

@section('content')

<div class="container">

    <a href="{{ route('invoice.add') }}" class="btn btn-success">Add Invoice details</a>

    <div>
        <table class="table table-striped" id="invoiceList">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Invoice Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

@endsection