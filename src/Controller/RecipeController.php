<?php

namespace App\Controller;

use App\Form\RecipeType;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RecipeController extends AbstractController
{
    /**
     * THIS CONTROLLER DISPLAY ALL RECIPES
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    
    #[Route('/recette', name: 'recipe', methods: ['GET'])]
    public function index(
    RecipeRepository $repository 
    ,PaginatorInterface $paginator, 
    Request $request
    ): Response

    {
        $recipes = $paginator->paginate(
            $repository->findAll() ,  
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes ,
        ]);
    }

    #[Route('/recette/creation', 'recipe.new', methods: ['GET' , 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager ): Response {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType:: class, $recipe) ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre Recette à été crée avec succès ! '
            );

            return $this->redirectToRoute('recipe');
            
        }

        return $this->render('pages/recipe/new.html.twig', [
             'form' => $form->createView()]);

    }

    #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', "POST"])]

public function edit(
 Recipe $recipe ,
 Request $request,  
 EntityManagerInterface $manager
 
 ): Response {

    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request); 
    if($form->isSubmitted() && $form->isValid()) {
        $recipe= $form->getData();

        $manager->persist($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre Recette à été modifié avec succés ! '
        );

        return $this->redirectToRoute('recipe');    


    }


    return  $this->render('pages/recipe/edit.html.twig' , [
        'form' =>$form->createView()
    ]); 

 } 

 
 #[Route('/recette/suppresion/{id}',    'recipe.delete', methods: ['GET'])]
 public function delete(EntityManagerInterface $manager,
  Recipe $recipe
   ) :Response
 {
         $manager->remove($recipe);
         $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé ! '
        );
        return $this->redirectToRoute('recipe');

    }}
  
  


