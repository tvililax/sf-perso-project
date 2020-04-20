<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\UserRepository;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // /**
    //  * @Route("/new", name="user_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('user_index');
    //     }

    //     return $this->render('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'msg' => '',
            'ads' => $user->getAds(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->render('user/show.html.twig', [
                'user' => $user,
                'msg' => 'Profil modifié avec succès !',
                'ads' => $user->getAds(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'ads' => $user->getAds(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/addfriend/{targetId}", name="user_addfriend", methods={"GET","POST"})
     */
    public function addFriendAction (Request $request, User $id, User $targetId): Response
    {
        
        $id->addFriend($targetId);

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($id);
            $entityManager->flush();

        return $this->render('user/show.html.twig', [
            'user' => $targetId,
            'msg' => 'Vous suivez désormais cet utilisateur',
            'ads' => $targetId->getAds(),
        ]);
    }

    /**
     * @Route("/{id}/removefriend/{targetId}", name="user_removefriend", methods={"GET","POST"})
     */
    public function removeFriendAction (Request $request, User $id, User $targetId): Response
    {
        
        $id->removeFriend($targetId);

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($id);
            $entityManager->flush();

        return $this->render('user/show.html.twig', [
            'user' => $targetId,
            'msg' => 'Vous ne suivez plus cet utilisateur',
            'ads' => $targetId->getAds(),
        ]);
    }
}
