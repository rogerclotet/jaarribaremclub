<?php

namespace App\Controller;

use App\Entity\Caminada;
use App\Entity\File;
use App\Form\CaminadaType;
use App\Service\FileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/caminades")
 */
class HistoryController extends AbstractController
{
    private $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @Route("/", name="history", methods={"GET"})
     */
    public function historyAction()
    {
        $parameters = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $parameters['caminades'] = $this
                ->getDoctrine()
                ->getRepository(Caminada::class)
                ->createQueryBuilder('c')
                ->orderBy('c.year', 'asc')
                ->getQuery()
                ->getResult();

            $parameters['form'] = $this->createForm(CaminadaType::class)->createView();
        }

        return $this->render('web/history.html.twig', $parameters);
    }

    /**
     * @Route("/", name="create_caminada", methods={"POST"})
     */
    public function createCaminadaAction(Request $request)
    {
        $caminada = new Caminada();
        $form     = $this->createForm(CaminadaType::class, $caminada);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Caminada $caminada */
            $caminada = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $this->handleCaminadaForm($caminada);

            $em->persist($caminada);
            $em->flush();

            return $this->redirectToRoute('caminada', ['number' => $caminada->getNumber()]);
        }

        return $this->redirectToRoute('history');
    }

    /**
     * @Route("/{number}", name="caminada", methods={"GET"})
     */
    public function caminadaAction(int $number)
    {
        $caminada = $this->getDoctrine()->getRepository(Caminada::class)->findOneBy(['number' => $number]);

        $parameters = [
            'caminada' => $caminada,
        ];

        if ($this->isGranted('ROLE_ADMIN')) {
            $formData = new Caminada();
            $formData->setNumber($caminada->getNumber());
            $formData->setYear($caminada->getYear());
            $formData->setPath(implode(' - ', $caminada->getPath()));
            $formData->setDescription($caminada->getDescription());
            $formData->setNotes($caminada->getNotes());
            $formData->setImage('');
            $formData->setMap('');
            $formData->setElevation('');
            $formData->setLeaflet('');
            $formData->setGpsTrack('');

            $parameters['form'] = $this->createForm(CaminadaType::class, $formData)->createView();
        }

        return $this->render('web/caminada.html.twig', $parameters);
    }

    /**
     * @Route("/{number}", name="edit_caminada", methods={"POST"})
     */
    public function editCaminadaAction(int $number, Request $request)
    {
        $caminada          = $this->getDoctrine()->getRepository(Caminada::class)->findOneBy(['number' => $number]);
        $originalImage     = $caminada->getImage();
        $originalMap       = $caminada->getMap();
        $originalElevation = $caminada->getElevation();
        $originalLeaflet   = $caminada->getLeaflet();
        $originalGpsTrack  = $caminada->getGpsTrack();

        $form = $this->createForm(CaminadaType::class, $caminada);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCaminadaForm(
                $caminada,
                $originalImage,
                $originalMap,
                $originalElevation,
                $originalLeaflet,
                $originalGpsTrack
            );

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('caminada', ['number' => $number]);
    }

    /**
     * @Route("/{number}/delete", name="delete_caminada", methods={"POST"})
     */
    public function deleteCaminadaAction(int $number)
    {
        $caminada = $this->getDoctrine()->getRepository(Caminada::class)->findOneBy(['number' => $number]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($caminada);

        $image       = $caminada->getImage();
        $em->remove($image);
        $this->fileHandler->delete($image);

        $map = $caminada->getMap();
        $em->remove($map);
        $this->fileHandler->delete($map);

        $elevation = $caminada->getElevation();
        $em->remove($elevation);
        $this->fileHandler->delete($elevation);

        $leaflet = $caminada->getLeaflet();
        if ($leaflet instanceof File) {
            $em->remove($leaflet);
            $this->fileHandler->delete($leaflet);
        }

        $track = $caminada->getGpsTrack();
        if ($track instanceof File) {
            $em->remove($track);
            $this->fileHandler->delete($track);
        }

        $em->flush();

        return $this->redirectToRoute('history');
    }

    /**
     * @Route("/{number}/track", name="download_track", methods={"GET"})
     *
     * @param mixed $number
     */
    public function downloadTrackAction($number)
    {
        $track = $this
            ->getDoctrine()
            ->getRepository(Caminada::class)
            ->findOneBy(['number' => $number])
            ->getGpsTrack();

        return $this->redirect(
            $this->getParameter('env(STATIC_URL)') . $track->getPath()
        );
    }

    private function handleCaminadaForm(
        Caminada $caminada,
        File $originalImage = null,
        File $originalMap = null,
        File $originalElevation = null,
        File $originalLeaflet = null,
        File $originalGpsTrack = null
    ): void {
        $em = $this->getDoctrine()->getManager();

        $caminada->setPath(array_map('trim', explode('-', $caminada->getPath())));

        $image = $caminada->getImage();
        if ($image instanceof UploadedFile) {
            $file = $this->fileHandler->upload($image);
            $em->persist($file);
            $caminada->setImage($file);
        } else {
            $caminada->setImage($originalImage);
        }

        $map = $caminada->getMap();
        if ($map instanceof UploadedFile) {
            $file = $this->fileHandler->upload($map);
            $em->persist($file);
            $caminada->setMap($file);
        } else {
            $caminada->setMap($originalMap);
        }

        $elevation = $caminada->getElevation();
        if ($elevation instanceof UploadedFile) {
            $file = $this->fileHandler->upload($elevation);
            $em->persist($file);
            $caminada->setElevation($file);
        } else {
            $caminada->setElevation($originalElevation);
        }

        $leaflet = $caminada->getLeaflet();
        if ($leaflet instanceof UploadedFile) {
            $file = $this->fileHandler->upload($leaflet);
            $em->persist($file);
            $caminada->setLeaflet($file);
        } else {
            $caminada->setLeaflet($originalLeaflet);
        }

        $track = $caminada->getGpsTrack();
        if ($track instanceof UploadedFile) {
            $file = $this->fileHandler->upload($track);
            $em->persist($file);
            $caminada->setGpsTrack($file);
        } else {
            $caminada->setGpsTrack($originalGpsTrack);
        }
    }
}
