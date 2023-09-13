/* eslint-disable no-unused-vars */

import './styles/_main.scss'
import { debounce } from './services/debounce.js'

import { Collapse } from 'bootstrap'

export const $ = document.querySelector.bind(document)
export const $$ = document.querySelectorAll.bind(document)

const $searchInput = $('.su-main-search-input')
const $searchResults = $('.su-main-search-results')
const $mainSearchClean = $('.su-main-search-clean')
const $searchModal = $('#searchModal')

const searchClothings = debounce(() => {
  fetch(`/api/clothings?name=${$searchInput.value}`)
    .then(res => res.json())
    .then(clothings => {
      const results = clothings.results || []
      updateSearchResults(results)
    })
    .catch(err => console.error(err))
}, 250)

const updateSearchResults = clothings => {
  $searchResults.innerHTML = ''
  clothings.forEach(clothing => {
    const $clothing = document.createElement('a')
    $clothing.classList.add('list-group-item')
    $clothing.classList.add('list-group-item-action')
    $clothing.innerHTML = `
      <a href="${clothing._links.webui}">
        ${clothing.name} - <span>${clothing.price}€</span>
      </a>
    `
    $searchResults.appendChild($clothing)
  })
}

const removeSearchResults = () => {
  $searchResults.innerHTML = ''
}

$searchInput.addEventListener('input', searchClothings)
$mainSearchClean.addEventListener('click', removeSearchResults)
await $searchModal.addEventListener('hidden.bs.modal', removeSearchResults)
