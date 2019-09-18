<?php

namespace sasquatch\Models;

class Picture
{
    private $author;
    private $date;
    private $path;
    private $location;

    public function __construct( string $author, \DateTimeImmutable $date, string $location, string $path )
    {
        $this->author = $author;
        $this->date = $date;
        $this->path = $path;
        $this->location = $location;
    }

    public function getAuthor()
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
                    $fileInfo->getPath().DIRECTORY_SEPARATOR.$fileInfo->getFilename()
                );
            }
        }

        return $pictures;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function save()
    {
    }
}