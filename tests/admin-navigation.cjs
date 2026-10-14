const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const path = require('node:path');
const components = {};
class Criteria {
    constructor(page, limit) { this.page = page; this.limit = limit; this.filters = []; this.sorting = []; }
    setTerm(term) { this.term = term; }
    addSorting(sort) { this.sorting.push(sort); }
    addAssociation(name) { this.association = name; }
    addFilter(filter) { this.filters.push(filter); }
    static equals(field, value) { return { type: 'equals', field, value }; }
    static sort(field, order) { return { field, order }; }
}
let moduleDefinition;
const Shopware = {
    Component: { register: (name, value) => { components[name] = value; } },
    Module: { register: (_, value) => { moduleDefinition = value; } },
    Mixin: { getByName: () => ({}) }, Context: {}, Data: { Criteria },
    Service: () => ({ registerCmsElement() {}, addPrivilegeMappingEntry() {} }),
};
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../src/Resources/app/administration/src/core.js'), 'utf8'), { Shopware, Intl, Date, console, setTimeout, clearTimeout });
assert.equal(moduleDefinition.navigation.length, 3);
const [parent, faqs, groups] = moduleDefinition.navigation;
assert.equal(parent.parent, 'sw-catalogue');
assert.equal(parent.path, undefined);
for (const entry of [faqs, groups]) {
    assert.equal(entry.parent, parent.id);
    assert.equal(entry.privilege, 'tuami_faq.viewer');
}
assert.equal(faqs.path, 'tuami.faq.index');
assert.equal(groups.path, 'tuami.faq.groups');
for (const language of ['de-DE', 'en-GB']) {
    assert.equal(moduleDefinition.snippets[language]['tuami-faq'].general.mainMenuItem, 'FAQ Pro');
    assert.equal(moduleDefinition.snippets[language]['tuami-faq'].cms.label, 'FAQ Pro');
}
for (const name of ['tuami-faq-list', 'tuami-faq-group-list']) {
    const component = components[name];
    assert(component.template.includes('<tm-faq-icon />'));
    assert(component.template.includes("acl.can('tuami_faq.creator')"));
    assert(component.template.includes('#column-updatedAt'));
    const format = component.methods.formatDate.bind({ $i18n: { locale: 'de-DE' } });
    assert.equal(format(null), '—');
    assert.equal(format('invalid'), '—');
    assert(!format('2026-10-14T12:00:00Z').includes('T12:'));
}
assert.equal(moduleDefinition.routes.create.meta.privilege, 'tuami_faq.creator');
assert.equal(moduleDefinition.routes.groupCreate.meta.privilege, 'tuami_faq.creator');
console.log('PASS navigation hierarchy, labels, creation permissions, header icons and localized dates');

(async () => {
    const def = components['tuami-faq-list'];
    assert(def.template.includes("$t('tuami-faq.list.searchLabel')"));
    assert(def.template.includes('@update:model-value="onSearchInput"'));
    assert(!def.template.includes('sw-search-bar'));
    assert(def.template.includes('flex-wrap:wrap'));
    for (const name of ['tuami-faq-list', 'tuami-faq-group-list', 'tuami-faq-detail', 'tuami-faq-group-detail']) {
        const buttons = components[name].template.match(/<mt-button\b[^>]*>/g);
        assert(buttons.every(button => button.includes('size="large"')));
    }
    const queries = [];
    const instance = { ...def.data(), repository: { search: async criteria => { queries.push(criteria); return { total: 2 }; } } };
    for (const [name, method] of Object.entries(def.methods)) { instance[name] = method.bind(instance); }
    instance.term = 'Versand';
    instance.page = 4;
    await instance.onGroupFilterChange('group-a');
    assert.equal(queries.at(-1).page, 1);
    assert.equal(queries.at(-1).term, 'Versand');
    assert.equal(queries.at(-1).filters[0].field, 'groupId');
    assert.equal(queries.at(-1).filters[0].value, 'group-a');
    instance.onPageChange({ page: 2, limit: 25 });
    assert.equal(queries.at(-1).page, 2);
    assert.equal(queries.at(-1).filters[0].value, 'group-a');
    instance.onSearch('Neu');
    assert.equal(queries.at(-1).page, 1);
    assert.equal(queries.at(-1).term, 'Neu');
    await instance.onGroupFilterChange(null);
    assert.equal(queries.at(-1).filters.length, 0);
    assert.equal(instance.total, 2);
    const count = queries.length;
    instance.onSearchInput('Ver');
    instance.onSearchInput('Versand');
    await new Promise(resolve => setTimeout(resolve, 350));
    assert.equal(queries.length, count + 1, 'Typing is debounced');
    assert.equal(queries.at(-1).term, 'Versand');
    instance.onSearchInput('  Lieferung  ');
    await instance.onSearch(instance.term);
    assert.equal(queries.at(-1).term, 'Lieferung', 'Enter searches immediately and trims');
    const afterEnter = queries.length;
    await new Promise(resolve => setTimeout(resolve, 350));
    assert.equal(queries.length, afterEnter, 'Enter cancels pending timer');
    await instance.onGroupFilterChange('group-a');
    instance.onSearchInput('');
    await new Promise(resolve => setTimeout(resolve, 350));
    assert.equal(queries.at(-1).term, '');
    assert.equal(queries.at(-1).filters[0].value, 'group-a', 'Clearing search preserves group');
    instance.onSearchInput('pending');
    const beforeUnmount = queries.length;
    def.beforeUnmount.call(instance);
    await new Promise(resolve => setTimeout(resolve, 350));
    assert.equal(queries.length, beforeUnmount, 'Unmount cancels pending search');
    const pending = [];
    instance.repository.search = () => new Promise(resolve => pending.push(resolve));
    const oldRequest = instance.onGroupFilterChange('old');
    const newRequest = instance.onGroupFilterChange('new');
    pending[1]({ total: 7 }); await newRequest;
    pending[0]({ total: 99 }); await oldRequest;
    assert.equal(instance.total, 7);
    assert.equal(instance.isLoading, false);
    console.log('PASS large buttons, dedicated search, debounce, Enter, clearing, lifecycle, group filter, pagination and out-of-order responses');
})().catch(error => { console.error(error); process.exitCode = 1; });
