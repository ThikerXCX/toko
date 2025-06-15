<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenjualanResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Penjualan::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Penjualan';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';


    public static function form(Form $form): Form
    {
        function updateTotal(callable $set, callable $get): void {
            $details = $get('details') ?? [];
            $total = collect($details)->sum(fn($d) => ($d['harga'] ?? 0) * ($d['qty'] ?? 0));
            $set('total', $total);
        }
        return $form->schema([
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->readOnly()
                ->default(now()),

            TextInput::make('total')
                ->numeric()
                ->readOnly()
                ->default(0),

            Repeater::make('details')
                ->relationship()
                ->label('Detail Penjualan')
                ->schema([
                    Select::make('product_id')
                        ->label('Produk')
                        ->relationship('product', 'name')
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state) {
                                $hargaJual = \App\Models\Product::find($state)?->harga_jual ?? 0;
                                $set('harga', $hargaJual);
                                $set('qty', 1);
                                $set('subtotal', $hargaJual * 1);

                                // Cukup hitung total dari details, JANGAN tambah manual subtotal baris ini
                                $details = $get('../../details') ?? [];
                                $total = collect($details)->sum(fn ($item) => ($item['harga'] ?? 0) * ($item['qty'] ?? 0));
                                $set('../../total', $total);
                            }
                        }),

                    TextInput::make('harga')
                        ->numeric()
                        ->required()
                        ->label('Harga')
                        ->lazy()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $qty = $get('qty') ?? 0;
                            $set('subtotal', (int)$state * (int)$qty);

                            // Update total di parent
                            $details = $get('../../details') ?? [];
                            $total = collect($details)->sum(fn ($item) => ($item['harga'] ?? 0) * ($item['qty'] ?? 0));
                            $set('../../total', $total);
                        }),

                    TextInput::make('qty')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->label('Qty')
                        ->lazy()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $harga = $get('harga') ?? 0;
                            $set('subtotal', (int)$harga * (int)$state);

                            // Update total di parent
                            $details = $get('../../details') ?? [];
                            $total = collect($details)->sum(fn ($item) => ($item['harga'] ?? 0) * ($item['qty'] ?? 0));
                            $set('../../total', $total);
                        }),

                    TextInput::make('subtotal')
                        ->numeric()
                        ->readOnly()
                        ->label('Subtotal'),
                ])
                ->columns(2)
                ->required()
                ->reactive()
                // Optional: tetap tambahkan afterStateUpdated di repeater untuk sync jika ada tambah/hapus baris
                ->afterStateUpdated(function (callable $set, callable $get) {
                    $details = $get('details') ?? [];
                    $total = collect($details)->sum(fn ($item) => ($item['harga'] ?? 0) * ($item['qty'] ?? 0));
                    $set('total', $total);
                })
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode Penjualan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Dilayani Oleh')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('idr')
                    ->sortable()
                    ->searchable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'details.product']);
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
