<?php


namespace Sasquatch\Services;


use Sasquatch\Models\Picture;

class PictureXMLSerializer implements IPictureSerializer
{
    /**
     * @param Picture $picture
     * @return string
     */
    public function serialize(Picture $picture): string
    {
        $xml = new \SimpleXMLElement('<picture/>');

        $xml->author = $picture->getAuthor();
        $xml->date = $picture->getDate()->format('Y-m-d');
        $xml->location = $picture->getLocation();
        $xml->fileName = basename($picture->getFileName());

        return $xml->asXML();
    }

    /**
     * @param string $string
     * @return Picture
     */
    public function unserialize(string $string): Picture
    {
        $xml = new \SimpleXMLElement($string);

        return new Picture(
            $xml->xpath('/picture/author')[0],
            new \DateTimeImmutable($xml->xpath('/picture/date')[0]),
            $xml->xpath('/picture/location')[0],
            $xml->xpath('/picture/fileName')[0],
        );
    }
}