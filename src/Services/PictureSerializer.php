<?php


namespace sasquatch\Services;


use sasquatch\Models\Picture;

class PictureSerializer
{
    /**
     * @param \DirectoryIterator $fileInfo
     * @return mixed
     */
    public function unserializePicture(\DirectoryIterator $fileInfo): array
    {
        return json_decode(file_get_contents($fileInfo->getPathname()), true);
    }

    /**
     * @param Picture $picture
     * @return false|string
     */
    public function serializePicture(Picture $picture): string
    {
        return json_encode([
            'date' => $picture->getDate()->format('Y/m/d'),
            'author' => $picture->getAuthor(),
            'location' => $picture->getLocation(),
            'fileName' => basename($picture->getFileName()),
        ]);
    }
}