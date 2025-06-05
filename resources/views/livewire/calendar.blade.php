<div class="bg-gray-900 min-h-screen flex items-start justify-center px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-7xl space-y-6">
        <h2 class="text-3xl font-bold text-white text-center mt-3" style="margin-bottom: 6px;">Férias 2025</h2>

        <div class="relative">
            <!-- Botões centralizados -->
            <div class="flex justify-center gap-4 mt-1.5 flex-wrap">
                <button wire:click="setFilter('all')" 
                        class="vacation-button {{ $activeFilter === 'all' ? 'active' : 'inactive' }}">
                    Mostrar todas as férias
                </button>
                <button wire:click="setFilter('disi')" 
                        class="vacation-button {{ $activeFilter === 'disi' ? 'active' : 'inactive' }}">
                    Mostrar férias DISI
                </button>
                <button wire:click="setFilter('pe')" 
                        class="vacation-button {{ $activeFilter === 'pe' ? 'active' : 'inactive' }}">
                    Mostrar férias PE
                </button>
                <button wire:click="setFilter('my')" 
                        class="vacation-button {{ $activeFilter === 'my' ? 'active' : 'inactive' }}">
                    Mostrar minhas férias
                </button>
            </div>

            <!-- Ícone flutuando à direita -->
            <div class="flex items-center absolute top-0" style="right: 92px;">

                {{-- Seu megaphone --}}
                <livewire:megaphone />
            
                {{-- Lupa que mostra/oculta o campo --}}
                <button wire:click="toggleSearch" class="ml-3 p-2 hover:bg-gray-200 rounded" type="button" aria-label="Buscar">
                    🔍
                </button>
            
                {{-- Campo de busca só aparece quando showSearch é true --}}
                @if($showSearch)
                    <div class="ml-2 relative">
                        <input
                            type="text"
                            wire:model.debounce.300ms="query"
                            placeholder="Buscar usuários..."
                            class="border rounded px-2 py-1"
                            autofocus
                        />
            
                        @if(!empty($searchResults))
                            <ul class="absolute bg-white border rounded mt-1 max-h-48 overflow-auto w-full z-50">
                                @foreach($searchResults as $result)
                                    <li class="p-2 hover:bg-gray-200 cursor-pointer">
                                        {{ $result['name'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            
            </div>
        </div>

        <div class="flex items-center justify-between mb-3">
            <button wire:click="prevMonths" class="text-3xl p-3 bg-orange-700 text-white rounded-full hover:bg-orange-600 transition" aria-label="Meses anteriores">
                &#8592;
            </button>

            <div class="grid grid-cols-4 gap-6 flex-grow">
                @foreach($monthsData as $monthData)
                    <div class="relative bg-gradient-to-t from-gray-800 to-gray-700 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-center mb-3">{{ $monthData['name'] }} 2025</h3>
                        <div class="grid grid-cols-7 text-center mb-2">
                            @foreach($monthData['daysOfWeek'] as $dayOfWeek)
                                <div class="font-medium text-gray-400" aria-hidden="true">{{ $dayOfWeek }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach($monthData['days'] as $day)
                                <div class="text-center py-2">
                                    @if($day)
                                    @php
    $dayKey = "{$monthData['monthIndex']}-{$day}";
    $isSelected = in_array($dayKey, $selectedDays);
    $isSaved = in_array($dayKey, $savedDays);
    $reservedBy = $reservedDays[$dayKey] ?? null;
    $isHoliday = $showHolidays && $this->isHoliday($monthData['monthIndex'], $day);
@endphp

<button
    wire:click="selectDay({{ $day }}, {{ $monthData['monthIndex'] }})"
    type="button"
    class="day-wrapper
        {{ is_array($reservedBy) && count($reservedBy) > 1 ? 'occupied' : ($reservedBy ? 'saved' : '') }}
        {{ $isSelected ? 'selected' : '' }}
        {{ !$reservedBy && !$isHoliday ? 'free' : '' }}"
    x-data="{
        showHolidays: @entangle('showHolidays'),

        // Feriados Nacionais
        isJan1: {{ $monthData['monthIndex'] === 0 && $day === 1 ? 'true' : 'false' }},
        isApr18: {{ $monthData['monthIndex'] === 3 && $day === 18 ? 'true' : 'false' }},
        isApr21: {{ $monthData['monthIndex'] === 3 && $day === 21 ? 'true' : 'false' }},
        isMay1: {{ $monthData['monthIndex'] === 4 && $day === 1 ? 'true' : 'false' }},
        isSep7: {{ $monthData['monthIndex'] === 8 && $day === 7 ? 'true' : 'false' }},
        isOct12: {{ $monthData['monthIndex'] === 9 && $day === 12 ? 'true' : 'false' }},
        isNov2: {{ $monthData['monthIndex'] === 10 && $day === 2 ? 'true' : 'false' }},
        isNov15: {{ $monthData['monthIndex'] === 10 && $day === 15 ? 'true' : 'false' }},
        isDec25: {{ $monthData['monthIndex'] === 11 && $day === 25 ? 'true' : 'false' }},

        // Feriados Municipais (Rio de Janeiro)
        isJan20: {{ $monthData['monthIndex'] === 0 && $day === 20 ? 'true' : 'false' }},
        isMar1: {{ $monthData['monthIndex'] === 2 && $day === 1 ? 'true' : 'false' }},

        // Feriados Estaduais (Rio de Janeiro)
        isMar4: {{ $monthData['monthIndex'] === 2 && $day === 4 ? 'true' : 'false' }},
        isApr23: {{ $monthData['monthIndex'] === 3 && $day === 23 ? 'true' : 'false' }},
        isNov20: {{ $monthData['monthIndex'] === 10 && $day === 20 ? 'true' : 'false' }},

        // Pontos Facultativos
        isMar3: {{ $monthData['monthIndex'] === 2 && $day === 3 ? 'true' : 'false' }},
        isMar5: {{ $monthData['monthIndex'] === 2 && $day === 5 ? 'true' : 'false' }},
        isJun19: {{ $monthData['monthIndex'] === 5 && $day === 19 ? 'true' : 'false' }},
        isJun20: {{ $monthData['monthIndex'] === 5 && $day === 20 ? 'true' : 'false' }},
        isOct28: {{ $monthData['monthIndex'] === 9 && $day === 28 ? 'true' : 'false' }},
        isDec24: {{ $monthData['monthIndex'] === 11 && $day === 24 ? 'true' : 'false' }},
        isDec31: {{ $monthData['monthIndex'] === 11 && $day === 31 ? 'true' : 'false' }},

        // Feriados extras anteriores
        isFeb5: {{ $monthData['monthIndex'] === 1 && $day === 5 ? 'true' : 'false' }},
        isFeb10: {{ $monthData['monthIndex'] === 1 && $day === 10 ? 'true' : 'false' }},
    }"
    x-bind:style="showHolidays && (
        isJan1 || isApr18 || isApr21 || isMay1 || isSep7 || isOct12 || isNov2 || isNov15 || isDec25 ||
        isJan20 || isMar1 ||
        isMar4 || isApr23 || isNov20 ||
        isMar3 || isMar5 || isJun19 || isJun20 || isOct28 || isDec24 || isDec31 ||
        isFeb5 || isFeb10
    ) ? 'background-color: #3b82f6; color: white;' : ''"
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

            <button wire:click="nextMonths" class="text-3xl p-3 bg-orange-700 text-white rounded-full hover:bg-orange-600 transition" aria-label="Meses seguintes">
                &#8594;
            </button>
        </div>

        <!-- Checkbox: Mostrar feriados -->
        <!-- Checkbox: Mostrar feriados -->
<div 
class="flex items-center justify-center mt-4"
x-data="{ showHolidays: @entangle('showHolidays') }"
>
<input
    type="checkbox"
    id="toggleHolidays"
    x-model="showHolidays"
    class="h-4 w-4 text-orange-600 bg-gray-700 border-gray-600 rounded
           focus:ring-2 focus:ring-orange-500 mr-2"
>
<label for="toggleHolidays" class="text-white select-none cursor-pointer">
    mostrar feriados
</label>
</div>


        <div style="min-height: 60px; margin: 14px auto; max-width: 600px;">
            @if (session()->has('message'))
                @php
                    $isWarning = session('type') === 'warning';
                    $bgColor = $isWarning ? '#facc15' : '#22c55e';
                    $textColor = $isWarning ? '#000' : '#fff';
                @endphp

                <div style="
                    background-color: {{ $bgColor }};
                    color: {{ $textColor }};
                    padding: 16px 24px;
                    border-radius: 8px;
                    font-weight: bold;
                    text-align: center;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    transition: opacity 0.3s ease;
                ">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        @if (!$vacationRequestSent)
            <div class="text-center text-white font-semibold">
                @if ($remainingDays === 0)
                    <p>Os 5 dias de férias já foram selecionados.</p>
                @else
                    <p>Selecione mais {{ $remainingDays }} dia(s) de férias.</p>
                @endif
            </div>

            <div class="flex flex-col items-center mt-3 gap-4">

                <!-- Botões lado a lado -->
                <div class="flex gap-4 flex-wrap justify-center">
                    <button wire:click="clearSelectedDays" class="vacation-button inactive">
                        Limpar seleção atual de dias
                    </button>
            
                    <button 
                        wire:click="sendVacationRequestAndNotify" 
                        class="vacation-button {{ $remainingDays === 0 ? 'active' : 'inactive' }}" 
                        {{ $remainingDays !== 0 ? 'disabled' : '' }}>
                        Enviar pedido de férias
                    </button>

                </div>
            
                <!-- Botão abaixo -->
                @if($activeFilter === 'my' && count($savedDays) > 0)
                    <div>
                        <button 
                            wire:click="deleteUserVacationDays"
                            onclick="if(!confirm('Tem certeza que deseja deletar seus dias de férias? Esta ação não pode ser desfeita.')) event.stopImmediatePropagation();"
                            class="text-white bg-red-600 hover:bg-red-700 py-2 px-4 rounded-md transition"
                        >
                            Deletar do BD os dias de férias do usuário logado
                        </button>
                    </div>
                @endif
            
            </div>
            
        @else
            {{-- Aqui você pode colocar uma mensagem pós envio --}}
        @endif

    </div>

    {{-- Botões Font Awesome fixos, acima da legenda --}}
    <div
    class="fixed z-[1000] flex flex-row items-center gap-4"
    style="
        right: 80px;
        bottom: 200px;
        background-color: rgba(31, 41, 55, 0.9);
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        color: white;
        width: auto;
    "
>
    <div class="relative group">
        <i class="fa-regular fa-file-pdf fa-2x cursor-pointer"></i>
        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-0 pointer-events-none whitespace-nowrap z-50">
            Baixar PDF
        </div>
    </div>

    <div class="relative group">
        <i class="fa-regular fa-envelope fa-2x cursor-pointer"></i>
        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-0 pointer-events-none whitespace-nowrap z-50">
            Enviar Email
        </div>
    </div>

    <div class="relative group">
        <i class="fa-solid fa-file-excel fa-2x cursor-pointer"></i>
        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-0 pointer-events-none whitespace-nowrap z-50">
            Baixar Excel
        </div>
    </div>
</div>

    {{-- Container fixo da legenda, abaixo dos ícones --}}
    <div style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgba(31, 41, 55, 0.9);
        padding: 12px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        color: white;
        font-weight: 600;
        font-size: 14px;
        min-width: 220px;
        z-index: 1000;
        ">
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
