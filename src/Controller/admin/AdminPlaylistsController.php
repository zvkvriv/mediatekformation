<?php
namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class AdminPlaylistsController extends AbstractController {

    const PLAYLISTS = "admin/playlists.html.twig";
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        if($champ === "name"){
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        }
        if($champ === "videos"){
            $playlists = $this->playlistRepository->findAllOrderByVideos($ordre);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
	
    /**
     * @Route("admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("admin/playlists/playlist/{id}", name="admin.playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    /**
     * @Route("/admin/suppr/{id}", name="admin.playlist.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response{
        $this->playlistRepository->remove($playlist, true);
        return $this->redirectToRoute('admin.playlists');
    }

    /**
     * @Route("/admin/playlist/ajout/", name="admin.playlist.ajout")
     * @param Request $request
     * @param Playlist $playlist
     * @return Response
     */

     public function ajout(Request $request): Response{
        $playlist = new Playlist();
        $formPlaylist =$this->createForm(PlaylistType::class, $playlist);

        $formPlaylist->handleRequest($request);

        if(($formPlaylist->isSubmitted())  && ($formPlaylist->isValid())) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/playlist.ajout.html.twig",[
            'formplaylist' => $formPlaylist->createView(),
            'playlist' => $playlist
        ]);
    
    }

    /**
     * @Route("/admin/playlist/edit/{id}", name="admin.playlist.edit")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    public function edit(Playlist $playlist): Response{
        $formPlaylist =$this->createForm(PlaylistType::class, $playlist);
        return $this->render("admin/playlist.edit.html.twig",[
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
}



