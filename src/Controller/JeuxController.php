<?php
// src/Controller/JeuxController.php
namespace App\Controller;

use PdoJeux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

require_once 'modele/class.PdoJeux.inc.php';

class JeuxController extends AbstractController
{
    /**
     * @Route("/jeux", name="jeux_afficher")
     */
    public function index(SessionInterface $session)
    {
        if ($session->has('idUtilisateur')) {
            $db = PdoJeux::getPdoJeux();
            return $this->afficherJeux($db, -1, -1, 'rien');
        } else {
            return $this->render('connexion.html.twig');
        }
    }

    /**
     * fonction pour afficher la liste des jeux
     * @param $db
     * @param $idJeuModif  positionné si demande de modification
     * @param $idJeuNotif  positionné si mise à jour dans la vue
     * @param $notification  pour notifier la mise à jour dans la vue
     */
    private function afficherJeux(PdoJeux $db, int $idJeuModif, int $idJeuNotif, string $notification)
    {
        $tbJeux = $db->getLesJeux_HP();
        return $this->render('lesJeux.html.twig', array(
            'menuActif' => 'Jeux',
            'tbJeux' => $tbJeux,
            'idJeuModif' => $idJeuModif,
            'idJeuNotif' => $idJeuNotif,
            'notification' => $notification
        ));
    }

    /**
     * @Route("/jeux/ajouter", name="jeux_ajouter")
     */
    public function ajouter(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        if (!empty($request->request->get('txtLibJeu'))) {
            $idJeuNotif = $db->ajouterJeu($request->request->get('txtLibJeu'), $request->request->get('txtNbJeuxDispo'));
            $notification = 'Ajouté';
        }
        return $this->afficherJeux($db, -1, $idJeuNotif, $notification);
    }

    /**
     * @Route("/jeux/demandermodifier", name="jeux_demandermodifier")
     */
    public function demanderModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        return $this->afficherJeux($db, $request->request->get('txtIdJeu'), -1, 'rien');
    }

    /**
     * @Route("/jeux/validerModifier", name="jeux_validerModifier")
     */
    public function validerModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->modifierJeu($request->request->get('txtIdJeu'), $request->request->get('txtLibJeu'), $request->request->get('txtNbJeuxDispo'));
        return $this->afficherJeux($db, -1, $request->request->get('txtIdJeu'), 'Modifié');
    }

    /**
     * @Route("/jeux/supprimer", name="jeux_supprimer")
     */
    public function supprimer(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->supprimerJeu($request->request->get('txtIdJeu'));
        $this->addFlash(
            'success', 'Le jeu a été supprimé'
        );

        return $this->afficherJeux($db, -1, -1, 'rien');
    }
}
