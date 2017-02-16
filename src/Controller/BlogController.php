<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\NextCaminada;
use App\Entity\Post;
use App\Form\NextCaminadaType;
use App\Form\PostType;
use App\Service\FileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private const POSTS_PER_PAGE = 5;
    private $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @Route("/", name="blog", methods={"GET"})
     */
    public function blogAction(Request $request)
    {
        $page   = $request->get('p', 1);
        if ($page < 0) {
            return $this->redirectToRoute('blog');
        }

        $start = ($page - 1) * self::POSTS_PER_PAGE;

        $posts = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->addOrderBy('p.createdAt', 'desc')
            ->addOrderBy('p.id', 'desc')
            ->setFirstResult($start)
            ->setMaxResults(self::POSTS_PER_PAGE)
            ->getQuery()
            ->getResult();

        $count = intval(
            $this
                ->getDoctrine()
                ->getRepository(Post::class)
                ->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->getQuery()
                ->getSingleScalarResult()
        );

        $next = $this
            ->getDoctrine()
            ->getRepository(NextCaminada::class)
            ->find(NextCaminada::ID);

        if ($next instanceof NextCaminada && $next->getDatetime() < new \DateTime()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($next);
            $em->flush();
            $next = null;
        }

        $parameters = [
            'next'       => $next,
            'posts'      => $posts,
            'pagination' => [
                'page'       => $page,
                'page_count' => $count / self::POSTS_PER_PAGE,
            ],
        ];

        if ($this->isGranted('ROLE_ADMIN')) {
            $results = $this->getDoctrine()->getRepository(Post::class)->findBy([], ['createdAt' => 'desc'], 1);
            $post    = new Post();
            if (!empty($results)) {
                $post->setCreatedAt($results[0]->getCreatedAt());
            }

            $parameters['form_create'] = $this->createForm(PostType::class, $post)->createView();
            $parameters['form_next']   = $this->createForm(NextCaminadaType::class, $next, ['action' => $this->generateUrl('set_next_caminada')])->createView();
        }

        return $this->render('web/blog.html.twig', $parameters);
    }

    /**
     * @Route("/", name="create_post", methods={"POST"})
     */
    public function createPostAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $this->handleImageUpload($post);
            $this->handleFilesUpload($post);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/next", name="set_next_caminada", methods={"POST"})
     */
    public function setNextCaminadaAction(Request $request)
    {
        $next = $this
            ->getDoctrine()
            ->getRepository(NextCaminada::class)
            ->find(NextCaminada::ID);

        if (!$next instanceof NextCaminada) {
            $next = new NextCaminada();
        }

        $form = $this->createForm(NextCaminadaType::class, $next);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NextCaminada $next */
            $next = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($next);
            $em->flush();
        }

        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/{id}/delete", name="delete_post", methods={"POST"})
     *
     * @param mixed $id
     */
    public function deletePostAction($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        try {
            $image = $post->getImage();
            if (!empty($image)) {
                $this->fileHandler->delete($image);
            }

            foreach ($post->getFiles() as $file) {
                $this->fileHandler->delete($file);
            }
        } catch (\Exception $e) {
            // Do nothing, if an image cannot be deleted just ignore it.
        }

        return $this->redirectToRoute('blog');
    }

    private function handleImageUpload(Post $post)
    {
        $uploadedFile = $post->getImage();
        if (!$uploadedFile instanceof UploadedFile) {
            return;
        }

        $file = $this->saveFile($uploadedFile);

        $post->setImage($file);

        $this->getDoctrine()->getManager()->persist($file);
    }

    private function handleFilesUpload(Post $post)
    {
        $postFiles = $post->getFiles();
        if (is_array($postFiles)) {
            $files = [];
            foreach ($postFiles as $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $file = $this->saveFile($uploadedFile);
                    $this->getDoctrine()->getManager()->persist($file);

                    $files[] = $file;
                }
            }

            $post->setFiles($files);
        }
    }

    private function saveFile(UploadedFile $file): File
    {
        return $this->fileHandler->upload($file);
    }
}
