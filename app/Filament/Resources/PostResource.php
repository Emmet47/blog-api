<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use App\Models\User;
use App\Models\Category;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Author and Category')
                    ->description('Provide details about the author and category.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->required()
                            ->label('Author')
                            ->options(User::all()->pluck('name', 'id'))
                            ->placeholder('Select an author'),
                        Forms\Components\Select::make('category_id')
                            ->required()
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->placeholder('Select a category'),
                    ])->columns(2),

                Forms\Components\Section::make('Post Information')
                    ->description('Fill in the details about the post.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Post Title')
                            ->placeholder('Enter the post title'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label('Slug')
                            ->placeholder('Enter the URL slug for the post'),
                            Forms\Components\FileUpload::make('image')
                            ->image()
                            ->label('Image')
                            ->disk('public')
                            ->directory('images')
                            ->preserveFilenames()
                            ->nullable()
                            ->default(fn ($record) => $record ? $record->image : null),

                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->label('Post Content')
                            ->placeholder('Enter the post content here')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Schedule & Status')
                    ->description('Set the publishing schedule and status.')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Start Date')
                            ->placeholder('Pick the start date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('End Date')
                            ->placeholder('Pick the end date (optional)'),
                        Radio::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->label('Publication Status')
                            ->descriptions([
                                'scheduled' => 'The post will become visible later.',
                                'published' => 'The post is currently visible.',
                            ])->default('scheduled'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn(string $state): string => match ($state) {
                        'scheduled' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'published' => 'success',
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
