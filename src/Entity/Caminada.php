<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caminada.
 *
 * @ORM\Table(name="caminada")
 * @ORM\Entity(repositoryClass="App\Repository\CaminadaRepository")
 */
class Caminada implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="path", type="array")
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="elevation_id", referencedColumnName="id")
     */
    private $elevation;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="leaflet_id", referencedColumnName="id")
     */
    private $leaflet;

    /**
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     */
    private $gpsTrack;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", unique=true)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", unique=true)
     */
    private $year;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path.
     *
     * @param array $path
     *
     * @return Caminada
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return array
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Caminada
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Caminada
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set image.
     *
     * @param File $image
     *
     * @return Caminada
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set map.
     *
     * @param File $map
     *
     * @return Caminada
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map.
     *
     * @return File
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set elevation.
     *
     * @param File $elevation
     *
     * @return Caminada
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Get elevation.
     *
     * @return File
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * Set leaflet.
     *
     * @param File $leaflet
     */
    public function setLeaflet($leaflet)
    {
        $this->leaflet = $leaflet;
    }

    /**
     * Get leaflet.
     *
     * @return File
     */
    public function getLeaflet()
    {
        return $this->leaflet;
    }

    /**
     * Set track.
     *
     * @param File $gpsTrack
     *
     * @return Caminada
     */
    public function setGpsTrack($gpsTrack)
    {
        $this->gpsTrack = $gpsTrack;

        return $this;
    }

    /**
     * Get files.
     *
     * @return File
     */
    public function getGpsTrack()
    {
        return $this->gpsTrack;
    }

    /**
     * Set number.
     *
     * @param int $number
     *
     * @return Caminada
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Caminada
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
