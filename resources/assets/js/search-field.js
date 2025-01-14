import BaseComponent from 'bootstrap/js/src/base-component'

/**
 * ------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------
 */

const NAME = 'search-field'

const CLASS_NAME_SEARCH_FIELD = NAME
const CLASS_NAME_RESULTS_CONTAINER = 'search-results'
const CLASS_NAME_RESULT = 'search-result'
const CLASS_NAME_SELECTED_CONTAINER = 'selected-items'
const CLASS_NAME_SELECTED = 'selected-item'

const Default = {
    optionTemplate: (el, item) => (el.innerHTML = item.name),
    selectedTemplate: (item) => item.name ?? item.id,
    sorter: null,
}

const DefaultType = {
    optionTemplate: 'function',
    selectedTemplate: 'function',
    sorter: '(null|function)',
}

/**
 * ------------------------------------------------------------------------
 * Class Definition
 * ------------------------------------------------------------------------
 */

class SearchField extends BaseComponent {
    constructor(element, route, config) {
        super(element)

        this._route = route
        this._config = this._getConfig(config)
        this._name = this._element.name
        this._id = this._element.id ?? this._element.name
        this._multiple = this._element.multiple
        this._required = this._element.required

        this._initializeSearchField()
    }

    // Getters

    static get Default() {
        return Default
    }

    static get DefaultType() {
        return DefaultType
    }

    static get NAME() {
        return NAME
    }

    // Public

    clearSearchField() {
        this._element.value = ''
    }

    clearResults() {
        this._resultsContainer.innerHTML = ''
    }

    // Private

    _initializeSearchField() {
        this._resultsContainer = this._createResultsContainer()
        this._invalidMessage = this._createInvalidMessage()
        this._selectedContainer = this._createSelectedContainer()
        this._inputElement = this._createInputElement()
        this._inputElement.value = this._element.value

        this._element.parentNode.append(this._resultsContainer)
        this._element.parentNode.append(this._invalidMessage)
        if (this._multiple)
            this._element.parentNode.append(this._selectedContainer)
        else this._element.parentNode.append(this._inputElement)

        this._element.classList.add(CLASS_NAME_SEARCH_FIELD)
        this._element.name = ''
        this._element.value = this._element.placeholder
        this._element.required = false

        this._element.form.removeEventListener('submit', preventSubmitBounce)
        this._element.form.addEventListener(
            'submit',
            this._checkRequired.bind(this)
        )
        this._element.onkeyup = debounce(this._search.bind(this))
    }

    _checkRequired(e) {
        const selectedAny =
            this._selectedContainer.children.length !== 0 ||
            this._inputElement.value !== ''
        if (!this._required || selectedAny) return true
        this._invalidMessage.style.display = 'block'
        e.preventDefault()
    }

    _createInvalidMessage() {
        let el = document.createElement('div')
        el.classList.add('invalid-feedback')
        el.innerHTML = 'Please select an option!'
        return el
    }

    _createResultsContainer() {
        let el = document.createElement('div')
        el.classList.add(CLASS_NAME_RESULTS_CONTAINER)
        el.id = this._id + '-search-results'
        el.innerHTML = '<span>type at least 3 characters</span>'
        return el
    }

    _createResultElement(item) {
        let el = document.createElement('div')
        el.classList.add(CLASS_NAME_RESULT)
        this._config.optionTemplate(el, item)
        el.addEventListener('click', (_) => {
            if (this._multiple) this._addSelected(item)
            else this._setSelected(item)
            this._search()
        })
        return el
    }

    _createInputElement() {
        let input = document.createElement('input')
        input.type = 'hidden'
        input.name = this._name
        input.required = this._element.required
        return input
    }

    _createSelectedContainer() {
        let el = document.createElement('div')
        el.classList.add(CLASS_NAME_SELECTED_CONTAINER)
        el.id = this._id + '-selected-items'
        return el
    }

    _createSelectedElement(item) {
        let el = document.createElement('span')
        el.innerHTML = this._config.selectedTemplate(item)
        el.classList.add(CLASS_NAME_SELECTED)
        el.onclick = (_) => {
            el.remove()
            if (this._selectedContainer.children.length === 0) {
                this._element.required = true
                this._selectedContainer.style.display = 'none'
            }
        }
        let input = this._createInputElement()
        input.value = item.id
        el.append(input)
        return el
    }

    _setSelected(item) {
        this._inputElement.value = item.id
        this._element.value = this._config.selectedTemplate(item)
        this._resultsContainer.innerHTML = ''
        this._resultsContainer.append(this._createResultElement(item))
    }

    _addSelected(item) {
        this._element.required = false
        this._selectedContainer.style.display = 'block'
        let newSelectedItem = this._createSelectedElement(item)
        this._selectedContainer.append(newSelectedItem)
        this.clearSearchField()
        this.clearResults()
    }

    _setResults(item) {
        this._resultsContainer.innerHTML = item
    }

    _addResults(item) {
        this._resultsContainer.append(item)
    }

    _search() {
        this.clearResults()

        if (this._element.value.length < 3)
            return this._setResults('<span>type at least 3 characters</span>')
        this._setResults('<span>searching...</span>')

        this._invalidMessage.style.display = 'none'

        get(this._route, { q: this._element.value })
            .then((data) => {
                if (data.length === 0)
                    return this._setResults('<span>no results</span>')
                this.clearResults()
                if (this._config.sorter) data.sort(this._config.sorter)
                data.forEach((item) =>
                    this._addResults(this._createResultElement(item))
                )
            })
            .catch((err) => {
                this._setResults(
                    '<span class="text-danger">there was an error!</span>'
                )
                console.error(err)
            })
    }
}

export default SearchField
