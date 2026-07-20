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