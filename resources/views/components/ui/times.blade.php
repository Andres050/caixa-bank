@php
    $color = 'bg-success';

    if (isset($defaultBg)) {
        $color = $defaultBg;
    } else {
        if (!isset($count)) {
            $count = 0;
        }
        if (!isset($maxTimes)) {
            $maxTimes = \App\Models\ScheduledTasks::$MAX_TIMES;
        }
        if (!isset($warningTimes)) {
            $warningTimes = \App\Models\ScheduledTasks::$WARNING_TIMES;
        }

        if ($count >= $maxTimes) {
            $color = 'bg-error';
        } elseif ($count >= $warningTimes) {
            $color = 'bg-warning';
        }
    }
@endphp

<span class="{{ $color }} text-main1 text-xs font-semibold px-2.5 py-0.5 rounded-full">
    {{ $count }} {{ __('times') }}
</span>
