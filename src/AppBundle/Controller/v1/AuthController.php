<?php
namespace AppBundle\Controller\v1;

use AppBundle\Entity\User;
use AppBundle\Utils\ApiUtils;
use AppBundle\Utils\ResponseError;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Google_Client;
use Google_Service_Oauth2;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package AppBundle\Controller
 * @Route("/auth")
 */
class AuthController extends FOSRestController
{
    /**
     * @Method("POST")
     * @Route("/sign-in-google", name="signInGoogle")
     *
     * @param Request $request
     * @return Response
     */
    public function signInGoogleAction(Request $request)
    {
        ApiUtils::checkRequired(['token'], $request);

        $token = $request->get('token');
        $googleId = null;

        try {
            $client = new Google_Client();
            $client->setDeveloperKey($this->container->getParameter('google_api_key'));

            $oauth = new Google_Service_Oauth2($client);
            $userInfo = $oauth->tokeninfo(['access_token' => $token]);

            if ($userInfo) {
                $googleId = $userInfo['userId'];
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            ResponseError::force(ResponseError::WRONG_GOOGLE_TOKEN);
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByGoogleId($googleId);

        if (!$user) {
            $em = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setGoogleId($googleId);
            $em->persist($user);
            $em->flush();
        }

        $authToken = $this->get('app.session_provider')->login($user);

        $view = $this
            ->view(null, 200)
            ->setHeader('Authorization', $authToken);

            return $this->handleView($view);
    }
}
