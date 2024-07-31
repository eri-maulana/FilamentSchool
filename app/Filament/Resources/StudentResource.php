<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\Collection;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';

    protected static ?string $navigationLabel = 'Student';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?int $navigationSort = 12;

    // protected static ?string $navigationLabel = 'Student';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nis')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female'
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('birthday'),
                Forms\Components\Select::make('religion')
                ->options([
                    'Islam' => 'Islam',
                    'Katolik' => 'Katolik',
                    'Protestan' => 'Protestan',
                    'Hindu' => 'Hindu',
                    'Buddha' => 'Buddha',
                        'Khonghucu' => 'Khonghucu'
                    ])
                    ->required(),
                Forms\Components\TextInput::make('contact')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('profile')
                ->directory('profile')
                ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                Tables\Columns\TextColumn::make('nis')
                ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                ->label('Nama Murid')
                    ->searchable(),
            Tables\Columns\TextColumn::make('gender')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birthday')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('religion')
                ->label('Agama')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contact')
                ->label('Kontak'),
            ImageColumn::make('profile')
                ->label('Profil'),
            TextColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn (string $state): string => ucwords("{$state}")),
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
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'accept' => 'Accept',
                        'off' => 'Off',
                        'move' => 'Move',
                        'grade' => 'Grade',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Ubah Status')
                        ->icon('heroicon-c-arrow-path')
                        ->requiresConfirmation()
                    ->form([
                        Select::make('Status')
                            ->label('Status')
                            ->options(['accept' => 'Accept', 'off' => 'Off', 'move' => 'Move', 'grade' => 'Grade',])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each(function ($record) use ($data) {
                            Student::where('id', $record->id)->update(['status' => $data['Status']]);
                        });
                    }),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }

    public static function getLabel(): ?string
    {
        $locale = app()->getLocale();
        if ($locale == 'id') {
            return 'Murid';
        } else {
            return 'Students';
        }
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Fieldset::make('Biodata')
                        ->schema([
                            Split::make([
                                ImageEntry::make('profile')
                                ->hiddenLabel()
                                    ->grow(false),
                                Grid::make(2)
                                    ->schema([
                                        Group::make([
                                            TextEntry::make('nis'),
                                            TextEntry::make('name'),
                                            TextEntry::make('gender'),
                                            TextEntry::make('birthday'),

                                        ])
                                            ->inlineLabel()
                                            ->columns(1),

                                        Group::make([
                                            TextEntry::make('religion'),
                                            TextEntry::make('contact'),
                                            TextEntry::make('status')
                                            ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'accept' => 'success',
                                                    'off' => 'danger',
                                                    'grade' => 'success',
                                                    'move' => 'warning',
                                                    'wait' => 'gray'
                                                }),
                                            ViewEntry::make('QRCode')
                                            ->view('filament.resources.students.qrcode'),
                                        ])
                                            ->inlineLabel()
                                            ->columns(1),
                                    ])

                            ])->from('lg')
                        ])->columns(1)
                    ])->columns(2)
            ]);
    }
}