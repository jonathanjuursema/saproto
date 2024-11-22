window.global ||= window;
// Vendors
import SignaturePad from 'signature_pad';

global.SignaturePad = SignaturePad;
import moment from 'moment/moment.js';

global.moment = moment;

//Temporarily disabled due to an import error: Uncaught ReferenceError: glMatrixArrayType is not defined
// import quagga from 'quagga';
// global.Quagga = quagga;

import './countdown-timer';
import './utilities';
import './broto';
import './nightMode';
// Execute theme JavaScript
if ((new Date().getMonth() + 1) !== 12) {
    window[config.theme]?.();
}

// Disable submit buttons after a form has been submitted so
// spamming the button does not result in multiple requests
let formList = Array.from(document.getElementsByTagName('form'));
formList.forEach(form => form.addEventListener('submit', preventSubmitBounce, { once: true }));

// Get online Discord users
const discordOnlineCount = document.getElementById('discord__online');
if (discordOnlineCount) {
    get('https://discordapp.com/api/guilds/' + config.discord_server_id + '/widget.json')
        .then(data => {
            discordOnlineCount.innerHTML = data.presence_count;
        })
        .catch(_ => {
            discordOnlineCount.innerHTML = '...';
        });
}

// Enables tooltips elements
import { Tooltip } from 'bootstrap';

const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
window.tooltips = {};
if (tooltipTriggerList.length) {
    tooltipTriggerList.forEach(el => {
        window.tooltips[el.id] = Tooltip.getOrCreateInstance(el, { container: el.parentNode, boundary: document.body });
    });
}

// Enable popover elements
import { Popover } from 'bootstrap';

const popoverTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="popover"]'));
if (popoverTriggerList.length) popoverTriggerList.forEach(el => new Popover(el));

// Enable modal elements
import { Modal } from 'bootstrap';

let modalList = Array.from(document.getElementsByClassName('modal'));
window.modals = {};
if (modalList.length) {
    modalList.forEach(el => {
        window.modals[el.id] = Modal.getOrCreateInstance(el);
    });
}

// Enable custom file input elements
const customFileInputList = Array.from(document.getElementsByClassName('custom-file-input'));
if (customFileInputList.length) {
    customFileInputList.forEach(el => {
        el.addEventListener('change', _ => {
            let fileName = this.value.split('\\').pop();
            let label = this.nextElementSibling;
            label.classList.add('selected');
            label.innerHTML = fileName;
        });
    });
}

// Enable Swiper with default settings
import Swiper, { Autoplay, Navigation } from 'swiper';
import 'swiper/css';
import 'swiper/css/autoplay';
import 'swiper/css/navigation';

if (document.querySelectorAll('.swiper').length) {
    window.swiper = new Swiper('.swiper', {
        modules: [Autoplay, Navigation],
        loop: config.company_count > 2,
        slidesPerView: config.company_count > 1 ? 2 : 1,
        spaceBetween: 10,
        watchOverflow: false,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        breakpoints: {
            1200: {
                slidesPerView: (config.company_count > 4 ? 4 : config.company_count),
                spaceBetween: 50,
            },
        },
    });
}

// Enable EasyMDE markdown fields
import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

const markdownFieldList = Array.from(document.getElementsByClassName('markdownfield'));
if (markdownFieldList.length) {
    window.easyMDEFields = {};
    markdownFieldList.forEach(el => {
        window.easyMDEFields[el.id] =
            new EasyMDE({
                element: el,
                toolbar: ['bold', 'italic', 'strikethrough', '|', 'table', 'unordered-list', 'ordered-list', '|', 'image', 'link', 'quote', 'code', '|', 'preview', 'guide'],
                toolbarButtonClassPrefix: 'mde-',
                autoDownloadFontAwesome: false,
            });
    });
}

// Enable FontAwesome icon pickers
import Iconpicker from 'codethereal-iconpicker';

const iconPickerList = Array.from(document.getElementsByClassName('iconpicker-wrapper'));
window.iconPickers = {};
if (iconPickerList.length) {
    // Get available icons from fontawesome GraphQL api
    post('https://api.fontawesome.com/', {
        query:
            `{
              release(version: "latest") {
                version
                icons {
                  id
                  membership { free }
                }
              }
            }`,
    })
        .then(data => {
            const icons = data.data.release.icons.reduce((collection, icon) => {
                const styles = icon.membership.free;
                for (const key in styles) {
                    collection.push(`fa${styles[key].charAt(0)} fa-${icon.id}`);
                }
                return collection;
            }, []);
            iconPickerList.forEach(el => {
                const iconpicker = el.querySelector('.iconpicker');
                window.iconPickers[el.id] = new Iconpicker(iconpicker, {
                    icons: icons,
                    defaultValue: iconpicker.value,
                    showSelectedIn: el.querySelector('.selected-icon'),
                });
            });
            console.log(`Icon-picker initialized (FontAwesome v${data.data.release.version}, ${icons.length} icons)`);
        }).catch(err => {
        console.log('Error retrieving icons for icon pickers: ', err);
    });
}

// Enables fancy scrolling effect
const navbar = document.getElementById('nav');
if (navbar) {
    const navbarHeight = 100;
    let currentScroll = 0;
    window.addEventListener('wheel', _ => {
        currentScroll = document.documentElement.scrollTop;
        if (currentScroll > navbarHeight) navbar.classList.add('navbar-scroll');
        else navbar.classList.remove('navbar-scroll');
    });
}

// Scroll to top of collapse on show.
// https://stackoverflow.com/a/44303674/7316014
// https://stackoverflow.com/a/18673641/14133333
const collapseList = Array.from(document.querySelectorAll('.collapse:not(#navbar)'));
collapseList.map(el => {
    el.addEventListener('shown.bs.collapse', e => {
        let card = e.target.closest('.card').getBoundingClientRect();
        window.scrollTo(0, card.top + window.scrollY - 60);
    });
});

// Enable search autocomplete fields
import SearchField from './search-field';

const userSearchList = Array.from(document.querySelectorAll('.user-search'));
userSearchList.forEach(el => new SearchField(el, config.routes.api_search_user, {
    optionTemplate: (el, item) => {
        el.className = item.is_member ? '' : 'text-muted';
        el.innerHTML = `#${item.id} ${item.name}`;
    },
}));

const eventSearchList = Array.from(document.querySelectorAll('.event-search'));
eventSearchList.forEach(el => new SearchField(el, config.routes.api_search_event, {
    optionTemplate: (el, item) => {
        el.className = item.is_future ? '' : 'text-muted';
        el.innerHTML = `${item.title} (${item.formatted_date.simple})`;
    },
    selectedTemplate: item => item.title,
    sorter: (a, b) => {
        if (a.start < b.start) return 1;
        else if (a.start > b.start) return -1;
        else return 0;
    },
}));

const productSearchList = Array.from(document.querySelectorAll('.product-search'));
productSearchList.forEach(el => new SearchField(el, config.routes.api_search_product, {
    optionTemplate: (el, item) => {
        el.className = item.is_visible ? '' : 'text-muted';
        el.innerHTML = `${item.name} (€${item.price.toFixed(2)}; ${item.stock} in stock)`;
    },
    selectedTemplate: item => item.name + (el.multiple ? ` (€${item.price.toFixed(2)})` : ''),
    sorter: (a, b) => {
        if (a.is_visible === 0 && b.is_visible === 1) return 1;
        else if (a.is_visible === 1 && b.is_visible === 0) return -1;
        else return 0;
    },
}));

const committeeSearchList = Array.from(document.querySelectorAll('.committee-search'));
committeeSearchList.forEach(el => new SearchField(el, config.routes.api_search_committee));

const achievementSearchList = Array.from(document.querySelectorAll('.achievement-search'));
achievementSearchList.forEach(el => new SearchField(el, config.routes.api_search_achievement, {
    optionTemplate: (el, item) => {
        el.innerHTML = `#${item.id} ${item.name}`;
    },
}));

// Enable countdown timers
global.timerList = [];
import CountdownTimer from './countdown-timer';

const countdownList = Array.from(document.querySelectorAll('.proto-countdown'));
countdownList.forEach((el) => {
    timerList.push(new CountdownTimer(el));
});

// Shift select
import shiftSelect from './shift-select';

document.querySelectorAll('.shift-select').forEach(el => el.hasAttribute('data-name') ? shiftSelect(el, el.getAttribute('data-name')) : null);

//Lazy load background images
if ('IntersectionObserver' in window) {
    document.addEventListener('DOMContentLoaded', function() {
        function handleIntersection(entries) {
            entries.map((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.backgroundPosition = 'center';
                    entry.target.style.backgroundRepeat = 'no-repeat';
                    entry.target.style.backgroundSize = 'cover';
                    entry.target.style.backgroundImage = 'url(\'' + entry.target.dataset.bgimage + '\')';
                    observer.unobserve(entry.target);
                }
            });
        }

        const headers = document.querySelectorAll('div[data-bgimage]:not([data-bgimage=""])');
        const observer = new IntersectionObserver(
            handleIntersection,
            { rootMargin: '200px' },
        );
        headers.forEach(header => observer.observe(header));
    });
} else {
    // No interaction support? Load all background images automatically
    const headers = document.querySelectorAll('.bg-img');
    headers.forEach(header => {
        header.style.backgroundImage = 'url(\'' + header.dataset.bgimage + '\')';
    });
}

// Matomo Analytics
const _paq = window._paq = window._paq || [];
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
    let u = '//' + config.analytics_url + '/';
    _paq.push(['setTrackerUrl', u + 'matomo.php']);
    _paq.push(['setSiteId', '1']);
    const d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
    g.type = 'text/javascript';
    g.async = true;
    g.src = u + 'matomo.js';
    s.parentNode.insertBefore(g, s);
})();
