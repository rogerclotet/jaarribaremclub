<?php

namespace App\Service;

use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;

class RemoteStreamLoader implements LoaderInterface
{
    /**
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(ImagineInterface $imagine, string $prefix)
    {
        $this->imagine = $imagine;
        $this->prefix  = $prefix;
    }

    public function find($path)
    {
        return $this->imagine->load(file_get_contents($this->prefix . $path));
    }
}
