<!-- Fundo escurecido atrás da notificação -->
<div
    x-cloak
    x-show="open"
    x-transition.opacity.duration.300ms
    @click="open = false"
    class="fixed inset-0 z-[99998] bg-black/40 backdrop-blur-sm"
></div>

<!-- Caixa da notificação -->
<div
    x-cloak
    x-show="open"
    x-transition
    class="fixed top-0 right-0 z-[99999] w-[380px] max-w-full rounded-b-xl bg-white p-6 shadow-xl"
    id="notification"
    style="position: fixed; z-index: 99999;"
>
    <div class="relative w-full max-h-[80vh] min-h-[300px] overflow-y-auto bg-white shadow-xl rounded-b-lg">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <p class="py-1 text-base font-semibold text-gray-900">Notificações</p>
            <button @click="open = false"
                class="absolute top-0 right-0 flex items-center justify-center px-2 py-1 mt-4 mr-5 text-xs font-medium uppercase border rounded-md border-neutral-200 text-neutral-600 hover:bg-neutral-100">
                <x-megaphone::icons.close />
            </button>
        </div>

        <!-- Conteúdo -->
        <div class="p-4 pt-2">
            @if ($unread->count() > 0)
                <div class="flex justify-between pb-2 text-gray-600 border-b border-gray-300">
                    <h2 class="pt-8 text-sm leading-normal">Notificações não lidas</h2>
                    @if ($unread->count() > 1)
                        <button class="pt-8 text-sm leading-normal hover:text-red-700" wire:click="markAllRead()">
                            Marcar tudo como lida
                        </button>
                    @endif
                </div>

                @foreach ($unread as $announcement)
                    <div class="relative w-full p-3 mt-4 bg-white rounded-xl flex flex-shrink-0 {{ $announcement->read_at === null ? 'drop-shadow shadow border' : '' }}">
                        <x-megaphone::display :notification="$announcement" />
                        @if($announcement->read_at === null)
                            <button
                                class="absolute top-0 right-0 px-1 py-1 mt-2 mr-2 border rounded-md text-neutral-600 border-neutral-200 hover:bg-neutral-100"
                                x-on:click="$wire.markAsRead('{{ $announcement->id }}')"
                                title="Marcar como lida"
                            >
                                <x-megaphone::icons.read class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                @endforeach
            @endif

            @if ($announcements->count() > 0)
                <div class="flex justify-between pb-2 text-gray-600 border-b border-gray-300">
                    <h2 class="pt-8 text-sm leading-normal">Próxima notificação</h2>
                    @if($allowDelete)
                        <button
                            class="pt-8 text-sm leading-normal hover:text-red-500"
                            wire:click="deleteAllReadNotification"
                        >
                            Limpar tudo
                        </button>
                    @endif
                </div>
            @endif

            @foreach ($announcements as $announcement)
                <div class="relative flex flex-shrink-0 w-full p-3 mt-4 rounded bg-gray-50">
                    <x-megaphone::display :notification="$announcement" />
                    @if($allowDelete)
                        <button
                            class="absolute top-0 right-0 px-1 py-1 mt-2 mr-2 border rounded-md text-neutral-600 border-neutral-200 hover:bg-neutral-200"
                            x-on:click="$wire.deleteNotification('{{ $announcement->id }}')"
                            title="Deletar notificação"
                        >
                            <x-megaphone::icons.delete class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            @endforeach

            @if ($unread->count() === 0 && $announcements->count() === 0)
                <div class="flex items-center justify-between">
                    <hr class="w-full">
                    <p class="flex flex-shrink-0 px-3 py-16 text-sm text-gray-500">Sem novas notificações</p>
                    <hr class="w-full">
                </div>
            @endif
        </div>
    </div>
</div>
