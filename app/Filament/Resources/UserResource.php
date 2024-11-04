<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('User Information')
                ->description('Provide the user\'s personal information.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Full Name')
                        ->placeholder('Enter the user\'s full name'),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->label('Email Address')
                        ->placeholder('Enter the user\'s email address'),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(fn ($record) => $record === null)
                        ->maxLength(255)
                        ->label('Password')
                        ->placeholder('Enter a password for the user')
                        ->hidden(fn ($record) => $record !== null),
                    Radio::make('role')
                        ->required()
                        ->label('User Role')
                        ->options([
                            'user' => 'User',
                            'author' => 'Author',
                            'admin' => 'Admin',
                        ])
                        ->descriptions([
                            'user' => 'Regular user with limited permissions.',
                            'author' => 'Can create and edit posts.',
                            'admin' => 'Full access to the system.',
                        ])
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Full Name'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email Address'),

                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->label('User Role'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
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
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
