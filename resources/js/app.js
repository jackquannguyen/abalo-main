import './bootstrap';
import { createApp } from 'vue';
import * as math from 'mathjs';
import NewSite from '../components/NewSite.vue';
import ArticleSearch from '../components/ArticleSearch.vue';
import ArticleCreate from '../components/ArticleCreate.vue';
import PrimeVue from 'primevue/config';



"use strict";

// Aufgabe 7 run vue
// const elementVue = document.getElementById('vue-app');

// if (elementVue) {
//     createApp({
//         data() {
//             return {
//                 message: 'Hallo Welt'
//             };
//         }
//     }).mount('#vue-app');
// }


//Refactoring from public/js to app.js
// cookiecheck.js
function getCookie(name) {
    const cookies = document.cookie.split("; ");

    for (const cookie of cookies) {
        const parts = cookie.split("=");

        if (parts[0] === name) {
            return parts[1];
        }
    }

    return null;
}

function setCookieSeconds(name, value, seconds) {
    const date = new Date();
    date.setTime(date.getTime() + seconds * 1000);

    document.cookie = name + "=" + value
        + "; expires=" + date.toUTCString()
        + "; path=/; SameSite=Lax";
}

// document.addEventListener("DOMContentLoaded", function () {
//     if (getCookie("cookieAccepted") === "true") {
//         return;
//     }

//     const banner = document.createElement("div");
//     banner.id = "cookie-banner";

//     banner.innerHTML = `
//         <div id="cookie-text">
//             Diese Webseite verwendet Cookies, um die Benutzerfreundlichkeit zu verbessern.
//         </div>
//         <button id="accept-cookies">Akzeptieren</button>
//     `;

//     document.body.appendChild(banner);

//     const button = document.getElementById("accept-cookies");

//     button.addEventListener("click", function () {
//         setCookieSeconds("cookieAccepted", "true", 30);
//         banner.remove();
//     });
// });


// navigation.js
const navigationMenu = {
    menuList: [
        { title: "Home", link: "/" },
        { title: "Kategorien", link: "/articles" },
        { title: "Verkaufen", link: "/newarticle" },
        {
            title: "Unternehmen",
            children: [
                { title: "Philosophie", link: "/philosophie" },
                { title: "Karriere", link: "/karriere" }
            ]
        }
    ],

    createMenu(items, className = "menu") {
        const ul = document.createElement("ul");
        ul.className = className;

        for (const item of items) {
            const li = document.createElement("li");
            const a = document.createElement("a");

            if (item.children) { //chỉ item nào có children ms vào if (Unternehmen)
                a.textContent = item.title + " ▼";
                const submenu = this.createMenu(item.children, "submenu");
                a.addEventListener("click", function (event) { //classList là d.sách class css có trên 1 element html
                    submenu.classList.toggle("show"); //toggle tự nó đã thêm, xóa "show", ko cần add ở trc
                    if (submenu.classList.contains("show")) {
                        a.textContent = item.title + " ▲";
                    } else {
                        a.textContent = item.title + " ▼";
                    }
                });

                li.appendChild(a); //appendChild là thêm 1 ele con vào ele cha
                li.appendChild(submenu);
            } else {
                a.href = item.link;
                a.textContent = item.title;
                li.appendChild(a);
            }

            ul.appendChild(li);
        }

        return ul;
    },

    render(containerId) {
        const navigation = document.getElementById(containerId);
        if (navigation) navigation.appendChild(this.createMenu(this.menuList));
    }
};

// render menu
// navigationMenu.render("navigation");


// document.addEventListener('DOMContentLoaded', function () {
//     console.log('newarticle.js wurde geladen');

//     const container = document.getElementById('article-form-container');

//     if (!container) {
//         console.error('Container #article-form-container wurde nicht gefunden.');
//         return;
//     }

//     const csrfMeta = document.querySelector('meta[name="csrf-token"]');
//     const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

//     const form = document.createElement('form');
//     form.id = 'new-article-form';

//     // Quan trọng: không set action="/articles" và không set method="POST"
//     // Vì Aufgabe 9 yêu cầu gửi bằng AJAX/fetch đến /api/articles.

//     const nameLabel = document.createElement('label');
//     nameLabel.setAttribute('for', 'name');
//     nameLabel.textContent = 'Name *';

//     const nameInput = document.createElement('input');
//     nameInput.id = 'name';
//     nameInput.name = 'name';
//     nameInput.type = 'text';

//     const priceLabel = document.createElement('label');
//     priceLabel.setAttribute('for', 'price');
//     priceLabel.textContent = 'Preis in Cent *';

//     const priceInput = document.createElement('input');
//     priceInput.id = 'price';
//     priceInput.name = 'price';
//     priceInput.type = 'number';
//     priceInput.min = '1';

//     const descriptionLabel = document.createElement('label');
//     descriptionLabel.setAttribute('for', 'description');
//     descriptionLabel.textContent = 'Beschreibung';

//     const descriptionInput = document.createElement('textarea');
//     descriptionInput.id = 'description';
//     descriptionInput.name = 'description';
//     descriptionInput.rows = 5;

//     const submitButton = document.createElement('button');
//     submitButton.type = 'submit';
//     submitButton.textContent = 'Artikel erstellen';

//     const messageBox = document.createElement('p');
//     messageBox.id = 'article-message';
//     messageBox.className = 'info';

//     form.appendChild(nameLabel);
//     form.appendChild(nameInput);

//     form.appendChild(priceLabel);
//     form.appendChild(priceInput);

//     form.appendChild(descriptionLabel);
//     form.appendChild(descriptionInput);

//     form.appendChild(submitButton);

//     container.appendChild(form);
//     container.appendChild(messageBox);

//     form.addEventListener('submit', async function (event) {
//         event.preventDefault(); //nếu thiếu dòng này browser sẽ redirect form về /articles

//         console.log('AJAX submit läuft');

//         const name = nameInput.value.trim();
//         const price = Number(priceInput.value);
//         const description = descriptionInput.value.trim();

//         if (name === '') {
//             messageBox.className = 'error';
//             messageBox.textContent = 'Fehler: Der Name darf nicht leer sein.';
//             return;
//         }

//         if (math.smallerEq(price, 0)) { // import from math.js
//             messageBox.className = 'error';
//             messageBox.textContent = 'Fehler: Der Preis muss größer als 0 sein.';
//             return;
//         }

//         try {
//             //Đoạn chính của Auf 9
//             const response = await fetch('/api/articles', {
//                 method: 'POST',
//                 headers: {
//                     'Accept': 'application/json', //client muốn nhận json
//                     'Content-Type': 'application/json', //dữ liệu gửi lên là json
//                     'X-CSRF-TOKEN': csrfToken
//                 },
//                 body: JSON.stringify({
//                     name: name,
//                     price: price,
//                     description: description
//                 })
//             });

//             const data = await response.json();

//             if (!response.ok || data.success === false) {
//                 messageBox.className = 'error';
//                 messageBox.textContent = data.error || 'Fehler: Artikel konnte nicht gespeichert werden.';
//                 return;
//             }

//             messageBox.className = 'success';
//             messageBox.textContent = 'Erfolgreich. Neue Artikel-ID: ' + data.id;

//             form.reset();

//         } catch (error) {
//             console.error(error);
//             messageBox.className = 'error';
//             messageBox.textContent = 'Fehler: AJAX-Anfrage konnte nicht durchgeführt werden.';
//         }
//     });
// });


// shoppingcart.js
let currentShoppingCartId = null;

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function formatPrice(cents) {
    const value = math.divide(math.bignumber(cents || 0), 100); // function import from math.js
    return Number(value).toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR'
    });
}

function showCartMessage(message) {
    const box = document.getElementById('cart-message');
    if (box) {
        box.textContent = message || '';
    }
}

async function loadCart() {
    const response = await fetch('/api/shoppingcart', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (!response.ok || data.success === false) {
        showCartMessage(data.error || 'Warenkorb konnte nicht geladen werden.');
        return;
    }

    renderCart(data);
}

async function addToCart(articleId) {
    const response = await fetch('/api/shoppingcart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ articleid: articleId })
    });

    const data = await response.json();

    if (!response.ok || data.success === false) {
        showCartMessage(data.error || 'Artikel konnte nicht hinzugefügt werden.');
        return;
    }

    renderCart(data);
}

async function removeFromCart(articleId) {
    if (!currentShoppingCartId) {
        showCartMessage('Kein Warenkorb gefunden.');
        return;
    }

    const response = await fetch(`/api/shoppingcart/${currentShoppingCartId}/articles/${articleId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        }
    });

    const data = await response.json();

    if (!response.ok || data.success === false) {
        showCartMessage(data.error || 'Artikel konnte nicht entfernt werden.');
        return;
    }

    renderCart(data);
}

function renderCart(data) {
    currentShoppingCartId = data.shoppingcartid;

    const list = document.getElementById('cart-items');
    const sum = document.getElementById('cart-sum');

    if (!list || !sum) {
        return;
    }

    list.innerHTML = '';

    if (!data.items || data.items.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'Der Warenkorb ist leer.';
        list.appendChild(li);
    } else {
        data.items.forEach(item => {
            const li = document.createElement('li');

            const text = document.createElement('span');
            text.textContent = `${item.name} - ${formatPrice(item.price)} `;

            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = '-';
            button.addEventListener('click', () => removeFromCart(item.article_id));

            li.appendChild(text);
            li.appendChild(button);
            list.appendChild(li);
        });
    }

    sum.textContent = formatPrice(data.sum);
}

// document.addEventListener('DOMContentLoaded', () => {
//     loadCart();

//     document.querySelectorAll('.add-to-cart').forEach(button => {
//         button.addEventListener('click', () => {
//             const articleId = button.dataset.id;
//             addToCart(articleId);
//         });
//     });

document.addEventListener('DOMContentLoaded', () => {

    // Only render JS nav on non-Vue pages
    if (!document.getElementById('newsite-app') && !document.getElementById('article-create-app')) {
        navigationMenu.render("navigation");
    }

    // Cookie banner
    if (getCookie("cookieAccepted") !== "true") {
        const banner = document.createElement("div");
        banner.id = "cookie-banner";
        banner.innerHTML = `
            <div id="cookie-text">
                Diese Webseite verwendet Cookies, um die Benutzerfreundlichkeit zu verbessern.
            </div>
            <button id="accept-cookies">Akzeptieren</button>
        `;
        document.body.appendChild(banner);
        document.getElementById("accept-cookies").addEventListener("click", function () {
            setCookieSeconds("cookieAccepted", "true", 30);
            banner.remove();
        });
    }

    // Shopping cart (only on pages that have it)
    if (document.getElementById('cart-items')) {
        loadCart();
    }

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => addToCart(button.dataset.id));
    });


//M4 - Auf 10, 13
const searchElement = document.getElementById('article-search-app');
if (searchElement) {
    //createApp(ArticleSearch).mount(searchElement);
    const searchApp = createApp(ArticleSearch);
    searchApp.use(PrimeVue);
    searchApp.mount(searchElement);
}

const createElement = document.getElementById('article-create-app');
if (createElement) {
    //createApp(ArticleCreate).mount(createElement);
    const articleApp = createApp(ArticleCreate);
    articleApp.use(PrimeVue);
    articleApp.mount(createElement);
}
});


const elementVue = document.getElementById('newsite-app');
if (elementVue) {
    //createApp(NewSite).mount(elementVue);
    const newSiteApp = createApp(NewSite);
    newSiteApp.use(PrimeVue);
    newSiteApp.mount(elementVue);
}

// const footerEl = document.getElementById('footer-app');
//     if (footerEl) createApp(SiteFooter).mount(footerEl);
