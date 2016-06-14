<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationList;

use AppBundle\Entity\Post;

class PostController extends FOSRestController
{

    /**
     * Return the overall Post List.
     * @ApiDoc(
     *   section = "Post",
     *   description = "Return the overall Post List",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     */

    public function getPostsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('AppBundle:Post');

        $data = $repo->findAll();

        $view = $this->view($data);
        return $this->handleView($view);
    }


    /**
     * Return one Post.
     * @View()
     * @ApiDoc(
     *   section = "Post",
     *   description = "Return one Post",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     */
    public function getPostAction(Post $post)
    {
        return $post;
    }


    /**
     * Create a Post from the submitted data.
     *
     * @ApiDoc(
     *   section = "Post",
     *   resource = true,
     *   description = "Create a Post from the submitted data.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="title", nullable=false, strict=true, description="Title.")
     * @RequestParam(name="slug", nullable=false, strict=true, description="Slug.")
     * @RequestParam(name="content", nullable=false, strict=true, description="Content.")
     */

    public function postPostAction(ParamFetcher $paramFetcher)
    {
        $post = new Post();

        $post->setTitle($paramFetcher->get('title'));
        $post->setSlug($paramFetcher->get('slug'));
        $post->setContent($paramFetcher->get('content'));

        $post->setCreateAt(new \DateTime());
        $post->setUpdateAt(new \DateTime());

        $em = $this->get('doctrine.orm.entity_manager');


        $view = $this->view();

        $errors = $this->get('validator')->validate($user, array('Create'));
        if (count($errors) == 0) {

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($post);
            $em->flush();

            $view->setData($post)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Return one Post.
     * @ApiDoc(
     *   section = "Post",
     *   description = "Return one Post",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="title", nullable=false, strict=false, description="Title.")
     * @RequestParam(name="slug", nullable=false, strict=false, description="Slug.")
     * @RequestParam(name="content", nullable=false, strict=false, description="Content.")
     */
    public function putPostAction(Post $post, ParamFetcher $paramFetcher)
    {
        $paramFetcher->get('title') ? $post->setTitle($paramFetcher->get('title')):0;
        $paramFetcher->get('slug') ? $post->setTitle($paramFetcher->get('slug')):0; 
        $paramFetcher->get('content') ? $post->setTitle($paramFetcher->get('content')):0; 

        $errors = $this->get('validator')->validate($post, array('Update'));

        $view = $this->view();

        if (count($errors) == 0) {

            $em = $this->get('doctrine.orm.entity_manager');
            $post->setUpdateAt(new \DateTime());
            $em->flush();

            $view->setData($user)->setStatusCode(200);
            return $view;
        } else {
            $view = $this->getErrorsView($errors);
            return $view;
        }
    }

    /**
     * Delete Post.
     * @ApiDoc(
     *   section = "Post",
     *   description = "Delete Post",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *   }
     * )
     */
    public function deletePostAction(Post $post)
    {
        if (!$post) {
            throw $this->createNotFoundException('Data not found.');
        }
        
        $em = $this->get('doctrine.orm.entity_manager');

        $em->remove($post);
        $em->flush();

        $view = $this->view();
        $view->setData("User deteled.")->setStatusCode(204);
        return $view;
    }

}
