<?php

namespace App\Imports;

use App\Enums\FileStatusEnum;
use App\Models\FileRecord;
use App\Models\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Events\AfterBatch;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

class FileImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithEvents
{
    public function __construct(private readonly File $file) {}

    public function model(array $row)
    {
        $attributes = array_merge($row, ['file_id' => $this->file->id]);

        return (new FileRecord($attributes));
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $this->file->updateStatus(FileStatusEnum::PROCESSING);
            },
            AfterImport::class => function(AfterImport $event) {
                $this->file->updateStatus(FileStatusEnum::COMPLETED);
            },
            ImportFailed::class => function(ImportFailed $event) {
                $this->file->updateStatus(FileStatusEnum::FAILED);
            }
        ];
    }
}
