<?php

namespace app\en_us\controller;

use app\common\model\About as AboutModel;
use app\common\model\Category as CategoryModel;
use app\common\model\Language as LanguageModel;
use app\common\model\Product as ProductModel;
use app\common\model\Article as ArticleModel;
use app\common\model\ServiceCategory as ServiceCategoryModel;
use app\common\model\Setting as SettingModel;
use think\Cache;
use think\Controller;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Lang;
use think\Request;
use app\common\model\Document as DocumentModel;

class Base extends Controller
{
    protected $code;//受保护的成员变量
    protected $language; //语言
    protected $category; // 产品分类列表
    protected $imagesNew; // 最新产品
    protected $imageBest; //最热产品
    protected $imagesRecommend;//推荐产品列表
    protected $seo; //网站信息SEO
    protected $about; // 关于我们
    protected $articleList; // 最新事件列表
    protected $productList; // 最热产品列表
    protected $template;
    protected $fileHost;
    protected $debug;
    protected $dbError;
    protected $beforeActionList = ['footerList', 'about', 'getLanguage', 'getBaseSetting', 'getNormalCategory', 'isMobile', 'languageStatus', 'getHot', 'interested'];

    public function _initialize()
    {
//      获取当前模块名，由于我们的多语言网站是一个语种一个模块，所以需要获取；
        $this->code = request()->module();
        $url = Request::instance()->controller();
        $this->dbError = "Database error, our webmaster is trying to fix it";
        $this->debug = config('app_debug');
        $this->assign('code', $this->code);
        $this->assign('url', $url);
        //加载当前模块语言包
        Lang::load(APP_PATH . 'en_us/lang/lang.php');
    }

    /***
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function footerList()
    {
        $articleList = (new ArticleModel())->getArticleList($this->code, 5);
        $documentList = (new DocumentModel())->getDocumentList($this->code, 5);
        $list = (new AboutModel())->getListByCode($this->code);
        $this->assign('articleList', $articleList);
        $this->assign('documentList', $documentList);
        $this->assign('list', $list);
    }

    /***
     * 关于我们
     * 在公共底部次导航位置，需要使用
     * 缓存更新已完成
     */
    public function about()
    {
        try {
            $about = (new AboutModel())->getAbouts($this->code);
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        $this->assign("about", $about);
    }

    /***
     * @throws \think\exception\DbException
     *
     */
    public function getLanguage()
    {
        if (!$this->debug) {
            if (Cache::get('getLanguage')) {
                Cache::set('getLanguage', LanguageModel::all(array('status' => 1)));
            }
        }
        $language = $this->debug ? LanguageModel::all(array('status' => 1)) : Cache::get('getLanguage');
        $this->assign("language", $language);
    }

    /***
     * 基础信息，例如 title 后缀、放静态文件的服务器前缀，主SEO信息等
     *
     */
    public function getBaseSetting()
    {
        $Website = "By winstars";
        $seo = (new SettingModel())->getSeo($this->code);
        $this->assign("seo", $seo);
        $this->fileHost = '//files.win-star.com';
        $this->assign('website', $Website);
        $this->assign('fileHost', $this->fileHost);
    }

    /***
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * 缓存已更新
     *
     */
    public function getNormalCategory()
    {
        $category = (new CategoryModel())->getNormalCategory($this->code);//获取一级分类,parent_id = 0
        $this->assign('category', $category);
    }

    /***
     * 用于判断是移动端访问还是PC端访问，从而给定不同的渲染模板
     */
    public function isMobile()
    {
        if (isMobile()) {
            $this->template = APP_PATH . request()->module() . '/view/mobile';
        } else {
            $this->template = APP_PATH . request()->module() . '/view/pc';
        }
    }

    /***
     * 缓存已更新
     * 语言状态判断，如果语言状态不为1 则表示我们并不希望别人访问到我们的这个语言网站内容
     */
    public function languageStatus()
    {
        $langStatus = LanguageModel::getIDStatusByCode($this->code);
        if ($langStatus['status'] !== 1) {
            abort(404);
        }
    }

    /***
     * 本方法不需要缓存，因为只截取了4个产品或者6个产品，速度并不会太慢
     */
    public function getHot()
    {
        $hotSales = (new ProductModel())->getHotSales($this->code, $count = 4);
        $hotSale = getAlbum($hotSales);
        $this->assign('hotSale', $hotSale);
    }

    /***
     * 方法同上
     */
    public function interested()
    {
        $interesteds = (new ProductModel())->getHotSales($this->code, $count = 6);
        $interested = getAlbum($interesteds);
        $this->assign('interested', $interested);
    }

    //404页面
    public function NotFound()
    {
        return view('404');
    }

    //IE9以下浏览器打开跳转页面
    public function IE()
    {
        return $this->fetch('ie');
    }

    /***
     * $this->code 为 当前的模块名，即在上面_initialize(初始化中)赋予的
     * 缓存已更新
     */
    protected function cate()
    {
        //获取服务分类下对应的二级分类
        try {
            $cate = ServiceCategoryModel::getSecondCategory($this->code);
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        $this->assign('cate', $cate);
    }

    /***
     * @param mixed|string $code
     * 错误
     *
     * @return mixed
     */
    public function customerError($code)
    {
        return $this->fetch('', ['code' => $code]);
    }
}