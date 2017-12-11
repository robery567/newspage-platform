<?php
namespace AppBundle\Controller;

use AppBundle\Util\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostMessageController extends Controller
{
    /**
     * @Route("/post-message/receiver")
     * @return Response
     */
    public function postMessageAction(): Response
    {
        /** @var Settings $settings */
        $settings = $this->get(Settings::class);

        $apiUrl = json_decode($settings->get('api.base_url'));

        $response = <<<EOV
<!DOCTYPE html>
<html>
    <head>
        <title>Receiver</title>
    </head>
    <body>
        <h1>Receiver</h1>
        <p>This page receives message from API with postMessage...</p>
        <hr>
        <iframe id="receiver" src="http://api.robery.eu/post-message/sender"></iframe>
    </body>
    
    <script type="text/javascript">
        window.addEventListener('message',function(e) {
            var key = e.message ? 'message' : 'data';
            var data = e[key];
            
            alert("Received data: " + data);
        }, false);
    </script>
</html>
EOV;

        return new Response($response, Response::HTTP_OK, ['Content-Type' => 'text/html']);
    }
}