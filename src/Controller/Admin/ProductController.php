<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends AbstractFOSRestController
{
    /**
     * Create New Product
     * @param Request $request
     * @Rest\Put(
     *     "/admin/api/products",
     *     name = "products_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @SWG\Response(
     *     response=201,
     *     description="Returns product {id} created.",
     *     @Model(type=Product::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns errors.",
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
            $id = $product->getId();
            return $this->handleView($this->view(['status' => 'Product '. $id .' create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }

    /**
     * Delete product
     * @Rest\Delete(
     *     "/admin/api/products/{id}",
     *     name = "products_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=200,
     *     description="Returns success deleted.",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Customer not found.",
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
            return $this->handleView($this->view(['status' => 'Product delete success.'], Response::HTTP_OK));
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_NOT_FOUND));
        }

    }

}