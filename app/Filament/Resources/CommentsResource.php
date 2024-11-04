<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentsResource\Pages;
use App\Filament\Resources\CommentsResource\RelationManagers;
use App\Models\Comments;
use Filament\Forms;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsResource extends Resource
{
    protected static ?string $model = Comments::class;

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Relationship Details')
                ->description('Provide the related post and user information.')
                ->schema([
                    Forms\Components\Select::make('post_id')
                        ->required()
                        ->label('Post')
                        ->options(Post::all()->pluck('title', 'id'))
                        ->placeholder('Select a post'),
                    Forms\Components\Select::make('user_id')
                        ->required()
                        ->label('User')
                        ->options(User::all()->pluck('name', 'id'))
                        ->placeholder('Select a user'),
                ])->columns(2),

            Forms\Components\Section::make('Content Details')
                ->description('Add the content and status for this comment.')
                ->schema([
                    Forms\Components\Textarea::make('content')
                        ->required()
                        ->label('Comment Content')
                        ->placeholder('Write your comment here...')
                        ->columnSpanFull(),

                    Radio::make('status')
                        ->options([
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->label('Publication Status')
                        ->descriptions([
                            'scheduled' => 'The comment will be visible later.',
                            'published' => 'The comment is currently visible.',
                        ])->default('scheduled'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('post.title')
                ->label('Post Title')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('User Name')
                ->sortable()
                ->searchable(),
                IconColumn::make('status')
                ->label('Status')
                ->icon(fn (string $state): string => match ($state) {
                    'scheduled' => 'heroicon-o-clock',
                    'published' => 'heroicon-o-check-circle',
                    default => 'heroicon-o-x-circle',
                })
                ->color(fn (string $state): string => match ($state) {
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComments::route('/create'),
            'view' => Pages\ViewComments::route('/{record}'),
            'edit' => Pages\EditComments::route('/{record}/edit'),
        ];
    }
}
