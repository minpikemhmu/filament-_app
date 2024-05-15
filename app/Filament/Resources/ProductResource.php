<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section::make()->schema([
                    
                // ])
                TextInput::make('name')->rules('min:3')->required(),

                TextInput::make('product_code')
                ->rules(function ($livewire) {
                    $rules = ['required', 'string', 'max:255'];

                    if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                        $rules[] = Rule::unique('products', 'product_code');
                    }

                    return $rules;
                })->required(),

                Select::make('category_id')
                ->label('Category')
                ->options(Category::all()->pluck('name', 'id'))
                ->searchable()
                ->rules('required', 'exists:categories,id'),

                Select::make('size')
                ->options([
                    'small' => 'small',
                    'medium' => 'medium',
                    'large' => 'large',
                ])->required(),

                MarkdownEditor::make('description')->required(),
                
                FileUpload::make('thumbnail')->label('Product Image')->disk('public')->directory('thumbnail')
                    ->rules('required', 'image', 'max:2048'),
            ])->columns([
                'default'=>1,
                'md'=>2,
                'lg'=>2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('product_code'),
                TextColumn::make('category.name'),
                TextColumn::make('size'),
                ImageColumn::make('thumbnail')->label('Product Image'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
