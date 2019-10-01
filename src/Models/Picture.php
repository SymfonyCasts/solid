<?php

namespace sasquatch\Models;

class Picture
{
    private $author;
    private $date;
    private $fileName;
    private $location;

    public function __construct(string $author, \DateTimeImmutable $date, string $location, string $fileName)
    {
        $this->author = $author;
        $this->date = $date;
        $this->fileName = $fileName;
        $this->location = $location;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return Picture[]
     */
    public static function findAll(): array
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

    public function getFileName(): string
    {
        return $this->fileName;
    }

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

    public static function createFromUpload(array $uploadedFile, \DateTimeImmutable $date, string $author, string $location): Picture
    {
        $destination = __DIR__.'/../../uploads/'.basename($uploadedFile['name']);
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

    public function save()
    {
        $destination = __DIR__.'/../../picture_info/'.basename($this->getFileName());
        $infoFileName = substr($destination, 0, strrpos($destination, '.')).'.json';

        file_put_contents($infoFileName, json_encode([
            'date' => $this->getDate()->format('Y/m/d'),
            'author' => $this->getAuthor(),
            'location' => $this->getLocation(),
            'fileName' => basename($this->getFileName()),
        ]));
    }
}
