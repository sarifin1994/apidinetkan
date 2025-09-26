@extends('backend.layouts.app')

@section('title', __('My Profile'))

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>{{ __('My Profile') }}</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg>
              </a>
            </li>
            <li class="breadcrumb-item">{{ __('Account') }}</li>
            <li class="breadcrumb-item active">{{ __('My Profile') }}</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6 d-flex align-items-stretch">
        <div class="card w-100 position-relative overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title fw-semibold">{{ __('Profile') }}</h5>
            <p class="card-subtitle mb-4">{{ __('Please complete or update your personal information') }}</p>
            <form x-load x-data="form" method="POST" data-method="patch">
              <template x-if="success">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>{{ __('Success') }}!</strong> {{ __('Your profile has been updated.') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </template>

              @csrf
              <div class="row mb-4">
                <div class="col-md-12 mb-3">
                  <label for="name" class="form-label">
                    <x-lucide-user-round class="me-1" height="14" width="14" /> {{ __('Name') }}
                  </label>

                  <div class="input-group">
                    <template x-if="errors.name">
                      <span class="invalid-feedback">
                        <strong x-text="errors.name"></strong>
                      </span>
                    </template>
                    <input type="text" name="name" id="name" class="form-control"
                      placeholder="{{ __('Enter your name') }}" value="{{ $user->name }}"
                      :class="errors.name && 'is-invalid'" required>
                  </div>
                </div>

                <div class="col-md-12">
                  <label for="email" class="form-label">
                    <x-lucide-mail class="me-1" height="14" width="14" /> {{ __('Email') }}
                  </label>

                  <div class="input-group">
                    <input type="email" name="email" id="email" class="form-control"
                      placeholder="{{ __('Enter your email') }}" value="{{ $user->email }}" readonly>
                  </div>
                  <small class="text-muted d-block">{{ __('You cannot change your email address.') }}</small>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                <button class="btn btn-warning" type="reset">Cancel</button>
                <button class="btn btn-success" type="submit" x-ref="button">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6 d-flex align-items-stretch">
        <div class="card w-100 position-relative overflow-hidden">
          <div class="card-body p-4">
            <h5 class="card-title fw-semibold">{{ __('Change Password') }}</h5>
            <p class="card-subtitle mb-4">{{ __('You can change your password here') }}</p>

            <form x-load x-data="form" method="POST" data-method="patch">
              <template x-if="success">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>{{ __('Success') }}!</strong> {{ __('Your password has been changed.') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </template>

              @csrf
              <div class="row mb-4">
                <div class="col-md-12 mb-3">
                  <label for="current_password" class="form-label">
                    <x-lucide-lock class="me-1" height="14" width="14" /> {{ __('Current Password') }}
                  </label>

                  <div class="input-group">
                    <template x-if="errors.current_password">
                      <span class="invalid-feedback">
                        <strong x-text="errors.current_password"></strong>
                      </span>
                    </template>
                    <input type="password" name="current_password" id="current_password" class="form-control"
                      :class="errors.current_password && 'is-invalid'"
                      placeholder="{{ __('Enter your current password') }}" required>
                  </div>
                </div>

                <div class="col-md-12 mb-3">
                  <label for="password" class="form-label">
                    <x-lucide-lock class="me-1" height="14" width="14" /> {{ __('New Password') }}
                  </label>

                  <div class="input-group">
                    <template x-if="errors.password">
                      <span class="invalid-feedback">
                        <strong x-text="errors.password"></strong>
                      </span>
                    </template>
                    <input type="password" name="password" id="password" class="form-control"
                      :class="errors.password && 'is-invalid'" placeholder="{{ __('Enter your new password') }}"
                      required>
                  </div>
                </div>

                <div class="col-md-12 mb-3">
                  <label for="password_confirmation" class="form-label">
                    <x-lucide-lock class="me-1" height="14" width="14" /> {{ __('Confirm Password') }}
                  </label>

                  <div class="input-group">
                    <template x-if="errors.password_confirmation">
                      <span class="invalid-feedback">
                        <strong x-text="errors.password_confirmation"></strong>
                      </span>
                    </template>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                      class="form-control" :class="errors.password_confirmation && 'is-invalid'"
                      placeholder="{{ __('Repeat your password') }}" required>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-end mt-4 gap-3">
                <button class="btn btn-warning" type="reset">Cancel</button>
                <button class="btn btn-success" type="submit" x-ref="button">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
