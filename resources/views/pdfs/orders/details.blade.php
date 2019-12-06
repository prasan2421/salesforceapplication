<table>
	<tbody>
		<tr>
			<td>Date</td>
			<td>{{ $order->created_at }}</td>
		</tr>
		<tr>
			<td>Retailer</td>
			<td>{{ $order->customer ? $order->customer->name : '' }}</td>
		</tr>
		<tr>
			<td>DSM</td>
			<td>{{ $order->user ? $order->user->name : '' }}</td>
		</tr>
	</tbody>
</table>

<table>
	<thead>
		<tr>
			<th>ITEM SAP CODE</th>
			<th>ITEM DESCRIPTION</th>
			<th>QTY</th>
			<th>UOM</th>
			<th>RATE</th>
			<th>AMOUNT</th>
		</tr>
	</thead>
	<tbody>
		@foreach($order->orderProducts as $orderProduct)
		<tr>
			<td>{{ $orderProduct->product ? $orderProduct->product->sap_code : '' }}</td>
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