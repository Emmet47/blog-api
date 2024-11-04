<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Basic Information')
                    ->description('Provide the name and URL-friendly slug for the category.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Category Name'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label('Slug (URL Identifier)'),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->placeholder('Select a status'),
                    ])->columns(2),


                Forms\Components\Section::make('Styling Information')
                    ->description('Customize the text and background colors for the category.')
                    ->schema([

                        Forms\Components\ColorPicker::make('text_color')
                            ->label('Text Color'),
                        Forms\Components\ColorPicker::make('bg_color')
                            ->label('Background Color'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\ColorColumn::make('text_color')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('bg_color')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn(string $state): string => match ($state) {
                        'inactive' => 'heroicon-o-x-mark',
                        'active' => 'heroicon-o-check',
                        default => 'heroicon-o-x-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'inactive' => 'danger',
                        'active' => 'success',
                        default => 'gray',
                    })
                    ->tooltip('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
