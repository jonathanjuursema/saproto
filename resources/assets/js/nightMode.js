window.nightMode = (_) => {
    for (let i = 0; i < 240; i++) {
        let star = document.createElement('div')
        star.setAttribute('class', 'star')
        document.body.appendChild(star)
    }
}
