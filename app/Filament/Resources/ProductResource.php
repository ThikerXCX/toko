<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Traits\FilamentPermissionAwareNavigation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Product::class;
    use FilamentPermissionAwareNavigation;
    protected static string $requiredPermission = 'view_product';
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessMenu(); // Panggil method yang sudah aman
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(50)
                        ->lazy()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->maxLength(20)
                        ->unique(Product::class, 'sku', fn ($record) => $record),

                    TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->maxLength(50),

                    Select::make('category_id')
                        ->label('Kategori')
                        ->required()
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih Kategori')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('description')
                                ->maxLength(255),
                        ]),

                    Select::make('merek_id')
                        ->label('Merek')
                        ->required()
                        ->relationship('merek', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih Merek')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('description')
                                ->maxLength(255),
                        ]),

                    Select::make('satuan_id')
                        ->label('Satuan')
                        ->required()
                        ->relationship('satuan', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih Satuan')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('description')
                                ->maxLength(255),
                            TextInput::make('symbol')
                                ->required()
                                ->maxLength(10),
                        ]),
                    TextInput::make('harga_beli')
                        ->label('Harga Beli')
                        ->required()
                        ->numeric(),
                    TextInput::make('harga_jual')
                        ->label('Harga Jual')
                        ->required()
                        ->numeric(),

                    TextInput::make('stok')
                        ->label('Stok')
                        ->required()
                        ->numeric()
                        ->hiddenOn('edit'),
                    TextInput::make('stok_minimal')
                        ->label('Stok Minimal')
                        ->required()
                        ->numeric(),
                    FileUpload::make('image')
                        ->label('Gambar')
                        ->image()
                        ->nullable()
                        ->disk('public')
                        ->directory('products')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->circular()->size(50),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('merek.name')->label('Merek'),
                TextColumn::make('satuan.name')->label('Satuan'),
                TextColumn::make('harga_jual')->money('IDR'),
                TextColumn::make('stok')->label('Stok')
                ->color(fn ($record) => $record->stok < $record->stok_minimal ? 'danger' : 'success')
                ->badge(fn ($record) => $record->stok < $record->stok_minimal ? 'Stok Kurang' : 'Stok Cukup')
                ->sortable(),
            
                ])->defaultSort('name', 'asc')
            ->filters([
                //
            ])
            ->actions([
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
            // RelationManagers\YourRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
