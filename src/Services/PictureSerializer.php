<?php


namespace Sasquatch\Services;


use Sasquatch\Models\Picture;

class PictureSerializer
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    private $format;

    /**
     * PictureSerializer constructor.
     * @param string $format
     * @throws \Exception
     */
    public function __construct(string $format)
    {
        if ($format != self::FORMAT_XML && $format != self::FORMAT_JSON) {

            throw new \Exception('Format ' . $format . ' is unknown for serialization');
        }
        $this->format = $format;
    }

    /**
     * @param \DirectoryIterator $fileInfo
     * @return Picture
     */
    public function unserializePicture(string $contents): Picture
    {
        switch (strtolower($this->format)) {
            case self::FORMAT_JSON:

                return $this->json2picture($contents);
            case self::FORMAT_XML:

                return $this->xml2picture($contents);
        }
    }

    /**
     * @param Picture $picture
     * @return false|string
     */
    public function serializePicture(Picture $picture): string
    {
        switch ($this->format) {
            case self::FORMAT_JSON:

                return $this->picture2json($picture);
            case self::FORMAT_XML:

                return $this->picture2xml($picture);
        }
    }

    /**
     * @param Picture $picture
     * @return string
     */
    private function picture2xml(Picture $picture): string
    {
        $xml = new \SimpleXMLElement('<picture/>');

        $xml->author = $picture->getAuthor();
        $xml->date = $picture->getDate()->format('Y-m-d');
        $xml->location = $picture->getLocation();
        $xml->fileName = basename($picture->getFileName());

        return $xml->asXML();
    }

    /**
     * @param string $xml
     * @return array
     */
    private function xml2picture(string $xmlString): Picture
    {
        $xml = new \SimpleXMLElement($xmlString);

        return new Picture(
            $xml->xpath('/picture/author')[0],
            new \DateTimeImmutable($xml->xpath('/picture/date')[0]),
            $xml->xpath('/picture/location')[0],
            $xml->xpath('/picture/fileName')[0],
        );
    }

    /**
     * @param Picture $picture
     * @return string
     */
    private function picture2json(Picture $picture): string
    {
        return json_encode([
            'date' => $picture->getDate()->format('Y/m/d'),
            'author' => $picture->getAuthor(),
            'location' => $picture->getLocation(),
            'fileName' => basename($picture->getFileName()),
        ]);
    }

    /**
     * @param string $json
     * @return Picture
     */
    private function json2picture(string $json): Picture
    {
        $data = json_decode($json, true);

        return new Picture(
            $data['author'],
            new \DateTimeImmutable($data['date']),
            $data['location'],
            $data['fileName'],
        );
    }
}