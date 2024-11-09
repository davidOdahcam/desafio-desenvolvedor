<?php

namespace App\Imports;

use App\Models\FileRecord;
use App\Models\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FileImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use RegistersEventListeners;

    public function __construct(private readonly File $file) {}

    public function model(array $row)
    {
        $attributes = array_merge($row, ['file_id' => $this->file->id]);

        return (new FileRecord($attributes));
    }

    public function batchSize(): int
    {
        return 10000;
    }

    public function chunkSize(): int
    {
        return 10000;
    }

    public function headingRow(): int
    {
        return 2;
    }
}
