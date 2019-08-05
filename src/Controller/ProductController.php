<?php


namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Hateoas\Configuration\Annotation as Hateoas;
use FOS\RestBundle\Controller\Annotations as Rest;


class ProductController extends AbstractFOSRestController
{

    /**
     * Get All Product
     * @Rest\Get(
     *     "/api/products",
     *      name = "app_products"
     * )
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all products",
     *     @Model(type=Product::class)
     * )
     * @SWG\Tag(name="products")
     */
    public function getAllProducts()
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findall();
        return $this->handleView($this->view($products));
    }

    /**
     * Get One Product
     * @Rest\Get(
     *     "/api/products/{id}",
     *     name = "app_products_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param $id
     * @SWG\Tag(name="products")
     * @SWG\Response(
     *     response=200,
     *     description="Returns one product",
     *     @Model(type=Product::class)
     * )
     * @return Response
     */
    public function getOneProduct($id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);
        if(!is_null($product)){
            return $this->handleView($this->view($product));
        }else{
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_CREATED));
        }

    }

    /**
     * Create New Product
     * @param Request $request
     * @Rest\Post(
     *     "/api/products",
     *     name = "app_products_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("article", converter="fos_rest.request_body")
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
     * @Rest\Delete(
     *     "/api/products/{id}",
     *     name = "app_products_delete",
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
            return $this->handleView($this->view(['status' => 'Product not found.'], Response::HTTP_CREATED));
        }

    }

}