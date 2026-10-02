<?php declare(strict_types=1);
// Isolated regression checks: real plugin classes with minimal framework doubles.
namespace Shopware\Core\Framework\DataAbstractionLayer {
 class Entity {}
 trait EntityIdTrait { protected string $id; public function getId(): string{return $this->id;} public function setId(string $id): void{$this->id=$id;} }
 class EntityRepository {}
}
namespace Shopware\Core\Content\Product {
 class ProductEntity {
  public function __construct(public string $id='product-a', public ?string $parentId=null) {}
  public function getId(): string {return $this->id;}
  public function getParentId(): ?string {return $this->parentId;}
  public function getCategoryTree(): array {return ['category-parent','category-a'];}
  public function getName(): string {return 'Outdoor Rollup';}
  public function getDescription(): string {return 'Weather resistant';}
  public function getProductNumber(): string {return 'SW10000.16';}
 }
}
namespace Shopware\Core\Content\ProductStream\Service { interface ProductStreamBuilderInterface {} }
namespace Shopware\Core\Framework\DataAbstractionLayer\Search {
 class Criteria { public array $filters=[]; public function addAssociation($v): void{} public function addFilter($v): void{$this->filters[]=$v;} public function addSorting($v): void{} }
}
namespace Shopware\Core\Framework\DataAbstractionLayer\Search\Filter { class EqualsFilter { public function __construct(public string $field,public mixed $value){} } }
namespace Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting { class FieldSorting { public function __construct($v){} } }
namespace Shopware\Core\Framework\Struct { class ArrayStruct { public function __construct(public array $data, string $name=''){} } }
namespace Shopware\Core\System\SystemConfig {
 class SystemConfigService {
  public array $values=[];
  public function get(string $key, ?string $channel=null): mixed {return $this->values[$channel][$key] ?? $this->values['global'][$key] ?? null;}
 }
}
namespace Shopware\Core\Content\Cms\DataResolver\Element {
 abstract class AbstractCmsElementResolver {}
 class ElementDataCollection {}
}
namespace Shopware\Core\Content\Cms\DataResolver {
 class CriteriaCollection {public array $items=[]; public function add($key,$definition,$criteria): void{$this->items[$key]=$criteria;}}
}
namespace Shopware\Core\Content\Cms\DataResolver\ResolverContext {
 class ResolverContext {public function __construct(private object $context){} public function getSalesChannelContext(): object{return $this->context;}}
}
namespace Shopware\Core\Content\Cms\Aggregate\CmsSlot {
 class CmsSlotEntity {
  public mixed $data=null;
  public function setData($data): void {$this->data=$data;}
  public function getUniqueIdentifier(): string{return 'slot';}
  public function getFieldConfig(): object {return new class {
   public function get($name): ?object {return $name==='groupId' ? new class {public function getStringValue(): string{return 'group-a';}} : null;}
  };}
 }
}
namespace {
 $root=$argv[1];
 foreach(['Core/Content/Faq/FaqEntity.php','Core/Content/FaqGroup/FaqGroupEntity.php','Storefront/FaqVisibilityService.php','Storefront/FaqPresentationConfig.php','Storefront/Cms/FaqCmsElementResolver.php'] as $file){require $root.'/src/'.$file;}
 $count=0;
 function check(bool $ok,string $label): void {global $count;if(!$ok){throw new \RuntimeException($label);}++$count;echo "PASS $label\n";}
 $builder=new class implements \Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface {};
 $service=new \Tuami\FaqPro\Storefront\FaqVisibilityService($builder,new \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository());
 $faq=new \Tuami\FaqPro\Core\Content\Faq\FaqEntity();
 $group=new \Tuami\FaqPro\Core\Content\FaqGroup\FaqGroupEntity();
 $faq->setGroup($group);
 $product=new \Shopware\Core\Content\Product\ProductEntity();
 $group->setCategoryIds(['category-a']);
 check(!$service->matchesProduct($faq,$product),'category-only group excluded from assigned product category');
 check($service->matchesCategory($faq,'category-a'),'direct category included');
 check(!$service->matchesCategory($faq,'category-child'),'child category excluded');
 check(!$service->matchesCategory($faq,'category-other'),'unrelated category excluded');
 $group->setProductIds(['product-a']);
 check($service->matchesProduct($faq,$product),'explicit product included even with category assignment');
 $variant=new \Shopware\Core\Content\Product\ProductEntity('variant-a','product-a');
 check($service->matchesProduct($faq,$variant),'parent assignment includes variant');
 check(!$service->matchesProduct($faq,new \Shopware\Core\Content\Product\ProductEntity('other-variant','other-parent')),'unrelated parent excluded');
 $group->setProductIds(['variant-a']);
 check($service->matchesProduct($faq,$variant),'specific variant assignment matches');
 check(!$service->matchesProduct($faq,new \Shopware\Core\Content\Product\ProductEntity('variant-b','product-a')),'variant assignment excludes sibling');
 check(!$service->matchesProduct($faq,$product),'variant assignment excludes parent');
 $group->setProductIds([]);
 check(!$service->matchesProduct($faq,$variant),'category-only assignment excludes variant');
 $group->setProductStreamIds(['stream-a']);
 check($service->matchesProduct($faq,$product,['stream-a']),'matching dynamic group included');
 check(!$service->matchesProduct($faq,$product,['stream-b']),'unmatched stream excluded');
 $group->setProductStreamIds([]);
 $group->setKeywords('outdoor; irrelevant');
 check($service->matchesProduct($faq,$product),'product keyword still matches');
 $group->setKeywords(null);$group->setCategoryIds([]);
 check(!$service->matchesProduct($faq,$product),'unassigned group excluded from product');
 check(!$service->matchesCategory($faq,'category-a'),'unassigned group excluded from category');
 check($service->isAvailableInSalesChannel($faq,'channel-a'),'unrestricted group available for CMS');
 $group->setActive(false);
 check(!$service->isAvailableInSalesChannel($faq,'channel-a'),'inactive group excluded');
 $group->setActive(true);$group->setSalesChannelIds(['channel-b']);
 check(!$service->isAvailableInSalesChannel($faq,'channel-a'),'wrong sales channel excluded');
 $group->setSalesChannelIds([]);$group->setRuleId('rule-a');
 check(!$service->isAvailableInSalesChannel($faq,'channel-a',[]),'unmatched rule excluded');
 check($service->isAvailableInSalesChannel($faq,'channel-a',['rule-a']),'matched rule included');
 $config=new \Shopware\Core\System\SystemConfig\SystemConfigService();
 $presentation=new \Tuami\FaqPro\Storefront\FaqPresentationConfig($config);
 $cms=new \Tuami\FaqPro\Storefront\Cms\FaqCmsElementResolver($service,$config,$presentation);
 $context=new \Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext(new class {
  public function getSalesChannelId(): string{return 'channel-a';}
 });
 $slot=new \Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity();
 $config->values=['global'=>['TuamiFaqPro.config.enabled'=>false]];
 check($cms->collect($slot,$context)===null,'global off prevents CMS query');
 $cms->enrich($slot,$context,new \Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection());
 check($slot->data->data['items']===[],'global off empties CMS output');
 $config->values['channel-a']=['TuamiFaqPro.config.enabled'=>true];
 check($cms->collect($slot,$context)!==null,'channel enable overrides global disable');
 $config->values=['global'=>['TuamiFaqPro.config.enabled'=>true],'channel-a'=>['TuamiFaqPro.config.enabled'=>false]];
 check($cms->collect($slot,$context)===null,'channel disable overrides global enable');
 $config->values=[];
 check($cms->collect($slot,$context)!==null,'missing config uses default enabled');
 echo "$count checks passed\n";
}