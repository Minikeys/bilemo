<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends AbstractFOSRestController
{
    /**
     * Create New Product
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @Rest\Put(
     *     "/admin/api/products",
     *     name = "products_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @SWG\Response(
     *     response=201,
     *     description="Returns product created",
     *     @Model(type=Product::class)
     * )
     * @SWG\Tag(name="products")
     * @return Response
     */
    public function createProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->handleView($this->view(['status' => 'Product create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Delete product
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Delete(
     *     "/admin/api/products/{id}",
     *     name = "products_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=201,
     *     description="Returns success"
     * )
     * @return Response
     */
    public function deleteProduct($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);
        if(!is_null($product)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            return $this->handleView($this->view(['status' => 'Product delete success.'], Response::HTTP_CREATED));
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}