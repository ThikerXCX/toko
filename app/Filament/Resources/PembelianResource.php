<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Models\Pembelian;
use App\Traits\FilamentPermissionAwareNavigation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class PembelianResource extends Resource implements HasShieldPermissions
{
    use FilamentPermissionAwareNavigation;
    protected static string $requiredPermission = 'view_pembelian';
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessMenu(); // Panggil method yang sudah aman
    }
    protected static ?string $model = Pembelian::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pembelian';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['supplier', 'details.product']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('no_faktur')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->label('No. Faktur'),

            Select::make('supplier_id')
                ->relationship('supplier', 'name')
                ->required()
                ->searchable()
                ->label('Supplier')
                ->createOptionForm([
                    TextInput::make('name')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true),
                    TextInput::make('no_tlp')
                        ->maxLength(15)
                        ->default('-'),
                    Textarea::make('alamat')
                        ->maxLength(50)
                        ->default('-')
                        ->columnSpanFull(),
                ]),

            DatePicker::make('tanggal')
                ->required()
                ->default(now()),

            TextInput::make('total')
                ->numeric()
                ->readOnly()
                ->label('Total')
                ->default(0),

            Repeater::make('details')
                ->relationship()
                ->schema([
                    Select::make('product_id')
                        ->relationship('product', 'name')
                        ->required()
                        ->searchable()
                        ->label('Produk')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state) {
                                $hargaBeli = \App\Models\Product::find($state)?->harga_beli ?? 0;
                                $set('harga', $hargaBeli);
                                $set('qty', 1);
                                $set('subtotal', $hargaBeli * 1);

                                // Update total di parent
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
                Tables\Columns\TextColumn::make('no_faktur')->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('total')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
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
