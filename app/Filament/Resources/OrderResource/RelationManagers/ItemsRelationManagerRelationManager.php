<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Models\OrderTask;
use App\Models\TaskEntry;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items'; // Relasi dari Order ke OrderItems

    protected static ?string $title = 'Progress Pengerjaan (Live)';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Form ini jarang dipakai karena kita pakai Action, tapi biarkan default
            TextInput::make('resource_type')->readOnly(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('resource_type')
            // FITUR LIVE: Refresh tabel otomatis setiap 5 detik agar admin lain melihat update
            ->poll('5s') 
            ->columns([
                TextColumn::make('resource_type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info'),

                // Progress Bar Visual
                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function ($record) {
                        // Hitung persentase: (Terisi / Target) * 100
                        $target = $record->amount; // Ini sudah angka asli (100.000.000)
                        $filled = $record->amount_filled;
                        $percent = $target > 0 ? ($filled / $target) * 100 : 0;
                        
                        return number_format($percent, 1) . '%';
                    })
                    ->badge()
                    ->color(fn ($state) => (float)$state >= 100 ? 'success' : 'warning'),

                TextColumn::make('amount_filled')
                    ->label('Terkirim (M)')
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 2) . ' M'),

                TextColumn::make('amount')
                    ->label('Target (M)')
                    ->formatStateUsing(fn ($state) => number_format($state / 1000000, 0) . ' M'),

                // Menampilkan siapa saja yang sedang mengerjakan item ini
                TextColumn::make('workers')
                    ->label('Tim Pengirim')
                    ->html()
                    ->getStateUsing(function ($record) {
                        // Ambil list nama admin & karakter dari tabel tasks
                        $tasks = $record->tasks()->with(['user', 'character'])->get();
                        if ($tasks->isEmpty()) return '<span class="text-gray-400 text-xs">Belum ada</span>';
                        
                        $html = '<div class="flex flex-col gap-1">';
                        foreach($tasks as $task) {
                            $sent = number_format($task->total_sent, 2); // Total yg dikirim orang ini
                            $userName = $task->user->name;
                            $charName = $task->character->ign;
                            $html .= "<span class='text-xs bg-gray-100 rounded px-1 border'>Running: <b>{$charName}</b> ({$userName}) - <span class='text-primary-600 font-bold'>{$sent} M</span></span>";
                        }
                        $html .= '</div>';
                        return $html;
                    }),
            ])
            ->headerActions([
                // Tidak ada tombol create standard
            ])
            ->actions([
                // ACTION 1: MULAI TUGAS (Ambil Akun)
                Tables\Actions\Action::make('start_task')
                    ->label('Ambil Tugas')
                    ->icon('heroicon-m-play')
                    ->button()
                    ->color('primary')
                    ->visible(fn ($record) => $record->amount_filled < $record->amount) // Sembunyi kalau sudah selesai
                    ->form([
                        Select::make('character_id')
                            ->label('Pilih Akun Farm')
                            ->options(function ($record) {
                                // Hanya tampilkan karakter yg punya resource sesuai tipe ini
                                // Dan berada di kingdom yang sama dengan order
                                $order = $record->order; 
                                return \App\Models\Character::where('kingdom_id', $order->kingdom_id)
                                    ->where($record->resource_type, '>', 2000000) // Minimal punya stok 2M
                                    ->pluck('ign', 'id');
                            })
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan tugas baru
                        OrderTask::create([
                            'order_item_id' => $record->id,
                            'user_id' => auth()->id(), // Admin yang login
                            'character_id' => $data['character_id'],
                        ]);
                        
                        Notification::make()->title('Tugas dimulai!')->success()->send();
                    }),

                // ACTION 2: INPUT PROGRESS (Lapor Sesi)
                Tables\Actions\Action::make('log_session')
                    ->label('Input Sesi')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn ($record) => $record->amount_filled < $record->amount)
                    ->form([
                        // Admin harus memilih Task mana yang dia update (jika dia pegang banyak akun)
                        Select::make('order_task_id')
                            ->label('Akun yang digunakan')
                            ->options(function ($record) {
                                // Cari task milik admin ini untuk item ini
                                return OrderTask::where('order_item_id', $record->id)
                                    ->where('user_id', auth()->id())
                                    ->with('character')
                                    ->get()
                                    ->pluck('character.ign', 'id');
                            })
                            ->required(),

                        TextInput::make('amount_sent')
                            ->label('Jumlah Terkirim (M)')
                            ->placeholder('Contoh: 2.1')
                            ->numeric()
                            ->suffix('M')
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        // Simpan Log Sesi
                        TaskEntry::create([
                            'order_task_id' => $data['order_task_id'],
                            'amount_sent' => $data['amount_sent'],
                        ]);
                        
                        Notification::make()->title('Progress dicatat! Stok berkurang.')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }
}