<?php

namespace App\Controller;

use App\Entity\AttributeCategory;
use App\Entity\AttributeCategoryModel;
use App\Form\AttributeCategoryType;
use App\Repository\AttributeCategoryRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web-master/attribute-category")
 */
class AttributeCategoryController extends AbstractController
{
    /**
     * @Route("/", name="attribute_category_index", methods={"GET"})
     */
    public function index(AttributeCategoryRepository $attributeCategoryRepository): Response
    {
        return $this->render('attribute_category/index.html.twig', [
            'attribute_categories' => $attributeCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="attribute_category_new", methods={"GET","POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {


        $choices = [];
 

        $categories = $categoryRepository->findBy(['parent'=>null]); 
        for ($i=0; $i < sizeof($categories) ; $i++) { 
                $tmp = $categories[$i];

              if (sizeof($tmp->getSubCategories()) == 0 ) {
                    $choices[$tmp->getName()] = $tmp->getId();
              
              }else{
                  // go deeper
                  $subs = [];

                  for ($j=0; $j < sizeof($tmp->getSubCategories()) ; $j++) { 
                      $tmpSub = $tmp->getSubCategories()[$j];
                      $subs[$tmpSub->getName()]= $tmpSub->getId();
                  }

                  $choices[$tmp->getName()] = $subs;
              }
        }


        $attributeCategory = new AttributeCategory();

        $attributeCategoryModel = new AttributeCategoryModel();

        $form = $this->createForm(AttributeCategoryType::class, $attributeCategoryModel,['categories'=>$choices]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $attributeCategory->setAttribute($attributeCategoryModel->getAttribute());
            $attributeCategory->setCategory($categoryRepository->findOneBy(['id'=>$attributeCategoryModel->getCategory()]));
            

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($attributeCategory);
            $entityManager->flush();

            return $this->redirectToRoute('attribute_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute_category/new.html.twig', [
            'attribute_category' => $attributeCategory,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="attribute_category_show", methods={"GET"})
     */
    public function show(AttributeCategory $attributeCategory): Response
    {
        return $this->render('attribute_category/show.html.twig', [
            'attribute_category' => $attributeCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="attribute_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AttributeCategory $attributeCategory, CategoryRepository $categoryRepository): Response
    {
        $model = new AttributeCategoryModel();
        $model->setAttribute($attributeCategory->getAttribute());
        $model->setCategory($attributeCategory->getCategory()->getId());


        $choices = [];
 

        $categories = $categoryRepository->findBy(['parent'=>null]); 
        for ($i=0; $i < sizeof($categories) ; $i++) { 
                $tmp = $categories[$i];

              if (sizeof($tmp->getSubCategories()) == 0 ) {
                    $choices[$tmp->getName()] = $tmp->getId();
              
              }else{
                  // go deeper
                  $subs = [];

                  for ($j=0; $j < sizeof($tmp->getSubCategories()) ; $j++) { 
                      $tmpSub = $tmp->getSubCategories()[$j];
                      $subs[$tmpSub->getName()]= $tmpSub->getId();
                  }

                  $choices[$tmp->getName()] = $subs;
              }
        }




        $form = $this->createForm(AttributeCategoryType::class, $model,['categories'=>$choices]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attributeCategory->setAttribute($model->getAttribute());
            $attributeCategory->setCategory( $categoryRepository->findOneBy(['id'=>$model->getCategory()]) );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attribute_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute_category/edit.html.twig', [
            'attribute_category' => $attributeCategory,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="attribute_category_delete", methods={"POST"})
     */
    public function delete(Request $request, AttributeCategory $attributeCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attributeCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($attributeCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('attribute_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
