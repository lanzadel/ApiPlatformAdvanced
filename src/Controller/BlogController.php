<?php
/**
 * Created by PhpStorm.
 * User: Ch'Mohamed
 * Date: 5/19/2020
 * Time: 10:06 AM
 */

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_posts", defaults={"page":1}, requirements={"page"="\d+"})
     */
    public function lists($page = 1, Request $request) {

        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json(
            [
                "page" => $page,
                "limit" => $limit,
                "data" => array_map(function(BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     */
    public function post($post) {
        return $this->json(
            $post
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     */
    public function postBySlug(BlogPost $post) {
        return $this->json(
            $post
        );
    }
    /**
     * @Route("/add", name="add_blog")
     */
    public function add(Request $request) {

        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/delete/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(BlogPost $post) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}