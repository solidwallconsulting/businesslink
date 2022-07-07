<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Town;
use App\Form\CityType;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web-master/city")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/list-parent/{id}", name="city_index", methods={"GET"})
     */
    public function index(CityRepository $cityRepository,Town $town): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findBy(['town'=>$town]),
            'town' => $town,
            
        ]);
    }

    /**
     * @Route("/new/parent/{id}", name="city_new", methods={"GET","POST"})
     */
    public function new(Request $request,Town $town): Response
    {
        $city = new City();
        $city->setTown($town);

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index', ['id'=>$town->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('city/new.html.twig', [
            'city' => $city,
            'form' => $form,
            'town' => $town,
        ]);
    }

    /**
     * @Route("/{id}", name="city_show", methods={"GET"})
     */
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('city_index', ['id'=>$city->getTown()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
            'errorMessage'=>$request->query->get('errorMessage') != null ? $request->query->get('errorMessage') : ''
        ]);
    }

    /**
     * @Route("/{id}", name="city_delete", methods={"POST"})
     */
    public function delete(Request $request, City $city): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($city);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('city_index', ['id'=>$city->getTown()->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $th) {
            return $this->redirectToRoute('city_edit', ['id'=>$city->getId(),'success'=>false,'errorMessage'=>'Cette délégation ne peut pas être supprimée car elle a des annonces associés'], Response::HTTP_SEE_OTHER);

        }
    }
}
