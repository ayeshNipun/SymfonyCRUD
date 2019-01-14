<?php
    namespace App\Controller;

    //Entites
    use App\Entity\Article;

    //request and response
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    //for annotations
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\RedirectResponse;

    //for using methods in annotations
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

    //twig template
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    //forms
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class ArticleController extends Controller {
        /**
         * @Route("/", name="article_list")
         * @Method({"GET"})
         */
        public function index() {

            $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

            return $this->render('articles/index.html.twig', array('articles'=>$articles));
        }


        //form handling
        /**
         * @Route("/article/new", name="new_article") 
         * @Method({"GET", "POST"})
         */
        public function new(Request $request) {
            $article = new Article();

            $form = $this->createFormBuilder($article)
                    ->add(
                        'title', TextType::class, 
                        array(
                            'attr' => array('class'=>'form-control')
                            )
                        )

                    ->add(
                        'body', TextareaType::class,
                        array(
                            'attr' => array('class'=>'form-control'),
                            'required' => false
                        )
                    )

                    ->add(
                        'save', SubmitType::class,
                        array(
                            'label' => 'Create',
                            'attr' => array('class' => 'btn btn-primary mt-3')
                        )
                    )
                    ->getForm();

                    $form->handleRequest($request);

                    if($form->isSubmitted() && $form->isValid()) {
                        $article = $form->getData();
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($article);
                        $entityManager->flush();

                        return $this->redirect('/');
                    }

                    return $this->render('articles/new.html.twig', array(
                        'form' => $form->createView()
                    ));
        }

        
        
        /**
         * @Route("/article/{id}", name="article_show")
         */
        public function show($id) {
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            return $this->render('articles/show.html.twig', array('article'=>$article));
        }

        
        // /**
        //  * @Route("/article/save")
        //  */
        // public function save() {
        //     $entityManager = $this->getDoctrine()->getManager();

        //     $article = new Article();
        //     $article->setTitle('Article Three');
        //     $article->setBody('This is the body of article three');

        //     $entityManager->persist($article);

        //     $entityManager->flush();

        //     return new Response('saved'.$article->getId());
        // }
    }
?>