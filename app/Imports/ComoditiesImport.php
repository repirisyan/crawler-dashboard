<?php

namespace App\Imports;

use App\Models\Comodity;
use App\Models\Notification;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ComoditiesImport implements ToModel, WithChunkReading, ShouldQueue, WithEvents, WithHeadingRow
{
    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Comodity([
            'name' => $row['name'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function(ImportFailed $event) {
                Notification::create([
                    'user_id' => $this->user_id,
                    'message' => 'Import Comodity Failed',
                    'category' => 'error'
                ]);
            },
        ];
    }
}
