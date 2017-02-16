<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Caminada;
use App\Service\FileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    private $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @Route("/fotos", name="gallery", methods={"GET"})
     */
    public function galleryAction(Request $request)
    {
        $albums = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Album::class)
            ->createQueryBuilder('a')
            ->join('a.caminada', 'c')
            ->orderBy('c.number', 'desc')
            ->getQuery()
            ->getResult();

        $parameters = [
            'albums' => $albums,
        ];

        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->buildForm();

            $parameters['form'] = $form->createView();
        }

        return $this->render('web/gallery.html.twig', $parameters);
    }

    /**
     * @Route("/fotos/albums", name="publish_photos", methods={"POST"})
     */
    public function publishPhotosAction(Request $request)
    {
        $formAlbum = new Album();
        $form      = $this->buildForm($formAlbum);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Album $formAlbum */
            $formAlbum = $form->getData();
            $caminada  = $formAlbum->getCaminada();
            if (!$caminada instanceof Caminada) {
                $caminada = $this->getDoctrine()->getRepository(Caminada::class)->find($caminada);
                $formAlbum->setCaminada($caminada);
            }

            $uploadedPhotos = $this->handlePhotosUpload($formAlbum->getPhotos());

            $album = $this
                ->getDoctrine()
                ->getRepository(Album::class)
                ->createQueryBuilder('a')
                ->join('a.caminada', 'c')
                ->where('c.id = ?1')
                ->setParameter(1, $caminada->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if ($album instanceof Album) {
                array_unshift($uploadedPhotos, ...$album->getPhotos());
            } else {
                $album = $formAlbum;
            }

            $album->setPhotos($uploadedPhotos);

            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
        }

        return $this->redirectToRoute('gallery');
    }

    /**
     * @Route("/fotos/albums/{number}", name="album", methods={"GET"})
     *
     * @param mixed $number
     */
    public function albumAction($number)
    {
        $album = $this
            ->getDoctrine()
            ->getRepository(Album::class)
            ->createQueryBuilder('a')
            ->join('a.caminada', 'c')
            ->where('c.number = ?1')
            ->setParameter(1, $number)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }

        return $this->render('web/album.html.twig', [
            'album' => $album,
        ]);
    }

    private function buildForm($data = null): FormInterface
    {
        /** @var Caminada[] $caminades */
        $caminades = $this
            ->getDoctrine()
            ->getRepository(Caminada::class)
            ->createQueryBuilder('c')
            ->orderBy('c.number', 'desc')
            ->getQuery()
            ->getResult();

        $choices = [];
        foreach ($caminades as $caminada) {
            $name           = sprintf('%da Caminada (%d)', $caminada->getNumber(), $caminada->getYear());
            $choices[$name] = $caminada->getId();
        }

        return $this
            ->createFormBuilder($data)
            ->setAction($this->generateUrl('publish_photos'))
            ->add('caminada', ChoiceType::class, [
                    'choices' => $choices,
            ])
            ->add('photos', FileType::class, ['multiple' => true])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-primary',
                ],
            ])
            ->getForm();
    }

    /**
     * @param UploadedFile|string $photos
     *
     * @return string[]
     */
    private function handlePhotosUpload(array $photos): array
    {
        $uploadedPhotos = [];
        foreach ($photos as $photo) {
            if ($photo instanceof UploadedFile) {
                $file             = $this->fileHandler->upload($photo);
                $uploadedPhotos[] = $file;
                $this->getDoctrine()->getManager()->persist($file);
            }
        }

        return $uploadedPhotos;
    }
}
