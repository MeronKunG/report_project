<?php

namespace App\Controller;

use App\Repository\ParcelMemberRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {
            return $this->redirectToRoute('report_cod');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/login_auth", name="app_login_auth")
     * @throws TransportExceptionInterface
     */
    public function login_auth(Request $request, ParcelMemberRepository $parcelMemberRepository)
    {
        $client = HttpClient::create();
        $data = json_encode([
            'username' => $request->request->get('username'),
            'passcode' => $request->request->get('password')
        ]);
        $response = $client->request('POST', '', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $data,
        ]);
        try {
            $content = $response->getContent();
            if ($content == 'false') {
                // fail authentication with a custom error
                $this->addFlash('error', 'ไม่พบข้อมูล');
                return $this->redirect($this->generateUrl('app_login'));
            }
            $data = $parcelMemberRepository->getMemberIdByPhoneNo($this->ZeroToDoubleSix($request->request->get('username')));
            $this->session->set('phoneNo', $request->request->get('username'));
            $this->session->set('memberId', $data[0]['memberId']);
            $this->session->set('name', $data[0]['firstname'].' '.$data[0]['lastname']);
        } catch (TransportExceptionInterface $e) {
        }
        return $this->redirect($this->generateUrl('report_cod'));
    }

    public function ZeroToDoubleSix($phoneNO)
    {
        $arr = str_split($phoneNO);
        if (isset($arr[0])) {
            if ($arr[0] == '0') {
                $phoneNO = '66';
                for ($i = 1; $i < count($arr); $i++) {
                    $phoneNO .= $arr[$i];
                }
            }
        }
        return $phoneNO;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->session->remove('phoneNo');
        $this->session->remove('memberId');
        $this->session->remove('name');
        return $this->redirect($this->generateUrl('app_login'));
    }
}
