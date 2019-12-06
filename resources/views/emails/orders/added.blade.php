<table>
	<tbody>
		<tr>
			<td>Date</td>
			<td>{{ $order->created_at }}</td>
		</tr>
		<tr>
			<td>Customer</td>
			<td>{{ $order->customer ? $order->customer->name : '' }}</td>
		</tr>
		<tr>
			<td>DSM</td>
			<td>{{ $order->user ? $order->user->name : '' }}</td>
		</tr>
	</tbody>
</table>