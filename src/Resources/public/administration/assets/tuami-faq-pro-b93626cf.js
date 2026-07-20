(function(){
const { Component, Module, Mixin, Context } = Shopware;
const { Criteria } = Shopware.Data;

const faqListTemplate = `
<sw-page class="tuami-faq-list">
    <template #search-bar>
        <sw-search-bar :initial-search="term" @search="onSearch" />
    </template>
    <template #smart-bar-header>
        <h2>{{ $t('tuami-faq.list.title') }} <span v-if="!isLoading">({{ total }})</span></h2>
    </template>
    <template #smart-bar-actions>
        <mt-button variant="secondary" @click="$router.push({ name: 'tuami.faq.groups' })">
            {{ $t('tuami-faq.general.groups') }}
        </mt-button>
        <mt-button
            variant="primary"
            :disabled="!acl.can('tuami_faq.creator') || undefined"
            @click="$router.push({ name: 'tuami.faq.create' })">
            {{ $t('tuami-faq.list.add') }}
        </mt-button>
    </template>
    <template #language-switch>
        <sw-language-switch @on-change="onChangeLanguage" />
    </template>
    <template #content>
        <sw-loader v-if="isLoading" />
        <sw-entity-listing
            v-else
            identifier="tuami-faq-list"
            detail-route="tuami.faq.detail"
            :data-source="items"
            :repository="repository"
            :columns="columns"
            :is-loading="isLoading"
            :disable-data-fetching="true"
            :allow-edit="acl.can('tuami_faq.editor') || undefined"
            :allow-inline-edit="false"
            :allow-delete="acl.can('tuami_faq.deleter') || undefined"
            :show-selections="acl.can('tuami_faq.deleter') || undefined"
            :full-page="true"
            @page-change="onPageChange">
            <template #column-active="{ item }">
                <sw-icon :name="item.active ? 'regular-checkmark-xs' : 'regular-times-xs'" small />
            </template>
        </sw-entity-listing>
    </template>
</sw-page>`;

const groupListTemplate = `
<sw-page class="tuami-faq-group-list">
    <template #search-bar>
        <sw-search-bar :initial-search="term" @search="onSearch" />
    </template>
    <template #smart-bar-header>
        <h2>{{ $t('tuami-faq.groupList.title') }} <span v-if="!isLoading">({{ total }})</span></h2>
    </template>
    <template #smart-bar-actions>
        <mt-button variant="secondary" @click="$router.push({ name: 'tuami.faq.index' })">
            {{ $t('tuami-faq.general.faqs') }}
        </mt-button>
        <mt-button
            variant="primary"
            :disabled="!acl.can('tuami_faq.creator') || undefined"
            @click="$router.push({ name: 'tuami.faq.groupCreate' })">
            {{ $t('tuami-faq.groupList.add') }}
        </mt-button>
    </template>
    <template #language-switch>
        <sw-language-switch @on-change="onChangeLanguage" />
    </template>
    <template #content>
        <sw-loader v-if="isLoading" />
        <sw-entity-listing
            v-else
            identifier="tuami-faq-group-list"
            detail-route="tuami.faq.groupDetail"
            :data-source="items"
            :repository="repository"
            :columns="columns"
            :is-loading="isLoading"
            :disable-data-fetching="true"
            :allow-edit="acl.can('tuami_faq.editor') || undefined"
            :allow-inline-edit="false"
            :allow-delete="acl.can('tuami_faq.deleter') || undefined"
            :show-selections="acl.can('tuami_faq.deleter') || undefined"
            :full-page="true"
            @page-change="onPageChange">
            <template #column-active="{ item }">
                <sw-icon :name="item.active ? 'regular-checkmark-xs' : 'regular-times-xs'" small />
            </template>
        </sw-entity-listing>
    </template>
</sw-page>`;

const faqDetailTemplate = `
<sw-page class="tuami-faq-detail">
    <template #smart-bar-header>
        <h2>{{ isCreate ? $t('tuami-faq.detail.newTitle') : (entity && entity.question) }}</h2>
    </template>
    <template #smart-bar-actions>
        <mt-button variant="secondary" @click="$router.push({ name: 'tuami.faq.index' })">
            {{ $t('tuami-faq.general.cancel') }}
        </mt-button>
        <mt-button
            variant="primary"
            :is-loading="isSaving"
            :disabled="!canSave || undefined"
            @click="onSave">
            {{ $t('tuami-faq.general.save') }}
        </mt-button>
    </template>
    <template #language-switch>
        <sw-language-switch :disabled="isCreate" @on-change="onChangeLanguage" />
    </template>
    <template #content>
        <sw-loader v-if="isLoading" />
        <sw-card-view v-else-if="entity">
            <mt-card :title="$t('tuami-faq.detail.contentCard')" position-identifier="tuami-faq-detail-content">
                <sw-entity-single-select
                    v-model:value="entity.groupId"
                    entity="tuami_faq_group"
                    label-property="name"
                    required
                    :label="$t('tuami-faq.detail.group')"
                    :placeholder="$t('tuami-faq.detail.groupPlaceholder')" />
                <mt-text-field
                    v-model="entity.question"
                    required
                    :label="$t('tuami-faq.detail.question')"
 />
                <sw-text-editor
                    v-model:value="entity.answer"
                    sanitize-input
                    sanitize-field-name="tuami_faq_translation.answer"
                    :label="$t('tuami-faq.detail.answer')" />
                <sw-container columns="1fr 1fr" gap="24px">
                    <mt-switch v-model="entity.active" bordered :label="$t('tuami-faq.detail.active')" />
                    <mt-number-field v-model="entity.position" number-type="int" :label="$t('tuami-faq.detail.position')" />
                </sw-container>
            </mt-card>
        </sw-card-view>
    </template>
</sw-page>`;

const groupDetailTemplate = `
<sw-page class="tuami-faq-group-detail">
    <template #smart-bar-header>
        <h2>{{ isCreate ? $t('tuami-faq.groupDetail.newTitle') : (entity && entity.name) }}</h2>
    </template>
    <template #smart-bar-actions>
        <mt-button variant="secondary" @click="$router.push({ name: 'tuami.faq.groups' })">
            {{ $t('tuami-faq.general.cancel') }}
        </mt-button>
        <mt-button
            variant="primary"
            :is-loading="isSaving"
            :disabled="!canSave || undefined"
            @click="onSave">
            {{ $t('tuami-faq.general.save') }}
        </mt-button>
    </template>
    <template #language-switch>
        <sw-language-switch :disabled="isCreate" @on-change="onChangeLanguage" />
    </template>
    <template #content>
        <sw-loader v-if="isLoading" />
        <sw-card-view v-else-if="entity">
            <mt-card :title="$t('tuami-faq.groupDetail.contentCard')" position-identifier="tuami-faq-group-detail-content">
                <mt-text-field v-model="entity.name" required :label="$t('tuami-faq.groupDetail.name')" />
                <mt-textarea v-model="entity.description" :label="$t('tuami-faq.groupDetail.description')" />
                <sw-container columns="1fr 1fr" gap="24px">
                    <mt-switch v-model="entity.active" bordered :label="$t('tuami-faq.groupDetail.active')" />
                    <mt-number-field v-model="entity.position" number-type="int" :label="$t('tuami-faq.groupDetail.position')" />
                </sw-container>
            </mt-card>

            <mt-card :title="$t('tuami-faq.groupDetail.assignmentCard')" position-identifier="tuami-faq-group-detail-assignment">
                <p>{{ $t('tuami-faq.groupDetail.assignmentHelp') }}</p>
                <sw-entity-multi-id-select :value="entity.productIds" :repository="productRepository" label-property="name" @update:value="updateIdSelection('productIds', $event)" :label="$t('tuami-faq.groupDetail.products')" />
                <sw-entity-multi-id-select :value="entity.categoryIds" :repository="categoryRepository" label-property="name" @update:value="updateIdSelection('categoryIds', $event)" :label="$t('tuami-faq.groupDetail.categories')" />
                <sw-entity-multi-id-select :value="entity.productStreamIds" :repository="productStreamRepository" label-property="name" @update:value="updateIdSelection('productStreamIds', $event)" :label="$t('tuami-faq.groupDetail.productStreams')" />
                <mt-textarea v-model="entity.keywords" :label="$t('tuami-faq.groupDetail.keywords')" :help-text="$t('tuami-faq.groupDetail.keywordsHelp')" />
            </mt-card>

            <mt-card :title="$t('tuami-faq.groupDetail.availabilityCard')" position-identifier="tuami-faq-group-detail-availability">
                <sw-entity-multi-id-select :value="entity.salesChannelIds" :repository="salesChannelRepository" label-property="name" @update:value="updateIdSelection('salesChannelIds', $event)" :label="$t('tuami-faq.groupDetail.salesChannels')" :help-text="$t('tuami-faq.groupDetail.salesChannelsHelp')" />
                <sw-entity-single-select v-model:value="entity.ruleId" entity="rule" label-property="name" show-clearable-button :label="$t('tuami-faq.groupDetail.rule')" :help-text="$t('tuami-faq.groupDetail.ruleHelp')" />
            </mt-card>
        </sw-card-view>
    </template>
</sw-page>`;

function createListComponent(entityName, template, columnsFactory, association) {
    return {
        template,
        inject: ['repositoryFactory', 'acl'],
        mixins: [Mixin.getByName('notification')],
        data() {
            return {
                items: null,
                isLoading: false,
                page: 1,
                limit: 25,
                total: 0,
                term: '',
            };
        },
        computed: {
            repository() {
                return this.repositoryFactory.create(entityName);
            },
            columns() {
                return columnsFactory.call(this);
            },
        },
        created() {
            this.getList();
        },
        methods: {
            async getList() {
                this.isLoading = true;
                const criteria = new Criteria(this.page, this.limit);
                criteria.setTerm(this.term);
                criteria.addSorting(Criteria.sort('position', 'ASC'));
                if (association) {
                    criteria.addAssociation(association);
                }
                try {
                    const result = await this.repository.search(criteria, Context.api);
                    this.items = result;
                    this.total = result.total;
                } catch (error) {
                    this.items = [];
                    this.total = 0;
                    const detail = error?.response?.data?.errors?.[0]?.detail || error?.message || '';
                    this.createNotificationError({
                        message: detail
                            ? `${this.$t('tuami-faq.general.loadError')} ${detail}`
                            : this.$t('tuami-faq.general.loadError'),
                    });
                    console.error('[TUAMI FAQ Pro] FAQ list loading failed', error);
                } finally {
                    this.isLoading = false;
                }
            },
            onSearch(term) {
                this.term = term;
                this.page = 1;
                this.getList();
            },
            onPageChange({ page, limit }) {
                this.page = page;
                this.limit = limit;
                this.getList();
            },
            onChangeLanguage() {
                this.getList();
            },
        },
    };
}

Component.register('tuami-faq-list', createListComponent('tuami_faq', faqListTemplate, function columns() {
    return [
        { property: 'question', label: this.$t('tuami-faq.list.question'), primary: true, routerLink: 'tuami.faq.detail' },
        { property: 'group.name', label: this.$t('tuami-faq.list.group') },
        { property: 'active', label: this.$t('tuami-faq.list.active'), align: 'center' },
        { property: 'position', label: this.$t('tuami-faq.list.position'), align: 'right' },
        { property: 'updatedAt', label: this.$t('tuami-faq.list.updatedAt') },
    ];
}, 'group'));

Component.register('tuami-faq-group-list', createListComponent('tuami_faq_group', groupListTemplate, function columns() {
    return [
        { property: 'name', label: this.$t('tuami-faq.groupList.name'), primary: true, routerLink: 'tuami.faq.groupDetail' },
        { property: 'active', label: this.$t('tuami-faq.groupList.active'), align: 'center' },
        { property: 'position', label: this.$t('tuami-faq.groupList.position'), align: 'right' },
        { property: 'updatedAt', label: this.$t('tuami-faq.groupList.updatedAt') },
    ];
}));

function createDetailComponent(entityName, template, defaults, listRoute, detailRoute, requiredFields) {
    return {
        template,
        inject: ['repositoryFactory', 'acl'],
        mixins: [Mixin.getByName('notification')],
        data() {
            return {
                entity: null,
                isLoading: false,
                isSaving: false,
            };
        },
        computed: {
            repository() {
                return this.repositoryFactory.create(entityName);
            },
            isCreate() {
                return !this.$route.params.id;
            },
            canSave() {
                const allowed = this.isCreate ? this.acl.can('tuami_faq.creator') : this.acl.can('tuami_faq.editor');
                return allowed && !this.isLoading && !this.isSaving && requiredFields.every((field) => {
                    const value = this.entity && this.entity[field];
                    return value !== null && value !== undefined && String(value).trim() !== '';
                });
            },
            productRepository() {
                return this.repositoryFactory.create('product');
            },
            categoryRepository() {
                return this.repositoryFactory.create('category');
            },
            salesChannelRepository() {
                return this.repositoryFactory.create('sales_channel');
            },
            productStreamRepository() {
                return this.repositoryFactory.create('product_stream');
            },
        },
        created() {
            this.loadEntity();
        },
        methods: {
            async loadEntity() {
                this.isLoading = true;
                try {
                    if (this.isCreate) {
                        this.entity = this.repository.create(Context.api);
                        Object.assign(this.entity, defaults);
                    } else {
                        const criteria = new Criteria();
                        if (entityName === 'tuami_faq') {
                            criteria.addAssociation('group');
                        }
                        this.entity = await this.repository.get(this.$route.params.id, Context.api, criteria);
                    }

                    this.normalizeIdSelections();
                } catch (error) {
                    this.createNotificationError({ message: this.$t('tuami-faq.general.loadError') });
                    this.$router.push({ name: listRoute });
                } finally {
                    this.isLoading = false;
                }
            },
            async onSave() {
                if (!this.canSave) {
                    return;
                }
                this.normalizeIdSelections();
                this.isSaving = true;
                try {
                    await this.repository.save(this.entity, Context.api);
                    this.createNotificationSuccess({ message: this.$t('tuami-faq.general.saveSuccess') });
                    if (this.isCreate) {
                        await this.$router.replace({ name: detailRoute, params: { id: this.entity.id } });
                    }
                    await this.loadEntity();
                } catch (error) {
                    this.createNotificationError({ message: this.$t('tuami-faq.general.saveError') });
                } finally {
                    this.isSaving = false;
                }
            },
            onChangeLanguage() {
                if (!this.isCreate) {
                    this.loadEntity();
                }
            },
            updateIdSelection(field, ids) {
                this.entity[field] = this.normalizeIds(ids);
            },
            normalizeIdSelections() {
                if (!this.entity) { return; }
                ['productIds', 'categoryIds', 'salesChannelIds', 'productStreamIds'].forEach((field) => {
                    if (field in this.entity) { this.entity[field] = this.normalizeIds(this.entity[field]); }
                });
            },
            normalizeIds(ids) {
                if (!Array.isArray(ids)) { return []; }
                return [...new Set(ids.filter((id) => typeof id === 'string' && id.length > 0))];
            },
        },
    };
}

Component.register('tuami-faq-detail', createDetailComponent(
    'tuami_faq',
    faqDetailTemplate,
    {
        groupId: null,
        question: '',
        answer: '',
        active: true,
        position: 0,
    },
    'tuami.faq.index',
    'tuami.faq.detail',
    ['groupId', 'question', 'answer'],
));

Component.register('tuami-faq-group-detail', createDetailComponent(
    'tuami_faq_group',
    groupDetailTemplate,
    {
        name: '',
        description: '',
        active: true,
        position: 0,
        salesChannelIds: [],
        productStreamIds: [],
        productIds: [],
        categoryIds: [],
        keywords: '',
    },
    'tuami.faq.groups',
    'tuami.faq.groupDetail',
    ['name'],
));

Component.register('sw-cms-el-tuami-faq', {
    template: `
        <div class="sw-cms-el-tuami-faq">
            <h3>{{ element.config.headline.value || $t('tuami-faq.cms.label') }}</h3>
            <div style="border-top: 1px solid #d1d9e0; padding: 12px 0;">{{ $t('tuami-faq.cms.previewQuestionOne') }}</div>
            <div style="border-top: 1px solid #d1d9e0; padding: 12px 0;">{{ $t('tuami-faq.cms.previewQuestionTwo') }}</div>
        </div>`,
    mixins: [Mixin.getByName('cms-element')],
    created() {
        this.initElementConfig('tuami-faq');
    },
});

Component.register('sw-cms-el-preview-tuami-faq', {
    template: `
        <div style="padding: 16px; background: #fff;">
            <strong>{{ $t('tuami-faq.cms.label') }}</strong>
            <div style="margin-top: 12px; border-top: 1px solid #d1d9e0; padding-top: 8px;">Q + A</div>
        </div>`,
});

Component.register('sw-cms-el-config-tuami-faq', {
    template: `
        <div class="sw-cms-el-config-tuami-faq">
            <sw-entity-single-select
                v-model:value="element.config.groupId.value"
                entity="tuami_faq_group"
                label-property="name"
                required
                :label="$t('tuami-faq.cms.group')"
                @update:value="onElementUpdate" />
            <mt-text-field
                v-model="element.config.headline.value"
                :label="$t('tuami-faq.cms.headline')"
                @update:model-value="onElementUpdate" />
        </div>`,
    emits: ['element-update'],
    mixins: [Mixin.getByName('cms-element')],
    created() {
        this.initElementConfig('tuami-faq');
    },
    methods: {
        onElementUpdate() {
            this.$emit('element-update', this.element);
        },
    },
});

Shopware.Service('cmsService').registerCmsElement({
    name: 'tuami-faq',
    label: 'tuami-faq.cms.label',
    component: 'sw-cms-el-tuami-faq',
    configComponent: 'sw-cms-el-config-tuami-faq',
    previewComponent: 'sw-cms-el-preview-tuami-faq',
    defaultConfig: {
        groupId: { source: 'static', value: null },
        headline: { source: 'static', value: '' },
    },
});

Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'catalogues',
    key: 'tuami_faq',
    roles: {
        viewer: {
            privileges: [
                'tuami_faq:read',
                'tuami_faq_group:read',
                'product:read',
                'category:read',
                'sales_channel:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: ['tuami_faq:update', 'tuami_faq_group:update'],
            dependencies: ['tuami_faq.viewer'],
        },
        creator: {
            privileges: ['tuami_faq:create', 'tuami_faq_group:create'],
            dependencies: ['tuami_faq.viewer', 'tuami_faq.editor'],
        },
        deleter: {
            privileges: ['tuami_faq:delete', 'tuami_faq_group:delete'],
            dependencies: ['tuami_faq.viewer'],
        },
    },
});

Module.register('tuami-faq', {
    type: 'plugin',
    name: 'tuami-faq',
    title: 'tuami-faq.general.mainMenuItem',
    description: 'tuami-faq.general.description',
    color: '#0870d1',
    icon: 'regular-comments',
    entity: 'tuami_faq',
    version: '1.1.0',
    targetVersion: '1.1.0',
    snippets: {
        'de-DE': {
            'tuami-faq': {
                general: {
                    mainMenuItem: 'FAQ Pro',
                    description: 'FAQs zentral verwalten und gezielt ausspielen',
                    faqs: 'FAQs',
                    groups: 'Gruppen',
                    save: 'Speichern',
                    cancel: 'Abbrechen',
                    loadError: 'Die Daten konnten nicht geladen werden.',
                    saveError: 'Die Änderungen konnten nicht gespeichert werden.',
                    saveSuccess: 'Die Änderungen wurden gespeichert.',
                },
                list: {
                    title: 'FAQs',
                    add: 'FAQ anlegen',
                    question: 'Frage',
                    group: 'Gruppe',
                    active: 'Aktiv',
                    position: 'Position',
                    updatedAt: 'Geändert am',
                },
                groupList: {
                    title: 'FAQ-Gruppen',
                    add: 'Gruppe anlegen',
                    name: 'Name',
                    active: 'Aktiv',
                    position: 'Position',
                    updatedAt: 'Geändert am',
                },
                detail: {
                    newTitle: 'Neue FAQ',
                    contentCard: 'Inhalt',
                    assignmentCard: 'Dynamische Zuordnung',
                    seoCard: 'SEO und Detailseite',
                    group: 'FAQ-Gruppe',
                    groupPlaceholder: 'Gruppe auswählen',
                    question: 'Frage',
                    answer: 'Antwort',
                    active: 'Aktiv',
                    position: 'Position',
                    assignmentHelp: 'Ohne Zuordnung wird die FAQ überall angezeigt. Mehrere Kriterien werden mit ODER verknüpft.',
                    products: 'Bestimmte Produkte',
                    categories: 'Produktkategorien',
                    keywords: 'Produkt-Schlüsselwörter',
                    keywordsHelp: 'Komma-, Semikolon- oder zeilengetrennt; Abgleich mit Produktname, Beschreibung und Produktnummer.',
                    slug: 'URL-Slug',
                    metaTitle: 'Meta-Titel',
                    metaDescription: 'Meta-Beschreibung',
                    noIndex: 'Detailseite nicht indexieren (noindex)',
                },
                groupDetail: {
                    newTitle: 'Neue FAQ-Gruppe',
                    contentCard: 'Gruppe',
                    name: 'Name',
                    description: 'Beschreibung',
                    active: 'Aktiv',
                    position: 'Position',
                    salesChannels: 'Verkaufskanäle',
                    salesChannelsHelp: 'Leer lassen, um die Gruppe in allen Verkaufskanälen zu verwenden.',
                },
                cms: {
                    label: 'FAQ Pro',
                    group: 'FAQ-Gruppe',
                    headline: 'Überschrift',
                    previewQuestionOne: 'Wie funktioniert dieses Produkt?',
                    previewQuestionTwo: 'Welche Vorteile bietet es?',
                },
            },
        },
        'en-GB': {
            'tuami-faq': {
                general: {
                    mainMenuItem: 'FAQ Pro',
                    description: 'Manage and display FAQs centrally',
                    faqs: 'FAQs',
                    groups: 'Groups',
                    save: 'Save',
                    cancel: 'Cancel',
                    loadError: 'The data could not be loaded.',
                    saveError: 'The changes could not be saved.',
                    saveSuccess: 'The changes were saved.',
                },
                list: {
                    title: 'FAQs',
                    add: 'Add FAQ',
                    question: 'Question',
                    group: 'Group',
                    active: 'Active',
                    position: 'Position',
                    updatedAt: 'Updated at',
                },
                groupList: {
                    title: 'FAQ groups',
                    add: 'Add group',
                    name: 'Name',
                    active: 'Active',
                    position: 'Position',
                    updatedAt: 'Updated at',
                },
                detail: {
                    newTitle: 'New FAQ',
                    contentCard: 'Content',
                    assignmentCard: 'Dynamic assignment',
                    seoCard: 'SEO and detail page',
                    group: 'FAQ group',
                    groupPlaceholder: 'Select group',
                    question: 'Question',
                    answer: 'Answer',
                    active: 'Active',
                    position: 'Position',
                    assignmentHelp: 'With no assignment, the FAQ is shown everywhere. Multiple criteria are combined with OR.',
                    products: 'Specific products',
                    categories: 'Product categories',
                    keywords: 'Product keywords',
                    keywordsHelp: 'Separate by commas, semicolons or new lines; matched against product name, description and product number.',
                    slug: 'URL slug',
                    metaTitle: 'Meta title',
                    metaDescription: 'Meta description',
                    noIndex: 'Do not index detail page (noindex)',
                },
                groupDetail: {
                    newTitle: 'New FAQ group',
                    contentCard: 'Group',
                    name: 'Name',
                    description: 'Description',
                    active: 'Active',
                    position: 'Position',
                    salesChannels: 'Sales channels',
                    salesChannelsHelp: 'Leave empty to use the group in every sales channel.',
                },
                cms: {
                    label: 'FAQ Pro',
                    group: 'FAQ group',
                    headline: 'Headline',
                    previewQuestionOne: 'How does this product work?',
                    previewQuestionTwo: 'What are its benefits?',
                },
            },
        },
    },
    routes: {
        index: {
            component: 'tuami-faq-list',
            path: 'index',
            meta: { privilege: 'tuami_faq.viewer' },
        },
        create: {
            component: 'tuami-faq-detail',
            path: 'create',
            meta: { parentPath: 'tuami.faq.index', privilege: 'tuami_faq.creator' },
        },
        detail: {
            component: 'tuami-faq-detail',
            path: 'detail/:id',
            meta: { parentPath: 'tuami.faq.index', privilege: 'tuami_faq.viewer' },
        },
        groups: {
            component: 'tuami-faq-group-list',
            path: 'groups',
            meta: { parentPath: 'tuami.faq.index', privilege: 'tuami_faq.viewer' },
        },
        groupCreate: {
            component: 'tuami-faq-group-detail',
            path: 'groups/create',
            meta: { parentPath: 'tuami.faq.groups', privilege: 'tuami_faq.creator' },
        },
        groupDetail: {
            component: 'tuami-faq-group-detail',
            path: 'groups/detail/:id',
            meta: { parentPath: 'tuami.faq.groups', privilege: 'tuami_faq.viewer' },
        },
    },
    navigation: [{
        id: 'tuami-faq',
        label: 'tuami-faq.general.mainMenuItem',
        color: '#0870d1',
        path: 'tuami.faq.index',
        icon: 'regular-comments',
        parent: 'sw-catalogue',
        privilege: 'tuami_faq.viewer',
        position: 80,
    }],
    settingsItem: {
        group: 'plugins',
        to: 'tuami.faq.index',
        icon: 'regular-comments',
        privilege: 'tuami_faq.viewer',
    },
});



})();
(function(){
const { Component, Locale } = Shopware;

const advancedGroupDetailTemplate = `
<sw-page class="tuami-faq-group-detail">
    <template #smart-bar-header><h2>{{ isCreate ? $t('tuami-faq.groupDetail.newTitle') : (entity && entity.name) }}</h2></template>
    <template #smart-bar-actions>
        <mt-button variant="secondary" @click="$router.push({ name: 'tuami.faq.groups' })">{{ $t('tuami-faq.general.cancel') }}</mt-button>
        <mt-button variant="primary" :is-loading="isSaving" :disabled="!canSave || undefined" @click="onSave">{{ $t('tuami-faq.general.save') }}</mt-button>
    </template>
    <template #language-switch><sw-language-switch :disabled="isCreate" @on-change="onChangeLanguage" /></template>
    <template #content>
        <sw-loader v-if="isLoading" />
        <sw-card-view v-else-if="entity">
            <mt-card :title="$t('tuami-faq.groupDetail.contentCard')" position-identifier="tuami-faq-group-detail-content">
                <mt-text-field v-model="entity.name" required :label="$t('tuami-faq.groupDetail.name')" />
                <mt-textarea v-model="entity.description" :label="$t('tuami-faq.groupDetail.description')" />
                <sw-container columns="1fr 1fr" gap="24px">
                    <mt-switch v-model="entity.active" bordered :label="$t('tuami-faq.groupDetail.active')" />
                    <mt-number-field v-model="entity.position" number-type="int" :label="$t('tuami-faq.groupDetail.position')" />
                </sw-container>
            </mt-card>

            <mt-card :title="$t('tuami-faq.groupDetail.assignmentCard')" position-identifier="tuami-faq-group-detail-assignment">
                <p>{{ $t('tuami-faq.groupDetail.assignmentHelp') }}</p>
                <sw-entity-multi-id-select :value="entity.productIds" :repository="productRepository" label-property="name" @update:value="updateIdSelection('productIds', $event)" :label="$t('tuami-faq.groupDetail.products')" />
                <sw-entity-multi-id-select :value="entity.categoryIds" :repository="categoryRepository" label-property="name" @update:value="updateIdSelection('categoryIds', $event)" :label="$t('tuami-faq.groupDetail.categories')" />
                <sw-entity-multi-id-select :value="entity.productStreamIds" :repository="productStreamRepository" label-property="name" @update:value="updateIdSelection('productStreamIds', $event)" :label="$t('tuami-faq.groupDetail.productStreams')" />
                <mt-textarea v-model="entity.keywords" :label="$t('tuami-faq.groupDetail.keywords')" :help-text="$t('tuami-faq.groupDetail.keywordsHelp')" />
            </mt-card>

            <mt-card :title="$t('tuami-faq.groupDetail.availabilityCard')" position-identifier="tuami-faq-group-detail-availability">
                <sw-entity-multi-id-select :value="entity.salesChannelIds" :repository="salesChannelRepository" label-property="name" @update:value="updateIdSelection('salesChannelIds', $event)" :label="$t('tuami-faq.groupDetail.salesChannels')" :help-text="$t('tuami-faq.groupDetail.salesChannelsHelp')" />
                <sw-entity-single-select v-model:value="entity.ruleId" entity="rule" label-property="name" show-clearable-button :label="$t('tuami-faq.groupDetail.rule')" :help-text="$t('tuami-faq.groupDetail.ruleHelp')" />
            </mt-card>
        </sw-card-view>
    </template>
</sw-page>`;

Component.override('tuami-faq-group-detail', {
    template: advancedGroupDetailTemplate,
    computed: {
        productStreamRepository() { return this.repositoryFactory.create('product_stream'); },
        ruleRepository() { return this.repositoryFactory.create('rule'); },
    },
    methods: {
        async loadEntity() {
            await this.$super('loadEntity');
            this.normalizeIdSelections();
        },
    },
});

Locale.extend('de-DE', {
    'tuami-faq': { groupDetail: {
        assignmentCard: 'Zuordnung zu Produkten und Kategorien',
        assignmentHelp: 'Die Gruppe wird nur angezeigt, wenn mindestens eine Zuordnung passt. Produkte, Kategorien, dynamische Produktgruppen und Schlüsselwörter werden mit ODER verknüpft.',
        products: 'Bestimmte Produkte',
        productStreams: 'Dynamische Produktgruppen',
        categories: 'Produktkategorien',
        keywords: 'Produkt-Schlüsselwörter',
        keywordsHelp: 'Komma-, Semikolon- oder zeilengetrennt. Der Abgleich erfolgt mit Produktname, Beschreibung und Produktnummer.',
        availabilityCard: 'Verkaufskanal und Regeln',
        rule: 'Rule-Builder-Regel',
        ruleHelp: 'Optional: Die Gruppe ist nur sichtbar, wenn diese Regel im aktuellen Verkaufskanal erfüllt ist.',
    } },
});

Locale.extend('en-GB', {
    'tuami-faq': { groupDetail: {
        assignmentCard: 'Product and category assignment',
        assignmentHelp: 'The group is only displayed when at least one assignment matches. Products, categories, dynamic product groups and keywords are combined with OR.',
        products: 'Specific products',
        productStreams: 'Dynamic product groups',
        categories: 'Product categories',
        keywords: 'Product keywords',
        keywordsHelp: 'Separate with commas, semicolons or new lines. Matching uses the product name, description and product number.',
        availabilityCard: 'Sales channel and rules',
        rule: 'Rule Builder rule',
        ruleHelp: 'Optional: The group is only visible when this rule matches in the current sales channel.',
    } },
});

Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions', parent: 'catalogues', key: 'tuami_faq',
    roles: { viewer: { privileges: ['product_stream:read', 'rule:read'], dependencies: [] } },
});
})();
(function(){
const { Component, Locale } = Shopware;

Component.register('sw-cms-block-tuami-faq', {
    template: `
        <div class="sw-cms-block-tuami-faq" style="padding: 20px;">
            <slot name="content"></slot>
        </div>`,
});

Component.register('sw-cms-preview-tuami-faq', {
    template: `
        <div style="padding: 16px; background: #fff;">
            <strong>{{ $t('tuami-faq.cms.label') }}</strong>
            <div style="margin-top: 12px; border-top: 1px solid #d1d9e0; padding: 8px 0;">Q + A</div>
            <div style="border-top: 1px solid #d1d9e0; padding-top: 8px;">Q + A</div>
        </div>`,
});

Shopware.Service('cmsService').registerCmsBlock({
    name: 'tuami-faq',
    label: 'tuami-faq.cms.label',
    category: 'text',
    component: 'sw-cms-block-tuami-faq',
    previewComponent: 'sw-cms-preview-tuami-faq',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: null,
        marginRight: null,
        sizingMode: 'boxed',
    },
    slots: {
        content: 'tuami-faq',
    },
});

Locale.extend('de-DE', {
    'tuami-faq': {
        cms: {
            label: 'FAQ Pro',
        },
    },
});

Locale.extend('en-GB', {
    'tuami-faq': {
        cms: {
            label: 'FAQ Pro',
        },
    },
});

})();