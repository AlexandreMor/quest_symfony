<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\User;


/**
 * @Route("/comment",name="comment_")
 */

class CommentController extends AbstractController
{
    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        // Check wether the logged in user is the owner of the comment
        
        if (!($this->getUser() == $comment->getAuthor()) && !in_array('ROLE_ADMIN',$this->getUser()->getRoles())) {

            // If not the owner, throws a 403 Access Denied exception

            throw new AccessDeniedException('Only the owner or an admin can delete the comment!');
        }
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        return $this->redirectToRoute('episode_index');
    }
}
