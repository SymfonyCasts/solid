<?php


namespace Sasquatch\Services;


use Sasquatch\Models\Picture;

class PictureSerializer
{
    /**
     * @param \DirectoryIterator $fileInfo
     * @return Picture
     */
    public function unserializePicture(string $contents): Picture
    {
        $data = json_decode($contents, true);

        return new Picture(
            $data['author'],
            new \DateTimeImmutable($data['date']),
            $data['location'],
            $data['fileName']
        );
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