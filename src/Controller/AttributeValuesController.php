<?php

namespace App\Controller;

use App\Entity\AttributeValues;
use App\Form\AttributeValuesType;
use App\Repository\AttributesRepository;
use App\Repository\AttributeValuesRepository;
use Attribute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web-master/attribute-values")
 */
class AttributeValuesController extends AbstractController
{
    /**
     * @Route("/parent/{id}", name="attribute_values_index", methods={"GET"})
     */
    public function index(AttributeValuesRepository $attributeValuesRepository, AttributesRepository $attributesRepository, int $id): Response
    {
        return $this->render('attribute_values/index.html.twig', [
            'attribute'=>$attributesRepository->findOneBy(['id'=>$id]),
            'attribute_values' => $attributeValuesRepository->findBy(['attribute'=>$attributesRepository->findOneBy(['id'=>$id])]),
        ]);
    }

    /**
     * @Route("/new/{id}", name="attribute_values_new", methods={"GET","POST"})
     */
    public function new(Request $request, int $id, AttributesRepository $attributesRepository): Response
    {
        $attribute = $attributesRepository->findOneBy(['id'=>$id]);

        $attributeValue = new AttributeValues();
        $attributeValue->setAttribute($attribute);
 

        $form = $this->createForm(AttributeValuesType::class, $attributeValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attributeValue);
            $entityManager->flush();

            return $this->redirectToRoute('attribute_values_index', ['id'=>$attribute->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute_values/new.html.twig', [
            'attribute_value' => $attributeValue,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="attribute_values_show", methods={"GET"})
     */
    public function show(AttributeValues $attributeValue): Response
    {
        return $this->render('attribute_values/show.html.twig', [
            'attribute_value' => $attributeValue,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attribute_values_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AttributeValues $attributeValue): Response
    {
        $form = $this->createForm(AttributeValuesType::class, $attributeValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

           return $this->redirectToRoute('attribute_values_index', ['id'=>$attributeValue->getAttribute()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute_values/edit.html.twig', [
            'attribute_value' => $attributeValue,
            'form' => $form,
            'errorMessage'=>$request->query->get('errorMessage') != null ? $request->query->get('errorMessage') : ''
        ]);
    }

    /**
     * @Route("/{id}", name="attribute_values_delete", methods={"POST"})
     */
    public function delete(Request $request, AttributeValues $attributeValue): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete'.$attributeValue->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($attributeValue);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('attribute_values_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->redirectToRoute('attribute_values_edit', ['id'=>$attributeValue->getId(),'success'=>false,'errorMessage'=>"Cet attribut ne peut pas être supprimé."], Response::HTTP_SEE_OTHER);

        }
    }
}
