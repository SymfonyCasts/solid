<?php


namespace Sasquatch\Services;


use Sasquatch\Models\Picture;

/**
 * Class PictureJSONSerializer
 * @package Sasquatch\Services
 */
class PictureJSONSerializer implements IPictureSerializer
{
    /**
     * @param Picture $picture
     * @return string
     */
    public function serialize(Picture $picture): string
    {
        return json_encode([
            'date' => $picture->getDate()->format('Y/m/d'),
            'author' => $picture->getAuthor(),
            'location' => $picture->getLocation(),
            'fileName' => basename($picture->getFileName()),
        ]);
    }

    /**
     * @param string $string
     * @return Picture
     */
    public function unserialize(string $string): Picture
    {
        if ( $data = json_decode($string, true) ) {

            return new Picture(
                $data['author'],
                new \DateTimeImmutable($data['date']),
                $data['location'],
                $data['fileName'],
            );
        }

        throw new \Exception("The string doesn't seem to be valid json");
    }
}