window.global ||= window

// Find CSRF token in page meta tags
const token = document.head.querySelector('meta[name="csrf-token"]').content
if (token === undefined) console.error('X-CSRF token could not be found!')

// Global wrapper methods for the native fetch api
function request(method, url, params, options) {
    options.method = method
    options.headers = options.headers ?? {}
    if (method === 'GET') {
        url += `?${new URLSearchParams(params)}`
    } else {
        if (!(params instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json'
            params = JSON.stringify(params)
        }
        options.body = params
        options.headers['X-Requested-With'] = 'XMLHttpRequest'
        options.headers['X-CSRF-TOKEN'] = token
        options.credentials = 'same-origin'
    }
    const result = fetch(url, options)
    return result.then((response) => {
        if (!response.ok) throw response
        if (options.parse !== undefined && options.parse === false)
            return response
        return response.json()
    })
}

global.get = (url, params = {}, options = {}) =>
    request('GET', url, params, options)
global.post = (url, params = {}, options = {}) =>
    request('POST', url, params, options)

// Method to debounce function calls
global.debounce = (callback, timeout = 300) => {
    let timer
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout((_) => {
            callback.apply(this, args)
        }, timeout)
    }
}

global.preventSubmitBounce = (e) => (e.target.onsubmit = (_) => false)

import { Alert } from 'bootstrap'
const alertWrapper = document.getElementById('alert-wrapper')
let currentAlert = null

const createAlertElement = (type, message) => {
    const el = document.createElement('div')
    el.classList.add(
        'fade',
        'show',
        'alert',
        `alert-${type}`,
        'alert-dismissible'
    )
    el.role = 'alert'
    el.innerHTML = `<div>${message}</div><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`
    return el
}
global.flash = (type, message, duration = 2000) => {
    if (currentAlert) currentAlert.close()
    let closeTimeout
    setTimeout((_) => {
        clearTimeout(closeTimeout)
        let el = createAlertElement(type, message)
        alertWrapper.append(el)
        currentAlert = new Alert(el)
        closeTimeout = setTimeout(() => {
            if (currentAlert) currentAlert.close()
            currentAlert = null
        }, duration)
    }, 200)
}
