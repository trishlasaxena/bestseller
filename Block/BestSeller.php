<?php
 
namespace Nagarro\BestSeller\Block;
 
use Magento\Framework\View\Element\Template;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;
 
class BestSeller extends Template
{
    
    
    protected $storeManager;
    protected $_productCollectionFactory;
    protected $_productImageHelper;
    protected $scopeConfig;
    /**
     * @var Data
     */
    protected $urlHelper;
      /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;
     /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_compareProduct;
    /**
    * @var \Magento\Catalog\Api\ProductRepositoryInterfaceFactory
    */
    protected $productRepositoryFactory;
   
    /**
    * @var \Magento\Catalog\Helper\ImageFactory
    */
    protected $imageHelperFactory;
    protected $_productRepository;
    protected $product;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Block\Product\Context $productcontext,
        Product $product,
        Data $urlHelper
        
    )
    { 
         $this->storeManager = $storeManager;
         $this->scopeConfig = $scopeConfig;
         $this->_productCollectionFactory  = $productsCollectionFactory;
         $this->productRepositoryFactory = $productRepositoryFactory;
         $this->imageHelperFactory = $imageHelperFactory;
         $this->_productRepository = $productRepository;
         $this->urlHelper = $urlHelper;
         $this->_wishlistHelper = $wishlistHelper;
         $this->_compareProduct = $productcontext->getCompareProduct();
         $this->product = $product;
        parent::__construct($context);
    }

        public function getBaseUrl(){
        return $this->_storeManager->getStore()->getBaseUrl();
        }


    public function productToDisplay(){
        
        $allConfigSkus = $this->getProductConfigSkus();
        $skusArray = explode(',', $allConfigSkus); 
        foreach($skusArray as $skuVal){
              $skus[] = trim($skuVal);
          }
        
        foreach($skus as $key => $value){  
            if(!$this->productExistBySku($value))
            {
                unset($skus[$key]);
            }
          } 
        $storeId = $this->storeManager->getStore()->getId();
        $condition = ['in' => $skus];

        $productCollection = $this->_productCollectionFactory->create()
        ->addFieldToFilter('sku', $condition)->addAttributeToSelect('*')
        ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->load();

        return $productCollection;
     
        }
        public function getImage($sku){
             $productCollection = $this->productRepositoryFactory->create()->get($sku);
           
             $imageUrl = $this->imageHelperFactory->create()
             ->init($productCollection, 'product_thumbnail_image')->constrainOnly(FALSE)
             ->keepAspectRatio(TRUE)->keepFrame(TRUE)->resize(200, 200)->getUrl();
           
            return $imageUrl; 
        }

      
    public function getAddToCartPostParams(Product $product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $listBlock = $objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
        $url =  $listBlock->getAddToCartUrl($product); 
      
        return [
            'action' => $url,
            'data' => [
                'product' => (int) $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
                    ]
                ];
    }

         /**
     * Get add to wishlist params
     *
     * @param Product $product
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }
    /**
     * Retrieve Add Product to Compare Products List URL
     *
     * @return string
     */
    public function getAddToCompareUrl()
    {
        return $this->_compareProduct->getAddUrl();
    }

    public function getSectionTitle()
    {
        return 'Best Selling Products';
    }

    public function getProductConfigSkus()
    {
        return $this->scopeConfig->getValue('bestseller/general/skus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $this->storeManager->getStore());
    }
    
    public function productExistBySku($sku)
    {
        if ($this->product->getIdBySku($sku)) 
        {
            return "1";
        }
        else 
            return "0"; 
    }
    public function checkEnable()
    {
        return $this->scopeConfig->getValue('bestseller/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $this->storeManager->getStore());
    }
    public function isDots()
    {
        return $this->scopeConfig->getValue('bestseller/general/dots', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        $this->storeManager->getStore());
    }
} 