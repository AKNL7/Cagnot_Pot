<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Participant;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class CampaignController extends AbstractController
{
    #[Route('/', name: 'app_campaign_index', methods: ['GET'])]
    public function index(CampaignRepository $campaignRepository, PaymentRepository $paymentRepository, ParticipantRepository $participantRepository): Response
    {
        $campaigns = $campaignRepository->findAll();
        
        return $this->render('campaign/index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }

    #[Route('/new', name: 'app_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaign = new Campaign(); //Nouvel objet Campaign

        $form = $this->createForm(CampaignType::class, $campaign); // Il va créer un formulaire a partir de la page CampaignType et va envoyer le formulaire avec la fonction render
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setId();
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
          
        ]);
    }

    #[Route('/{id}/show', name: 'app_campaign_show', methods: ['GET'])]
    // L'id ici envoye dans la route est utilise pour instancier l'objet Campaign qui correspond a l'id de la route dans la base de données. 


    public function show(Campaign $campaign, PaymentRepository $paymentRepository, ParticipantRepository $participantRepository): Response //l'objet campagne est recuperer ici
    {
        $allParticipantsFromCampaign = $participantRepository->findBy(['campaign' => $campaign]);
        
        $allPaymentsFromCampaign = $paymentRepository->findBy(['campaign' => $campaign]);

        // va donner la somme des payment des participants 
        $countParticipants = count($allParticipantsFromCampaign);
        $totalCountAmount = 0; // On le met a zero pour ajouter les autres payments 

        foreach ($allPaymentsFromCampaign as $payment) {
            $totalCountAmount += $payment->getAmount(); // On boucle sur les payments et on ajoute ici les montants des amount
        }

        $progressBar = $totalCountAmount / $campaign->getGoal() * 100; // Le total des paiment divisé par l'objectif de la campagne multiplié par 100 pour avoir un pourcentage 
        
        
        $progressBar = floor($progressBar); // arrondi le nombre 
      
      
      
        // dd(floor($progressBar));
        // dd($totalCountAmount);
        // dd($campaign, $allParticipantsFromCampaign, $allPaymentsFromCampaign);

        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
            'participants' => $allParticipantsFromCampaign,
            'payments' => $allPaymentsFromCampaign,
            'countParticipants' => $countParticipants,
            'totalCountAmount' => $totalCountAmount,
            'progressBar' => $progressBar

            //La fonction show nous renvoie une campaign 
        ]);
    }



    #[Route('/{id}/edit', name: 'app_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campaign_delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
