<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nome Completo')
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->nullable() // Permite que a senha seja nula na edição
                    ->required(fn ($context) => $context === 'create') // Só obrigatório na criação
                    ->maxLength(255),

                Forms\Components\Select::make('roles')
                ->label('Roles')
                ->options([
                    'admin' => 'Admin',
                    'editor' => 'Editor',
                    'user' => 'User',
                ])
                ->multiple()
                // ->relationship('roles', 'name')
                ->saveRelationshipsWhenHidden(false)
                ->afterStateHydrated(function ($state, Forms\Set $set) {
                    $set('roles', $state ? explode(',', $state) : []);
                })
                ->dehydrateStateUsing(function ($state) {
                    return $state && count($state) > 0 ? json_encode($state) : null;
                }),

                Forms\Components\Select::make('team')
                    ->label('Equipe')
                    ->options([
                        'DISI' => 'DISI',
                        'PE' => 'PE',
                    ])
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Nome Completo')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->searchable(),

            Tables\Columns\TextColumn::make('team')
                ->label('Equipe')
                ->sortable()
                ->searchable(),

            // Coluna para exibir os papéis do usuário
            Tables\Columns\TextColumn::make('roles')  // Acessando o campo 'roles'
                ->label('Roles') // Nome da coluna
                ->getStateUsing(function ($record) {
                    // Converte a string de roles em um array e exibe os papéis como uma lista separada por vírgulas
                    $roles = explode(',', $record->roles); // Caso os papéis estejam armazenados como uma string separada por vírgulas
                    return implode(', ', $roles); // Exibe os papéis na tabela
                })
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->label('Data de Criação')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            EditAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Método para salvar o usuário com lógica personalizada para o campo "password"
    public static function save(Form $form, $record)
    {
        $data = $form->getState();

        // Verifica se a senha foi preenchida, caso contrário, não altera o valor
        if (!empty($data['password'])) {
            $record->password = bcrypt($data['password']);
        }

        // Atualiza os outros campos
        $record->fill($data);
        $record->save();
    }
}
