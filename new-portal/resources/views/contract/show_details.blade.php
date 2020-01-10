@extends('layouts.app')

@section('content')
    <p style="text-align: center;"> {{ $contract->id }} </p>
    <p style="text-align: center;"> {{ $current_contract->current_value }} </p>

    Yields:
    @forelse ($yields as $y)
        @component('components.yield_component', [ 'id' => $y->id])
        @endcomponent
    @empty
        No Yield found
    @endforelse
    <br>

    Withdrawals:
    @forelse ($withdrawals as $w)
    <br>
        <p onclick="window.location.href='{{ route('show_withdraw_details', ['withdraw' => encrypt($w->id)]) }}'"> Withdraw: {{ $w->id }} </p>
    @empty
        No Withdrawn found
    @endforelse
    <br>

    Customers:
    @forelse ($customers as $c)
    <br>
        Customer: {{ $c->id }}
    @empty
        No Customer found
    @endforelse
@endsection
