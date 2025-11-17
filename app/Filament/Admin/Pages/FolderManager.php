<?php

namespace App\Filament\Admin\Pages;

use App\Models\Folder;
use App\Models\Photo;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleSheetService;

class FolderManager extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.admin.pages.folder-manager';
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Create Folder & Upload';
    protected static ?string $navigationGroup = 'Folders';
    protected static ?int $navigationSort = 10;

    public ?Folder $currentFolder = null;
    public array $uploadedFiles = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function getFoldersProperty()
    {
        return Folder::where('user_id', Auth::id())->whereNull('parent_id')->get();
    }

    public function getFilesProperty()
    {
        return $this->currentFolder
            ? Photo::where('folder_id', $this->currentFolder->id)->get()
            : collect();
    }

    /** -----------------------------
     *  Filament Modal Action
     * ----------------------------- */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createFolder')
                ->label('Create Folder')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalHeading('Create New Folder')
                ->modalSubmitActionLabel('Create')
                ->modalWidth('md')
                ->form([
                    Forms\Components\TextInput::make('folderName')
                        ->label('Folder Name')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data, Action $action) {
                    $user = Auth::user();
                    $parent = Folder::find($data['parent_id'] ?? null);
                    $path = $parent ? "{$parent->name}/{$data['folderName']}" : $data['folderName'];

                    $folder = Folder::create([
                        'name' => $path,
                        'user_id' => $user->id,
                        'parent_id' => $parent?->id,
                    ]);

                    try {
                        $sheet = new GoogleSheetService();
                        $sheet->addRow($user->id, $folder->name);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Google Sheet Sync Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }

                    Notification::make()
                        ->title("Folder '{$path}' created successfully!")
                        ->success()
                        ->send();

                    // Auto-close modal
                    $action->success();

                    // Trigger refresh in Livewire
                    $this->dispatch('$refresh');
                }),
        ];
    }

    /** -----------------------------
     *  Folder + File Logic
     * ----------------------------- */
    public function openFolder($id): void
    {
        $this->currentFolder = Folder::find($id);
    }

    public function closeFolder(): void
    {
        $this->currentFolder = null;
    }

    public function uploadFiles(): void
    {
        $user = Auth::user();

        // ✅ 1. Check if a folder is open
        if (!$this->currentFolder) {
            Notification::make()
                ->title('Please open a folder first!')
                ->danger()
                ->send();
            return;
        }

        // ✅ 2. Check if files are selected before uploading
        if (empty($this->uploadedFiles) || count($this->uploadedFiles) === 0) {
            Notification::make()
                ->title('No files selected')
                ->body('Please select at least one file to upload before continuing.')
                ->danger()
                ->duration(3000)
                ->send();
            return;
        }

        // ✅ 3. Continue with upload logic
        foreach ($this->uploadedFiles as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $type = match ($ext) {
                'jpg', 'jpeg', 'png' => 'image',
                'pdf' => 'pdf',
                'doc', 'docx' => 'word',
                default => 'other',
            };

            $fileName = time() . '_' . $file->getClientOriginalName();
            $folderPath = "{$user->id}/{$this->currentFolder->name}";

            $storedPath = $file->storeAs($folderPath, $fileName, 'public');

            Photo::create([
                'path' => $storedPath,
                'type' => $type,
                'user_id' => $user->id,
                'uploaded_by' => $user->id,
                'folder_id' => $this->currentFolder->id,
            ]);
        }

        // ✅ 4. Reset file input and notify success
        $this->uploadedFiles = [];

        Notification::make()
            ->title('Files uploaded successfully!')
            ->success()
            ->duration(3000)
            ->send();
    }
}
