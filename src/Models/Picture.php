<?php

namespace sasquatch\Models;

class Picture
{
    private $author;
    private $date;
    private $fileName;
    private $location;

    public function __construct( string $author, \DateTimeImmutable $date, string $location, string $fileName )
    {
        $this->author = $author;
        $this->date = $date;
        $this->fileName = $fileName;
        $this->location = $location;
    }

    public function getAuthor() : string
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

    public static function findAll() : array
    {
        $dir = new \DirectoryIterator(__DIR__.'/../../uploads');

        $pictures = [];
        foreach ( $dir as $fileInfo ) {
            if ( $fileInfo->getExtension() == 'jpg' ) {
                $metaDataFileName = $fileInfo->getPath().DIRECTORY_SEPARATOR.substr( $fileInfo->getFilename(), 0, -3).'json';
                $metadata = json_decode( file_get_contents( $metaDataFileName ), true );

                $pictures[] = new Picture(
                    $metadata['author'],
                    new \DateTimeImmutable( $metadata['date']),
                    $metadata['location'],
                    $fileInfo->getFilename()
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
}