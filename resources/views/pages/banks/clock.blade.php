<x-app-layout>
    <div class="md:h-[90vh] flex flex-col justify-center items-center">
        <div class="pt-10 md:pt-0 w-full flex justify-center items-center  text-white">
            <div class="u-clock" id="clock">00:00:00</div>
        </div>

        <div class="pt-10 md:pt-32 w-full flex justify-center items-center  text-white">
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                @if (count($schedules) > 0)
                    @foreach($schedules as $schedule)
                        <div class="u-clock">
                            <x-input-label for="schedule_time_{{ $loop->index }}" :value="__('Schedule Time') . ' ' . ($loop->index + 1)" />
                            <x-text-input id="schedule_time_{{ $loop->index }}" name="times[]" type="time" class="mt-1 block w-full" disabled :value="$schedule->hour" autocomplete="schedule_time_{{ $loop->index }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('schedule_time_' . $loop->index)" />
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>


    <script>
        function updateClock() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');

            document.getElementById('clock').textContent = `${horas}:${minutos}:${segundos}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-app-layout>
