<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    protected static $stations = [
        'Schlump', 'Saarlandstraße', 'Borgweg', 'Jungfernstieg', 'Hauptbahnhof', 'Gänsemarkt', 'Landungsbrücken', 'Rödingsmarkt'
    ];

    protected static $lines = [
        'U1', 'U2', 'U3', 'U4'
    ];

    /**
     * @Route("/", name="list_action")
     * @Method("GET")
     */
    public function indexAction()
    {
        $rides = $this->getRepository()
            ->findAll();

        $serializer = $this->getSerializer();
        $result = $serializer->serialize($rides, 'json');

        return $this->createJSONResponse($result);
    }

    /**
     * @Route("/", name="post_action")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        if (!$request->request->has('uuid'))
            throw new BadRequestHttpException('You have to pass an uuid!');

        $uuid = $request->request->get('uuid');
        $om = $this->getDoctrine()->getManager();

        if (!$request->request->has('startTime')) {
            $result = $this->getRepository()
                ->findNotFinishedRides($uuid);
            if (0 >= sizeof($result))
                throw new BadRequestHttpException('You have to pass data for a new ride, if you pass a new uuid!');

            if (!$request->request->get('endTime'))
                throw new BadRequestHttpException('You have to pass the end date!');

            /** @var Ride $ride */
            $ride = $result[0];
            $ride->setEndTime(new \DateTime($request->request->get('endTime')));
            $ride->setEndStation($this->getRandStation());
        }
        $ride = new Ride(new \DateTime($request->request->get('startTime')), $this->getRandStation(), $uuid, $this->getRandLine());

        $om->persist($ride);
        $om->flush();

        $serializer = $this->getSerializer();
        $result = $serializer->serialize($ride, 'json');

        return $this->createJSONResponse($result);
    }

    /**
     * @return RideRepository
     */
    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Ride');
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        return new Serializer($normalizers, $encoders);
    }

    /**
     * @param string $data
     * @return Response
     */
    private function createJSONResponse($data)
    {
        return new Response($data, 200, [
            'Content-type' => 'application/json',
        ]);
    }

    /**
     * @return string
     */
    private function getRandStation()
    {
        return self::$stations[round(mt_rand(0, count(self::$stations) - 1))];
    }

    /**
     * @return string
     */
    private function getRandLine()
    {
        return self::$lines[round(mt_rand(0, count(self::$lines) - 1))];
    }
}
