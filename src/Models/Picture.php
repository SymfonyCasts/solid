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

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param array $uploadedFile
     * @param \DateTimeImmutable $date
     * @param string $author
     * @param string $location
     * @return Picture
     * @throws \Exception
     */
    public static function createFromUpload( array $uploadedFile, \DateTimeImmutable $date, string $author, string $location ) : Picture
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
}
