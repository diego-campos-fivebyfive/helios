<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Cart\Service\Integrador;

use AppBundle\Manager\ParameterManager;
use AppBundle\Entity\AccountInterface;
use AppBundle\Service\Common\MaskHelper;
use Ecommerce\CartPool\Service\GetnetClient;
use Ecommerce\Cart\Entity\Cart;
use Ecommerce\Cart\Entity\CartHasKit;
use Ecommerce\Cart\Manager\CartHasKitManager;
use Ecommerce\Cart\Manager\CartManager;
use Ecommerce\CartPool\Service\Integrador\CartPoolHelper;
use Symfony\Component\Form\Form;

class CartCheckoutService
{
    /**
     * @var CartManager
     */
    private $manager;

    /**
     * @var CartPoolHelper
     */
    private $cartPoolHelper;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var CartHasKitManager
     */
    private $cartHasKitManager;

    /**
     * @var ParameterManager
     */
    private $parameterManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        CartManager $manager,
        CartPoolHelper $cartPoolHelper,
        CartService $cartService,
        CartHasKitManager $cartHasKitManager,
        ParameterManager $parameterManager
    ) {
        $this->manager = $manager;
        $this->cartPoolHelper = $cartPoolHelper;
        $this->cartService = $cartService;
        $this->cartHasKitManager = $cartHasKitManager;
        $this->parameterManager = $parameterManager;
    }

    /**
     * @param Form $form
     * @param AccountInterface $account
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException|\Exception
     */
    public function confirm(Form $form, AccountInterface $account)
    {
        $data = $this->getInfo($form, $account);

        $numbers = [
            "phone" => $form->get('phone')->getData(),
            "document" => $form->get('document')->getData(),
        ];

        $kits = $data['kits'];
        unset($data['kits']);

        $cartPool = $this->cartPoolHelper->findOrCreateCartPool($account);
        $this->cartPoolHelper->updateCartPool($cartPool, $account);
        $shipping = $this->formatShipping($data);

        $parameter = $this->parameterManager->findOrCreate('platform_settings');
        $numberOfInstallments = $parameter->get('getnet_number_of_installments');

        return [
            'account' => $cartPool->getAccount(),
            'data' => $data,
            'shipping' => $shipping,
            'kits' => $kits,
            'numberInstallments' => $numberOfInstallments,
            'numbers' => $numbers,
            'cartPoolId' => $cartPool->getId()
        ];
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    public function itemsList(AccountInterface $account)
    {
        $cartHasKits = $this->getCartHasKits($account);

        $cartTotal = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            if ($cartHasKit->getKit()->isAvailable()) {
                $kitTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();
                $cartTotal += $kitTotal;

                $kits[] = [
                    'kit' => $cartHasKit->getKit(),
                    'quantity' => $cartHasKit->getQuantity(),
                    'total' => $kitTotal
                ];
            }
        }

        return [
            'kits' => $kits,
            'total' => $cartTotal,
            'kitsQuantity' => count($cartHasKits)
        ];
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    public function getCartHasKits(AccountInterface $account)
    {
        $cart = $this->cartService->getCart($account);

        return $this->cartHasKitManager->findBy([
            'cart' => $cart
        ]);
    }

    /**
     * @param $data
     * @param AccountInterface $account
     */
    public function updateCheckout($data, AccountInterface $account)
    {
        /** @var Cart $cart */
        $cart = $this->cartService->getCart($account);

        unset($data['items'], $data['kits'], $data['amount'], $data['token'], $data['customerId']);

        $cart->setCheckout($data);

        $this->manager->save($cart);
    }

    /**
     * @param Form $form
     * @return mixed
     */
    private function getData(Form $form)
    {
        $keys = [
            "firstName",
            "lastName",
            "documentType",
            "email",
            "postcode",
            "state",
            "city",
            "neighborhood",
            "street",
            "number",
            "complement",
            "differentDelivery",
            "shippingFirstName",
            "shippingLastName",
            "shippingEmail",
            "shippingPostcode",
            "shippingState",
            "shippingCity",
            "shippingNeighborhood",
            "shippingStreet",
            "shippingNumber",
            "shippingComplement",
        ];

        foreach ($keys as $key) {
            $dataForm[$key] = $form->get($key)->getData();
        }

        $formatKeys = [
            "document",
            "phone",
            "shippingPhone"
        ];

        foreach ($formatKeys as $key) {
            $data = $form->get($key)->getData();

            $dataForm[$key] = preg_replace("/[ ()-.\/]/",'', $data);
        }

        return $dataForm;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getToken()
    {
        /** @var GetnetClient $getNet */
        $getNet = new GetnetClient(GetnetClient::HOMOLOG);

        return "Bearer " . $getNet->getAccessToken();
    }

    /**
     * @param Form $form
     * @param AccountInterface $account
     * @return array
     * @throws \Exception
     */
    private function getInfo(Form $form, AccountInterface $account)
    {
        $cartHasKits = $this->getCartHasKits($account);

        $dataForm = $this->getData($form);

        $data = $this->cartPoolHelper->formatCheckout($dataForm);

        $items = $this->cartPoolHelper->formatItems($cartHasKits);
        $data['items'] = json_encode($items);

        $cartTotal = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            if ($cartHasKit->getKit()->isAvailable()) {
                $kitTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();
                $cartTotal += $kitTotal;

                $kits[] = [
                    'kit' => $cartHasKit->getKit(),
                    'quantity' => $cartHasKit->getQuantity(),
                    'total' => $kitTotal
                ];
            }
        }

        $data['kits'] = $kits;

        $data['amount'] = number_format($cartTotal, 2, '.', '');
        $data['token'] = $this->getToken();
        $data['customerId'] = $account->getId();

        $this->updateCheckout($data, $account);

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    private function formatShipping($data)
    {
        $different = $data['differentDelivery'];
        $shipping = json_decode($data['shipping'], true)[0];

        $phone = $different ? $shipping['phone_number'] : $data['phone'];
        $postcode = $different ? $shipping['address']['postal_code'] : $data['zipcode'];

        $documentPattern = $data['documentType'] === 'CPF' ?  '###.###.###-##' : '##.###.###/####-##';
        $phonePattern = strlen($phone) <= 10 ? '(##) ####-####' : '(##) #####-####';
        $postcodePattern = '#####-###';

        $formattedDocument = MaskHelper::genericMask($data['documentNumber'], $documentPattern);
        $formattedPhone = MaskHelper::genericMask($phone, $phonePattern);
        $formattedPostcode = MaskHelper::genericMask($postcode, $postcodePattern);

        return [
            "firstName" => $different ? $shipping['first_name'] : $data['firstName'],
            "lastName" => $different ? $shipping['name'] : $data['lastName'],
            "document" => $formattedDocument,
            "email" => $different ? $shipping['email'] : $data['email'],
            "phone" => $formattedPhone,
            "postcode" => $formattedPostcode,
            "state" => $different ? $shipping['address']['state'] : $data['state'],
            "city" => $different ? $shipping['address']['city'] : $data['city'],
            "neighborhood" => $different ? $shipping['address']['district'] : $data['neighborhood'],
            "street" => $different ? $shipping['address']['street'] : $data['street'],
            "number" => $different ? $shipping['address']['number'] : $data['number'],
            "complement" => $different ? $shipping['address']['complement'] : $data['firstName']
        ];
    }
}
