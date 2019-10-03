<?php

namespace sasquatch\Repositories;

use sasquatch\Models\Picture;

class PictureRepository
{
    const PICTURE_FILE_EXTENSION = 'json';

    private $baseDir;

    /**
     * PictureRepository constructor.
     * @param string $baseDir
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @param Picture $picture
     * @throws \Exception
     */
    public function save(Picture $picture)
    {
        $destination = $this->baseDir . DIRECTORY_SEPARATOR . basename($picture->getFileName());
        $infoFileName = substr($destination, 0, strrpos($destination, '.')) . '.' . self::PICTURE_FILE_EXTENSION;

        if (!file_put_contents($infoFileName, $this->serializePicture($picture))) {

            throw new \Exception('Couldn\'t save picture information :(');
        }
    }

    /**
     * @return Picture[]
     */
    public function findAll(): array
    {
        $dir = new \DirectoryIterator($this->baseDir);

        $pictures = [];
        foreach ($dir as $fileInfo) {
            if ($this->isPictureFile($fileInfo)) {
                $metadata = $this->unserializePicture($fileInfo);

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
    public function createFromFile(string $fileName, \DateTimeImmutable $date, string $author, string $location): Picture
    {
        return new Picture(
            $author,
            $date,
            $location,
            basename($fileName)
        );
    }

    /**
     * @param Picture $picture
     * @return false|string
     */
    private function serializePicture(Picture $picture): string
    {
        return json_encode([
            'date' => $picture->getDate()->format('Y/m/d'),
            'author' => $picture->getAuthor(),
            'location' => $picture->getLocation(),
            'fileName' => basename($picture->getFileName()),
        ]);
    }

    /**
     * @param \DirectoryIterator $fileInfo
     * @return bool
     */
    private function isPictureFile(\DirectoryIterator $fileInfo): bool
    {
        return self::PICTURE_FILE_EXTENSION == $fileInfo->getExtension();
    }

    /**
     * @param \DirectoryIterator $fileInfo
     * @return mixed
     */
    private function unserializePicture(\DirectoryIterator $fileInfo): array
    {
        return json_decode(file_get_contents($fileInfo->getPathname()), true);
    }
}