<?php /** @noinspection PhpDocMissingReturnTagInspection */


namespace App\Controller;


use App\Entity\User;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * Page d'inscription
     * @Route("/register", name="user_registration")
     *
     * @param Request                      $request         Pour que le formulaire récupère les données POST
     * @param UserPasswordEncoderInterface $passwordEncoder Pour hasher le mot de passe de l'utilisateur
     * @param EntityManagerInterface       $entityManager   Pour enregistrer l'utilisateur en base de données
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $registerForm = $this->createForm(UserRegistrationFormType::class);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            // Le formulaire permet de récupérer l'entité User créée
            /** @var User $user */
            $user = $registerForm->getData();

            // Pour récupérer la valeur d'un champ dissocié par l'option "mapped"
            // il faut utiliser le formulaire comme un tableau:
            $password = $registerForm['plainPassword']->getData();

            // Définir le hash du mot de passe de l'utilisateur
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
            // Enregistrer l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message de succès et rediriger vers la page de connexion
            $this->addFlash('success', 'Vous êtes bien inscrit !');
            $this->addFlash('info', 'Vous devrez confirmez votre compte, un lien vous a été envoyé par email.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'register_form' => $registerForm->createView()
        ]);
    }
}