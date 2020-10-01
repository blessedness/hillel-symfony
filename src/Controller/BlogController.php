<?php

namespace App\Controller;

use App\Commands\BlogCreate;
use App\Entity\Blog;
use App\Events\BlogViewEvent;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Services\BlogService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var PaginatorInterface
     */
    private $pagination;
    /**
     * @var BlogService
     */
    private $service;

    public function __construct(PaginatorInterface $pagination, BlogService $service)
    {
        $this->pagination = $pagination;
        $this->service = $service;
    }

    /**
     * @Route("/", name="blog_index", methods={"GET"})
     * @param  Request  $request
     * @param  BlogRepository  $blogRepository
     * @return Response
     */
    public function index(Request $request, BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $this->pagination->paginate(
                $blogRepository->querySearchForIndex(
                    $request->get('q'),
                    $request->request->has('tag') ? (int)$request->get('tag') : null
                ),
                $request->query->get('page', 1),
                12
            ),
        ]);
    }

    /**
     * @Route("/new", name="blog_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $command = new BlogCreate();
        $form = $this->createForm(BlogType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command->user = $this->getUser();

            $this->service->create($command);

            $this->addFlash('success', 'Blog page created.');

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $command,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_show", methods={"GET"})
     * @param  Blog  $blog
     * @param  EventDispatcherInterface  $dispatcher
     * @return Response
     */
    public function show(Blog $blog, EventDispatcherInterface $dispatcher): Response
    {
        $dispatcher->dispatch(
            new BlogViewEvent($blog)
        );

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Blog $blog): Response
    {
        $command = new BlogCreate($blog->getTitle(), $blog->getDescription());

        $form = $this->createForm(BlogType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->service->update($blog, $command);

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index');
    }
}
