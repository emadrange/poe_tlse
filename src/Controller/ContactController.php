<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact", name="contact_")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(), // SELECT * FROM contact
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $contact = new Contact();
        $form = $this->createFormBuilder($contact)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TelType::class)
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'date',
                ],
            ])
            ->getForm();

        $form->handleRequest($request); // $firstname = $_POST['firstname']...

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact); // INSERT INTO contact...
            $entityManager->flush();

            $this->addFlash('success', 'Votre contact a bien été ajouté.');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/new.html.twig', [
            'new_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "PUT"})
     */
    public function edit(
        Contact $contact,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $form = $this->createForm(ContactType::class, $contact, [
            'method' => 'put',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le contact a bien été modifié.');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/edit.html.twig', [
            'edit_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"})
     */
    public function delete(Contact $contact, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Le contact a bien été supprimé.');

        return $this->redirectToRoute('contact_index');
    }
}
