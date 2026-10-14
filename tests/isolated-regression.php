<?php declare(strict_types=1);
// Isolated regression checks: real plugin classes with minimal framework doubles.
namespace Shopware\Core\Framework\DataAbstractionLayer {
 class Entity {}
 class EntityCollection implements \IteratorAggregate {
  public function __construct(private array $elements=[]){}
  public function getElements(): array{return $this->elements;}
  public function getIterator(): \Traversable{return new \ArrayIterator($this->elements);}
 }
 trait EntityIdTrait { protected string $id; public function getId(): string{return $this->id;} public function setId(string $id): void{$this->id=$id;} }
 class EntityRepository {
  public array $queries=[];
  public array $searches=[];
  public function __construct(public array $fixtures=[]){}
  public function search($criteria,$context): object {
   $this->searches[]=$criteria;
   $items=$this->fixtures;
   foreach($criteria->filters as $filter){
    if($filter instanceof \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter && $filter->field==='groupId'){
     $items=array_filter($items,fn($faq)=>in_array($faq->getGroup()->getId(),$filter->value,true));
    }
   }
   return new class($items){
    public function __construct(private array $items){}
    public function getEntities(): EntityCollection{return new EntityCollection($this->items);}
   };
  }
  public function searchIds($criteria,$context): object {
   $this->queries[]=$criteria;
   return new class($criteria->filters) {
    public function __construct(private array $filters){}
    public function getTotal(): int {return in_array('match',$this->filters,true) ? 1 : 0;}
   };
  }
 }
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
namespace Shopware\Core\Framework {
 class Context {}
 class Plugin {public object $container; public function uninstall(\Shopware\Core\Framework\Plugin\Context\UninstallContext $context): void{}}
}
namespace Shopware\Core\Framework\Plugin\Context {
 class UninstallContext {public function __construct(private bool $keep){} public function keepUserData(): bool{return $this->keep;}}
}
namespace Doctrine\DBAL {
 class Connection {public array $statements=[]; public function executeStatement(string $sql): void{$this->statements[]=$sql;}}
}
namespace Symfony\Component\EventDispatcher {interface EventSubscriberInterface {public static function getSubscribedEvents(): array;}}
namespace Symfony\Contracts\Translation {interface TranslatorInterface {public function trans(string $id,array $parameters=[],?string $domain=null,?string $locale=null): string;}}
namespace Shopware\Core\Framework\Adapter\Cache {
 class CacheTagCollector {public array $tags=[];public function addTag(string ...$tags): void{array_push($this->tags,...$tags);}}
 class CacheInvalidator {public array $calls=[];public function invalidate(array $tags,bool $force=false): void{$this->calls[]=[$tags,$force];}}
}
namespace Shopware\Core\Framework\DataAbstractionLayer\Event {
 class EntityWrittenContainerEvent {public function __construct(private array $entities){}public function getPrimaryKeys(string $entity): array{return $this->entities[$entity] ?? [];}}
}
namespace Shopware\Core\System\SalesChannel {
 class SalesChannelContext {
  public function __construct(private string $id='channel-a',private array $rules=[]){}
  public function getSalesChannelId(): string{return $this->id;}
  public function getRuleIds(): array{return $this->rules;}
  public function getContext(): \Shopware\Core\Framework\Context{return new \Shopware\Core\Framework\Context();}
 }
}
namespace Shopware\Core\Content\ProductStream\Service {
 interface ProductStreamBuilderInterface {}
 abstract class AbstractProductStreamBuilder {
  abstract public function enrichCriteria(\Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria $criteria,string $id,\Shopware\Core\Framework\Context $context): void;
 }
}
namespace Shopware\Core\Framework\DataAbstractionLayer\Search {
 class Criteria {
  public array $filters=[];
  public function __construct(public array $ids=[]){}
  public function addAssociation($v): void{}
  public function addFilter(...$values): void{array_push($this->filters,...$values);}
  public function addSorting(...$v): void{}
 }
}
namespace Shopware\Core\Framework\DataAbstractionLayer\Search\Filter {
 class EqualsFilter { public function __construct(public string $field,public mixed $value){} }
 class EqualsAnyFilter extends EqualsFilter {}
 class MultiFilter {const CONNECTION_OR='OR';public function __construct(public string $operator,public array $queries){}}
}
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
 foreach(['Core/Content/Faq/FaqEntity.php','Core/Content/FaqGroup/FaqGroupEntity.php','Storefront/FaqVisibilityService.php','Storefront/FaqPresentationConfig.php','Storefront/FaqLoader.php','Storefront/Subscriber/FaqCacheSubscriber.php','TuamiFaqPro.php','Storefront/Cms/FaqCmsElementResolver.php'] as $file){require $root.'/src/'.$file;}
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
 $translator=new class implements \Symfony\Contracts\Translation\TranslatorInterface {
  public string $language='en';
  public function trans(string $id,array $parameters=[],?string $domain=null,?string $locale=null): string{return $this->language==='de' ? 'Häufig gestellte Fragen' : 'Frequently asked questions';}
 };
 $presentation=new \Tuami\FaqPro\Storefront\FaqPresentationConfig($config,$translator);
 $cacheTags=new \Shopware\Core\Framework\Adapter\Cache\CacheTagCollector();
 $cms=new \Tuami\FaqPro\Storefront\Cms\FaqCmsElementResolver($service,$config,$presentation,$cacheTags);
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

 $modernBuilder=new class extends \Shopware\Core\Content\ProductStream\Service\AbstractProductStreamBuilder implements \Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface {
  public array $calls=[];
  public int $legacyCalls=0;
  public function enrichCriteria(\Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria $criteria,string $id,\Shopware\Core\Framework\Context $context): void {
   $this->calls[]=$id;
   if($id==='deleted' || $id==='empty'){throw new \RuntimeException('Invalid or empty product stream');}
   $criteria->addFilter($id==='stream-a' ? 'match' : 'no-match');
  }
  public function buildFilters($id,$context): array {++$this->legacyCalls;throw new \RuntimeException('Deprecated API must not be called');}
 };
 $repository=new \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository();
 $modernService=new \Tuami\FaqPro\Storefront\FaqVisibilityService($modernBuilder,$repository);
 $dalContext=new \Shopware\Core\Framework\Context();
 check($modernService->matchingProductStreamIds(['stream-a','stream-a','stream-b','deleted','empty'],'variant-a',$dalContext)===['stream-a'],'modern builder matches, deduplicates and excludes invalid streams');
 check($modernBuilder->legacyCalls===0,'modern builder never calls deprecated buildFilters');
 check($modernBuilder->calls===['stream-a','stream-b','deleted','empty'],'each stream evaluated once');
 check(count($repository->queries)===2,'invalid and empty streams never cause unfiltered product search');
 check($repository->queries[0]->ids===['variant-a'],'stream criteria stay restricted to displayed variant');
 check($modernService->matchingProductStreamIds([],'variant-a',$dalContext)===[],'no streams means no matches');
 $legacyBuilder=new class implements \Shopware\Core\Content\ProductStream\Service\ProductStreamBuilderInterface {
  public function buildFilters($id,$context): array {return [$id==='stream-a' ? 'match' : 'no-match'];}
 };
 $legacyService=new \Tuami\FaqPro\Storefront\FaqVisibilityService($legacyBuilder,new \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository());
 check($legacyService->matchingProductStreamIds(['stream-a','stream-b'],'product-a',$dalContext)===['stream-a'],'older Shopware 6.7 builder remains supported');
 check(in_array(\Tuami\FaqPro\Storefront\FaqLoader::CACHE_TAG,$cacheTags->tags,true),'CMS collection adds FAQ cache tag');
 $config->values=[];
 check($presentation->headline('channel-a')==='Frequently asked questions','missing headline is translated to English');
 $translator->language='de';
 check($presentation->headline('channel-a')==='Häufig gestellte Fragen','missing headline follows German locale');
 $config->values=['global'=>['TuamiFaqPro.config.headline'=>'Meine Überschrift']];
 check($presentation->headline('channel-a')==='Meine Überschrift','custom headline preserved');
 $config->values['channel-a']=['TuamiFaqPro.config.headline'=>''];$translator->language='en';
 check($presentation->headline('channel-a')==='Frequently asked questions','empty channel headline uses translation');

 $invalidator=new \Shopware\Core\Framework\Adapter\Cache\CacheInvalidator();
 $subscriber=new \Tuami\FaqPro\Storefront\Subscriber\FaqCacheSubscriber($invalidator);
 foreach(['tuami_faq','tuami_faq_translation','tuami_faq_group','tuami_faq_group_translation','product_stream','product_stream_filter','rule','rule_condition'] as $entity){
  $subscriber->invalidate(new \Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent([$entity=>['changed-or-deleted-id']]));
  check(end($invalidator->calls)===[['tuami-faq'],true],"immediate FAQ invalidation for $entity");
 }
 $before=count($invalidator->calls);
 $subscriber->invalidate(new \Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent(['customer'=>['id']]));
 check(count($invalidator->calls)===$before,'unrelated writes leave FAQ cache intact');

 $group->setId('group-a');$group->setRuleId(null);$group->setSalesChannelIds([]);$group->setProductIds(['product-a']);$group->setCategoryIds(['category-a']);
 $faq->setQuestion('Question');$faq->setAnswer('Answer');
 $otherGroup=clone $group;$otherGroup->setId('group-b');$otherGroup->setProductIds(['product-b']);$otherGroup->setCategoryIds(['category-b']);
 $otherFaq=clone $faq;$otherFaq->setGroup($otherGroup);
 $groupRepo=new \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository([$group,$otherGroup]);
 $faqRepo=new \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository([$faq,$otherFaq]);
 $tags=new \Shopware\Core\Framework\Adapter\Cache\CacheTagCollector();
 $loader=new \Tuami\FaqPro\Storefront\FaqLoader($faqRepo,$groupRepo,$modernService,$tags);
 $channel=new \Shopware\Core\System\SalesChannel\SalesChannelContext();
 check($loader->load($channel,$variant)===[$faq],'loader limits parent-product assignment to matching group');
 check($faqRepo->searches[0]->filters[0]->value===['group-a'],'answer query is restricted by group IDs');
 check($loader->load($channel,categoryId:'category-a')===[$faq],'loader matches direct category only');
 $before=count($faqRepo->searches);
 check($loader->load($channel,categoryId:'category-child')===[],'loader excludes subcategory');
 check(count($faqRepo->searches)===$before,'unmatched category does not load any FAQ answers');
 check(count($tags->tags)===3,'empty results are also cache tagged');
 $group->setSalesChannelIds(['other-channel']);
 check($loader->load($channel,$variant)===[],'loader excludes unavailable sales channel');
 $group->setSalesChannelIds([]);$group->setRuleId('rule-a');
 check($loader->load($channel,$variant)===[],'loader excludes unmatched rule');
 check($loader->load(new \Shopware\Core\System\SalesChannel\SalesChannelContext('channel-a',['rule-a']),$variant)===[$faq],'loader includes matching rule');

 $connection=new \Doctrine\DBAL\Connection();
 $plugin=new \Tuami\FaqPro\TuamiFaqPro();
 $plugin->container=new class($connection){public function __construct(private object $connection){}public function get(string $id): object{return $this->connection;}};
 $plugin->uninstall(new \Shopware\Core\Framework\Plugin\Context\UninstallContext(true));
 check($connection->statements===[],'keepUserData never executes destructive SQL');
 $plugin->uninstall(new \Shopware\Core\Framework\Plugin\Context\UninstallContext(false));
 check($connection->statements===array_map(fn($table)=>'DROP TABLE IF EXISTS `'.$table.'`',['tuami_faq_translation','tuami_faq_group_translation','tuami_faq','tuami_faq_group']),'uninstall removes only four owned tables in dependency order');
 echo "$count checks passed\n";
}
