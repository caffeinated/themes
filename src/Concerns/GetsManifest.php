<?php

namespace Caffeinated\Themes\Concerns;

use Illuminate\Support\Facades\File;
use ReflectionClass;

trait GetsManifest
{
    /**
     * Get directory of inheriting class.
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getDirectory()
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }

    protected function getManifest()
    {
        $moduleJsonPath = realpath($this->getDirectory() . '/../../theme.json');

        return json_decode(File::get($moduleJsonPath), true);
    }
}
