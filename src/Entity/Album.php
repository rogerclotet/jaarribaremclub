<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Album.
 *
 * @ORM\Table(name="album")
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 */
class Album
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
     * @var Caminada
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Caminada")
     * @ORM\JoinColumn(name="caminada_id", referencedColumnName="id")
     */
    private $caminada;

    /**
     * @var File[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinTable(name="photos",
     *      joinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $photos;

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
     * Set caminada.
     *
     * @param Caminada $caminada
     *
     * @return Album
     */
    public function setCaminada($caminada)
    {
        $this->caminada = $caminada;

        return $this;
    }

    /**
     * Get caminada.
     *
     * @return Caminada
     */
    public function getCaminada()
    {
        return $this->caminada;
    }

    /**
     * Set photos.
     *
     * @param File[] $photos
     *
     * @return Album
     */
    public function setPhotos(array $photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photos.
     *
     * @return File[]
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
