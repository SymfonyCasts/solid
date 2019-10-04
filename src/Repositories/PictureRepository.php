<?php

namespace Sasquatch\Repositories;

use Sasquatch\Models\Picture;
use Sasquatch\Services\PictureXMLSerializer;

class PictureRepository
{
    private $baseDir;
    private $serializer;

    /**
     * PictureRepository constructor.
     * @param string $baseDir
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
        $this->serializer = new PictureXMLSerializer();
    }

    /**
     * @param Picture $picture
     * @throws \Exception
     */
    public function save(Picture $picture)
    {
        if (!file_put_contents($this->getInfoFileName($picture),  $this->serializer->serialize($picture))) {

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
            try {
                $pictures[] = $this->serializer->unserialize(file_get_contents($fileInfo->getPathname()));
            } catch ( \Exception $e ) {
                error_log('Couldn\'t unserialize file '.$fileInfo->getPathname());
            }
        }

        return $pictures;
    }

    /**
     * @param Picture $picture
     * @return string
     */
    private function getInfoFileName(Picture $picture): string
    {
        $destination = $this->baseDir . DIRECTORY_SEPARATOR . basename($picture->getFileName());

        return substr($destination, 0, strrpos($destination, '.')) . '.info';
    }
}