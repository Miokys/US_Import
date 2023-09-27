<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);  // créer le formulaire depuis le modèle ContactType.

        $form->handleRequest($request);

        $to = 'contact@nabais-daniel.fr';

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est soumis et valide. traîtement de celui-ci.

            $email = (new TemplatedEmail()) // Création du mail à envoyer avec les information récupérée du formulaire.
                ->from($contact->getMail())
                ->to($to)
                ->subject('Demande de contact de : MR. ' . $contact->getNom() . ' ' . $contact->getPrenom())
                ->text($contact->getContent())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    "contact" => $contact
                ]);

            $mailer->send($email); // Envoyer le mail.

            $this->addFlash('success', 'Votre message a bien été envoyé!'); // Affiche un message de confirmation.

            return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil.
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView()
        ]);
    }
}
