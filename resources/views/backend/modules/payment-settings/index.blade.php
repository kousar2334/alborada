@php
    $links = [['title' => 'Payment Settings', 'route' => '', 'active' => true]];
@endphp
@extends('backend.layouts.dashboard_layout')
@section('page-title')
    Payment Settings
@endsection
@section('page-content')
    <x-admin-page-header title="Payment Settings" :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                {{-- SSLCommerz Settings --}}
                <div class="col-lg-6 mb-4">
                    <form action="{{ route('admin.payment.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="ssl">
                        <div class="card card-outline card-primary h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    {{ __tr('SSLCommerz (Online Payment)') }}
                                </h3>
                                <div class="card-tools">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ssl_enabled"
                                            name="ssl_enabled" value="1"
                                            {{ get_setting('ssl_enabled', 0) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ssl_enabled">Enable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Store ID') }}</label>
                                    <input type="text" name="sslcommerz_store_id" class="form-control"
                                        value="{{ get_setting('sslcommerz_store_id') }}" placeholder="your_store_id">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Store Password') }}</label>
                                    <input type="password" name="sslcommerz_store_password" class="form-control"
                                        value="{{ get_setting('sslcommerz_store_password') }}"
                                        placeholder="your_store_password" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Currency') }}</label>
                                    <input type="text" name="sslcommerz_currency" class="form-control"
                                        value="{{ get_setting('sslcommerz_currency', 'BDT') }}" placeholder="BDT"
                                        maxlength="10">
                                    <small class="form-text text-muted">e.g. BDT, USD, EUR</small>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="sslcommerz_sandbox"
                                            name="sslcommerz_sandbox" value="1"
                                            {{ get_setting('sslcommerz_sandbox', 1) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sslcommerz_sandbox">
                                            {{ __tr('Sandbox / Test Mode') }}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        {{ __tr('Disable sandbox mode for live payments.') }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> {{ __tr('Save SSLCommerz Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Bank Transfer Settings --}}
                <div class="col-lg-6 mb-4">
                    <form action="{{ route('admin.payment.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="bank">
                        <div class="card card-outline card-success h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-university mr-2"></i>
                                    {{ __tr('Bank Transfer (Manual Approval)') }}
                                </h3>
                                <div class="card-tools">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="bank_transfer_enabled"
                                            name="bank_transfer_enabled" value="1"
                                            {{ get_setting('bank_transfer_enabled', 0) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="bank_transfer_enabled">Enable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __tr('Bank Name') }}</label>
                                    <input type="text" name="bank_name" class="form-control"
                                        value="{{ get_setting('bank_name') }}" placeholder="e.g. Dutch Bangla Bank">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Account Name') }}</label>
                                    <input type="text" name="bank_account_name" class="form-control"
                                        value="{{ get_setting('bank_account_name') }}" placeholder="Account holder name">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Account Number') }}</label>
                                    <input type="text" name="bank_account_number" class="form-control"
                                        value="{{ get_setting('bank_account_number') }}" placeholder="e.g. 1234567890">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Routing Number') }}</label>
                                    <input type="text" name="bank_routing_number" class="form-control"
                                        value="{{ get_setting('bank_routing_number') }}" placeholder="Optional">
                                </div>
                                <div class="form-group">
                                    <label>{{ __tr('Branch Name') }}</label>
                                    <input type="text" name="bank_branch" class="form-control"
                                        value="{{ get_setting('bank_branch') }}" placeholder="e.g. Motijheel Branch">
                                </div>
                                <div class="form-group mb-0">
                                    <label>{{ __tr('Payment Instructions') }}</label>
                                    <textarea name="bank_instructions" class="form-control" rows="3"
                                        placeholder="Instructions shown to users before they transfer (e.g. include your name in the transfer note)">{{ get_setting('bank_instructions') }}</textarea>
                                    <small class="form-text text-muted">
                                        {{ __tr('Displayed on the checkout page to guide the customer.') }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> {{ __tr('Save Bank Transfer Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
@endsection
