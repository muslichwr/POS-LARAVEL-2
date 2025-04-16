<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = "Data Utama";

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Supplier')
                        ->placeholder('Masukkan nama supplier')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('contact')
                        ->label('Kontak')
                        ->placeholder('Nama PIC')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->placeholder('Masukkan alamat lengkap')
                        ->required()
                        ->rows(3),
                        
                    Forms\Components\TextInput::make('phone')
                        ->label('No. Telepon')
                        ->placeholder('08xx-xxxx-xxxx')
                        ->tel()
                        ->required()
                        ->maxLength(20),
                        
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->placeholder('contoh@supplier.com')
                        ->email()
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2)
                ->columnSpan(['lg' => 2]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Nama Supplier')
                ->searchable()
                ->sortable(),
                
                Tables\Columns\TextColumn::make('contact')
                    ->label('Kontak')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Registrasi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
