<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends FOSRestController
{
    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param mixed $data    The response data
     * @param int   $status  The status code to use for the Response
     * @param array $headers Array of extra headers to add
     * @param array $context Context to pass to serializer when using serializer component
     *
     * @return JsonResponse
     */
    protected function json($data, $status = 200, $headers = array(), $context = array())
    {
        $jsonResponse = new JsonResponse();
        if ($this->container->has('serializer')) {

            $json = $this->container->get('serializer')->serialize($data, 'json'/*, array_merge(array(
                'json_encode_options' => $jsonResponse->getEncodingOptions()
            ), $context)*/);

            return new JsonResponse($json, $status, $headers, true);
        }

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    public function manager($id)
    {
        return $this->get(sprintf('%s_manager', $id));
    }

    /**
     * @param $string
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     * @return string
     */
    protected function translate($string, array $parameters = [], $domain = null, $locale = null)
    {
        /** @var \Symfony\Component\Translation\DataCollectorTranslator $translator */
        $translator = $this->get('translator');

        return $translator->trans($string, $parameters, $domain, $locale);
    }
}