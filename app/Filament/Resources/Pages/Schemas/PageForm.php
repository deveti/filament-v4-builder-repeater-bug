<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Builder::make('content')
                    ->blocks([
                        Block::make('faq')
                            ->schema([
                                Repeater::make('items')
                                    ->schema([
                                        TextInput::make('question')->required(),
                                        TextInput::make('answer')->required(),
                                    ]),
                            ]),
                    ]),
            ])->columns(1);
    }
}
