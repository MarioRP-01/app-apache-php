const cards = document.getElementById('product-cards')
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
            let items = ''
            data.results.forEach(element => {
                items += `
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 position-relative">
                        <div class="product-card">
                        <img src="${element._expandable.image}" alt="${element.file_name}">
                        <div class="product-card-body">
                            <div class="product-name">${element.file_name}</div>
                            <div class="container product-card-body-content">
                                <div class="product-label">${element.label}</div>
                                <div class="product-description">
                                    <p class="ellipsis-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae dolor aperiam suscipit, consectetur, nostrum, illum corrupti libero aliquam ipsum debitis quos explicabo non eaque nesciunt beatae inventore assumenda eius. Possimus iusto distinctio debitis esse odio ipsam a labore animi magnam!</p>
                                </div>
                            </div>
                            <a class="link-show-product"> Show More </a>
                        </div>
                        </div>
                    </div>
                `
            })
            cards.innerHTML += items
        })
        .catch(error => console.error(error))
})