<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ContactUsType;
use App\Form\SearchEngineType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Service\ContactMailer;
use App\Service\MessageGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default", methods={"GET"})
     */
    public function index(
        ArticleRepository $articleRepository,
        MessageGenerator $generator
    ): Response {
        $date = new \DateTime();

        return $this->render('default/index.html.twig', [
            'message' => $generator->getMessage(),
            'day_date' => $date,
            'article' => $articleRepository->findLatest(),
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile", methods={"GET"})
     */
    public function profile(User $user)
    {
        return $this->render('default/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/{id}/edit", name="edit_profile", methods={"GET", "PUT"})
     */
    public function editProfile(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        $form = $this->createForm(UserType::class, $user, [
            'method' => 'put',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié.');

            return $this->redirectToRoute('profile', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('default/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Cette méthode va être appelée depuis le twig, c'est une sous requête
    public function getTime()
    {
        $time = new \DateTime();
        return $this->render('default/_time.html.twig', [
            'day_time' => $time,
        ]);
    }

    /**
     * @Route("/password/{id}/change", name="change_password", methods={"GET", "PUT"})
     */
    public function changePassword(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordEncoderInterface $encoder,
        TranslatorInterface $translator
    ) {
        $form = $this->createForm(ChangePasswordType::class, null, [
            'method' => 'put',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted()
            && $form->isValid()) {

            if (!$encoder->isPasswordValid($user, $form->get('old_password')->getData())) {
                $this->addFlash('danger', $translator->trans('password.danger'));
                return $this->redirectToRoute('default');
            }

            $password = $encoder->encodePassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($password);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié.');

            return $this->redirectToRoute('profile', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('default/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function getSearchForm()
    {
        $form = $this->createForm(SearchEngineType::class, null, [
            'method' => 'get',
            'action' => $this->generateUrl('search'),
            'attr' => [
                'class' => 'search-form',
            ],
        ]);

        return $this->render('default/_search_form.html.twig', [
            'search_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function search(Request $request, ArticleRepository $articleRepository)
    {
        $results = null;
        $word = '';
        if ('GET' === $request->getMethod() && $request->query->has('search')) {
            $word = $request->query->get('search_engine')['word'];
            $results = $articleRepository->findByWord($word);
        }

        return $this->render('default/search.html.twig', [
            'results' => $results ? $results : $articleRepository->findAll(),
            'word' => $word,
        ]);
    }

    /**
     * @Route("/contact_us", name="contact_us", methods={"GET", "POST"})
     */
    public function contact(
        Request $request,
        TranslatorInterface $translator,
        ContactMailer $mailer
    ) {
        $form = $this->createForm(ContactUsType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->sendMail($form->getData());
            $this->addFlash('success', $translator->trans('contact_us.success'));
            return $this->redirectToRoute('default');
        }

        return $this->render('default/contact_us.html.twig', [
            'mail_form' => $form->createView(),
        ]);
    }
}
