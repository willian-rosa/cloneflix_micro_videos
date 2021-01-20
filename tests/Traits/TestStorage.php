<?php


namespace Tests\Traits;


trait TestStorage
{
    protected function deleteAllFiles()
    {
        $directories = \Storage::directories();
        foreach ($directories as $directory) {
            $files = \Storage::files($directory);
            \Storage::delete($files);
            \Storage::deleteDirectory($directory);
        }
    }
}
