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
        if ( $format != self::FORMAT_XML && $format != self::FORMAT_JSON ) {

            throw new \Exception('Format ' . $format . ' is unknown for serialization');
        }
        $this->format = $format;
    }

    /**
     * @param \DirectoryIterator $fileInfo
     * @return mixed
     */
    public function unserializePicture(\DirectoryIterator $fileInfo): array
    {
        switch (strtolower($this->format)) {
            case self::FORMAT_JSON:

                return json_decode(file_get_contents($fileInfo->getPathname()), true);
            case self::FORMAT_XML:

                return $this->xml2array(new \SimpleXMLElement($fileInfo->getPathname(), true));
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

                return json_encode([
                    'date' => $picture->getDate()->format('Y/m/d'),
                    'author' => $picture->getAuthor(),
                    'location' => $picture->getLocation(),
                    'fileName' => basename($picture->getFileName()),
                ]);
            case self::FORMAT_XML:

                return $this->picture2xml($picture)->asXML();
        }
    }

    /**
     * @param Picture $picture
     * @return \SimpleXMLElement
     */
    private function picture2xml(Picture $picture): \SimpleXMLElement
    {
        $xml = new \SimpleXMLElement('<picture/>');
        $xml->author = $picture->getAuthor();

        return $xml;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return array
     */
    private function xml2array(\SimpleXMLElement $xml): array
    {
        return [
            'author' => $xml->xpath('/picture/author')[0],
        ];
    }
}