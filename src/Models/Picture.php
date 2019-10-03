<?php

namespace Sasquatch\Models;

/**
 * Picture Model
 * @package Sasquatch\Models
 */
class Picture
{
    private $author;
    private $date;
    private $fileName;
    private $location;

    const PICTURE_FILE_EXTENSION = 'json';
    const INFO_DIR = __DIR__ . '/../../picture_info';
    const UPLOADS_DIR = __DIR__ . '/../../uploads';

    /**
     * Picture constructor.
     * @param string $author
     * @param \DateTimeImmutable $date
     * @param string $location
     * @param string $fileName
     */
    public function __construct(string $author, \DateTimeImmutable $date, string $location, string $fileName)
    {
        $this->author = $author;
        $this->date = $date;
        $this->fileName = $fileName;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return Picture[]
     */
    public static function findAll(): array
    {
        $dir = new \DirectoryIterator(self::INFO_DIR);

        $pictures = [];
        foreach ($dir as $fileInfo) {
            if (self::PICTURE_FILE_EXTENSION == $fileInfo->getExtension()) {
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
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Returns an HTML representation of the picture object
     * @return string
     */
    public function render(): string
    {
        return "
            <div class='col-sm-4 mb-5'>
                <img src='show?file={$this->getFileName()}' class='big-foot-img'/>
                <p class='mt-3 mb-0'>Image taken by: <strong>{$this->getAuthor()}</strong></p> 
                <p class='mb-0'>Coordinates: <strong>{$this->getLocation()}</strong></p>
                <p class='mb-0'>Date: <strong>{$this->getDate()->format('d/m/Y')}</strong></p>
            </div>
        ";
    }

    /**
     * @param array $uploadedFile
     * @param \DateTimeImmutable $date
     * @param string $author
     * @param string $location
     * @return Picture
     * @throws \Exception
     */
    public static function createFromUpload(array $uploadedFile, \DateTimeImmutable $date, string $author, string $location): Picture
    {
        $destination = self::UPLOADS_DIR . DIRECTORY_SEPARATOR . basename($uploadedFile['name']);
        if (move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
            return new Picture(
                $author,
                $date,
                $location,
                basename($uploadedFile['name'])
            );
        } else {
            throw new \Exception('Couldn\'t store the upload :(');
        }
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        $destination = self::INFO_DIR . DIRECTORY_SEPARATOR . basename($this->getFileName());
        $infoFileName = substr($destination, 0, strrpos($destination, '.')) .'.'. self::PICTURE_FILE_EXTENSION;

        if (!file_put_contents($infoFileName, json_encode([
            'date' => $this->getDate()->format('Y/m/d'),
            'author' => $this->getAuthor(),
            'location' => $this->getLocation(),
            'fileName' => basename($this->getFileName()),
        ]))) {

            throw new \Exception('Couldn\'t save the picture information :(');
        }
    }
}