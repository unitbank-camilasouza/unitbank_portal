@extends('layouts.app')

@section('content')
<table>
    <thead>
        <th>
            <td> {{ __('value') }} </td>
            <td> {{ __('product') }} </td>
            <td> {{ __('status') }} </td>
        </th>
    </thead>
    <tbody>
    @forelse ($contracts as $contract)
        <tr>
            <td> {{ $contract->current_value }} </td>
            <td> {{ $contract->product }} </td>
            <td> {{ $contract->status }} </td>
        </tr>
    @empty
        <tr>
            <td></td>
            <td> No Data Found </td>
            <td></td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection
