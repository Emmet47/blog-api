<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Models\Policy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Policy Information')
                    ->description('Add the type and content for the policy.')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(255)
                            ->label('Policy Type')
                            ->placeholder('Enter the type of policy (e.g., Privacy, Terms)'),

                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->label('Policy Content')
                            ->placeholder('Enter the full policy content here')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('type')
                    ->label('Policy Type')
                    ->badge()
                    ->colors([
                        'primary' => fn ($state) => $state === 'Gizlilik',
                        'success' => fn ($state) => $state === 'Kullanım Koşulları',
                        'warning' => fn ($state) => $state === 'KVKK',
                    ])
                    ->sortable()
                    ->searchable(),

                // Date columns
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed in the future
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicy::route('/create'),
            'view' => Pages\ViewPolicy::route('/{record}'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }
}
