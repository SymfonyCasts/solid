<?php

namespace Sasquatch\Services;

use Sasquatch\Models\Picture;

interface IPictureSerializer
{
    public function serialize(Picture $picture) : string;

    public function unserialize(string $string) : Picture;
}