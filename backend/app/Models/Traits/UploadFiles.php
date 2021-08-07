<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait UploadFiles
{
    public $oldFiles = [];

    protected abstract function uploadDir(): string;

    public function relativeFilePath($value)
    {
        return "{$this->uploadDir()}/{$value}";
    }

    public static function bootUploadFiles()
    {
        static::updating(function(Model $model){
            $fieldsUpdated = array_keys($model->getDirty());
            $filesUpdated = array_intersect($fieldsUpdated, self::$fileFields);
            $filesFilters = Arr::where($filesUpdated, function ($fileField) use ($model){
                return $model->getOriginal($fileField); // valor antigo
            });

            $model->oldFiles = array_map(function ($fileField) use ($model){
                return $model->getOriginal($fileField);
            }, $filesFilters);
        });
    }

    /**
     * @param UploadFiles[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile($file)
    {
        $filename = ($file instanceof UploadedFile)? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$filename}");
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileFields as $fileField) {
            if (isset($attributes[$fileField]) && $attributes[$fileField] instanceof UploadedFile) {
                $files[] = $attributes[$fileField];
                $attributes[$fileField] = $attributes[$fileField]->hashName();
            }
        }
        return $files;
    }

    protected function getFileUrl($filename): ?string
    {
        return \Storage::url($this->relativeFilePath($filename));
    }
}
