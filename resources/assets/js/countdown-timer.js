import BaseComponent from 'bootstrap/js/src/base-component'

/**
 * ------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------
 */

const NAME = 'countdown-timer'

/**
 * ------------------------------------------------------------------------
 * Class Definition
 * ------------------------------------------------------------------------
 */

class CountdownTimer extends BaseComponent {
    constructor(element) {
        super(element)
        this._started = false
        this.start()
    }

    // Getters

    static get NAME() {
        return NAME
    }

    start() {
        this._config = this._getConfig()
        if (isNaN(this._config.start) || this._started) return
        this._start = new Date(this._config.start * 1000).getTime()
        this._started = true
        this._timerId = setInterval(this._update.bind(this), 1000)
    }

    stop() {
        clearInterval(this._timerId)
        this._started = false
        this._start = 0
        this._update()
    }

    // Private

    _getConfig() {
        const data = this._element.dataset
        let attributes = {}
        Object.keys(data).map((key) => {
            let pureKey = key.replace(/^countdown/, '')
            pureKey =
                pureKey.charAt(0).toLowerCase() +
                pureKey.slice(1, pureKey.length)
            attributes[pureKey] = data[key]
        })
        return attributes
    }

    _getTimeString(delta) {
        const seconds = Math.floor((delta / 1000) % 60)
        const minutes = Math.floor((delta / 1000 / 60) % 60)
        const hours = Math.floor((delta / (1000 * 60 * 60)) % 24)
        const days = Math.floor(delta / (1000 * 60 * 60 * 24))

        let string
        if (days > 1) string = days + ' days'
        else if (days === 1) string = '1 day'
        else if (hours > 0 || minutes > 0)
            string = `${pad(hours, 2)}:${pad(minutes, 2)}:${pad(seconds, 2)}`
        else string = seconds + ' seconds'
        return string
    }

    _update() {
        const current = new Date().getTime()
        const delta = this._start - current
        this._element.innerHTML =
            delta < 0
                ? this._config.textFinished
                : this._config.textCounting.replace(
                      '{}',
                      this._getTimeString(delta)
                  )
    }
}

function pad(n, w, z) {
    z = z || '0'
    n = n + ''
    return n.length >= w ? n : new Array(w - n.length + 1).join(z) + n
}

export default CountdownTimer
