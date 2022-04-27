// Vendors
global.SignaturePad = require('signature_pad')
global.moment = require('moment/moment')
global.Quagga = require('quagga')

import './countdown-timer'
import './utilities'
import './broto'

// Execute theme JavaScript
window[config.theme]?.()

// Disable submit buttons after a form has been submitted so
// spamming the button does not result in multiple requests
let formList = Array.from(document.getElementsByTagName("form"))
formList.forEach(form => form.addEventListener('submit', preventSubmitBounce, { once: true }))

// Get online Discord users
const discordOnlineCount = document.getElementById("discord__online")
if (discordOnlineCount) {
    get("https://discordapp.com/api/guilds/" + config.discord_server_id + "/widget.json")
        .then(data => { discordOnlineCount.innerHTML = data.presence_count })
        .catch(_ => { discordOnlineCount.innerHTML = "..." })
}

// Enables tooltips elements
import { Tooltip } from 'bootstrap'
const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
if (tooltipTriggerList.length) tooltipTriggerList.forEach(el => new Tooltip(el, {container: el.parentNode, boundary: document.body}))

// Enable popover elements
import { Popover } from 'bootstrap'
const popoverTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="popover"]'))
if (popoverTriggerList.length) popoverTriggerList.forEach(el => new Popover(el))

// Enable modal elements
import { Modal } from 'bootstrap'
let modalList = Array.from(document.getElementsByClassName('modal'))
window.modals = {}
if (modalList.length) {
    modalList.forEach(el => {
        window.modals[el.id] = Modal.getOrCreateInstance(el)
    })
}

// Enable custom file input elements
const customFileInputList = Array.from(document.getElementsByClassName('custom-file-input'))
if (customFileInputList.length) {
    customFileInputList.forEach(el => {
        el.addEventListener('change', _ => {
            let fileName = this.value.split('\\').pop()
            let label = this.nextElementSibling
            label.classList.add('selected')
            label.innerHTML = fileName
        })
    })
}

// Enable Swiper with default settings
import Swiper, { Autoplay, Navigation } from 'swiper'
import 'swiper/css'
import 'swiper/css/autoplay'
import 'swiper/css/navigation'
if(document.querySelectorAll('.swiper').length) {
    window.swiper = new Swiper('.swiper', {
        modules: [Autoplay, Navigation],
        loop: config.company_count > 2,
        slidesPerView: config.company_count > 1 ? 2 : 1,
        spaceBetween: 10,
        watchOverflow: false,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false
        },
        breakpoints: {
            1200: {
                slidesPerView: (config.company_count > 4 ? 4 : config.company_count),
                spaceBetween: 50,
            }
        }
    })
}

// Enable EasyMDE markdown fields
import EasyMDE from 'easymde'
import 'easymde/dist/easymde.min.css'
const markdownFieldList = Array.from(document.getElementsByClassName('markdownfield'))
if (markdownFieldList.length) {
    window.easyMDEFields = {}
    markdownFieldList.forEach(el => {
        window.easyMDEFields[el.id] =
            new EasyMDE({
                element: el,
                toolbar: ['bold', 'italic', '|', 'unordered-list', 'ordered-list', '|', 'image', 'link', 'quote', 'table', 'code', '|', 'preview'],
                autoDownloadFontAwesome: false
            })
    })
    const statusbarList = Array.from(document.querySelectorAll('.editor-statusbar'))
    const link = '<a class="md-ref float-start" target="_blank" href="https://www.markdownguide.org/basic-syntax/">markdown syntax</a>'
    statusbarList.forEach(el => el.innerHTML = link + el.innerHTML)
}

// Enable FontAwesome icon pickers
import Iconpicker from 'codethereal-iconpicker'
const iconPickerList = Array.from(document.getElementsByClassName('iconpicker-wrapper'))
window.iconPickers = {}
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
            }`
    })
    .then(data => {
        const icons = data.data.release.icons.reduce((collection, icon) => {
            const styles = icon.membership.free
            for (const key in styles) { collection.push(`fa${styles[key].charAt(0)} fa-${icon.id}`) }
            return collection
        }, [])
        iconPickerList.forEach(el => {
            const iconpicker = el.querySelector('.iconpicker')
            window.iconPickers[el.id] = new Iconpicker(iconpicker, {
                icons: icons,
                defaultValue: iconpicker.value,
                showSelectedIn: el.querySelector('.selected-icon'),
            })
        })
        console.log(`Icon-picker initialized (FontAwesome v${data.data.release.version}, ${icons.length} icons)`)
    })
}

// Enables fancy scrolling effect
const navbar = document.getElementById('nav')
if (navbar) {
    const navbarHeight = 100
    let currentScroll = 0
    window.addEventListener('wheel', _ => {
        currentScroll = document.documentElement.scrollTop
        if (currentScroll > navbarHeight) navbar.classList.add('navbar-scroll')
        else navbar.classList.remove('navbar-scroll')
    })
}

// Scroll to top of collapse on show.
// https://stackoverflow.com/a/44303674/7316014
// https://stackoverflow.com/a/18673641/14133333
const collapseList = Array.from(document.querySelectorAll('.collapse:not(#navbar)'))
collapseList.map(el => {
    el.addEventListener('shown.bs.collapse', e => {
        let card = e.target.closest('.card').getBoundingClientRect()
        window.scrollTo(0, card.top + window.scrollY - 60)
    })
})

// Enable search autocomplete fields
import SearchField from './search-field'
const userSearchList = Array.from(document.querySelectorAll('.user-search'))
userSearchList.forEach(el => new SearchField(el, config.routes.api_search_user, {
    optionTemplate: (el, item) => {
        el.className = item.is_member ? '' : 'text-muted'
        el.innerHTML = `#${item.id} ${item.name}`
    }
}))

const eventSearchList = Array.from(document.querySelectorAll('.event-search'))
eventSearchList.forEach(el => new SearchField(el, config.routes.api_search_event, {
    optionTemplate: (el, item) => {
        el.className = item.is_future ? '' : 'text-muted'
        el.innerHTML = `${item.title} (${item.formatted_date.simple})`
    },
    selectedTemplate: item => item.title,
    sorter: (a, b) => {
        if (a.start < b.start) return 1
        else if (a.start > b.start) return -1
        else return 0
    }
}))

const productSearchList = Array.from(document.querySelectorAll('.product-search'))
productSearchList.forEach(el => new SearchField(el, config.routes.api_search_product, {
    optionTemplate: (el, item) => {
        el.className = item.is_visible ? '' : 'text-muted'
        el.innerHTML = `${item.name} (€${item.price.toFixed(2)}; ${item.stock} in stock)`
    },
    selectedTemplate: item => item.name + (el.multiple ? ` (€${item.price.toFixed(2)})` : ''),
    sorter: (a, b) => {
        if (a.is_visible === 0 && b.is_visible === 1) return 1
        else if (a.is_visible === 1 && b.is_visible === 0) return -1
        else return 0
    }
}))

const committeeSearchList = Array.from(document.querySelectorAll('.committee-search'))
committeeSearchList.forEach(el => new SearchField(el, config.routes.api_search_committee))

// Enable countdown timers
import CountdownTimer from "./countdown-timer"
const countdownList = Array.from(document.querySelectorAll(".proto-countdown"))
countdownList.forEach(el => new CountdownTimer(el))

// Matomo Analytics
const _paq = _paq || [];
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(_ => {
    let u = '//'+config.analytics_url+'/';
    _paq.push(['setTrackerUrl', u + 'piwik.php']);
    _paq.push(['setSiteId', '1']);
    let d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
    g.type = 'text/javascript';
    g.async = true;
    g.defer = true;
    g.src = u + 'piwik.js';
    s.parentNode.insertBefore(g, s);
})()
