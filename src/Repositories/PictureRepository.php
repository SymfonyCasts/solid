<?php

namespace Sasquatch\Repositories;

use Sasquatch\Models\Picture;
use Sasquatch\Services\PictureSerializer;

class PictureRepository
{
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
        if (!file_put_contents($this->getInfoFileName($picture),  (new PictureSerializer())->serialize($picture))) {

            throw new \Exception('Couldn\'t save picture information :(');
        }
    }

    /**
     * @return Picture[]
     */
    public function findAll(): array
    {
        $dir = new \DirectoryIterator($this->baseDir);
        $serializer = new PictureSerializer(PictureSerializer::FORMAT_XML);

        $pictures = [];
        foreach ($dir as $fileInfo) {
            if ($this->isPictureFile($fileInfo)) {
                $pictures[] = $serializer->unserialize(file_get_contents($fileInfo->getPathname()));
            }
        }

        return $pictures;
    }

    /**
     * @param \DirectoryIterator $fileInfo
     * @return bool
     */
    private function isPictureFile(\DirectoryIterator $fileInfo): bool
    {
        return PictureSerializer::FORMAT_XML == $fileInfo->getExtension();
    }

    /**
     * @param Picture $picture
     * @return string
     */
    private function getInfoFileName(Picture $picture): string
    {
        $destination = $this->baseDir . DIRECTORY_SEPARATOR . basename($picture->getFileName());

        return substr($destination, 0, strrpos($destination, '.')) . '.' . PictureSerializer::FORMAT_XML;
    }
}