<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ad")
 */
class AdController extends AbstractController
{
    /**
     * @Route("/", name="ad_index", methods={"GET"})
     */
    public function index(AdRepository $adRepository): Response
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/new", name="ad_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $ad->setAuthor($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ad);
            $entityManager->flush();

            return $this->render('user/show.html.twig', [
                'user' => $user,
                'msg' => 'Evènement crée avec succès !',
                'ads' => $user->getAds()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_show", methods={"GET"})
     */
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
            'user' => $this->getUser(),
            'msg' => "",
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ad_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ad $ad): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ad_index');
        }

        return $this->render('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ad_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ad $ad): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_index');
    }

    /**
     * @Route("/{id}/addParticipate/{userId}", name="ad_addParticipate", methods={"GET","POST"})
     */
    public function addParticipateAction (Request $request, Ad $ad, User $userId): Response
    {
        
        $ad->addParticipant($userId);

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ad);
            $entityManager->flush();

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
            'msg' => 'Activité ajoutée !',
        ]);
    }
    
    /**
     * @Route("/{id}/removeParticipate/{userId}", name="ad_removeParticipate", methods={"GET","POST"})
     */
    public function removeParticipateAction (Request $request, Ad $ad, User $userId): Response
    {
        
        $ad->removeParticipant($userId);

        $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ad);
            $entityManager->flush();

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
            'msg' => 'Vous n\'êtes plus intéressé par l\'activité',
        ]);
    }

}
