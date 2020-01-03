@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    <table>
                        <thead>
                            <tr>
                                <th> {{ __('value') }} </td>
                                <th> {{ __('product') }} </td>
                                <th> {{ __('status') }} </td>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($contracts as $contract)
                            <tr onclick="window.location.href='{{ route('show_contract_details', ['contract' => encrypt($contract->id)]) }}'">
                                <td> {{ $contract->current_value }} </td>
                                <td> {{ $contract->product }} </td>
                                <td> {{ $contract->contract_status }} </td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td> No Data Found </td>
                                <td></td>
                            </tr>
                        @endforelse

                        {{ $contracts->links() }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
