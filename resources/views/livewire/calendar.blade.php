<div class="bg-gray-900 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-7xl space-y-8">
        <!-- Navegação entre meses -->
        <div class="flex justify-between items-center mb-4">
            <!-- Botão de voltar -->
            <button wire:click="prevMonths" class="text-3xl p-3 bg-orange-700 text-white rounded-full hover:bg-orange-600 transition">
                &#8592; <!-- Setas para a esquerda -->
            </button>

            <!-- Título do calendário -->
            <h2 class="text-3xl font-bold text-white">Férias 2025</h2>

            <!-- Botão de avançar -->
            <button wire:click="nextMonths" class="text-3xl p-3 bg-orange-700 text-white rounded-full hover:bg-orange-600 transition">
                &#8594; <!-- Setas para a direita -->
            </button>
        </div>

        <!-- Centralização dos Botões -->
        <div class="flex justify-center space-x-8">
            <!-- Botão Mostrar Todas as Férias -->
            <button 
                wire:click="showAllVacations"
                class="vacation-button px-6 py-3 rounded-md border transition-colors duration-300
                    {{ !$showDisiVacations && !$showPeVacations ? 'bg-green-500 text-white' : 'bg-gray-500 text-black' }}"
            >
                Mostrar todas as férias
            </button>
    
            <!-- Botão Mostrar Férias DISI -->
            <button 
                wire:click="showDISIVacations"
                class="vacation-button px-6 py-3 rounded-md border transition-colors duration-300
                    {{ $showDisiVacations ? 'bg-green-500 text-white' : 'bg-gray-500 text-black' }}"
            >
                Mostrar férias DISI
            </button>
    
            <!-- Botão Mostrar Férias PE -->
            <button 
                wire:click="showPEVacations"
                class="vacation-button px-6 py-3 rounded-md border transition-colors duration-300
                    {{ $showPeVacations ? 'bg-green-500 text-white' : 'bg-gray-500 text-black' }}"
            >
                Mostrar férias PE
            </button>
        </div>

        <!-- Grid de meses -->
        <div class="grid grid-cols-4 gap-6">
            @foreach($monthsData as $monthData)
                <div class="relative bg-gradient-to-t from-gray-800 to-gray-700 text-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-center mb-4">{{ $monthData['name'] }} 2025</h3>
                    <div class="grid grid-cols-7 text-center mb-2">
                        @foreach($monthData['daysOfWeek'] as $dayOfWeek)
                            <div class="font-medium text-gray-400">{{ $dayOfWeek }}</div>
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
                                    @endphp

                                    <span wire:click="selectDay({{ $day }}, {{ $monthData['monthIndex'] }})"
                                        class="relative inline-block w-8 h-8 text-center leading-8 cursor-pointer rounded-full 
                                        {{ $isSaved ? 'bg-green-500 text-white' : ($isSelected ? 'bg-green-500 text-white' : 'bg-gray-600 text-white') }} 
                                        hover:bg-orange-500 transition group">

                                        {{ $day }}

                                        @if($reservedBy)
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg z-50 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200">
                                                {{ $reservedBy }}
                                            </div>
                                        @endif
                                    </span>
                                @else
                                    <span></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Exibir a quantidade de dias restantes -->
        <div class="mt-6 text-center text-xl font-semibold text-gray-300">
            Você deve selecionar todos os 30 dias de férias.
            <br>
            Dias restantes a serem selecionados: 
            <span class="text-green-400">{{ $remainingDays }}</span>
        </div>

        <!-- Texto para solicitar férias (aparece após 3 dias selecionados) -->
        <div class="mt-6 text-center" style="min-height: 72px;"> <!-- Espaço reservado com altura mínima -->
            @if(count($selectedDays) >= 3)
                <button wire:click="sendVacationRequest"
                        class="px-6 py-3 bg-orange-600 text-white rounded-full text-xl font-semibold hover:bg-orange-500 transition">
                    Enviar solicitação de férias
                </button>
            @else
                <div class="inline-block px-6 py-3 invisible"></div>
            @endif
        </div>

        @if(count($selectedDays) >= 3)
            <div class="mt-4 text-center text-yellow-400 font-medium">
                Você atingiu o limite de 3 dias selecionados.
            </div>
        @endif

        <!-- Mensagem de sucesso após a solicitação -->
        @if (session()->has('message'))
            <div class="mt-6 text-center text-green-400 font-semibold">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>
