<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class GoogleSheetViewer extends Page
{
    protected static string $view = 'filament.admin.pages.google-sheet-viewer';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Google Excel Sheet';
    protected static ?string $navigationGroup = 'Admin Tools';
    protected static ?int $navigationSort = 11;

}
