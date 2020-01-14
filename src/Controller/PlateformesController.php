<?php
// src/Controller/PlateformesController.php
namespace App\Controller;

use PdoJeux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

require_once 'modele/class.PdoJeux.inc.php';

class PlateformesController extends AbstractController
{
    /**
     * @Route("/plateformes", name="plateformes_afficher")
     */
    public function index(SessionInterface $session)
    {
        if ($session->has('idUtilisateur')) {
            $db = PdoJeux::getPdoJeux();
            return $this->afficherPlateformes($db, -1, -1, 'rien');
        } else {
            return $this->render('connexion.html.twig');
        }
    }

    /**
     * fonction pour afficher la liste des plateformes
     * @param $db
     * @param $idPlateformeModif  positionné si demande de modification
     * @param $idPlateformeNotif  positionné si mise à jour dans la vue
     * @param $notification  pour notifier la mise à jour dans la vue
     */
    private function afficherPlateformes(PdoJeux $db, int $idPlateformeModif, int $idPlateformeNotif, string $notification)
    {
        $tbMembres = $db->getLesPersonnes_HP();
        $tbPlateformes = $db->getLesPlateformes();
        return $this->render('lesPlateformes.html.twig', array(
            'menuActif' => 'Jeux',
            'tbPlateformes' => $tbPlateformes,
            'tbMembres' => $tbMembres,
            'idPlateformeModif' => $idPlateformeModif,
            'idPlateformeNotif' => $idPlateformeNotif,
            'notification' => $notification
        ));
    }

    /**
     * @Route("/plateformes/ajouter", name="plateformes_ajouter")
     */
    public function ajouter(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        if (!empty($request->request->get('txtLibPlateforme'))) {
            $idPlateformeNotif = $db->ajouterPlateforme($request->request->get('txtLibPlateforme'), $request->request->get('lstPersonnes'));
            $notification = 'Ajouté';
        }
        return $this->afficherPlateformes($db, -1, $idPlateformeNotif, $notification);
    }

    /**
     * @Route("/plateformes/demandermodifier", name="plateformes_demandermodifier")
     */
    public function demanderModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        return $this->afficherPlateformes($db, $request->request->get('txtIdPlateforme'), -1, 'rien');
    }

    /**
     * @Route("/plateformes/validerModifier", name="plateformes_validerModifier")
     */
    public function validerModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->modifierPlateforme($request->request->get('txtIdPlateforme'), $request->request->get('txtLibPlateforme'), $request->request->get('lstPersonnes'));
        return $this->afficherPlateformes($db, -1, $request->request->get('txtIdPlateforme'), 'Modifié');
    }

    /**
     * @Route("/plateformes/supprimer", name="plateformes_supprimer")
     */
    public function supprimer(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->supprimerPlateforme($request->request->get('txtIdPlateforme'));
        $this->addFlash(
            'success', 'Le plateforme a été supprimé'
        );

        return $this->afficherPlateformes($db, -1, -1, 'rien');
    }
}
