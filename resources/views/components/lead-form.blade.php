@props(['variant' => 'top', 'withMessage' => false])

@php
    $isFooter = $variant === 'footer';
    $formId = $isFooter ? 'name-adding-form' : 'name-adding-form-new';
    $inputClass = $isFooter ? 'underline-input' : 'underline-input-new';
    $submitId = $isFooter ? 'submit-button' : 'submit-button-new';
@endphp

<form id="{{ $formId }}" data-lead-form method="POST" action="{{ route('lead-submissions.store') }}">
    @csrf

    <div class="form-group">
        <label for="{{ $formId }}-name">NAME</label>
        <input type="text" id="{{ $formId }}-name" name="name" value="{{ old('name') }}" class="{{ $inputClass }}" required>
    </div>
    <div class="form-group">
        <label for="{{ $formId }}-company">COMPANY NAME</label>
        <input type="text" id="{{ $formId }}-company" name="company_name" value="{{ old('company_name') }}" class="{{ $inputClass }}" required>
    </div>
    <div class="form-group">
        <label for="{{ $formId }}-city">CITY</label>
        <input type="text" id="{{ $formId }}-city" name="city" value="{{ old('city') }}" class="{{ $inputClass }}" required>
    </div>
    <div class="form-group">
        <label for="{{ $formId }}-phone">DIRECT NUMBER</label>
        <input type="tel" id="{{ $formId }}-phone" name="phone" value="{{ old('phone') }}" class="{{ $inputClass }}" required>
    </div>
    <div class="form-group">
        <label for="{{ $formId }}-email">EMAIL-ID</label>
        <input type="email" id="{{ $formId }}-email" name="email" value="{{ old('email') }}" class="{{ $inputClass }}" required>
    </div>

    @if ($withMessage)
        <div class="form-group">
            <label for="{{ $formId }}-message">MESSAGE BOX</label>
            <input type="text" id="{{ $formId }}-message" name="message" value="{{ old('message') }}" class="{{ $inputClass }}">
        </div>
    @endif

    <div class="form-group">
        <label for="{{ $formId }}-captcha">CAPTCHA</label>
        <div id="captcha-container" data-captcha-container>
            <span id="captcha-text" data-captcha-text aria-live="polite"></span>
            <button type="button" id="refresh-captcha" data-refresh-captcha>Refresh</button>
        </div>
        <input type="text" id="{{ $formId }}-captcha" name="captcha" class="underline-input" autocomplete="off" required>
    </div>

    <input type="hidden" name="captcha_key" value="">
    <input type="hidden" name="source_url" value="{{ url()->current() }}">
    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">

    @if ($errors->any())
        <div role="alert" style="color:#fff;margin:8px 0">{{ $errors->first() }}</div>
    @elseif (session('lead_submission_success'))
        <div role="status" style="color:#fff;margin:8px 0">{{ session('lead_submission_success') }}</div>
    @endif

    <button id="{{ $submitId }}" type="submit">SUBMIT</button>
</form>

@once
    <script src="{{ asset('script/lead-form.js') }}?v=2" defer></script>
@endonce
