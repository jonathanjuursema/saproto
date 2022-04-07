<!DOCTYPE html>
<html lang='en'>
    <head>
        <title>OmNomCom Store</title>

        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='initial-scale=1, maximum-scale=1, user-scalable=no'/>
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <link rel='shortcut icon' href='{{ asset('images/favicons/favicon'.mt_rand(1, 4).'.png') }}'/>
        <link rel='stylesheet' href='{{ mix('/css/application-dark.css') }}'>

        <style>
            * { box-sizing: border-box; }

            html, body {
                position: absolute;
                font-family: Lato, sans-serif;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                margin: 0;
                padding: 0;
                background-color: #555;
            }

            body {
                /**
                    Special OmNomCom monsters for special occasions. Priority is from top to bottom.
                    The first date is inclusive and should be the day when the monster should be appearing.
                    The second date is exclusive and should thus be the first day the monster should no longer be there.
                 */
                @php($bg_image = 'images/omnomcom/cookiemonster.png')
                @foreach(config('omnomcom.cookiemonsters') as $cookiemonster)
                    @if(date('U') > strtotime($cookiemonster->start) && date('U') < strtotime($cookiemonster->end))
                       @php($bg_image = "images/omnomcom/cookiemonster_seasonal/$cookiemonster->name.png")
                       @break
                    @endif
                @endforeach

                background-image: url('{{ asset($bg_image) }}');
                background-position: center 100%;
                background-repeat: no-repeat;
            }
        </style>
    </head>

    <body>

        <div id="display-fullscreen" class="modal" tabindex="-1">
            <div class="modal-dialog-centered mx-auto">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title w-100">Please display OmNomCom in fullscreen!</h5>
                    </div>
                    <div class="modal-body d-flex justify-content-center pb-0">
                        <img src="{{ asset('images/omnomcom/cookiemonster_seasonal/pixels.png') }}" alt="cookie monster">
                    </div>
                </div>
            </div>
        </div>

        <div id="omnomcom">

            <div class='d-flex ps-2'>
                @include('omnomcom.store.includes.categories')

                @include('omnomcom.store.includes.product_overview')
            </div>

            @include('omnomcom.store.includes.controls')

        </div>

        @include('omnomcom.store.includes.modals')

        @include('website.layouts.assets.javascripts')

        <script type='text/javascript' nonce='{{ csp_nonce() }}'>
            let actionStatus
            let purchaseProcessing
            let cartOverflowVisible = true
            let cartOverflowFirstClosed = false
            let cartOverflowMinimum = 4
            const server = establishNfcConnection()

            let images = []
            let cart = []
            let stock = []
            let price = []

            initializeOmNomCom()

            async function initializeOmNomCom() {
                await get(config.routes.api_omnomcom_stock, {store: "{{ $store_slug }}"})
                        .then(data => {
                            data.forEach(product => {
                                const id = product.id
                                images[id] = product.image_url ?? ''
                                cart[id] = 0
                                stock[id] = product.stock
                                price[id] = product.price
                            })
                        })

                const categoryBtnList = Array.from(document.getElementsByClassName('btn-category'))
                categoryBtnList.forEach(el => {
                    el.addEventListener('click', _ => {
                        Array.from(document.querySelectorAll('#category-nav > .active')).forEach(el => el.classList.remove('active'))
                        el.classList.add('active')
                        const categoryViewList = Array.from(document.getElementsByClassName('category-view'))
                        const id = el.getAttribute('data-id')
                        categoryViewList.forEach(el => {
                            if (el.getAttribute('data-id') !== id) el.classList.add('inactive')
                            else el.classList.remove('inactive')
                        })
                    })
                })

                const productList = Array.from(document.getElementsByClassName('product'))
                productList.forEach(el => {
                    el.addEventListener('click', _ => {
                        if (el.classList.contains('random')) {
                            if (el.getAttribute('data-stock') > 0) {
                                let data = el.getAttribute('data-list').split(',')
                                let selected = Math.floor(Math.random() * data.length)
                                if (stock[data[selected]] < 1)
                                    return el.dispatchEvent(new Event('click'))
                                const product = document.querySelector(`[data-id="${data[selected]}"]`)
                                product.dispatchEvent(new Event('click'))
                            } else {
                                modals['outofstock-modal'].show()
                            }
                        } else {
                            const id = el.getAttribute('data-id')
                            const s = stock[id]
                            if (s <= 0) {
                                modals['outofstock-modal'].show()
                            } else {
                                cart[id]++
                                stock[id]--
                                update('add')
                            }
                        }
                    })
                })

                document.getElementById('cart').addEventListener('click', e => {
                    if(e.target.classList.contains('cart-product')) {
                        const id = e.target.getAttribute('data-id')
                        cart[id]--
                        stock[id]++
                        update('remove')
                    } else if (e.target.id === 'cart-overflow') {
                        if (cartOverflowVisible === true) {
                            cartOverflowVisible = false
                            Array.from(document.getElementsByClassName('cart-product')).forEach(el => el.style.left = '0')
                        } else {
                            cartOverflowVisible = true
                            Array.from(document.getElementsByClassName('cart-product')).forEach((el, i) => el.style.left = `${(i + 1) * 110}px`)
                        }
                    }
                })

                /* Modal handlers */
                document.getElementById('rfid').addEventListener('click', _ => {
                    actionStatus = 'rfid'
                    modals['rfid-modal'].show()
                    document.querySelector('#rfid-modal .modal-body').innerHTML = '<h1>Please present your RFID card</h1>'
                })

                document.getElementById('purchase').addEventListener('click', _ => purchaseInitiate(
                    false, false,
                    'Payment of €' + document.getElementById('total').innerHTML + ' for purchases in Omnomcom',
                    'Complete purchase using your <i class="fas fa-cookie-bite"></i> OmNomCom bill.'
                ))

                const cashCompleted = document.getElementById('purchase-cash-initiate')
                if(cashCompleted) {
                    cashCompleted.addEventListener('click', _ => purchaseInitiate(
                        true, false,
                        'Cashier payment for cash purchases in Omnomcom',
                        'Complete purchase as cashier, payed with cash.'
                    ))
                }

                const cardCompleted = document.getElementById('purchase-bank-card-initiate')
                if(cardCompleted) {
                    cardCompleted.addEventListener('click', _ => purchaseInitiate(
                        false, true,
                        'Cashier payment for bank card purchases in Omnomcom',
                        'Complete purchase as cashier, payed with bank card.'
                    ))
                }
            }

            function anythingInCart() {
                for (let id in cart) if (cart[id] > 0) return true
                return false
            }

            function cart_to_object(cart) {
                let object_cart = {}
                for (let product in cart)
                    if (cart[product] > 0) object_cart[product] = cart[product]
                return object_cart
            }

            function purchaseInitiate(payedCash, payedCard, message, title) {
                modals['purchase-modal'].show()
                if (!document.querySelector('#purchase-modal .qrAuth img')) {
                    doQrAuth(
                        document.querySelector('#purchase-modal .qrAuth'),
                        message,
                        purchase
                    )
                }
                actionStatus = 'purchase'
                document.querySelector('#purchase-modal .modal-status').innerHTML = '<span class="modal-status">Authenticate using the QR code above.</span>'
                document.querySelector('#purchase-modal h1').innerHTML = title
                if (payedCard) document.getElementById('purchase-bank-card').classList.add('modal-toggle-true')
            }

            function purchase(credentials, type) {
                if (purchaseProcessing != null) return
                else purchaseProcessing = true

                post(
                    '{{ route('omnomcom::store::buy', ['store' => $store_slug]) }}', {
                        credential_type: type,
                        credentials: credentials,
                        cash: {{ ($store->cash_allowed ? 'true' : 'false') }},
                        bank_card: {{ ($store->bank_card_allowed ? 'true' : 'false') }},
                        cart: cart_to_object(cart)
                    })
                    .then(data => {
                        if (data.status === 'OK') {
                            finishPurchase(data.message)
                        } else {
                            purchaseInitiate(
                                false, false,
                                'Payment of €' + document.getElementById('total').innerHTML + ' for purchases in Omnomcom',
                                'Complete purchase using your <i class="fas fa-cookie-bite"></i> OmNomCom bill.'
                            )
                            modals['purchase-modal'].show()
                            document.querySelector('#purchase-modal .modal-status').innerHTML = `<span class="badge bg-danger text-white">${data.message}</span>`
                            purchaseProcessing = null
                        }
                    })
                    .catch(err => {
                        const status = document.querySelector('#purchase-modal .modal-status')
                        purchaseProcessing = null
                        if (err.status === 503) status.innerHTML = 'The website is currently in maintenance. Please try again in 30 seconds.'
                        else status.innerHTML = 'There is something wrong with the website, call someone to help!'
                    })
            }

            function doQrAuth(element, description, onComplete) {
                let authToken = null
                post('{{ route('qr::generate') }}', { description: description })
                    .then(data => {
                        const qrImg = "{{ route('qr::code', '') }}" + '/' + data.qr_token
                        const qrLink = "{{ route('qr::dialog', '') }}" + '/' + data.qr_token
                        element.innerHTML = 'Scan this QR code<br><br><img alt="QR code" class="bg-white p-2" src="' +  qrImg +
                            '" width="200px" height="200px"><br><br>or go to<br><strong>' + qrLink + '</strong>'
                        authToken = data.auth_token
                        const qrAuthInterval = setInterval(_ => {
                            if (actionStatus == null) return clearInterval(qrAuthInterval)
                            get('{{ route('qr::approved') }}', { code: authToken })
                                .then(approved => {
                                    if (approved) {
                                        element.innerHTML = 'Successfully authenticated :)'
                                        clearInterval(qrAuthInterval)
                                        onComplete(authToken, 'qr')
                                    }
                                })
                        }, 1000)
                    })
            }

            function finishPurchase(display_message = null) {
                Object.values(modals).forEach(modal => modal.hide())
                if (display_message) document.getElementById('finished-modal-message').innerHTML = `<span>${display_message}</span>`
                document.getElementById('finished-modal-continue').addEventListener('click', _ => window.location.reload())
                modals['finished-modal'].show()
                const movie = document.getElementById('purchase-movie')
                movie.addEventListener('ended', _ => window.location.reload())
                movie.play()
            }

            function createCartElement(index, id, amount, image) {
                return `<div class="cart-product stretched-link" data-id="${id}" style="left: ${cartOverflowVisible * index * 110}px">` +
                            '<div class="cart-product-image">' +
                                `<div class="cart-product-image-inner" style="background-image: url(${image});"></div>` +
                            '</div>' +
                            `<div class="cart-product-count">${amount}x</div>` +
                        '</div>'
            }

            async function update(context=null) {
                const cartEl = document.getElementById('cart')

                Array.from(document.getElementsByClassName('cart-product')).forEach(el => el.parentNode.removeChild(el))

                let uniqueItems = 0
                let totalItems = 0
                let orderTotal = 0

                await cart.forEach((amount, id) => {
                    if (amount === 0) return
                    uniqueItems += 1
                    totalItems += amount
                    orderTotal += price[id] * cart[id]
                    cartEl.innerHTML += createCartElement(uniqueItems, id, amount, images[id])
                })

                document.querySelector('#cart-overflow .cart-product-count').innerHTML = totalItems + " x"
                if (uniqueItems === cartOverflowMinimum && !cartOverflowFirstClosed && context !== 'remove') {
                    cartOverflowVisible = false
                    cartOverflowFirstClosed = true
                    Array.from(document.getElementsByClassName('cart-product')).forEach(el => el.style.left = '0')
                }

                stock.forEach((amount, id) => {
                    if (amount < 1000 )
                        document.querySelector(`[data-id="${id}"] .product-stock`).innerHTML = amount + ' x'
                })

                const purchaseEls = Array.from(document.getElementsByClassName('purchase-button'))
                if (anythingInCart()) purchaseEls.forEach(el => el.disabled = false)
                else purchaseEls.forEach(el => el.disabled = true)
                document.getElementById('total').innerHTML = orderTotal.toFixed(2)

                let lists = document.getElementsByClassName('random')
                for (let i = 0; i < lists.length; i++) {
                    let count = 0
                    let products = Array.from(lists[i].parentNode.children)
                    products.splice(products.indexOf(lists[i]), 1)
                    products.forEach(el => { if (stock[el.getAttribute('data-id')] > 0) count++ })
                    lists[i].setAttribute('data-stock', count.toString())
                }
            }

            function establishNfcConnection() {
                const status = document.getElementById('status')
                let server

                try {
                    status.classList.add('inactive')
                    status.innerHTML = 'RFID Service: Connecting...'
                    server = new WebSocket('ws://localhost:3000', 'nfc')
                } catch (error) {
                    if (error.message.split('/\s+/').contains('insecure')) {
                        status.classList.add('inactive')
                        status.innerHTML = 'RFID Service: Not Supported'
                    } else {
                        console.error('Unexpected error: ' + error.message)
                    }
                }

                server.onopen = _ => {
                    status.classList.remove('inactive')
                    status.innerHTML = 'RFID Service: Connected'
                }

                server.onclose = _ => {
                    status.classList.add('inactive')
                    status.innerHTML = 'RFID Service: Disconnected'
                    setTimeout(establishNfcConnection, 5000)
                }

                server.onmessage = raw => {
                    let data = JSON.parse(raw.data).uid
                    console.log('Received card input: ' + data)

                    if (data === '') {
                        Object.values(modals).forEach(el => el.hide())
                        modals['badcard-modal'].show()
                        actionStatus = 'badcard'
                        return
                    }

                    modals['badcard-modal'].hide()

                    if (actionStatus === 'rfid') {
                        const rfidLinkCard = data
                        document.querySelector('#rfid-modal .modal-body').innerHTML =
                            '<div class="qrAuth">Loading QR authentication...</div>' +
                            '<hr>' +
                            '<span class="modal-status">Authenticate using the QR code above to link RFID card.</span>'
                        doQrAuth(
                            document.querySelector('#rfid-modal .qrAuth'),
                            'Link RFID card to account',
                            (auth_token, credentialtype) => {
                                post(
                                    '{{ route('omnomcom::store::rfidadd') }}',
                                    {
                                        card: rfidLinkCard,
                                        credentialtype: credentialtype,
                                        credentials: auth_token,
                                    }
                                )
                                    .then(data => document.querySelector('#rfid-modal .modal-status').innerHTML =
                                        '<span class="' + (data.ok ? 'primary' : 'danger') + '">' + data.text + '</span>')
                            }
                        )
                    } else if (actionStatus === 'purchase') {
                        purchase(data, 'card')
                    } else {
                        if (anythingInCart()) purchase(data, 'card')
                        else modals['emptycart-modal'].show()
                    }
                }

                return server
            }

            /* Handle idle timeout */
            let idleTime = 0
            let idleWarning = false

            setInterval(_ => {
                idleTime = idleTime + 1

                if (idleTime > 60 && !idleWarning) {
                    if (anythingInCart() && Array.from(modals).every(el => el._isShown())) {
                        idleWarning = true
                        Object.values(modals).forEach(el => el.hide())
                        modals['idlewarning-modal'].show()

                        setTimeout(_ => { if (idleWarning) window.location.reload() }, 10000)
                    }
                }
            }, 1000)

            // Reset idle timer on mouse movement.
            document.body.addEventListener('mousemove', _ => {
                idleTime = 0
                idleWarning = false
            })

            // Reset idle timer on keydown
            document.body.addEventListener('keydown', _ => {
                idleTime = 0
                idleWarning = false
            })
        </script>
    </body>
</html>
