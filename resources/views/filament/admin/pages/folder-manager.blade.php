<x-filament::page>
    <div class="space-y-8">
        {{-- HEADER --}}
        <div class="relative flex items-center justify-between p-4 rounded-lg">
            @if ($currentFolder)
                {{-- Back Button (Left) --}}
                <div class="flex-shrink-0">
                    <x-filament::button wire:click="closeFolder" color="primary" icon="heroicon-o-arrow-left">
                        Back
                    </x-filament::button>
                </div>

                {{-- Centered Folder Name --}}
                <div class="absolute inset-x-0 flex justify-center pointer-events-none">
                    <h2 class="text-xl font-bold text-gray-800 text-center">
                        {{ $currentFolder->name }}
                    </h2>
                </div>

                {{-- Upload Button (Right) --}}
                <div class="flex-shrink-0">
                    <x-filament::button color="warning" wire:click="uploadFiles" class="font-semibold">
                        Upload
                    </x-filament::button>
                </div>
            @else
                <h2 class="text-xl font-bold text-gray-800">Your Folders</h2>
            @endif
        </div>

        {{-- FOLDER LIST --}}
        @if (!$currentFolder)
            <div class="mt-6">
                <div class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(7rem, 1fr));">
                    @forelse ($this->folders as $folder)
                        <div 
                            wire:click="openFolder({{ $folder->id }})"
                            class="group flex flex-col items-center cursor-pointer transition-transform hover:scale-105"
                        >
                            {{-- Folder Icon --}}
                            <x-heroicon-s-folder class="w-20 h-20 text-yellow-400 drop-shadow-md group-hover:scale-110 transition-transform duration-200" style="color: #facc15;"/>

                            {{-- Folder Name --}}
                            <p class="mt-3 text-sm font-semibold text-gray-700 text-center truncate w-28 group-hover:text-primary-600">
                                {{ $folder->name }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500">No folders yet. Click “+ Create Folder” to add one.</p>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- FILE VIEW --}}
        @if ($currentFolder)
            <div class="space-y-6">
                {{-- Upload input --}}
                {{-- Upload input --}}
<div class="bg-gray-50 border border-dashed border-gray-300 p-6 rounded-lg text-center hover:border-primary-400 transition flex flex-col items-center justify-center">
    {{-- File input --}}
    <label class="cursor-pointer inline-flex flex-col items-center">
        <input 
            type="file" 
            wire:model="uploadedFiles" 
            multiple 
            class="hidden" 
            id="fileInput"
        />
        <span class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
            Choose Files
        </span>
    </label>

    {{-- Helper text --}}
    <p class="text-xs text-gray-500 mt-3">
        You can select multiple files (images or PDFs)
    </p>

    {{-- ✅ Selected file names list --}}
    @if ($uploadedFiles && count($uploadedFiles) > 0)
        <div class="mt-4 w-full max-w-md text-left">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                Selected Files:
            </h4>
            <ul class="text-sm text-gray-600 space-y-1">
                @foreach ($uploadedFiles as $file)
                    <li class="flex items-center justify-between bg-white border border-gray-200 rounded px-3 py-1">
                        <span class="truncate w-3/4">
                            {{ $file->getClientOriginalName() }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ number_format($file->getSize() / 1024, 1) }} KB
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

                {{-- FILE PREVIEW GRID --}}
                <x-filament::section>
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">📂 Files in this folder</h3>

                    @if ($this->files->isEmpty())
                        <p class="text-gray-500">No files uploaded yet.</p>
                    @else
                        {{-- FILE LIST VIEW --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            File Name
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Uploaded On
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Type
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($this->files as $file)
                                        @php
                                            $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
                                            $isPdf = $extension === 'pdf';
                                            $isWord = in_array($extension, ['doc', 'docx']);
                                            $isExcel = in_array($extension, ['xls', 'xlsx']);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
                                            $isVideo = in_array($extension, ['mp4']);
                                            $iconColor = $isPdf ? 'text-red-500' : ($isWord ? 'text-blue-500' : ($isExcel ? 'text-green-500' : ($isImage ? 'text-yellow-500' : 'text-gray-400')));
                                        @endphp

                                        <tr 
                                            class="hover:bg-gray-50 transition cursor-pointer"
                                            onclick="window.open('{{ asset('storage/' . $file->path) }}', '_blank')"
                                        >
                                            {{-- FILE NAME + ICON --}}
                                            <td class="px-4 py-3 flex items-center space-x-3">
                                                @if ($isPdf)
                                                    <x-heroicon-o-document-text class="w-5 h-5 {{ $iconColor }}" />
                                                @elseif ($isWord)
                                                    <x-heroicon-o-document class="w-5 h-5 {{ $iconColor }}" />
                                                @elseif ($isExcel)
                                                    <x-heroicon-o-document class="w-5 h-5 {{ $iconColor }}" />
                                                @elseif ($isImage)
                                                    <x-heroicon-o-photo class="w-5 h-5 {{ $iconColor }}" />
                                                @elseif ($isVideo)
                                                    <x-heroicon-o-play-circle class="w-5 h-5 {{ $iconColor }}" />
                                                @else
                                                    <x-heroicon-o-document class="w-5 h-5 {{ $iconColor }}" />
                                                @endif

                                                <span class="text-sm font-medium text-gray-700 truncate max-w-[220px]">
                                                    {{ basename($file->path) }}
                                                </span>
                                            </td>

                                            {{-- DATE --}}
                                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $file->created_at ? $file->created_at->format('M d, Y') : '—' }}
                                            </td>

                                            {{-- FILE TYPE --}}
                                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                                {{ strtoupper($extension) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-filament::section>
            </div>
        @endif
    </div>
</x-filament::page>
