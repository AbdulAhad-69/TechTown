// GLOBAL VARIABLE
let products = [];

// 1. MAIN STARTUP FUNCTION
document.addEventListener('DOMContentLoaded', () => {
    // A. Render Cart IMMEDIATELY (Don't wait for DB)
    if (document.getElementById('cartTable')) {
        renderCart();
    }

    // B. Update Badge IMMEDIATELY
    updateCartCount();

    // C. Then Fetch Products from DB (for Shop/Details pages)
    fetchProducts();
});

// 2. FETCH PRODUCTS FROM DATABASE (API)
async function fetchProducts() {
    try {
        const response = await fetch('api_products.php');
        products = await response.json();

        // Safe Number Conversion
        products = products.map(p => {
            p.price = Number(p.price);
            p.id = Number(p.id);
            p.stock = Number(p.stock);
            return p;
        });

        // Initialize Shop/Details Pages
        initPage();
    } catch (error) {
        console.error("Error loading products:", error);
    }
}

// 3. INITIALIZE PAGE LOGIC (Run after DB loads)
function initPage() {
    if (document.getElementById('product-grid')) renderShop();
    if (document.getElementById('detail-container')) renderDetails();
    if (document.getElementById('home-smartphones')) renderHome();
}

// 4. CART PAGE RENDER LOGIC (Runs instantly)
function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const tbody = document.getElementById('cart-body');
    const emptyMsg = document.getElementById('empty-cart-msg');

    if (!tbody) return;

    tbody.innerHTML = '';
    let subtotal = 0;

    if (cart.length === 0) {
        if (emptyMsg) emptyMsg.style.display = 'block';
        if (document.getElementById('cartTable')) document.getElementById('cartTable').style.display = 'none';
        document.querySelector('.cart-summary').style.display = 'none';
    } else {
        if (emptyMsg) emptyMsg.style.display = 'none';
        if (document.getElementById('cartTable')) document.getElementById('cartTable').style.display = 'table';
        if (document.querySelector('.cart-summary')) document.querySelector('.cart-summary').style.display = 'block';

        cart.forEach((item, index) => {
            const total = item.price * item.quantity;
            subtotal += total;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="${item.image}" alt="${item.name}" width="50" style="object-fit:contain; border:1px solid #ddd; padding:2px;">
                            <div>${item.name}</div>
                        </div>
                    </td>
                    <td>৳ ${item.price.toLocaleString()}</td>
                    <td>
                        <input type="number" value="${item.quantity}" min="1" style="width:50px; padding:5px; text-align:center;" onchange="updateCartQty(${index}, this.value)">
                    </td>
                    <td>৳ ${total.toLocaleString()}</td>
                    <td>
                        <button onclick="removeCartItem(${index})" style="color:red; background:none; border:none; cursor:pointer; font-size:16px;"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });
    }

    const subTotalEl = document.getElementById('sub-total');
    const finalTotalEl = document.getElementById('final-total');

    if (subTotalEl) subTotalEl.innerText = '৳ ' + subtotal.toLocaleString();
    if (finalTotalEl) finalTotalEl.innerText = '৳ ' + (subtotal + 120).toLocaleString();
}

// 5. HELPER FUNCTIONS
window.updateCartQty = function (index, newQty) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (newQty < 1) newQty = 1;
    cart[index].quantity = parseInt(newQty);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
};

window.removeCartItem = function (index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
};

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const count = cart.reduce((sum, i) => sum + i.quantity, 0);
    const badge = document.getElementById('cart-count');
    if (badge) badge.innerText = `(${count})`;
}

// 6. SHOP/DETAILS ACTIONS
function addToCart(id, alertUser = true) {
    // Use 'products' if available, otherwise check if we can add just by ID (rare case)
    const product = products.find(p => p.id === id);
    if (!product) return;

    if (product.stock < 1) return alert("Sorry, this item is out of stock!");

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existing = cart.find(i => i.id === id);

    if (existing) {
        if (existing.quantity < product.stock) {
            existing.quantity++;
        } else {
            return alert("Max stock reached for this item!");
        }
    } else {
        cart.push({ id: product.id, name: product.name, price: product.price, image: product.image, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    if (alertUser) alert("Added to cart!");
}

function buyNow(id) {
    addToCart(id, false);
    window.location.href = "checkout.php";
}

// 7. SHOP PAGE RENDERERS
function filterAndSort() {
    const grid = document.getElementById('product-grid');
    const searchInput = document.querySelector('.search-bar');
    const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
    const cats = [...document.querySelectorAll('.filter-cat:checked')].map(c => c.value);
    const conds = [...document.querySelectorAll('.filter-cond:checked')].map(c => c.value);
    const min = parseInt(document.getElementById('min').value) || 0;
    const max = parseInt(document.getElementById('max').value) || 9999999;
    const sortValue = document.getElementById('sort-select').value;

    let filtered = products.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(searchQuery);
        const matchesCat = cats.length === 0 || cats.includes(p.category);
        const matchesCond = conds.length === 0 || conds.includes(p.condition_type);
        const matchesPrice = p.price >= min && p.price <= max;
        return matchesSearch && matchesCat && matchesCond && matchesPrice;
    });

    if (sortValue === 'low-high') filtered.sort((a, b) => a.price - b.price);
    else if (sortValue === 'high-low') filtered.sort((a, b) => b.price - a.price);

    if (filtered.length === 0) {
        grid.innerHTML = '<p style="text-align:center; grid-column:1/-1;">No products found.</p>';
        document.getElementById('count').innerText = 0;
    } else {
        grid.innerHTML = filtered.map(p => `
            <a href="product-details.html?id=${p.id}" class="product-card">
                <div class="product-img"><img src="${p.image}" alt="${p.name}"></div>
                <div class="product-info">
                    <h3>${p.name}</h3>
                    <div class="product-meta">${p.category} | ${p.condition_type}</div>
                    <div class="product-price">৳ ${p.price.toLocaleString()}</div>
                    ${p.stock < 1 ? '<div style="color:red; font-size:12px; font-weight:bold;">OUT OF STOCK</div>' : ''}
                </div>
            </a>
        `).join('');
        document.getElementById('count').innerText = filtered.length;
    }
}

function renderShop() {
    // Initial Filter
    filterAndSort();

    // Listeners
    document.querySelectorAll('input').forEach(i => i.addEventListener('change', filterAndSort));
    document.getElementById('sort-select')?.addEventListener('change', filterAndSort);
    document.querySelector('.search-bar')?.addEventListener('input', filterAndSort);

    // URL Params
    const params = new URLSearchParams(window.location.search);
    const urlSearch = params.get('search');
    const urlCat = params.get('category');

    if (urlSearch) {
        document.querySelector('.search-bar').value = urlSearch;
        filterAndSort();
    } else if (urlCat) {
        document.querySelectorAll('.filter-cat').forEach(cb => {
            if (cb.value === urlCat) cb.checked = true;
        });
        filterAndSort();
    }
}

function renderDetails() {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get('id'));
    const product = products.find(p => p.id === id);
    const container = document.getElementById('detail-container');

    if (product) {
        const buyBtn = product.stock > 0
            ? `<button class="btn-buy" onclick="buyNow(${product.id})" style="background:var(--primary-orange); color:white; border:none; padding:15px 40px; font-weight:bold; border-radius:6px; cursor:pointer;">Buy Now</button>`
            : `<button disabled style="background:#ccc; color:#666; border:none; padding:15px 40px; font-weight:bold; border-radius:6px; cursor:not-allowed;">Out of Stock</button>`;

        const cartBtn = product.stock > 0
            ? `<button class="btn-cart" onclick="addToCart(${product.id})" style="border:2px solid var(--primary-orange); background:white; color:var(--primary-orange); padding:15px 30px; font-weight:bold; border-radius:6px; cursor:pointer; margin-right:10px;">Add to Cart</button>`
            : ``;

        container.innerHTML = `
            <div class="details-img"><img src="${product.image}" alt="${product.name}"></div>
            <div class="details-info">
                <span style="background:var(--primary-orange); color:white; padding:4px 10px; border-radius:4px; font-size:12px;">${product.condition_type}</span>
                <h1 style="margin:15px 0;">${product.name}</h1>
                <h2 style="color:var(--primary-orange); margin-bottom:20px;">৳ ${product.price.toLocaleString()}</h2>
                <p><strong>Stock:</strong> ${product.stock > 0 ? product.stock + ' Available' : '<span style="color:red">Sold Out</span>'}</p>
                <p style="margin-top:10px;">${product.description}</p>
                <div style="margin-top:30px;">
                    ${cartBtn}
                    ${buyBtn}
                </div>
            </div>
        `;
    } else {
        container.innerHTML = '<h2>Product not found!</h2>';
    }
}

function renderHome() {
    const phones = document.getElementById('home-smartphones');
    const laptops = document.getElementById('home-laptops');

    renderCategory(phones, 'Smartphones');
    renderCategory(laptops, 'Laptops');
}

function renderCategory(container, cat) {
    if (!container) return;
    const items = products.filter(p => p.category === cat).slice(0, 5);
    container.innerHTML = items.map(p => `
        <a href="product-details.html?id=${p.id}" class="product-card">
            <div class="product-img"><img src="${p.image}" alt="${p.name}"></div>
            <div class="product-info">
                <h3>${p.name}</h3>
                <div class="product-price">৳ ${p.price.toLocaleString()}</div>
            </div>
        </a>
    `).join('');
}