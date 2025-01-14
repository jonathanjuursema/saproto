window.broto = (_) => {
    console.log('BROTO')
    document.querySelectorAll('img').forEach((el) => {
        if (el.classList.contains('rounded-circle'))
            el.src = '/images/default-avatars/cookiemonster.jpg'
        else if (el.src.includes('images/logo/inverse.png'))
            el.src = '/images/logo/broto-inverse.png'
        else if (el.src.includes('images/logo/regular.png'))
            el.src = '/images/logo/broto-regular.png'
    })

    document.querySelectorAll('*').forEach((el) => {
        el.childNodes.forEach((child) => {
            if (child.nodeType === 3)
                child.nodeValue = child.nodeValue.replace(
                    /(Proto)(.?)\b/g,
                    'Broto$2'
                )
        })
    })
}
