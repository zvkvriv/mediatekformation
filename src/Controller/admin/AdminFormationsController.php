<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur des formations
 *
 * @author emds
 */
class AdminFormationsController extends AbstractController {

    const ROUTE = "admin/formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("admin/formations", name="admin.formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ROUTE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ROUTE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ROUTE, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("admin/formations/formation/{id}", name="admin.formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }

    /**
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): Response{
        $this->formationRepository->remove($formation, true);
        return $this->redirectToRoute('admin.formations');
    }

    /**
     * @Route("/admin/formation/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation): Response{
        $formFormation =$this->createForm(FormationType::class, $formation);
        return $this->render("admin/formation.edit.html.twig",[
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
    }

    /**
     * @Route("/admin/ajout/", name="admin.formation.ajout")
     * @param Request $request
     * @param Formation $formation
     * @return Response
     */

        public function ajout(Request $request): Response{
            $formation = new Formation();
            $formFormation =$this->createForm(FormationType::class, $formation);

            $formFormation->handleRequest($request);

            if(($formFormation->isSubmitted())  && ($formFormation->isValid())) {
                $this->formationRepository->add($formation, true);
                return $this->redirectToRoute('admin.formation');
            }
            return $this->render("admin/formation.ajout.html.twig",[
                'formformation' => $formFormation->createView(),
                'formation' => $formation
            ]);

    
}
}

