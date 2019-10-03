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
     * @param string $fileName
     * @param \DateTimeImmutable $date
     * @param string $author
     * @param string $location
     * @return Picture
     */
    public static function createFromFile(string $fileName, \DateTimeImmutable $date, string $author, string $location ) : Picture
    {
        return new Picture(
            $author,
            $date,
            $location,
            basename($fileName)
        );
    }
}
