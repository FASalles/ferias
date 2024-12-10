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
                                    <span wire:click="selectDay({{ $day }}, {{ $monthData['monthIndex'] }})"
                                          class="inline-block w-8 h-8 text-center leading-8 cursor-pointer 
                                          {{ in_array("{$monthData['monthIndex']}-{$day}", $selectedDays) ? 'bg-green-500 text-white' : 'bg-gray-600 text-white' }} 
                                          rounded-full hover:bg-orange-500 transition">
                                        {{ $day }}
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

        <!-- Seção com checkboxes para DISI e PE -->
        <div class="flex items-center mt-6">
            <div class="flex items-center space-x-8">
                <div class="flex items-center">
                    <input type="checkbox" id="disi" class="h-5 w-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="disi" class="ml-2 text-gray-300 text-lg font-medium">Mostrar férias DISI</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="pe" class="h-5 w-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="pe" class="ml-2 text-gray-300 text-lg font-medium">Mostrar férias PE</label>
                </div>
            </div>
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
                <!-- Botão estilizado -->
                <button wire:click="sendVacationRequest"
                        class="px-6 py-3 bg-orange-600 text-white rounded-full text-xl font-semibold hover:bg-orange-500 transition">
                    Enviar solicitação de férias
                </button>
            @else
                <!-- Espaço reservado para manter a altura -->
                <div class="inline-block px-6 py-3 invisible"></div>
            @endif
        </div>

        <!-- Mensagem de sucesso após a solicitação -->
        @if (session()->has('message'))
            <div class="mt-6 text-center text-green-400 font-semibold">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>
