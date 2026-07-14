<?php

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Repository\ContactMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/contact')]
final class ContactMessageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ContactMessageRepository $repo,
    ) {}

    #[Route('', name: 'app_admin_contact_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/contact/index.html.twig', [
            'items' => $this->repo->findAllOrdered(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contact_show', methods: ['GET'])]
    public function show(ContactMessage $item): Response
    {
        if (!$item->isIsRead()) {
            $item->setIsRead(true);
            $this->em->flush();
        }

        return $this->render('admin/contact/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_contact_delete', methods: ['POST'])]
    public function delete(Request $request, ContactMessage $item): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $this->em->remove($item);
            $this->em->flush();
            $this->addFlash('success', 'Mensagem removida!');
        }
        return $this->redirectToRoute('app_admin_contact_index');
    }
}
