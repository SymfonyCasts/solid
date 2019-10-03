<?php


namespace Sasquatch\Services;


class UploadManager
{
    /**
     * @param array $uploadedFile
     * @return string
     * @throws \Exception
     */
    public function storeUploadedFile( array $uploadedFile )
    {
        $destination = __DIR__.'/../../uploads/'.basename($uploadedFile['name']);
        if (move_uploaded_file($uploadedFile['tmp_name'], $destination)) {

            return $destination;
        }

        throw new \Exception('Couldn\'t store the upload :(');
    }
}