<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class CampaignController extends AbstractController
{
    #[Route('/index', name: 'app_campaign_index', methods: ['GET'])]
    public function index(CampaignRepository $campaignRepository): Response
    {
        return $this->render('campaign/index.html.twig', [
            'campaigns' => $campaignRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaign = new Campaign(); //Nouvel objet Campaign
       
        $form = $this->createForm(CampaignType::class, $campaign); // Il va créer un formulaire a partir de la page CampaignType et va envoyer le formulaire avec la fonction render
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            dd($campaign);
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
    public function show(Campaign $campaign): Response
    {
        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
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
        if ($this->isCsrfTokenValid('delete'.$campaign->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
