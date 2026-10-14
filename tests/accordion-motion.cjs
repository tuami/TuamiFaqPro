// Usage: node tests/accordion-motion.cjs <sass-module> <playwright-module>
const assert = require('node:assert/strict');
const path = require('node:path');
const sass = require(process.argv[2]);
const { chromium } = require(process.argv[3]);
const css = sass.compile(path.join(__dirname, '../src/Resources/app/storefront/src/scss/base.scss')).css;
(async () => {
    const browser = await chromium.launch({ channel: 'msedge', headless: true });
    try {
        const page = await browser.newPage();
        for (const width of [390, 1280]) {
            await page.setViewportSize({ width, height: 900 });
            for (const layout of ['cards', 'dividers']) {
                await page.setContent(`<style>${css}</style><section class="tuami-faq-pro tuami-faq-pro--layout-${layout}"><div class="tuami-faq-pro__items"><details class="tuami-faq-pro__item"><summary class="tuami-faq-pro__question"><span>Wie funktioniert die Lieferung?</span><span class="tuami-faq-pro__marker" aria-hidden="true"></span></summary><div class="tuami-faq-pro__answer"><p>${'Eine lange Antwort mit dynamischer Höhe. '.repeat(35)}</p><a href="#test">Mehr erfahren</a></div></details></div></section>`);
                const question = page.locator('summary');
                const height = () => page.locator('details').evaluate(el => el.getBoundingClientRect().height);
                const closed = await height();
                await question.focus();
                await page.keyboard.press('Enter');
                await page.waitForTimeout(90);
                const during = await height();
                await page.waitForTimeout(300);
                const opened = await height();
                assert(during > closed && during < opened, 'Opening must interpolate');
                assert.equal(await page.locator('.tuami-faq-pro__marker').evaluate(el => getComputedStyle(el, '::after').opacity), '0');
                await page.keyboard.press('Space');
                await page.waitForTimeout(70);
                const closing = await height();
                assert(closing > closed && closing < opened, 'Closing must interpolate');
                await page.waitForTimeout(250);
                assert(Math.abs(await height() - closed) < 1);
                await question.click();
                await page.waitForTimeout(50);
                await question.click();
                await page.waitForTimeout(350);
                assert(Math.abs(await height() - closed) < 1, 'Rapid reversal must settle closed');
                await page.emulateMedia({ reducedMotion: 'reduce' });
                await question.click();
                assert.equal(await page.locator('details').evaluate(el => getComputedStyle(el, '::details-content').transitionDuration), '0s');
                assert(Math.abs(await height() - opened) < 1, 'Reduced motion must open immediately');
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                assert(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth));
                console.log(`PASS ${layout}, ${width}px: keyboard, opening, closing, reversal, marker, reduced motion, overflow`);
            }
        }
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
