<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;

class AdminCategoriesController extends AbstractController
{

    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository= $categorieRepository;
    }

    /**
     * @Route("/admin/categories", name="admin.categories")
     */
    public function index(): Response{
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/Categorie/suppr/{id}", name="admin.categorie.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response{
        $this->categorieRepository->remove($categorie, true);
        return $this->redirectToRoute('admin.categories');
    }
    
    /**
     * @Route("/admin/categorie/ajout", name="admin.categorie.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $nomCategorie = $request->get("nom");
        $categorie = new Categorie();
        $categorie->setName($nomCategorie);
        $this->categorieRepository->add($categorie, true);
        return $this->redirectToRoute('admin.categories');
    }
}
