<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\User;
use App\Models\VacationRequest;
use MBarlow\Megaphone\Types\Important;

class Calendar extends Component
{
    public $year = 2025;
    public $startMonth = 0;
    public $monthsPerRow = 4;
    public $selectedDays = [];
    public $remainingDays = 5;
    public $savedDays = [];

    public bool $userHasVacation = false;

    public $users = [];   // Array para armazenar os usuários do banco
    public $selectedUser = ''; // Para armazenar o usuário selecionado

    public $activeFilter = null;

    public $vacationRequestSent = false;

    public $showSearch = false;
    public $query = '';
    public $searchResults = [];

    public bool $showHolidays = false;

    private function checkUserHasVacation()
{
    $this->userHasVacation = VacationRequest::where('user_id', auth()->id())
        ->whereIn('status', ['pending', 'approved'])
        ->exists();
}

    private array $holidays = [
        // Janeiro
        '0-1',  // Confraternização Universal
        '0-20', // Dia de São Sebastião (RJ)

        // Fevereiro
        '1-3',  // Carnaval (segunda-feira)
        '1-4',  // Carnaval (terça-feira)
        '1-5',  // Quarta-feira de cinzas (ponto facultativo até 14h)

        // Março
        // Nenhum

        // Abril
        '3-18', // Paixão de Cristo (sexta-feira santa)
        '3-21', // Tiradentes

        // Maio
        '4-1',  // Dia do Trabalho

        // Junho
        '5-19', // Corpus Christi (ponto facultativo)

        // Julho
        // Nenhum

        // Agosto
        // Nenhum

        // Setembro
        '8-7',  // Independência do Brasil

        // Outubro
        '9-12', // Nossa Senhora Aparecida
        '9-28', // São Judas Tadeu (RJ)

        // Novembro
        '10-2',  // Finados
        '10-15', // Proclamação da República
        '10-20', // Dia da Consciência Negra (RJ)

        // Dezembro
        '11-24', // Véspera de Natal (ponto facultativo após 14h)
        '11-25', // Natal
        '11-31', // Véspera de Ano Novo (ponto facultativo após 14h)
    ];

    public function mount()
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $this->clearSelectedDays(); // limpa todos os dias no estado inicial

    $this->users = User::orderBy('name')->get(['id', 'name'])->toArray();

    $this->checkUserHasVacation();

    if ($this->userHasVacation) {
        session()->flash('message', 'As suas férias já estão marcadas.');
        session()->flash('type', 'primary'); // azul, para informação
    }
}



    public function updatedSelectedUser($userId)
    {
        if ($userId) {
            $this->filterByUser($userId);
        } else {
            $this->setFilter('all'); // mostra todas as férias
        }
    }

    public function filterByUser($userId)
    {
        $this->activeFilter = 'user';  // filtro por usuário customizado

        $vacations = VacationRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        $this->savedDays = $vacations
            ->flatMap(fn($vacation) => json_decode($vacation->days, true) ?: [])
            ->toArray();

        $this->selectedDays = $this->savedDays;

        $this->remainingDays = max(5 - count($this->savedDays), 0);
    }

    private function loadAllVacations()
    {
        $vacations = VacationRequest::whereIn('status', ['pending', 'approved'])->get();

        $this->savedDays = $vacations
            ->flatMap(fn($vacation) => json_decode($vacation->days, true) ?: [])
            ->unique()
            ->toArray();

        $this->selectedDays = $this->savedDays;

        $this->remainingDays = 0;  // como é todas, não pode selecionar mais dias
    }

    public function updatedShowHolidays($value)
    {
        if ($value) {
            foreach ($this->holidays as $dia) {
                if (!in_array($dia, $this->selectedDays)) {
                    $this->selectedDays[] = $dia;
                }
            }
        } else {
            $this->selectedDays = array_filter(
                $this->selectedDays,
                fn($day) => !in_array($day, $this->holidays)
            );
        }

        $this->selectedDays = array_values($this->selectedDays);
    }

    public function isHoliday(int $monthIndex, int $day): bool
    {
        if (!$this->showHolidays) {
            return false;
        }
        $key = "{$monthIndex}-{$day}";
        return in_array($key, $this->holidays);
    }

    public function clearSelectedDays()
    {
        $this->selectedDays = [];
        $this->savedDays = [];
        $this->remainingDays = 5;
    }

    public function setFilter($filter)
    {
        if ($this->activeFilter === $filter) {
            $this->activeFilter = null;
            $this->clearSelectedDays();
        } else {
            $this->activeFilter = $filter;

            if ($this->activeFilter === 'my') {
                $this->selectedUser = ''; // limpa o dropdown
                $this->loadUserVacations();
                $this->remainingDays = max(5 - count($this->savedDays), 0);
            }

            if ($this->activeFilter === 'all') {
                $this->loadAllVacations();
            }
        }
    }

    private function loadUserVacations()
{
    $this->selectedUser = ''; // limpa o dropdown

    $this->savedDays = VacationRequest::where('user_id', auth()->id())
        ->whereIn('status', ['pending', 'approved'])
        ->get()
        ->flatMap(function ($vacation) {
            return json_decode($vacation->days, true) ?: [];
        })
        ->toArray();

    $this->selectedDays = $this->savedDays;

    $this->userHasVacation = count($this->savedDays) > 0;
}


    public function deleteUserVacationDays()
    {
        $userId = auth()->id();

        VacationRequest::where('user_id', $userId)->delete();

        $this->selectedDays = [];
        $this->vacationRequestSent = false;
        $this->loadUserVacations();
        $this->checkUserHasVacation(); // ← adicione isso
        $this->remainingDays = 5;

        session()->flash('message', 'Todos os dias de férias foram deletados com sucesso.');
        session()->flash('type', 'success');
    }

    public function toggleSearch()
    {
        $this->showSearch = !$this->showSearch;
        if (!$this->showSearch) {
            $this->query = '';
            $this->searchResults = [];
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) > 2) {
            $this->searchResults = User::where('name', 'like', '%' . $this->query . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function toggleHolidays()
    {
        $this->showHolidays = !$this->showHolidays;
    }

    public function render()
    {
        $months = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        $daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $monthsData = [];
        for ($i = $this->startMonth; $i < $this->startMonth + $this->monthsPerRow; $i++) {
            $monthIndex = $i % 12;
            $firstDay = Carbon::create($this->year, $monthIndex + 1, 1);
            $daysInMonth = $firstDay->daysInMonth;
            $startDay = $firstDay->dayOfWeek;

            $monthsData[] = [
                'name' => $months[$monthIndex],
                'days' => $this->generateDays($startDay, $daysInMonth),
                'daysOfWeek' => $daysOfWeek,
                'monthIndex' => $monthIndex
            ];
        }

        $reservedDaysQuery = VacationRequest::query();

        switch ($this->activeFilter) {
            case 'all':
                break;
            case 'disi':
                $reservedDaysQuery->where('team', 'DISI');
                break;
            case 'pe':
                $reservedDaysQuery->where('team', 'PE');
                break;
            case 'my':
                $reservedDaysQuery->where('user_id', auth()->id());
                break;
            case 'user':
                if ($this->selectedUser) {
                    $reservedDaysQuery->where('user_id', $this->selectedUser);
                }
                break;
            default:
                $reservedDaysQuery->whereRaw('0 = 1');
                break;
        }

        $reservedDaysRaw = $reservedDaysQuery->get();

        $reservedDays = [];

        foreach ($reservedDaysRaw as $request) {
            $days = json_decode($request->days, true);
            if (is_array($days)) {
                foreach ($days as $dayKey) {
                    if (!isset($reservedDays[$dayKey])) {
                        $reservedDays[$dayKey] = [];
                    }
                    $reservedDays[$dayKey][] = $request->name;
                }
            }
        }

        return view('livewire.calendar', [
            'monthsData' => $monthsData,
            'reservedDays' => $reservedDays,
            'selectedDays' => $this->selectedDays,
            'savedDays' => $this->savedDays,
            'remainingDays' => $this->remainingDays,
            'vacationRequestSent' => $this->vacationRequestSent,
            'activeFilter' => $this->activeFilter,
            'showSearch' => $this->showSearch,
            'query' => $this->query,
            'searchResults' => $this->searchResults,
            'holidays' => $this->showHolidays ? $this->holidays : [],
            'showHolidays' => $this->showHolidays,
            'isHolidayCallback' => function($monthIndex, $day) {
                return $this->isHoliday($monthIndex, $day);
            },
            'users' => $this->users,
            'selectedUser' => $this->selectedUser,
        ])->layout('layouts.app');
    }

    public function nextMonths()
    {
        if ($this->startMonth + $this->monthsPerRow < 12) {
            $this->startMonth += $this->monthsPerRow;
        }
    }

    public function prevMonths()
    {
        if ($this->startMonth - $this->monthsPerRow >= 0) {
            $this->startMonth -= $this->monthsPerRow;
        }
    }

    private function generateDays($startDay, $daysInMonth)
    {
        $days = array_fill(0, $startDay, null);
        $days = array_merge($days, range(1, $daysInMonth));

        while (count($days) % 7 != 0) {
            $days[] = null;
        }

        return $days;
    }

    public function selectDay($day, $monthIndex)
    {
        $key = "{$monthIndex}-{$day}";

        if (in_array($key, $this->savedDays)) {
            return;
        }

        if (in_array($key, $this->selectedDays)) {
            $this->selectedDays = array_values(array_diff($this->selectedDays, [$key]));
            $this->remainingDays++;
        } else {
            if (count($this->selectedDays) >= 5) {
                session()->flash('message', 'Você não pode selecionar mais de 5 dias de férias.');
                session()->flash('type', 'warning');
                return;
            }

            $this->selectedDays[] = $key;
            $this->remainingDays--;
        }

        if (count($this->selectedDays) > 5) {
            $this->selectedDays = array_slice($this->selectedDays, 0, 5);
            $this->remainingDays = 0;
        }
    }

    public function sendVacationRequest()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Você precisa estar logado para enviar a solicitação de férias.');
            session()->flash('type', 'error');
            return;
        }

        $userId = auth()->id();

        $existingDaysCount = VacationRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->flatMap(function ($vacation) {
                return json_decode($vacation->days, true);
            })
            ->unique()
            ->count();

        $newDaysCount = count($this->selectedDays);

        if ($existingDaysCount + $newDaysCount > 5) {
            session()->flash('message', 'Você não pode reservar mais que 5 dias de férias no total.');
            session()->flash('type', 'warning');
            return;
        }

        VacationRequest::create([
            'user_id' => $userId,
            'name' => auth()->user()->name,
            'status' => 'pending',
            'team' => auth()->user()->team,
            'role' => auth()->user()->roles,
            'days' => json_encode($this->selectedDays),
        ]);

        $this->selectedDays = [];
        $this->remainingDays = 5 - $existingDaysCount - $newDaysCount;
        $this->vacationRequestSent = true;

        session()->flash('message', 'Solicitação de férias enviada com sucesso!');
        session()->flash('type', 'success');

        $this->loadUserVacations();
    }
    public function notificarAdmins()
    {
        $admins = User::whereJsonContains('roles', 'admin')->get();

        if ($admins->isEmpty()) {
            session()->flash('message', 'Nenhum administrador encontrado.');
            session()->flash('type', 'error');
            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new Important(
                title: 'Solicitação de férias',
                body: '❶ – Você recebeu uma notificação de novo pedido de férias!'
            ));
        }
    }

    public function sendVacationRequestAndNotify()
    {
        $this->sendVacationRequest();

        if ($this->vacationRequestSent) {
            $this->notificarAdmins();
        }
    }
}