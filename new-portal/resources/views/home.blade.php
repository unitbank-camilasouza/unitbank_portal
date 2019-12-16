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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
