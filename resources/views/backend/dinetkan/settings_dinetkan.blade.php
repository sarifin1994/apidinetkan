@extends('backend.layouts.app_new')

@section('title', 'Site Settings')

@section('main')
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-sm-6 ps-0">
                        <h3>Site Settings</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold">Payment Gateway</h5>
                        <p class="card-subtitle mb-4">Manage your Payment Gateway settings here.</p>

                        <form x-load x-data="form" method="POST" action="{{ route('dinetkan.settings_dinetkan.update.tripay') }}">
                            <template x-if="success">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>{{ __('Success') }}!</strong> {{ __('Your settings have been updated.') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </template>
                            @csrf

                            <div x-data="{ gateway: '{{ $settings->active_gateway }}' }">
                                <div class="mb-3">
                                    <label for="active_gateway" class="form-label">
                                        {{ __('Active Payment Gateway') }}
                                    </label>

                                    <div class="input-group">
                                        <template x-if="errors.active_gateway">
                                            <span class="invalid-feedback">
                                                <strong x-text="errors.active_gateway"></strong>
                                            </span>
                                        </template>
                                        <select x-model="gateway" name="active_gateway" id="active_gateway"
                                            class="form-select" :class="errors.active_gateway && 'is-invalid'" required>
                                            <!-- <option value="tripay">
                                                {{ __('TriPay') }}
                                            </option> -->
                                            <option value="duitku">
                                                {{ __('Duitku') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div x-show="gateway == 'duitku'">
                                    <div class="mb-3">
                                        <label for="duitku_merchant_code" class="form-label">
                                            {{ __('Duitku Merchant Code') }}
                                        </label>

                                        <div class="input-group">
                                            <template x-if="errors.duitku_merchant_code">
                                                <span class="invalid-feedback">
                                                    <strong x-text="errors.duitku_merchant_code"></strong>
                                                </span>
                                            </template>
                                            <input type="text" name="duitku_merchant_code" id="duitku_merchant_code"
                                                class="form-control"
                                                placeholder="{{ __('Enter your Duitku Merchant Code') }}"
                                                value="{{ $settings->duitku_merchant_code }}"
                                                :class="errors.duitku_merchant_code && 'is-invalid'" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="duitku_api_key" class="form-label">
                                            {{ __('Duitku API Key') }}
                                        </label>

                                        <div class="input-group">
                                            <template x-if="errors.duitku_api_key">
                                                <span class="invalid-feedback">
                                                    <strong x-text="errors.duitku_api_key"></strong>
                                                </span>
                                            </template>
                                            <input type="text" name="duitku_api_key" id="duitku_api_key"
                                                class="form-control" placeholder="{{ __('Enter your Duitku API Key') }}"
                                                value="{{ $settings->duitku_api_key }}"
                                                :class="errors.duitku_api_key && 'is-invalid'" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="duitku_sandbox" class="form-label">
                                            {{ __('Duitku Status') }}
                                        </label>

                                        <div class="input-group">
                                            <template x-if="errors.duitku_sandbox">
                                                <span class="invalid-feedback">
                                                    <strong x-text="errors.duitku_sandbox"></strong>
                                                </span>
                                            </template>
                                            <select name="duitku_sandbox" id="duitku_sandbox" class="form-select"
                                                :class="errors.duitku_sandbox && 'is-invalid'" required>
                                                <option value="1" @if ($settings->duitku_sandbox) selected @endif>
                                                    {{ __('Sandbox') }}
                                                </option>
                                                <option value="0" @if (!$settings->duitku_sandbox) selected @endif>
                                                    {{ __('Production') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ppn" class="form-label">
                                    {{ __('PPN') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.ppn">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.ppn"></strong>
                                        </span>
                                    </template>
                                    <input type="number" name="ppn" id="ppn" class="form-control"
                                        placeholder="{{ __('Enter PPN') }}" value="{{ $settings->ppn }}"
                                        :class="errors.ppn && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_fee" class="form-label">
                                    {{ __('Admin Fee') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.admin_fee">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.admin_fee"></strong>
                                        </span>
                                    </template>
                                    <input type="number" name="admin_fee" id="admin_fee" class="form-control"
                                        placeholder="{{ __('Enter Admin Fee') }}" value="{{ $settings->admin_fee }}"
                                        :class="errors.admin_fee && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                                <button class="btn btn-light-warning" type="reset">Cancel</button>
                                <button class="btn btn-light-success" type="submit" x-ref="button">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div style="margin-top:30px"></div>
            <div class="col-lg-12">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold">Site</h5>
                        <p class="card-subtitle mb-4">Manage your Site settings here.</p>

                        <form method="POST" action="{{ route('dinetkan.settings_dinetkan.update.site') }}">
                            <template x-if="success">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>{{ __('Success') }}!</strong> {{ __('Your settings have been updated.') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </template>
                            @csrf

                                <div class="mb-3">
                                    <label for="site_name" class="form-label">
                                        {{ __('Nama') }}
                                    </label>

                                    <div class="input-group">
                                        <template x-if="errors.site_name">
                                            <span class="invalid-feedback">
                                                <strong x-text="errors.site_name"></strong>
                                            </span>
                                        </template>
                                        <input type="text" name="site_name" id="site_name" class="form-control"
                                            placeholder="{{ __('Enter Site Name') }}" value="{{ $settings->name }}"
                                            :class="errors.site_name && 'is-invalid'" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="site_address" class="form-label">
                                        {{ __('Alamat') }}
                                    </label>

                                    <div class="input-group">
                                        <template x-if="errors.site_address">
                                            <span class="invalid-feedback">
                                                <strong x-text="errors.site_address"></strong>
                                            </span>
                                        </template>
                                        <textarea name="site_address" id="site_address" class="form-control" placeholder="{{ __('Enter Site Address') }}"
                                            :class="errors.site_address && 'is-invalid'" required>{!! nl2br($settings->address) !!}</textarea>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="$siklus_pembayaran" class="form-label">
                                        {{ __('Siklus Bulanan') }}
                                    </label>

                                    <div class="input-group">
                                        <template x-if="errors.siklus_pembayaran">
                                            <span class="invalid-feedback">
                                                <strong x-text="errors.siklus_pembayaran"></strong>
                                            </span>
                                        </template>
                                        <input type="text" name="siklus_pembayaran" id="siklus_pembayaran" class="form-control"
                                            placeholder="{{ __('Enter Site Name') }}" value="{{ $settings->siklus_pembayaran }}"
                                            :class="errors.siklus_pembayaran && 'is-invalid'" required>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                                    <button class="btn btn-light-warning" type="reset">Cancel</button>
                                    <button class="btn btn-light-success" type="submit" x-ref="button">Save</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="margin-top:30px"></div>
            <div class="col-lg-12">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold">License</h5>
                        <p class="card-subtitle mb-4">Manage your License settings here.</p>

                        <form x-load x-data="form" method="POST"
                            data-url="{{ route('dinetkan.settings_dinetkan.update.license') }}" data-method="put">
                            <template x-if="success">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>{{ __('Success') }}!</strong> {{ __('Your settings have been updated.') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </template>
                            @csrf

                            <div class="mb-3">
                                <label for="day_before_due" class="form-label">
                                    {{ __('Day Before Expiration') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.day_before_due">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.day_before_due"></strong>
                                        </span>
                                    </template>
                                    <input type="number" name="day_before_due" id="day_before_due" class="form-control"
                                        placeholder="{{ __('Enter Days Before Expiration') }}"
                                        value="{{ $licenseSettings->day_before_due }}"
                                        :class="errors.day_before_due && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="day_after_due" class="form-label">
                                    {{ __('Day After Expiration') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.day_after_due">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.day_after_due"></strong>
                                        </span>
                                    </template>
                                    <input type="number" name="day_after_due" id="day_after_due" class="form-control"
                                        placeholder="{{ __('Enter Days After Expiration') }}"
                                        value="{{ $licenseSettings->day_after_due }}"
                                        :class="errors.day_after_due && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="invoice_created_template" class="form-label">
                                    {{ __('Invoice Created Template') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.invoice_created_template">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.invoice_created_template"></strong>
                                        </span>
                                    </template>
                                    <textarea name="invoice_created_template" id="invoice_created_template" class="form-control"
                                        placeholder="{{ __('Enter Invoice Created Template') }}" :class="errors.invoice_created_template && 'is-invalid'"
                                        required>{{ $licenseSettings->invoice_created_template }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="invoice_reminder_template" class="form-label">
                                    {{ __('Reminder Template') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.invoice_reminder_template">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.invoice_reminder_template"></strong>
                                        </span>
                                    </template>
                                    <textarea name="invoice_reminder_template" id="invoice_reminder_template" class="form-control"
                                        placeholder="{{ __('Enter Reminder Template') }}" :class="errors.invoice_reminder_template && 'is-invalid'"
                                        required>{{ $licenseSettings->invoice_reminder_template }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="invoice_overdue_template" class="form-label">
                                    {{ __('Overdue Template') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.invoice_overdue_template">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.invoice_overdue_template"></strong>
                                        </span>
                                    </template>
                                    <textarea name="invoice_overdue_template" id="invoice_overdue_template" class="form-control"
                                        placeholder="{{ __('Enter Overdue Template') }}" :class="errors.invoice_overdue_template && 'is-invalid'"
                                        required>{{ $licenseSettings->invoice_overdue_template }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="invoice_paid_template" class="form-label">
                                    {{ __('Paid Template') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.invoice_paid_template">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.invoice_paid_template"></strong>
                                        </span>
                                    </template>
                                    <textarea name="invoice_paid_template" id="invoice_paid_template" class="form-control"
                                        placeholder="{{ __('Enter Paid Template') }}" :class="errors.invoice_paid_template && 'is-invalid'" required>{{ $licenseSettings->invoice_paid_template }}</textarea>
                                </div>
                            </div>

                            {{-- Template Variable --}}
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <strong>{{ __('Template Variable') }}:</strong>
                                    <ul>
                                        <li>[name] => {{ __('User Name') }}</li>
                                        <li>[license_name] => {{ __('License Name') }}</li>
                                        <li>[invoice_number] => {{ __('Invoice Number') }}</li>
                                        <li>[invoice_date] => {{ __('Invoice Date') }}</li>
                                        <li>[invoice_due_date] => {{ __('Invoice Due Date') }}</li>
                                        <li>[amount] => {{ __('Invoice Total') }}</li>
                                        <li>[ppn] => {{ __('PPN %') }}</li>
                                        <li>[discount] => {{ __('Discount %') }}</li>
                                        <li>[total] => {{ __('Total Amount with PPN & Discount calculated') }}</li>
                                        <li>[period] => {{ __('subscription period like 01/01/2021 - 01/02/2021') }}</li>
                                        <li>[payment_link] =>
                                            {{ __('Payment Link from active payment gateway which is TriPay') }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                                <button class="btn btn-light-warning" type="reset">Cancel</button>
                                <button class="btn btn-light-success" type="submit" x-ref="button">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div style="margin-top:30px"></div>
            <div class="col-lg-12">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold">Produk Mitra</h5>
                        <p class="card-subtitle mb-4">Manage your Product.</p>
                        <form method="POST" action="{{ route('dinetkan.settings_dinetkan.update.licensemitra') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="ppn_product_mitra" class="form-label">
                                    {{ __('PPN') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.ppn_product_mitra">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.ppn_product_mitra"></strong>
                                        </span>
                                    </template>
                                    <input type="text" name="ppn_product_mitra" id="ppn_product_mitra" class="form-control"
                                        placeholder="{{ __('PPN Product Mitra') }}" value="{{ $licenseSettings->ppn_product_mitra }}"
                                        :class="errors.ppn_product_mitra && 'is-invalid'" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bhp_product_mitra" class="form-label">
                                    {{ __('BHP') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.bhp_product_mitra">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.bhp_product_mitra"></strong>
                                        </span>
                                    </template>
                                    <input type="text" name="bhp_product_mitra" id="bhp_product_mitra" class="form-control"
                                        placeholder="{{ __('PPN Product Mitra') }}" value="{{ $licenseSettings->bhp_product_mitra }}"
                                        :class="errors.bhp_product_mitra && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="uso_product_mitra" class="form-label">
                                    {{ __('USO') }}
                                </label>

                                <div class="input-group">
                                    <template x-if="errors.uso_product_mitra">
                                        <span class="invalid-feedback">
                                            <strong x-text="errors.uso_product_mitra"></strong>
                                        </span>
                                    </template>
                                    <input type="text" name="uso_product_mitra" id="uso_product_mitra" class="form-control"
                                        placeholder="{{ __('PPN Product Mitra') }}" value="{{ $licenseSettings->uso_product_mitra }}"
                                        :class="errors.uso_product_mitra && 'is-invalid'" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                                <button class="btn btn-light-warning" type="reset">Cancel</button>
                                <button class="btn btn-light-success" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
    <!-- Container-fluid Ends-->
@endsection
