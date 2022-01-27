<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Entity\Vente;
use Psr\Log\LoggerInterface;
use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Vendeur;
use App\Entity\Prestation;
use App\Form\ClientFormType;
use App\Form\ArticleFormType;
use App\Form\ContactType;
use App\Form\VendeurFormType;
use App\Form\PrestationFormType;
use App\Repository\VendeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class WcController extends AbstractController
{
    /**
     * @Route("/wc", name="wc")
     */
    public function index()
    {
        return $this->render('wc/index.html.twig', [
            'controller_name' => 'WcController',
        ]);
    }



    /**
     * @Route("/vendeur", name="vendeur")
     */
    public function vendeur(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

            //Recuperer liste des vendeurs
            $repo = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vendeur::class) 
                    ->findAll();

            
            /////////////////////////////////////////////////////////////////////Creation vendeur//////////////////////////////////////////////////////////////////////// 
            $vendeur = new Vendeur();
            
            $form = $this->createForm(VendeurFormType::class, $vendeur);
            
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){    
                $manager->persist($vendeur);
                $manager->flush(); 

                return $this->redirectToRoute('vendeur');
    
            }
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->render('wc/vendeur.html.twig', [
            'formSignup' => $form->createView(),
            'listeVendeurs' => $repo
        ]);
    }



    /**
     * @Route("/reglerStock", name="reglerStock")
     */
    public function reglerStocks(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        //Recuperer liste des vendeurs
        $repo = $this->getDoctrine()
                ->getManager()
                ->getRepository(Prestation::class) 
                ->findBy(array('isArticle' =>true));

        $marques = array();

        foreach($repo as $r){
           $marques[$r->getMarque()] = $r->getMarque();
        }

        
        
        /////////////////////////////////////////////////////////////////////Creation vendeur//////////////////////////////////////////////////////////////////////// 
        $article = new Prestation();
        
        $form = $this->createForm(ArticleFormType::class, $article);
        
        $form->handleRequest($request);
        
        
        
        if($form->isSubmitted() && $form->isValid()){  
            $check = false;  
            $article->setIsArticle(1);
            $article->setCategory("Articles");
            foreach($repo as $r){
                if($article->getNom() == $r->getNom()){
                    $r->setPrix($article->getPrix());
                    $r->setStock($article->getStock());
                    $r->setMarque($article->getMarque());
                    $r->setCategory("Articles");
                    $check = true;
                    $manager->persist($r);
                    $manager->flush(); 
                    break;
                }
            } 
            if ($check == false){
                $manager->persist($article);
                $manager->flush(); 
            } 

            return $this->redirectToRoute('reglerStock');

        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->render('wc/reglerStock.html.twig', [
            'formSignup' => $form->createView(),
            'listeArticle' => $repo,
            'marques' => $marques
        ]);
    }

    /**
     * @Route("/prestation", name="prestation")
     */
    public function prestation(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, LoggerInterface $logger){

        //Recuperer liste des vendeurs
        $repo = $this->getDoctrine()
                ->getManager()
                ->getRepository(Prestation::class) 
                ->findBy(array('isArticle' => false));
                
        $categories = array();

        foreach($repo as $r){
           $categories[$r->getCategory()] = $r->getCategory();
        }

        /////////////////////////////////////////////////////////////////////Creation vendeur//////////////////////////////////////////////////////////////////////// 
        $prestation = new Prestation();
        $prestation->setIsArticle(0);
        
       

        $form = $this->createForm(PrestationFormType::class, $prestation);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){  
            $check = false; 
            foreach($repo as $r){
                if($prestation->getNom() == $r->getNom()){
                    $r->setPrix($prestation->getPrix());
                    $r->setCategory($prestation->getCategory());
                    $check = true;
                    $manager->persist($r);
                    $manager->flush(); 
                }
            }
            if($check == false){
                $manager->persist($prestation);
                $manager->flush(); 
            } 
            return $this->redirectToRoute('prestation');

        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

       


        return $this->render('wc/prestation.html.twig', [
            'formSignup' => $form->createView(),
            'listePrestations' => $repo,
            'categories' => $categories 
        ]);
    }

    /**
     * @Route("/nouveauClient", name="nouveauClient")
     */
    public function nouveauClient(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        /////////////////////////////////////////////////////////////////////Creation vendeur//////////////////////////////////////////////////////////////////////// 
        $client = new Client();
        
        $form = $this->createForm(ClientFormType::class, $client);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){    
            $manager->persist($client);
            $manager->flush(); 

            return $this->redirectToRoute('vente', ['idClient' => $client->getId()]);

        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->render('wc/nouveauClient.html.twig', [
            'formSignup' => $form->createView()
        ]);
    }

    /**
   * @Route("/supprimerPresta", name="supprimerPresta", methods={"POST"})
   */
  public function supprimerPresta(Request $request,EntityManagerInterface $em, LoggerInterface $logger)
  {
    $logger->error('tu es la motherfucker');
    $var = $request->request->get('idPresta');
    $presta = $this->getDoctrine()
    ->getManager()
    ->getRepository(Prestation::class)
    ->findOneBy(array('id' => $var));

    $em->remove($presta);
    $em->flush();

    return $this->redirectToRoute('wc', []);
  }

    /**
     * @Route("/rechercheClient", name="rechercheClient")
     */
    public function rechercheClient(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        /////////////////////////////////////////////////////////////////////Creation vendeur//////////////////////////////////////////////////////////////////////// 
        $client = new Client();
        
        $form = $this->createForm(ClientFormType::class, $client);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){    
            $manager->persist($client);
            $manager->flush(); 

            return $this->redirectToRoute('rechercheClient');

        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->render('wc/rechercheClient.html.twig', [
            'formSignup' => $form->createView()
        ]);
    }

    /**
     * @Route("/client/{idClient}/vente", name="vente")
     */
    public function vente($idClient, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $repoVendeur = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vendeur::class) 
                    ->findAll();

        $repoPrestation = $this->getDoctrine()
                ->getManager()
                ->getRepository(Prestation::class) 
                ->findAll();

        $categories = array();

        foreach($repoPrestation as $r){
            $categories[$r->getCategory()] = $r->getCategory();
        }

        

        
        $client = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Client::class)
                    ->findOneBy(array('id' => $idClient));

        $vente = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vente::class)
                    ->findOneBy(array('client' => $client), array('id' => 'DESC'));

        if ($vente == null){
            return $this->render('wc/vente.html.twig', [
                'idClient' => $client->getId(),
                'nomClient' => $client->getNom(),
                'prenomClient' => $client->getPrenom(),
                'vente' => null,
                'derniereVisite' => null,
                'listePrestations' => $repoPrestation,
                'listeVendeurs' => $repoVendeur,
                'categories' => $categories 
            ]);
        }
        return $this->render('wc/vente.html.twig', [
            'idClient' => $client->getId(),
            'nomClient' => $client->getNom(),
            'prenomClient' => $client->getPrenom(),
            'vente' => $vente,
            'derniereVisite' => $vente->getDate(),
            'listePrestations' => $repoPrestation,
            'listeVendeurs' => $repoVendeur,
            'categories' => $categories 
        ]);
    }

    /**
     * @Route("/search", name="ajax_search")
     */
    public function creerVente(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
    }

    /**
     * @Route("/recu",name="recu")
     */
    public function recu(Request $request, EntityManagerInterface $manager){

        $vente = new Vente();

        $vente->setDate((new \DateTime()));

        $data = json_decode($request->getContent(), true);
        
        $vente->setCommentaire($data['commentaire']);


        $idClient = $data['idClient'];

        $client = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Client::class)
                    ->findOneBy(array('id' => $idClient));
           
        $vente->setClient($client);

        $idVendeur = $data['idVendeur'];

        $vendeur = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vendeur::class)
                    ->findOneBy(array('id' => $idVendeur));

        $vente->setVendeur($vendeur);

        $vente->setPrix($data['prixTTL']);

        $ticket = $data['ticket'];

        $manager->persist($vente);

        foreach($ticket as $l){
            $ligne = new Ligne();

            $ligne->setVente($vente);

            $presta = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Prestation::class)
                    ->findOneBy(array('id' => $l["idPresta"]));
            
            if($presta->getIsArticle()){
                $presta->setStock($presta->getStock() - 1);
            }            

            $ligne->setNomPresta($presta);

            $ligne->setPrixInd($l['prixInd']);

            $ligne->setRemise($l['remise']);

            $ligne->setPrixFinal($l['prixF']);

            $manager->persist($ligne);
            $manager->flush(); 
        }
        
        $manager->persist($vente);
        $manager->flush(); 

        return new Response($vente->getId());
    }

    /**
     * @Route("/recuVente/{idVente}",name="recuVente")
     */
    public function recuVente($idVente, Request $request){
        $vente = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vente::class)
                    ->findOneBy(array('id' => $idVente));

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        
        
        return $this->render('wc/recuVente.html.twig', [
             'vente' => $vente,  
             'contactForm' => $form->createView()             
       ]);
        
    }
    
  /**
   * @Route("/sendEmail", name="sendEmail")
   */
  public function sendEmail(Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer,  LoggerInterface $logger)
  {

    $params = $request->query->all();
    $clientId=$params['idClient'];
    $venteId=$params['idVente'];


    $vente = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Vente::class)
                    ->findOneBy(array('id' => $venteId));    
                 

    $client = $this->getDoctrine()
                    ->getManager()
                    ->getRepository(Client::class)
                    ->findOneBy(array('id' => $clientId));


    $form = $this->createForm(ContactType::class);
    $form->handleRequest($request);
                     
    $contact = $form->getData();
                        
    //Ici on envoit le mail
    $message = (new \Swift_Message('Recu de votre visite chez WordlCut'))
                                //on attribut l'expediteur
         ->setFrom("worldcutparisrecu@gmail.com")
                                
                                //on attribut le destinataire
        ->setTo($vente->getClient()->getEmail())
            
                                //on cree le message avec la vue twig
        ->setBody(
        $this->renderView(
            'email/contact.html.twig', compact('vente'),       
        ),
        'text/html'
        );
            
        //on envoie le message
        $mailer->send($message);
            
        $this->addFlash('message', 'Le message a bien ete envoyé');

                    
    return $this->render('wc/recuVente.html.twig', [
         'vente' => $vente,       
         'contactForm' => $form->createView()  
    ]);
  }

   /**
   * @Route("/search", name="ajax_search")
   */
  public function searchAction(Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      $requestString = $request->get('q');

      $entities =  $em->getRepository(Client::class)->findEntitiesByString($requestString);

      if(!$entities) {
          $result['entities']['error'] = "Aucun client n'a ete trouvé";
      } else {
          $result['entities'] = $this->getRealEntities($entities);
      }

      return new Response(json_encode($result));
  }

  public function getRealEntities($entities){

      foreach ($entities as $entity){
          $result = $entity->getNom()." ".$entity->getPrenom()." | ".$entity->getEmail()." | ".$entity->getNumero();
          $realEntities[$entity->getId()] = $result;
      }

      return $realEntities;
  }


}
