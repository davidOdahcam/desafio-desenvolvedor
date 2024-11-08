<?php

namespace App\Imports;

use App\Enums\UploadStatusEnum;
use App\Models\FileRecord;
use App\Models\Upload;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FileImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithEvents
{
    use RegistersEventListeners;

    public function __construct(private readonly Upload $upload) {}

    public function model(array $row)
    {
        $attributes = array_merge($row, ['upload_id' => $this->upload->id]);

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

    public function beforeImport()
    {
        $this->upload->fill([
            'status' => UploadStatusEnum::PROCESSING,
        ])->saveQuietly();
    }

    public function afterImport()
    {
        $this->upload->updateStatus(UploadStatusEnum::PROCESSING);
    }

    public function afterBatch()
    {
        $this->upload->updateStatus(UploadStatusEnum::COMPLETED);
    }
}
