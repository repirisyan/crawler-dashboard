<?php

namespace App\Imports;

use App\Models\Notification;
use App\Models\SupervisionList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\ImportFailed;

class SupervisionListImport implements ShouldQueue, SkipsOnFailure, ToModel, WithChunkReading, WithEvents, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new SupervisionList([
            'name' => $row['name'],
            'category' => $row['category'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                Notification::create([
                    'user_id' => $this->user_id,
                    'message' => 'Import Supervision List Failed',
                    'category' => 'error',
                ]);
            },
        ];
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', Rule::unique('supervision_lists', 'name')],
            '*.category' => ['required']
        ];
    }
}
