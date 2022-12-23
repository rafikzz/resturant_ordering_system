@extends('layouts.admin.master')
@section('content')
<div class="row">
    <div class="col">
        <div class="card card-outline card-success ">
            <div class="card-header">
                <h2 class="card-title">Add Customer Wallet Transaction</h2>
                <div class="card-tools">
                    <a class="btn btn-primary" href="{{ route('admin.customers.wallet_transactions.index',$customer->id) }}"> Back</a></i></a>
                </div>
            </div>
            <div class="card-body ">
                <form method="POST" action="{{ route('admin.customers.wallet_transactions.store',$customer->id) }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6 ">
                            <label for="transaction_type_id"> Transaction Type  @component('compoments.required')

                                @endcomponent</label>
                            <select class="form-control" name="transaction_type_id" required>
                                <option selected value="" disabled>--Select Transaction Type--</option>
                                @foreach ($transaction_types as $transaction_type)
                                    <option value="{{ $transaction_type->id }}">{{ $transaction_type->name }}({{ $transaction_type->is_add?'Adding':'Deducting' }} )</option>
                                @endforeach
                            </select>
                            @error('transaction_type_id')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="label" for="amount">Transaction Amount  @component('compoments.required')

                                @endcomponent</label>
                            <input type="number" name="amount" min="0" step="0.01" value="{{ old('amount') }}"
                                class="form-control  @error('amount') is-invalid @enderror" autocomplete="off"
                                placeholder="Enter amount" required>
                            @error('amount')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">Description @component('compoments.required')

                                @endcomponent</label>
                            <textarea name="description" placeholder="Description About Transaction"  class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary  mt-3">Save and exit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
