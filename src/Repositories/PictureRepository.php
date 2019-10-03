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

    /**
     * @return Picture[]
     */
    public function findAll(): array
    {
        $dir = new \DirectoryIterator(__DIR__.'/../../picture_info');

        $pictures = [];
        foreach ($dir as $fileInfo) {
            if ('json' == $fileInfo->getExtension()) {
                $metadata = json_decode(file_get_contents($fileInfo->getPathname()), true);

                $pictures[] = new Picture(
                    $metadata['author'],
                    new \DateTimeImmutable($metadata['date']),
                    $metadata['location'],
                    $metadata['fileName']
                );
            }
        }

        return $pictures;
    }

    /**
     * @param string $fileName
     * @param \DateTimeImmutable $date
     * @param string $author
     * @param string $location
     * @return Picture
     */
    public function createFromFile(string $fileName, \DateTimeImmutable $date, string $author, string $location ) : Picture
    {
        return new Picture(
            $author,
            $date,
            $location,
            basename($fileName)
        );
    }
}