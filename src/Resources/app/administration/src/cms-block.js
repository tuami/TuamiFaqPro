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