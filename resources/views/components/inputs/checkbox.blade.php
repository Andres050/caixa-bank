<div class="switch-wrapper {{ $class ?? '' }}">
    <input type="checkbox" name="{{ $name }}" id="{{ $id }}" class="switch-checkbox" @if ($active) checked @endif />
    <label class="switch-label" for="{{ $id }}"></label>
</div>
