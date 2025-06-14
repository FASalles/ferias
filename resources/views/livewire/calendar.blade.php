<div class="flex items-start justify-center min-h-screen px-4 bg-gray-900 sm:px-6 lg:px-8">
    <div class="w-full space-y-6 max-w-7xl">
        <h2 class="mt-3 text-3xl font-bold text-center text-white" style="margin-bottom: 6px;">Férias 2025</h2>

        <div class="relative">
            <!-- Botões centralizados -->
            <div class="flex justify-center gap-4 mt-1.5 flex-wrap">
                <button wire:click="setFilter('all')" class="vacation-button {{ $activeFilter === 'all' ? 'active' : 'inactive' }}">
                    Mostrar todas as férias
                </button>
                <button wire:click="setFilter('disi')" class="vacation-button {{ $activeFilter === 'disi' ? 'active' : 'inactive' }}">
                    Mostrar férias DISI
                </button>
                <button wire:click="setFilter('pe')" class="vacation-button {{ $activeFilter === 'pe' ? 'active' : 'inactive' }}">
                    Mostrar férias PE
                </button>
                <button wire:click="setFilter('my')" class="vacation-button {{ $activeFilter === 'my' ? 'active' : 'inactive' }}">
                    Mostrar minhas férias
                </button>
            </div>

            <!-- Ícones flutuando à direita -->
            <div class="absolute top-0 flex items-center gap-x-4" style="right: 1px;">
                <div class="relative inline-block ml-3">
                    <!-- Cabeça pequena -->
                    <select wire:model="selectedUser"
    style="width: 5rem;"
    class="py-1 pl-2 pr-2 text-sm leading-tight text-center text-white bg-gray-800 border border-gray-600 rounded-md focus:ring-orange-500 focus:border-orange-500">
                        <option value="">👤</option>
                        @foreach ($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                </div>






                <livewire:megaphone />
            </div>
        </div>

        @if ($userHasVacation)

@endif








        <div class="flex items-center justify-between mb-3">
            <button wire:click="prevMonths" class="p-3 text-3xl text-white transition bg-orange-700 rounded-full hover:bg-orange-600" aria-label="Meses anteriores">
                &#8592;
            </button>

            <div class="grid flex-grow grid-cols-4 gap-6">
                @foreach($monthsData as $monthData)
                    <div class="relative p-6 text-white rounded-lg shadow-lg bg-gradient-to-t from-gray-800 to-gray-700">
                        <h3 class="mb-3 text-xl font-semibold text-center">{{ $monthData['name'] }} 2025</h3>
                        <div class="grid grid-cols-7 mb-2 text-center">
                            @foreach($monthData['daysOfWeek'] as $dayOfWeek)
                                <div class="font-medium text-gray-400" aria-hidden="true">{{ $dayOfWeek }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach($monthData['days'] as $day)
                                <div class="py-2 text-center">
                                    @if($day)
                                        @php
                                            $dayKey = "{$monthData['monthIndex']}-{$day}";
                                            $isSelected = in_array($dayKey, $selectedDays);
                                            $isSaved = in_array($dayKey, $savedDays);
                                            $isUserVacationDay = in_array($dayKey, $savedDays);
                                            $reservedBy = $reservedDays[$dayKey] ?? [];

                                            $isHoliday = $showHolidays && $this->isHoliday($monthData['monthIndex'], $day);
                                        @endphp

                                        <button
                                            wire:click="selectDay({{ $day }}, {{ $monthData['monthIndex'] }})"
                                            type="button"
                                            @if($userHasVacation) disabled @endif
                                            class="day-wrapper
                                            {{ is_array($reservedBy) && count($reservedBy) > 1 ? 'occupied' : '' }}
                                            {{ is_array($reservedBy) && count($reservedBy) === 1 ? 'saved' : '' }}
                                            {{ $isSelected ? 'selected' : '' }}
                                            {{ $isUserVacationDay ? 'user-vacation-day' : '' }}
                                            {{ $userHasVacation ? 'cursor-not-allowed opacity-50' : '' }}"
                                            x-data="{
                                                showHolidays: @entangle('showHolidays'),
                                                isHoliday: false,
                                                init() {
                                                    const m = {{ $monthData['monthIndex'] }};
                                                    const d = {{ $day }};
                                                    const holidays = [
                                                        '0-1', '3-18', '3-21', '4-1', '8-7', '9-12', '10-2', '10-15', '11-25',
                                                        '0-20', '2-1',
                                                        '2-4', '3-23', '10-20',
                                                        '2-3', '2-5', '5-19', '5-20', '9-28', '11-24', '11-31',
                                                        '1-5', '1-10'
                                                    ];
                                                    this.isHoliday = holidays.includes(`${m}-${d}`);
                                                }
                                            }"
                                            x-init="init()"
                                            x-bind:style="showHolidays && isHoliday ? 'background-color: #3b82f6; color: white;' : ''"
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                            aria-label="Dia {{ $day }} de {{ $monthData['name'] }} {{ $reservedBy ? ' - reservado' : ' - disponível para seleção' }}"
                                        >
                                            {{ $day }}

                                            @if($reservedBy)
                                                <div class="tooltip" role="tooltip" aria-hidden="true">
                                                    {{ implode(', ', $reservedBy) }}
                                                </div>
                                            @endif
                                        </button>
                                    @else
                                        <span aria-hidden="true">&nbsp;</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button wire:click="nextMonths" class="p-3 text-3xl text-white transition bg-orange-700 rounded-full hover:bg-orange-600" aria-label="Meses seguintes">
                &#8594;
            </button>
        </div>

        <!-- Checkbox: Mostrar feriados -->
        <div class="flex items-center justify-center gap-2 mt-4" x-data="{ showHolidays: @entangle('showHolidays') }">
            <input
                type="checkbox"
                id="toggleHolidays"
                x-model="showHolidays"
                class="w-4 h-4 text-orange-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-orange-500"
            >
            <label for="toggleHolidays" class="text-white cursor-pointer select-none">mostrar feriados</label>
        </div>

        <!-- Mensagem -->
<!-- Mensagem -->
<div
    x-data="{ showMessage: false }"
    x-init="setTimeout(() => showMessage = true, 500)"
    style="min-height: 60px; margin: 14px auto; max-width: 600px;"
>
    @if (session()->has('message'))
        @php
            $type = session('type', 'success');
            switch ($type) {
                case 'warning':
                    $bgColor = '#facc15';
                    $textColor = '#000';
                    break;
                case 'primary':
                    $bgColor = '#3b82f6';
                    $textColor = '#fff';
                    break;
                case 'error':
                    $bgColor = '#ef4444';
                    $textColor = '#fff';
                    break;
                case 'info':
                    $bgColor = '#0ea5e9';
                    $textColor = '#fff';
                    break;
                default:
                    $bgColor = '#22c55e';
                    $textColor = '#fff';
                    break;
            }
        @endphp
        <div
            x-show="showMessage"
            x-transition.opacity.duration.700ms
            style="background-color: {{ $bgColor }};
                   color: {{ $textColor }};
                   padding: 16px 24px;
                   border-radius: 8px;
                   font-weight: bold;
                   text-align: center;
                   box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);"
        >
            {{ session('message') }}
        </div>
    @elseif ($userHasVacation)
        <div
            x-show="showMessage"
            x-transition.opacity.duration.700ms
            style="background-color: #1e40af;
                   color: #ffffff;
                   padding: 16px 24px;
                   border-radius: 8px;
                   font-weight: bold;
                   text-align: center;
                   box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);"
        >
            As suas férias já estão marcadas.
        </div>
    @endif
</div>



        <!-- Instruções de seleção -->
        <div class="font-semibold text-center text-white">
            @if (!$userHasVacation)
                @if ($remainingDays === 0)
                    <p>Os 5 dias de férias já foram selecionados.</p>
                @else
                    <p>Selecione mais {{ $remainingDays }} dia(s) de férias.</p>
                @endif
            @endif
        </div>

        <div class="flex flex-col items-center gap-4 mt-3">
            <div class="flex flex-wrap justify-center gap-4">
                <button
    wire:click="clearSelectedDays"
    class="vacation-button
        {{ $userHasVacation || count($selectedDays) === 0 ? 'inactive opacity-50 cursor-not-allowed' : 'inactive' }}"
    {{ $userHasVacation || count($selectedDays) === 0 ? 'disabled' : '' }}>
    Limpar seleção atual de dias
</button>

                <button
                    wire:click="sendVacationRequestAndNotify"
                    class="vacation-button {{ $remainingDays === 0 && !$userHasVacation ? 'active' : 'inactive opacity-50 cursor-not-allowed' }}"
                    {{ $remainingDays !== 0 || $userHasVacation ? 'disabled' : '' }}>
                    Enviar pedido de férias
                </button>
            </div>

            @if($activeFilter === 'my' && count($savedDays) > 0)
                <div>
                    <button
                        wire:click="deleteUserVacationDays"
                        onclick="if(!confirm('Tem certeza que deseja deletar seus dias de férias? Esta ação não pode ser desfeita.')) event.stopImmediatePropagation();"
                        class="px-4 py-2 text-white transition bg-red-600 rounded-md hover:bg-red-700"
                    >
                        Deletar do BD os dias de férias do usuário logado
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Botões Font Awesome fixos -->
    <div class="fixed z-[1000] flex flex-row items-center gap-4" style="right: 80px; bottom: 350px; background-color: rgba(31, 41, 55, 0.9); padding: 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); color: white;">
        <div class="relative group">
            <i class="cursor-pointer fa-regular fa-file-pdf fa-2x"></i>
            <div class="absolute z-50 px-2 py-1 text-xs text-white transition-opacity transform -translate-x-1/2 bg-gray-700 rounded opacity-0 pointer-events-none -bottom-8 left-1/2 group-hover:opacity-100 duration-0 whitespace-nowrap">
                Baixar PDF
            </div>
        </div>
        <div class="relative group">
            <i class="cursor-pointer fa-regular fa-envelope fa-2x"></i>
            <div class="absolute z-50 px-2 py-1 text-xs text-white transition-opacity transform -translate-x-1/2 bg-gray-700 rounded opacity-0 pointer-events-none -bottom-8 left-1/2 group-hover:opacity-100 duration-0 whitespace-nowrap">
                Enviar Email
            </div>
        </div>
        <div class="relative group">
            <i class="cursor-pointer fa-solid fa-file-excel fa-2x"></i>
            <div class="absolute z-50 px-2 py-1 text-xs text-white transition-opacity transform -translate-x-1/2 bg-gray-700 rounded opacity-0 pointer-events-none -bottom-8 left-1/2 group-hover:opacity-100 duration-0 whitespace-nowrap">
                Baixar Excel
            </div>
        </div>
    </div>

    <!-- Legenda -->
    <div style="position: fixed; bottom: 120px; right: 20px; background: rgba(31, 41, 55, 0.9); padding: 12px 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); color: white; font-weight: 600; font-size: 14px; min-width: 220px; z-index: 1000;">
        <div class="legend-item" tabindex="0" aria-describedby="tooltip-feriado">
            <span class="legend-dot" style="background-color: #3b82f6;"></span>
            <span>Feriados</span>
            <div role="tooltip" id="tooltip-feriado" class="tooltip-legend">
                Dias de feriado nacional, estadual<br> ou municipal.
            </div>
        </div>
        <div class="legend-item" tabindex="0" aria-describedby="tooltip-naoclicado">
            <span class="legend-dot" style="background-color: #6b7280;"></span>
            <span>Dia Não Clicado</span>
            <div role="tooltip" id="tooltip-naoclicado" class="tooltip-legend">
                Dias disponíveis que você ainda<br> não selecionou.
            </div>
        </div>
        <div class="legend-item" tabindex="0" aria-describedby="tooltip-livre">
            <span class="legend-dot" style="background-color: #22c55e;"></span>
            <span>Dia Livre</span>
            <div role="tooltip" id="tooltip-livre" class="tooltip-legend">
                Dias disponíveis para seleção,<br> sem reserva atual.
            </div>
        </div>
        <div class="legend-item" tabindex="0" aria-describedby="tooltip-reservado">
            <span class="legend-dot" style="background-color: #facc15;"></span>
            <span>Dia Reservado</span>
            <div role="tooltip" id="tooltip-reservado" class="tooltip-legend">
                Dia reservado para algum usuário, <br> verifique se não é de seu turno<br> ou de sua equipe.
            </div>
        </div>
        <div class="legend-item" tabindex="0" aria-describedby="tooltip-ocupado">
            <span class="legend-dot" style="background-color: #ef4444;"></span>
            <span>Dia Ocupado</span>
            <div role="tooltip" id="tooltip-ocupado" class="tooltip-legend">
                Dia ocupado por mais de um usuário,<br>indisponível para seleção.
            </div>
        </div>
    </div>
</div>
