<?php

namespace App\Controller;

use App\Entity\Town;
use App\Form\TownType;
use App\Repository\TownRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web-master/town")
 */
class TownController extends AbstractController
{
    /**
     * @Route("/", name="town_index", methods={"GET"})
     */
    public function index(TownRepository $townRepository): Response
    {
        return $this->render('town/index.html.twig', [
            'towns' => $townRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="town_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $town = new Town();
        $form = $this->createForm(TownType::class, $town);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($town);
            $entityManager->flush();

            return $this->redirectToRoute('town_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('town/new.html.twig', [
            'town' => $town,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="town_show", methods={"GET"})
     */
    public function show(Town $town): Response
    {
        return $this->render('town/show.html.twig', [
            'town' => $town,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="town_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Town $town): Response
    {
        $form = $this->createForm(TownType::class, $town);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('town_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('town/edit.html.twig', [
            'town' => $town,
            'form' => $form,
            'errorMessage'=>$request->query->get('errorMessage') != null ? $request->query->get('errorMessage') : ''
        ]);
    }

    /**
     * @Route("/{id}", name="town_delete", methods={"POST"})
     */
    public function delete(Request $request, Town $town): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete'.$town->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($town);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('town_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $th) {
            return $this->redirectToRoute('town_edit', ['id'=>$town->getId(),'success'=>false,'errorMessage'=>'Cette Gouvernorat ne peut pas être supprimée car elle a des propriétés associés'], Response::HTTP_SEE_OTHER);

        }



           
    }
}
