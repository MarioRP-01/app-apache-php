const mosaic = document.getElementById('mosaic')
const near_to_bottom = 100
const url = new URL(window.location.href)

function fetchClothing() {

}

function loadMore() {
    const url = new URL(window.location.href)
    console.log(url)
}

document.addEventListener('scroll', () => {
    if (
        window.scrollY + window.innerHeight >
        document.documentElement.scrollHeight - near_to_bottom
    ) {
        loadMore()
        // ... my ajax here
    }
})

document.addEventListener("DOMContentLoaded", () => {

    const base_url = new URL(url.origin)
    const clothing_url = new URL('/api/clothings', base_url)

    console.log(clothing_url)

    fetch(clothing_url)
        .then(response => response.json())
        .then(data => {
            let mosaicItems = ''
            data.results.forEach(element => {
                mosaicItems += `
                    <div class="mosaic-item">
                        <img src="${element._expandable.image}" alt="${element.name}">
                    </div>
                `
            })
            mosaic.innerHTML += mosaicItems
        })
        .catch(error => console.error(error))
})