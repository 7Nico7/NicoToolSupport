{{-- file: resources/views/components/demo/input.blade.php --}}
@props([
    'name',
    'label'       => null,
    'type'        => 'text',
    'value'       => null,
    'validate'    => null,
    'placeholder' => '',
    'mask'        => null,
    'required'    => false,
    'class'       => '',
    'col'         => null,
    'hint'        => null,
])

@php
    $value    = $value ?? old($name);
    $hasError = $errors->has($name);
    $baseInput = 'w-full px-3.5 py-2.5 rounded-xl border text-sm transition-colors '
               . 'bg-surface-light dark:bg-surface-dark text-gray-900 dark:text-white '
               . 'placeholder-gray-400 dark:placeholder-gray-600 '
               . 'focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand ';
    $borderCls = $hasError
        ? 'border-danger focus:border-danger focus:ring-danger/30'
        : 'border-gray-200 dark:border-white/[0.10]';
@endphp

<div class="{{ $col ? 'col-span-'.$col : '' }} {{ $class }}">
    @if($label)
        <label for="{{ $name }}"
               class="block text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="{{ $baseInput }} {{ $borderCls }} js-validated"
        data-validate="{{ $validate }}"
        data-mask="{{ $mask }}"
        @if($required) required @endif
        aria-describedby="{{ $name }}-error"
    />

    @error($name)
        <p class="mt-1.5 text-xs text-danger flex items-center gap-1">
            <span class="material-symbols-outlined text-[13px]">error</span>
            {{ $message }}
        </p>
    @else
        <small id="{{ $name }}-error"
               class="js-error mt-1.5 text-xs text-danger hidden flex items-center gap-1"></small>
    @enderror

    @if($hint && !$hasError)
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $hint }}</p>
    @endif
</div>
