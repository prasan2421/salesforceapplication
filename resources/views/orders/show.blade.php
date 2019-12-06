@extends('layouts.master')

@section('title', 'Order Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('date', 'Date') }}
          {{ Form::text('date', $order->created_at, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('customer_id', 'Customer') }}
          {{ Form::text('customer_id', $order->customer ? $order->customer->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('user_id', 'User') }}
          {{ Form::text('user_id', $order->user ? $order->user->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h4>Products</h4>
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Product</th>
              <th scope="col">Quantity</th>
              <th scope="col">Unit</th>
              <th scope="col">Rate</th>
              <th scope="col">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->orderProducts as $orderProduct)
            <tr>
              <td>{{ $orderProduct->product ? $orderProduct->product->name : '' }}</td>
              <td>{{ $orderProduct->quantity }}</td>
              <td>
                @if($orderProduct->product && $orderProduct->product->unit)
                {{ $orderProduct->product->unit->name }}
                @endif
              </td>
              <td>{{ $orderProduct->product ? $orderProduct->product->distributorsellingprice : '' }}</td>
              <td>
                @if($orderProduct->quantity && $orderProduct->product && $orderProduct->product->distributorsellingprice)
                {{ $orderProduct->quantity * $orderProduct->product->distributorsellingprice }}
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection