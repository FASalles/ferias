<div x-data="{
        userName: @entangle('userName'), 
        promptVisible: true, 
        currentMonthIndex: 0,  // Indice atual dos meses
        monthsVisible: [],  // Meses visíveis na tela
        totalMonths: @js($months),  // Todos os meses
        monthsToShow: 3,  // Quantidade de meses por vez
        
        // Inicializa os meses visíveis
        init() {
            this.updateVisibleMonths();
        },
        
        // Atualiza os meses visíveis conforme o índice
        updateVisibleMonths() {
            this.monthsVisible = this.totalMonths.slice(this.currentMonthIndex, this.currentMonthIndex + this.monthsToShow);
        },

        // Avança para o próximo conjunto de meses
        nextMonths() {
            if (this.currentMonthIndex + this.monthsToShow < this.totalMonths.length) {
                this.currentMonthIndex += this.monthsToShow;
                this.updateVisibleMonths();
            }
        },

        // Volta para o conjunto anterior de meses
        prevMonths() {
            if (this.currentMonthIndex > 0) {
                this.currentMonthIndex -= this.monthsToShow;
                this.updateVisibleMonths();
            }
        },

        // Função que pergunta o nome do usuário
        askName() {
            const name = prompt('Qual é o seu nome?');
            if (name) {
                this.userName = name;
                this.promptVisible = false;
                alert('Olá, ' + name + '! Bem-vindo ao calendário!');
            }
        }
    }" 
    x-init="init()" class="p-6">
    
    <div class="flex justify-center mb-6">
        <!-- Botão de seta esquerda -->
        <button @click="prevMonths()" class="bg-indigo-500 text-white p-1.5 rounded-full hover:bg-indigo-400 transition-all">
            &#8592;
        </button>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mx-6">
            <!-- Exibe os 3 meses visíveis -->
            <template x-for="(month, index) in monthsVisible" :key="index">
                <div class="border rounded-lg shadow-lg p-4 bg-gradient-to-b from-gray-800 to-gray-900 text-white">
                    <h2 class="text-xl font-bold text-center text-indigo-300" x-text="month.name"></h2>
                    <div class="grid grid-cols-7 gap-1 mt-4 text-sm">
                        <!-- Dias da semana -->
                        <div class="font-bold text-center text-gray-400">Dom</div>
                        <div class="font-bold text-center text-gray-400">Seg</div>
                        <div class="font-bold text-center text-gray-400">Ter</div>
                        <div class="font-bold text-center text-gray-400">Qua</div>
                        <div class="font-bold text-center text-gray-400">Qui</div>
                        <div class="font-bold text-center text-gray-400">Sex</div>
                        <div class="font-bold text-center text-gray-400">Sáb</div>

                        <!-- Espaços vazios para os primeiros dias -->
                        <template x-for="i in month.firstDayOfWeek" :key="i">
                            <div></div>
                        </template>

                        <!-- Dias do mês -->
                        <template x-for="(day, dayIndex) in month.days" :key="dayIndex">
                            <div class="text-center p-2 border rounded-md bg-gray-700 hover:bg-indigo-500 text-indigo-100 font-medium cursor-pointer transition-all duration-200">
                                <span x-text="day"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Botão de seta direita -->
        <button @click="nextMonths()" class="bg-indigo-500 text-white p-1.5 rounded-full hover:bg-indigo-400 transition-all">
            &#8594;
        </button>
    </div>
</div>
