<?php

namespace sasquatch\Models;

class Picture
{
    private $author;
    private $date;
    private $fileName;
    private $location;

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
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
