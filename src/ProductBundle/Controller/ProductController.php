<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use ProductBundle\Document\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends Controller
{
    public function fetchAction()
    {

    		$dm = $this->get('doctrine_mongodb')->getManager();
 			$products=$dm->getRepository('ProductBundle:Product')
 					->findAll();



 			foreach ($products as $key => $prod) 
 			{
 				$data[$key] = $prod->showProduct();
 			}

 			$dm->clear();
    	
          return new JsonResponse([
          	'success' => true,
          	'data' => $data
          	]);
    }

    public function orderAction()
    {
    	//get the logged in user's email
    	$user = $this->container->get('security.context')->getToken()->getUser();
		$email = $user->getEmail();

        $message = \Swift_Message::newInstance()
		    ->setSubject('Thank you for ordering via Herpaka App!')
		    ->setFrom('herpakaapp@gmail.com')
		    ->setTo($email)
		    // ->setBody($this->renderView('ProductBundle:Resources:views:Default:index.html.twig', array('enquiry' => $enquiry)));
			->setBody($this->renderView('@ProductBundle/Resources/views/Default/index.html.twig'));		 
        $this->get('mailer')->send($message);

        return new JsonResponse([
          	'command' => $email
          	]);
    }


    public function discountAction() 
    {

    		$dm = $this->get('doctrine_mongodb')->getManager();
 			

 			 $req = $dm 
 		            	->createQueryBuilder('ProductBundle:Product')
 						->sort('variation', 'ASC')
 					    ->limit(12);

 			 $topTen = $req
 			 			->getQuery()
 				  		->execute();
 			
 			$it = 0;

 			foreach ($topTen as $prod) 
 			{
 				$data[$it] = $prod->showProduct();
 				$it++;
 			}

 			$dm->clear();

    	    return new JsonResponse([
          	'success' => true,
          	'data' => $data
          	]);

   	}






    public function productsInCategoryAction() 
    {

				$request = $this->getRequest();

			if($request->getMethod() !== 'POST')
				{ return new Response('<h1> Choose a Category!</h1>');};


			$asked = $request->request->get('lookingFor');


    		$dm = $this->get('doctrine_mongodb')->getManager();
 			

 			 $req = $dm 
 		            	->createQueryBuilder('ProductBundle:Product')
 						->field('category')->equals($asked);

 			 $prods = $req
 			 			->getQuery()
 				  		->execute();
 			
 			$it = 0;
 			

 			foreach ($prods as $prod) 
 			{
 				$data[$it] = $prod->showProduct();
 				$it++;
 			}


 			$dm->clear();

    	   return new JsonResponse([
          	'success' => true,
          	'data' => $data
          	]);

   	}




   	    public function categoryAction() 
   		 {


    		$dm = $this->get('doctrine_mongodb')->getManager();
 			

 			 $req = $dm 
 		            	->createQueryBuilder('ProductBundle:Product')
 						->distinct('category');


 			 $data = $req
 			 			->getQuery()
 				  		->execute()
 						->toArray();

 			$dm->clear();



    	    return new JsonResponse([
          	'success' => true,
          	'data' => $data
          	]);
  
   	}


   	    public function payAction() 
   		 {

				$request = $this->getRequest();
				   
				$mail = $request->request->get('email');
				$cart = $request->request->get('cart');
			   $CVV= $request->request->get('CVV');

			   $message = \Swift_Message::newInstance()
			   			->setSubject('Success Payment!')
			   			->setFrom('herpakaap@gmail.com')
			   			->setTo($mail)
			   			->setBody(
			   				$this->renderView('@FOS/views/email/pay.twig', array ('email' => $mail, 
			   																		'cart' => $cart,
			   																		'CVV' => $CVV)
			   				),
			   				'text/html'
			   				);

    	    return new Response();
  
   	}




}
