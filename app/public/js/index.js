const nearToBottom = 100;

function loadMore() {
    const url = new URL(window.location.href);
    console.log(url);
}

document.addEventListener('scroll', () => {
    if (
        window.scrollY + window.innerHeight > 
        document.documentElement.scrollHeight - nearToBottom
    ) { 
        loadMore()
        // ... my ajax here
    }
})