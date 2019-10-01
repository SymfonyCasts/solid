<?php

namespace sasquatch\Repositories;

use sasquatch\Models\Picture;

class PictureRepository
{
    public function save( Picture $picture )
    {
        $destination = __DIR__ . '/../../picture_info/' . basename($picture->getFileName());
        $infoFileName = substr( $destination, 0, strrpos($destination, '.') ).'.json';

        file_put_contents( $infoFileName, json_encode( [
            'date' => $picture->getDate()->format('Y/m/d'),
            'author' => $picture->getAuthor(),
            'location' => $picture->getLocation(),
            'fileName' => basename($picture->getFileName()),
        ] ) );
    }
}