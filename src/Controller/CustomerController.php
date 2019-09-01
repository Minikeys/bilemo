<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class CustomerController
 * @package App\Controller
 */
class CustomerController extends AbstractFOSRestController
{

    /**
     * Get All Customer
     * @Rest\Get(
     *     "/api/customers",
     *      name = "customers"
     * )
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     * @SWG\Response(
     *     response=200,
     *     description="Returns all customers",
     *     @Model(type=Customer::class)
     * )
     *
     * @SWG\Tag(name="customers")
     * @Rest\View(serializerGroups={"list"})
     */
    public function getAllCustomer()
    {
        $user = $this->getUser();
        $cache = new FilesystemAdapter();
        $value = $cache->get('customers'.$user->getId(), function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $repository = $this->getDoctrine()->getRepository(Customer::class);
            $customers = $repository->findBy(['user' => $this->getUser()]);
            return $customers;
        });

        return $value;

    }

    /**
     * Get One Customer
     * @Rest\Get(
     *     "/api/customers/{id}",
     *     name = "get_customers",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns one customer.",
     *     @Model(type=Customer::class)
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Customer not linked to your account.",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Customer not found.",
     * )
     * @return Response
     * @Rest\View(serializerGroups={"details"})
     */
    public function getOneCustomer($id)
    {
        $cache = new FilesystemAdapter();
        $customer = $cache->getItem('customer.'.$id);

        if (!$customer->isHit()) {
            $repository = $this->getDoctrine()->getRepository(Customer::class);
            $customer->set($repository->find($id));
            $value = $customer->get();

            if($value->getUser() == $this->getUser()) {
                return $value;
            }
            return $this->handleView($this->view(['status' => 'Customer is not linked to your account.'], Response::HTTP_UNAUTHORIZED));

        }else{

            $customer = $cache->getItem('product.'.$id);
            $value = $customer->get();

            if(!is_null($value)){
                if($value->getUser() == $this->getUser()) {
                    return $value;
                }
                return $this->handleView($this->view(['status' => 'Customer is not linked to your account.'], Response::HTTP_UNAUTHORIZED));

            }else{
                return $this->handleView($this->view(['status' => 'Customer not found.'], Response::HTTP_NOT_FOUND));
            }
        }

    }

    /**
     * Create New Customer
     *
     * @param Request $request
     * @return Response
     * @Rest\Put(
     *     "/api/customers",
     *     name = "create_custromers"
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns customer {id} created.",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns errors.",
     * )
     * @SWG\Tag(name="customers")
     */
    public function createCustomer(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $customer->setUser($this->getUser());
            $em->persist($customer);
            $em->flush();
            $id = $customer->getId();
            return $this->handleView($this->view(['status' => 'Customer '. $id .' create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * Delete customer
     * @Rest\Delete(
     *     "/api/customers/{id}",
     *     name = "delete_customers",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns success deleted.",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Customer not linked to your account.",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Customer not found.",
     * )
     * @return Response
     */
    public function deleteCustomer($id)
    {
        $repository = $this->getDoctrine()->getRepository(Customer::class);
        $customer = $repository->find($id);
        if(!is_null($customer)){
            if($customer->getUser() == $this->getUser()){
                $em = $this->getDoctrine()->getManager();
                $em->remove($customer);
                $em->flush();
                return $this->handleView($this->view(['status' => 'Customer delete success.'], Response::HTTP_OK));
            }

            return $this->handleView($this->view(['status' => 'Customer is not linked to your account.'], Response::HTTP_UNAUTHORIZED));

        }else{
            return $this->handleView($this->view(['status' => 'Customer not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}