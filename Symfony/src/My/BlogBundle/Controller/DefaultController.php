<?php
namespace My\BlogBundle\Controller;

use My\BlogBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use My\BlogBundle\Entity\Post;
use My\BlogBundle\Entity\Tag;

/*
 * 初期表示アクション
 *
 **/

class DefaultController extends Controller
{
    /*
     * 初期表示アクション
     *
     **/
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
            ->getRepository('MyBlogBundle:Post');
        $query = $repository->createQueryBuilder('p')
            ->setMaxResults(3)
            ->getQuery();

        $posts = $query->getResult();

        return $this->render('MyBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }

    /*
     * 詳細表示アクション
     *
     **/
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        return $this->render('MyBlogBundle:Default:show.html.twig', array('post' => $post));
    }

    /*
     * 新規作成アクション
     *
     **/
    public function newAction(Request $request)
    {
        $form = $this->createForm(new PostType(), new Post());

        if ('POST' == $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {
                // 各Formの値を設定
                $post = new Post();
                $post->setTitle($form["title"]->getData());
                $post->setBody($form["body"]->getData());
                $post->setImagePath($form["image_path"]->getData());
                $post->setLimitMember($form["limit_member"]->getData());
                $post->setPrefArea($form["pref_area"]->getData());
                $post->setCreatedAt(new \DateTime());
                $post->setUpdatedAt(new \DateTime());

                // タグ関連
                $tagA = $form["tags"]->getData();
                $tagB = new Tag();
                $tagB->setTagName('社会貢献');

                $post->addTag($tagB);

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        // 描画
        return $this->render('MyBlogBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /*
     * 編集アクション
     *
     **/
    public function editAction($id)
    {
        // DBから取得
        $em = $this->getDoctrine()->getManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        if (!$post) {
            throw new NotFoundHttpException('The post does not exist.');
        }

        // フォームのビルド
        $form = $this->createForm(new PostType(), new Post());

        // バリデーション
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->submit($request);
            if ($form->isValid()) {
                // 更新されたエンティティをデータベースに保存
                $post = $form->getData();
                $post->setUpdatedAt(new \DateTime());
                $em->flush();
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        // 描画
        return $this->render('MyBlogBundle:Default:edit.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /*
     * 削除アクション
     *
     **/
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        if (!$post) {
            throw new NotFoundHttpException('The post does not exist.');
        }
        $em->remove($post);
        $em->flush();
        return $this->redirect($this->generateUrl('blog_index'));
    }

    /*
     * 検索アクション
     *
     **/
    public function searchAction(Request $request)
    {
        $posts = [];
        $post = new Post();
        $token = $this->get('form.csrf_provider')->generateCsrfToken('csrf_token');

        $form = $this->createForm(new PostType(), $post);
        if ('POST' == $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {
                $repository = $this->getDoctrine()
                    ->getRepository('MyBlogBundle:Post');
                $query = $repository->createQueryBuilder('p')
                    ->where('p.title like :title
                            OR
                            p.body like :body')
                    ->setParameters(array(
                        'title' => $form["title"]->getData(),
                        'body' => $form["body"]->getData(),
                    ))
                    ->getQuery();

                $posts = $query->getResult();

            }
        }

        return $this->render('MyBlogBundle:Default:search.html.twig', array(
                'form' => $form->createView(),
                'posts' => $posts,
                'csrf_token' => $token
            )
        );
    }
}
